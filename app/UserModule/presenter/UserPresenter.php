<?php

declare(strict_types = 1);

namespace UserModule;

use Core\Presenter\AuthPresenter;
use Nette\DI\Attributes\Inject;
use User\Form\UserForm;
use User\Form\UserFormFactory;
use User\Grid\UserGrid;
use User\Grid\UserGridFactory;

class UserPresenter extends AuthPresenter
{
	#[Inject]
	public UserFormFactory $UserFormFactory;

	#[Inject]
	public UserGridFactory $UserGridFactory;

	private ?int $id = null;


	public function actionEdit(int $id): void
	{
		$this->id = $id;

		$this->getComponent('userForm')
			->prepare();

		$this->template->id = $id;
		$this->setView('form');
	}


	public function actionAdd(): void
	{
		$this->template->id = null;
		$this->setView('form');
	}


	protected function createComponentUserForm(): UserForm
	{
		return $this->UserFormFactory->create($this->id);
	}


	protected function createComponentUserGrid(): UserGrid
	{
		return $this->UserGridFactory->create();
	}
}
