<?php

namespace Admin\Grid;

class TermGrid extends \Core\Grid\BaseGrid
{
	/**
	 * @var \Nette\Database\Explorer
	 */
	protected $Database;


	public function __construct
	(
		\Nette\Database\Explorer $Database
	)
	{
		$this->Database = $Database;
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
			->setRenderer(function()
			{
				return '0';
			})
			->setAlign('center');

		return $grid;
	}
}
