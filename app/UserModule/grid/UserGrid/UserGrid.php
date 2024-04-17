<?php

namespace User\Grid;

class UserGrid extends \Core\Grid\BaseGrid
{
	/**
	 * @var \User\Repository\UserRepository
	 */
	protected $UserRepository;

	public function __construct
	(
		\User\Repository\UserRepository $UserRepository
	)
	{
		$this->UserRepository = $UserRepository;
	}

	public function createComponentGrid(): \Ublaboo\DataGrid\DataGrid
	{
		$grid = $this->getGrid();

		$grid->setDataSource($this->UserRepository->getTable());

		$grid->addColumnText('lastname', 'Příjmení')
			->setAlign('center')
			->setSortable()
			->setFilterText();

		$grid->addColumnText('firstname', 'Jméno')
			->setAlign('center')
			->setSortable()
			->setFilterText();

		$grid->addColumnText('email', 'Email')
			->setAlign('center')
			->setSortable()
			->setFilterText();

		$grid->addColumnText('role', 'Pravomoc')
			->setRenderer(function($item)
			{
				return implode(", ", \Nette\Utils\Json::decode($item->role));
			})
			->setAlign('center')
			->setSortable()
			->setFilterText();

		$grid->addAction('edit', '', 'User:userForm')
			->setTitle('Upravit')
			->setIcon('edit')
			->setClass('btn btn-sm btn-warning');

		return $grid;
	}
}
