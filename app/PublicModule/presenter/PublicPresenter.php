<?php

namespace PublicModule;

use Nette\Utils\DateTime;

class PublicPresenter extends \Core\Presenter\BasePresenter
{
	/**
	 * @inject
	 * @var \Nette\Database\Explorer
	 */
	public $Database;


	public function actionDefault(): void
	{
		$dateFrom = new DateTime;
		$dateTo = $dateFrom->modifyClone('+ 3 weeks');

		$teamArray = $this->getTeamArray();

		$termSelection = $this->Database->table('term')
			->where('date >= ?', $dateFrom->format('Y-m-d'))
			->where('date <= ?', $dateTo->format('Y-m-d'));

		$termArray = [];
		$termAttendanceCountArray = [];
		$termReasonCountArray = [];

		foreach($termSelection as $row)
		{
			$termArray[$row->date->format('Ymd')] = $row->available;
			
			$termAttendanceCountArray[$row->date->format('Ymd')] = 0;
			$termReasonCountArray[$row->date->format('Ymd')] = 0;
		}

		$selection = $this->Database->table('attendance')
			->select('term.date, player_id, type, reason')
			->where('term.date >= ?', $dateFrom->format('Y-m-d'))
			->where('term.date <= ?', $dateTo->format('Y-m-d'));

		$attendanceArray = [];
		
		foreach($selection as $row)
		{
			$attendanceArray[$row->date->format('Ymd')][$row->player_id] = $row;

			if($row->type === \Admin\Dial\AttendanceTypeDial::YES)
			{
				$termAttendanceCountArray[$row->date->format('Ymd')]++;
			}

			if($row->reason !== \Admin\Dial\AttendanceReasonDial::WAITING)
			{
				$termReasonCountArray[$row->date->format('Ymd')]++;
			}
		}

		$this->template->teamArray = $teamArray;
		$this->template->playerArray = $this->getPlayerArray($teamArray);
		$this->template->attendanceArray = $attendanceArray;
		$this->template->termArray = $termArray;
		$this->template->termAttendanceCountArray = $termAttendanceCountArray;
		$this->template->termReasonCountArray = $termReasonCountArray;
	}


	private function getPlayerArray(array $teamArray): array
	{
		$playerArray = [];

		foreach($teamArray as $teamId => $name)
		{
			$playerArray[$teamId] = $this->Database->table('player')
				->where('team_id', $teamId)
				->where('active', 1)
				->order('nick')
				->fetchPairs('id', 'nick');
		}

		$playerArray[null] = $this->Database->table('player')
			->where('team_id IS NULL')
			->where('active', 1)
			->order('nick')
			->fetchPairs('id', 'nick');

		return $playerArray;
	}

	private function getTeamArray(): array
	{
		$teamArray = $this->Database->table('team')
			->where('active', 1)
			->fetchPairs('id', 'name');

		$teamArray[null] = 'Hosti';

		return $teamArray;
	}
}
