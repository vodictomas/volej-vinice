<?php

namespace Core\Presenter;

class AuthPresenter extends BasePresenter
{
	public function startup(): void
	{
		parent::startup();
		
		if(!$this->getUser()->isLoggedIn())
		{
			$this->redirect(':User:Login:');
		}

		$this->setLayout(__DIR__ . '/../../layout/@adminLayout.latte');
	}

	public function handleLogout()
	{
		$this->getUser()->logout(true);

		$this->flashMessage('Úspěšné odhlášení', 'success');
		$this->redirect(':User:Login:');
	}
}
