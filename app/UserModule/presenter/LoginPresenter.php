<?php

namespace UserModule;

use Core\Presenter\BasePresenter;
use Nette\DI\Attributes\Inject;
use Nette\Security\Passwords;
use User\Form\ILoginFormFactory;
use User\Form\LoginForm;
use User\Form\ResetPasswordForm;

class LoginPresenter extends BasePresenter
{
    #[Inject]
	public ILoginFormFactory $ILoginFormFactory;

    #[Inject]
	public Passwords $Passwords;


	public function startup(): void
	{
		
		parent::startup();
		bdump($this->Passwords->hash('root'));
		if($this->getUser()->isLoggedIn())
		{
			$this->redirect(':Admin:Admin:');
		}
	}

	public function actionResetPassword($h): void
	{
		try
		{
			$this->UserModel->resetPassword($h);
		}
		catch(\User\Exception\LoginException $exc)
		{
			$this->flashMessage($exc->getMessage(), 'danger');
			$this->redirect(':User:Login:');
		}

		$this->flashMessage('Heslo úspěšně resetováno, na email Vám byly odeslány nové přihlašovací údaje', 'success');
		$this->redirect(':User:Login:');
	}

	public function createComponentLoginForm(): LoginForm
	{
		return $this->ILoginFormFactory->create();
	}

	public function createComponentResetPasswordForm(): ResetPasswordForm
	{
		return $this->IResetPasswordFormFactory->create();
	}
}
