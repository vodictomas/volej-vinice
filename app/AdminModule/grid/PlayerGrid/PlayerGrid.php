<?php

declare(strict_types = 1);

namespace Admin\Grid;

class PlayerGrid extends \Core\Grid\BaseGrid
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

		$grid->setDataSource($this->Database->table('player')->order('nick'));

		$grid->addColumnText('nick', 'Jméno')
			->addCellAttributes(['width' => '25%'])
			->setAlign('center')
			->setSortable()
			->setFilterText();

		$grid->addColumnText('team', 'Tým', 'team.name')
			->addCellAttributes(['width' => '25%'])
			->setAlign('center')
			->setSortable()
			->setFilterText('team.name');

		$filterArray = ['' => 'Vše', '0' => 'Ne', '1' => 'Ano'];

		$grid->addColumnText('active', 'Zobrazit')
			->addCellAttributes(['width' => '25%'])
			->setRenderer(fn($item) => $item->active ? 'Ano' : 'Ne')
			->setAlign('center')
			->setSortable()
			->setFilterSelect($filterArray);

		$grid->addColumnText('prefill', 'Předvyplnit docházku')
			->addCellAttributes(['width' => '25%'])
			->setRenderer(fn($item) => $item->prefill ? 'Ano' : 'Ne')
			->setAlign('center')
			->setSortable()
			->setFilterSelect($filterArray);

		$grid->addAction('edit', '', ':Admin:Player:edit')
			->setClass('btn btn-warning btn-sm')
			->setTitle('Upravit hráče')
			->setIcon('user');

		return $grid;
	}
}
