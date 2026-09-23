<?php

declare(strict_types = 1);

namespace PublicModule;

use Admin\Dial\AttendanceReasonDial;
use Admin\Dial\AttendanceTypeDial;
use Core\Presenter\BasePresenter;
use Nette\Application\Attributes\Requires;
use Nette\Database\Explorer;
use Nette\Database\Table\Selection;
use Nette\DI\Attributes\Inject;
use Nette\Utils\DateTime;
use Public\Component\Chat;
use Public\Component\ChatFactory;

class PublicPresenter extends BasePresenter
{
	private const TermCount = 3;

	/**
	 * Začátek tréninku, zároveň uzávěrka přihlašování
	 */
	private const TrainingTime = '20:00';

	#[Inject]
	public Explorer $Database;

	#[Inject]
	public ChatFactory $ChatFactory;


	public function renderDefault(): void
	{
		$termSelection = $this->Database->table('term')
			->where('date >= ?', (new DateTime)->format('Y-m-d'))
			->order('date')
			->limit(self::TermCount);

		$termArray = [];
		$termLockedArray = [];
		$termAttendanceCountArray = [];
		$termReasonCountArray = [];

		foreach($termSelection as $row)
		{
			$termArray[$row->date->format('Y-m-d')] = $row->available;

			$termLockedArray[$row->date->format('Y-m-d')] = !$this->isBeforeDeadline($row->date);
			$termAttendanceCountArray[$row->date->format('Y-m-d')] = 0;
			$termReasonCountArray[$row->date->format('Y-m-d')] = 0;
		}

		$pubReasonArray = array_keys(AttendanceReasonDial::getPubArray());

		$attendanceArray = [];

		if($termArray)
		{
			$selection = $this->Database->table('attendance')
				->select('term.date, player_id, type, reason')
				->where('player.active', 1)
				->where('term.date', array_keys($termArray));

			foreach($selection as $row)
			{
				$attendanceArray[$row->date->format('Y-m-d')][$row->player_id] = $row;

				if($row->type === AttendanceTypeDial::YES)
				{
					$termAttendanceCountArray[$row->date->format('Y-m-d')]++;
				}

				if(in_array($row->reason, $pubReasonArray, true))
				{
					$termReasonCountArray[$row->date->format('Y-m-d')]++;
				}
			}
		}

		$teamSelection = $this->getTeamSelection();

		/* barva jde rovnou do stylu, tak ji stejně jako v chatu pustíme dál jen ve tvaru #rrggbb */
		$this->template->addFilter('color', fn(?string $color) => preg_match('~^#[0-9a-fA-F]{6}$~', (string) $color) ? $color : 'inherit');

		$this->template->teamSelection = $teamSelection;
		$this->template->playerArray = $this->getPlayerArray($teamSelection);
		$this->template->attendanceArray = $attendanceArray;
		$this->template->termArray = $termArray;
		$this->template->termLockedArray = $termLockedArray;
		$this->template->termAttendanceCountArray = $termAttendanceCountArray;
		$this->template->termReasonCountArray = $termReasonCountArray;
		$this->template->pubReasonArray = AttendanceReasonDial::getPubArray();
		$this->template->excuseReasonArray = AttendanceReasonDial::getExcuseArray();
		$this->template->deadline = self::TrainingTime;
	}


	/**
	 * Parametry jsou nepovinné, aby šel v šabloně vygenerovat odkaz bez nich
	 */
	#[Requires(methods: 'POST', sameOrigin: true)]
	public function handleSaveAttendance(?string $term = null, ?int $playerId = null, ?string $attendance = null): void
	{
		$typeArray = [AttendanceTypeDial::YES, AttendanceTypeDial::NO, AttendanceTypeDial::WAITING];

		if(!in_array($attendance, $typeArray, true))
		{
			$this->error('Neplatný požadavek', 400);
		}

		$this->Database->query(
			'INSERT INTO attendance ? ON DUPLICATE KEY UPDATE type = VALUES(type)',
			['player_id' => $playerId, 'term_id' => $this->getEditableTermId($term, $playerId), 'type' => $attendance]
		);

		$this->redrawAttendance();
	}


	/**
	 * "Uvidím" (AttendanceReasonDial::WAITING) důvod maže
	 */
	#[Requires(methods: 'POST', sameOrigin: true)]
	public function handleSaveReason(?string $term = null, ?int $playerId = null, ?string $reason = null): void
	{
		$reasonArray = array_keys(AttendanceReasonDial::getPubArray() + AttendanceReasonDial::getExcuseArray());

		if($reason !== AttendanceReasonDial::WAITING && !in_array($reason, $reasonArray, true))
		{
			$this->error('Neplatný požadavek', 400);
		}

		$reason = $reason === AttendanceReasonDial::WAITING ? null : $reason;

		$this->Database->query(
			'INSERT INTO attendance ? ON DUPLICATE KEY UPDATE reason = VALUES(reason)',
			['player_id' => $playerId, 'term_id' => $this->getEditableTermId($term, $playerId), 'type' => AttendanceTypeDial::WAITING, 'reason' => $reason]
		);

		$this->redrawAttendance();
	}


	/**
	 * Termín musí existovat, konat se a nesmí být po uzávěrce, hráč musí být aktivní
	 */
	private function getEditableTermId(?string $term, ?int $playerId): int
	{
		$termRow = $this->Database->table('term')
			->where('date', $term)
			->where('available', 1)
			->fetch();

		$playerExists = $this->Database->table('player')
			->where('id', $playerId)
			->where('active', 1)
			->count('*') > 0;

		if(!$termRow || !$playerExists || !$this->isBeforeDeadline($termRow->date))
		{
			$this->error('Neplatný požadavek', 400);
		}

		return $termRow->id;
	}


	/**
	 * Docházku lze měnit do začátku tréninku
	 */
	private function isBeforeDeadline(\DateTimeInterface $date): bool
	{
		return new DateTime < DateTime::from($date->format('Y-m-d') . ' ' . self::TrainingTime);
	}


	private function redrawAttendance(): void
	{
		if($this->isAjax())
		{
			$this->redrawControl('attendance');
		}
		else
		{
			$this->redirect('this');
		}
	}


	private function getPlayerArray(Selection $teamSelection): array
	{
		$playerArray = [];

		foreach($teamSelection as $teamRow)
		{
			$playerArray[$teamRow->id] = $this->Database->table('player')
				->where('team_id', $teamRow->id)
				->where('active', 1)
				->order('nick')
				->fetchPairs('id', 'nick');
		}

		return $playerArray;
	}


	private function getTeamSelection(): Selection
	{
		return $this->Database->table('team')
			->select('team.name, team.id, team.color, COUNT(:player.id) AS player_count')
			->where('team.active', 1)
			->where(':player.active', 1)
			->group('team.id')
			->having('player_count > 0')
			->order('team.position, team.name');
	}


	public function createComponentChat(): Chat
	{
		return $this->ChatFactory->create();
	}
}
