<?php

declare(strict_types = 1);

namespace PublicModule;

use Admin\Dial\AttendanceReasonDial;
use Admin\Dial\AttendanceTypeDial;
use Core\Presenter\BasePresenter;
use Nette\Database\Explorer;
use Nette\Database\Table\Selection;
use Nette\DI\Attributes\Inject;
use Nette\Utils\DateTime;

class PublicPresenter extends BasePresenter
{
	#[Inject]
	public Explorer $Database;


	public function actionDefault(): void
	{
		$dateFrom = new DateTime;
		$dateTo = $dateFrom->modifyClone('+ 3 weeks');

		$termSelection = $this->Database->table('term')
			->where('date >= ?', $dateFrom->format('Y-m-d'))
			->where('date <= ?', $dateTo->format('Y-m-d'));

		$termArray = [];
		$termAttendanceCountArray = [];
		$termReasonCountArray = [];

		foreach($termSelection as $row)
		{
			$termArray[$row->date->format('Y-m-d')] = $row->available;

			$termAttendanceCountArray[$row->date->format('Y-m-d')] = 0;
			$termReasonCountArray[$row->date->format('Y-m-d')] = 0;
		}

		$selection = $this->Database->table('attendance')
			->select('term.date, player_id, type, reason')
			->where('term.date >= ?', $dateFrom->format('Y-m-d'))
			->where('term.date <= ?', $dateTo->format('Y-m-d'));

		$attendanceArray = [];

		foreach($selection as $row)
		{
			$attendanceArray[$row->date->format('Y-m-d')][$row->player_id] = $row;

			if($row->type === AttendanceTypeDial::YES)
			{
				$termAttendanceCountArray[$row->date->format('Y-m-d')]++;
			}

			if($row->reason !== AttendanceReasonDial::WAITING)
			{
				$termReasonCountArray[$row->date->format('Y-m-d')]++;
			}
		}

		$teamSelection = $this->getTeamSelection();

		$this->template->teamSelection = $teamSelection;
		$this->template->playerArray = $this->getPlayerArray($teamSelection);
		$this->template->attendanceArray = $attendanceArray;
		$this->template->termArray = $termArray;
		$this->template->termAttendanceCountArray = $termAttendanceCountArray;
		$this->template->termReasonCountArray = $termReasonCountArray;
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
			->where(':player.active')
			->group('team.id')
			->having('player_count > 0');
	}
}
