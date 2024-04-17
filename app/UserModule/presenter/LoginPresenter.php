<?php

namespace UserModule;

class LoginPresenter extends \Core\Presenter\BasePresenter
{
	/**
	 * @inject
	 * @var \User\Form\ILoginFormFactory
	 */
	public $ILoginFormFactory;


	public function startup(): void
	{
		parent::startup();

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

	public function createComponentLoginForm(): \User\Form\LoginForm
	{
		return $this->ILoginFormFactory->create();
	}

	public function createComponentResetPasswordForm(): \User\Form\ResetPasswordForm
	{
		return $this->IResetPasswordFormFactory->create();
	}
}
