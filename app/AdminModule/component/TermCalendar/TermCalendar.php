<?php

declare(strict_types = 1);

namespace Admin\Component;

use Admin\Dial\AttendanceTypeDial;
use Core\Component\BaseComponent;
use Nette\Application\Attributes\Persistent;
use Nette\Database\Explorer;
use Nette\Utils\DateTime;

/**
 * Měsíční kalendář termínů. Záměrně bez AJAXu – překlikaný měsíc tak zůstane
 * v URL a přežije odskok do úpravy termínu a zpět.
 */
class TermCalendar extends BaseComponent
{
	private const MonthFormat = 'Y-m';

	/**
	 * @var array<int, string>
	 */
	private const MonthNameArray = [
		1 => 'Leden',
		2 => 'Únor',
		3 => 'Březen',
		4 => 'Duben',
		5 => 'Květen',
		6 => 'Červen',
		7 => 'Červenec',
		8 => 'Srpen',
		9 => 'Září',
		10 => 'Říjen',
		11 => 'Listopad',
		12 => 'Prosinec',
	];

	/**
	 * Zobrazený měsíc ve tvaru Y-m, prázdný znamená aktuální
	 */
	#[Persistent]
	public string $month = '';


	public function __construct
	(
		private readonly Explorer $Database
	)
	{
	}


	public function beforeRender(): void
	{
		$monthStart = $this->getMonthStart();

		/* mřížka vždy začíná v pondělí a končí v neděli, ať měsíc padne kamkoliv */
		$gridStart = $monthStart->modifyClone('monday this week');
		$gridEnd = $monthStart->modifyClone('last day of this month')
			->modify('sunday this week');

		$termArray = [];
		$termIdArray = [];

		$selection = $this->Database->table('term')
			->where('date >= ?', $gridStart->format('Y-m-d'))
			->where('date <= ?', $gridEnd->format('Y-m-d'));

		foreach($selection as $row)
		{
			$termArray[$row->date->format('Y-m-d')] = $row;
			$termIdArray[] = $row->id;
		}

		$weekArray = [];

		for($day = $gridStart; $day <= $gridEnd; $day = $day->modifyClone('+1 day'))
		{
			$weekArray[$day->format('o-W')][] = $day;
		}

		$this->template->weekArray = $weekArray;
		$this->template->termArray = $termArray;
		$this->template->countArray = $this->getCountArray($termIdArray);
		$this->template->monthKey = $monthStart->format(self::MonthFormat);
		$this->template->monthLabel = self::MonthNameArray[(int) $monthStart->format('n')] . ' ' . $monthStart->format('Y');
		$this->template->prevMonth = $monthStart->modifyClone('-1 month')->format(self::MonthFormat);
		$this->template->nextMonth = $monthStart->modifyClone('+1 month')->format(self::MonthFormat);
		$this->template->currentMonth = (new DateTime)->format(self::MonthFormat);
		$this->template->today = (new DateTime)->format('Y-m-d');
		$this->template->dayNameArray = ['Po', 'Út', 'St', 'Čt', 'Pá', 'So', 'Ne'];
	}


	/**
	 * První den zobrazeného měsíce. Nesmysl v URL bere jako aktuální měsíc.
	 */
	private function getMonthStart(): DateTime
	{
		if(preg_match('~^\d{4}-\d{2}$~', $this->month))
		{
			$month = DateTime::createFromFormat('Y-m-d H:i:s', $this->month . '-01 00:00:00');

			if($month)
			{
				return $month;
			}
		}

		return (new DateTime)->modify('first day of this month')
			->setTime(0, 0);
	}


	/**
	 * Počty docházky aktivních hráčů, pojmenované, ať šablona nemusí do číselníku
	 *
	 * @param array<int> $termIdArray
	 * @return array<int, array{yes: int, no: int, waiting: int}>
	 */
	private function getCountArray(array $termIdArray): array
	{
		$countArray = [];

		foreach($termIdArray as $termId)
		{
			$countArray[$termId] = ['yes' => 0, 'no' => 0, 'waiting' => 0];
		}

		if(!$termIdArray)
		{
			return $countArray;
		}

		$keyArray = [
			AttendanceTypeDial::YES => 'yes',
			AttendanceTypeDial::NO => 'no',
			AttendanceTypeDial::WAITING => 'waiting',
		];

		$selection = $this->Database->table('attendance')
			->select('term_id, type, COUNT(*) AS count')
			->where('player.active', 1)
			->where('term_id', $termIdArray)
			->group('term_id, type');

		foreach($selection as $row)
		{
			if(isset($keyArray[$row->type]))
			{
				$countArray[$row->term_id][$keyArray[$row->type]] = $row->count;
			}
		}

		return $countArray;
	}
}
