<?php

namespace UserModule;

class UserPresenter extends \Core\Presenter\AuthPresenter
{
	/**
	 * @inject
	 * @var \User\Form\IUserFormFactory
	 
	public $IUserFormFactory;*/

	/**
	 * @inject
	 * @var \User\Grid\IUserGridFactory
	 
	 public $IUserGridFactory;*/

	public function actionUserForm(string $id = null)
	{
		$this['userForm']->prepare();

		$this->template->id = $id;
	}

	protected function createComponentUserGrid(): \User\Grid\UserGrid
	{
		return $this->IUserGridFactory->create();
	}

	protected function createComponentUserForm(): \User\Form\UserForm
	{
		return $this->IUserFormFactory->create()
			->setId($this->getParameter('id'));
	}
}
