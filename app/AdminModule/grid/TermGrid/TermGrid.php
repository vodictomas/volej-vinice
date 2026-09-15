<?php

declare(strict_types = 1);

namespace Admin\Grid;

use Admin\Dial\AttendanceTypeDial;
use Contributte\Datagrid\Column\Action\Confirmation\StringConfirmation;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Utils\DateTime;

class TermGrid extends \Core\Grid\BaseGrid
{
	/**
	 * [term_id => [type => count]]
	 */
	private ?array $attendanceCountArray = null;


	public function __construct
	(
		protected \Nette\Database\Explorer $Database
	)
	{
	}


	public function createComponentGrid(): \Contributte\Datagrid\Datagrid
	{
		$grid = $this->getGrid();

		$grid->setDataSource($this->Database->table('term'));

		$grid->setDefaultSort(['date' => 'ASC']);

		$grid->addColumnDateTime('date', 'Datum')
			->setFormat('j. n. Y')
			->setAlign('center')
			->setSortable();

		$grid->addColumnText('yes', 'Přijde')
			->setRenderer(fn(ActiveRow $item) => $this->getAttendanceCount($item->id, AttendanceTypeDial::YES))
			->setAlign('center');

		$grid->addColumnText('no', 'Nepřijde')
			->setRenderer(fn(ActiveRow $item) => $this->getAttendanceCount($item->id, AttendanceTypeDial::NO))
			->setAlign('center');

		$grid->addColumnText('waiting', 'Neví')
			->setRenderer(fn(ActiveRow $item) => $this->getAttendanceCount($item->id, AttendanceTypeDial::WAITING))
			->setAlign('center');

		$grid->addColumnText('available', 'Trénink se koná')
			->setRenderer(fn(ActiveRow $item) => $item->available ? 'Ano' : 'Ne')
			->setAlign('center')
			->setSortable()
			->setFilterSelect(['' => 'Vše', '0' => 'Ne', '1' => 'Ano']);

		$grid->addFilterSelect('period', 'Období', ['' => 'Vše', 'future' => 'Budoucí', 'past' => 'Proběhlé'])
			->setCondition(function(Selection $selection, string $value)
			{
				$today = (new DateTime)->format('Y-m-d');

				match($value)
				{
					'future' => $selection->where('date >= ?', $today),
					'past' => $selection->where('date < ?', $today),
					default => null,
				};
			});

		$grid->setDefaultFilter(['period' => 'future']);

		$grid->addActionCallback('toggle', '')
			->setClass(fn(ActiveRow $item) => 'btn btn-sm ' . ($item->available ? 'btn-warning' : 'btn-success'))
			->setTitle(fn(ActiveRow $item) => $item->available ? 'Zrušit trénink' : 'Obnovit trénink')
			->setIcon(fn(ActiveRow $item) => $item->available ? 'ban' : 'check')
			->onClick[] = [$this, 'toggleAvailable'];

		$grid->addActionCallback('delete', '')
			->setClass('btn btn-danger btn-sm')
			->setTitle('Smazat termín')
			->setIcon('trash')
			->setConfirmation(new StringConfirmation('Opravdu smazat termín včetně zapsané docházky?'))
			->onClick[] = [$this, 'delete'];

		return $grid;
	}


	public function toggleAvailable(int|string $id): void
	{
		$this->Database->query('UPDATE term SET available = NOT available WHERE id = ?', $id);

		$this->getPresenter()->flashMessage('Uloženo', 'success');
		$this->getPresenter()->redirect('this');
	}


	public function delete(int|string $id): void
	{
		$this->Database->table('term')
			->where('id', $id)
			->delete();

		$this->getPresenter()->flashMessage('Termín smazán', 'success');
		$this->getPresenter()->redirect('this');
	}


	private function getAttendanceCount(int $termId, string $type): int
	{
		if($this->attendanceCountArray === null)
		{
			$this->attendanceCountArray = [];

			$selection = $this->Database->table('attendance')
				->select('term_id, type, COUNT(*) AS count')
				->where('player.active', 1)
				->group('term_id, type');

			foreach($selection as $row)
			{
				$this->attendanceCountArray[$row->term_id][$row->type] = $row->count;
			}
		}

		return $this->attendanceCountArray[$termId][$type] ?? 0;
	}
}
