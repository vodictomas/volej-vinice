<?php

declare(strict_types = 1);

namespace Admin\Grid;

class TeamGrid extends \Core\Grid\BaseGrid
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

		$grid->setDataSource($this->Database->table('team')->order('name'));

		$grid->addColumnText('name', 'Název')
			->setAlign('center')
			->setSortable()
			->setFilterText();

		$filterArray = ['' => 'Vše', '0' => 'Ne', '1' => 'Ano'];

		$grid->addColumnText('active', 'Zobrazit')
			->setRenderer(fn($item) => $item->active ? 'Ano' : 'Ne')
			->setAlign('center')
			->setSortable()
			->setFilterSelect($filterArray);

		$grid->addAction('edit', '', ':Admin:Team:edit')
			->setClass('btn btn-warning btn-sm')
			->setTitle('Upravit tým')
			->setIcon('edit');

		return $grid;
	}
}
