<?php

declare(strict_types = 1);

namespace Admin\Grid;

class TermGrid extends \Core\Grid\BaseGrid
{
	public function __construct
	(
		protected \Nette\Database\Explorer $Database
	)
	{
	}


	public function createComponentGrid(): \Ublaboo\DataGrid\DataGrid
	{
		$grid = $this->getGrid();

		$dataSource = $this->Database->table('attendance')
			->select('term.*')
			->group('term.id')
			->order('date');

		$grid->setDataSource($dataSource);

		$grid->addColumnDateTime('date', 'Datum')
			->setAlign('center')
			->setSortable()
			->setFilterText();

		$grid->addColumnText('available', 'Dostupný')
			->setAlign('center')
			->setSortable()
			->setFilterText();

		$grid->addColumnText('attendance', 'Účast')
			->setRenderer(fn() => '0')
			->setAlign('center');

		return $grid;
	}
}
