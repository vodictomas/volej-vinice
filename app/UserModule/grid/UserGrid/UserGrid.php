<?php

declare(strict_types = 1);

namespace User\Grid;

use Contributte\Datagrid\Column\Action\Confirmation\StringConfirmation;
use Contributte\Datagrid\Datagrid;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Security\User;
use User\Model\UserModel;

class UserGrid extends \Core\Grid\BaseGrid
{
	public function __construct
	(
		private readonly Explorer $Database,
		private readonly UserModel $UserModel,
		private readonly User $User
	)
	{
	}


	public function createComponentGrid(): Datagrid
	{
		$grid = $this->getGrid();

		$grid->setDataSource($this->Database->table('user')->order('lastname, firstname'));

		$grid->addColumnText('login', 'Login')
			->setAlign('center')
			->setSortable()
			->setFilterText();

		$grid->addColumnText('lastname', 'Příjmení')
			->setAlign('center')
			->setSortable()
			->setFilterText();

		$grid->addColumnText('firstname', 'Jméno')
			->setAlign('center')
			->setSortable()
			->setFilterText();

		$grid->addColumnText('email', 'E-mail')
			->setAlign('center')
			->setSortable()
			->setFilterText();

		$grid->addAction('edit', '', ':User:User:edit')
			->setClass('btn btn-outline-secondary btn-sm')
			->setTitle('Upravit uživatele')
			->setIcon('edit');

		$grid->addActionCallback('delete', '')
			->setClass('btn btn-outline-danger btn-sm')
			->setTitle('Smazat uživatele')
			->setIcon('trash')
			->setConfirmation(new StringConfirmation('Opravdu smazat uživatele %s?', 'login'))
			->onClick[] = [$this, 'delete'];

		// Vlastní účet smazat nejde
		$grid->allowRowsAction('delete', fn(ActiveRow $item) => $item->id !== $this->User->getId());

		return $grid;
	}


	public function delete(int|string $id): void
	{
		if((int) $id === $this->User->getId())
		{
			$this->getPresenter()->flashMessage('Vlastní účet nelze smazat', 'warning');
		}
		else
		{
			$this->UserModel->delete((int) $id);
			$this->getPresenter()->flashMessage('Uživatel smazán', 'success');
		}

		$this->getPresenter()->redirect('this');
	}
}
