<?php

declare(strict_types = 1);

namespace UserModule;

use Core\Presenter\BasePresenter;
use Nette\Database\Table\ActiveRow;
use Nette\DI\Attributes\Inject;
use User\Form\LoginForm;
use User\Form\LoginFormFactory;
use User\Form\ResetPasswordForm;
use User\Form\ResetPasswordFormFactory;
use User\Form\SetPasswordForm;
use User\Form\SetPasswordFormFactory;
use User\Model\UserModel;

class LoginPresenter extends BasePresenter
{
	#[Inject]
	public LoginFormFactory $LoginFormFactory;

	#[Inject]
	public ResetPasswordFormFactory $ResetPasswordFormFactory;

	#[Inject]
	public SetPasswordFormFactory $SetPasswordFormFactory;

	#[Inject]
	public UserModel $UserModel;

	private ?ActiveRow $resetUserRow = null;


	public function startup(): void
	{
		parent::startup();

		if($this->getUser()->isLoggedIn())
		{
			$this->redirect(':Admin:Admin:');
		}
	}


	public function actionSetPassword(string $token = ''): void
	{
		$this->resetUserRow = $this->UserModel->getByResetToken($token);

		if(!$this->resetUserRow)
		{
			$this->flashMessage('Odkaz pro nastavení hesla je neplatný nebo mu vypršela platnost', 'danger');
			$this->redirect('reset');
		}
	}


	protected function createComponentLoginForm(): LoginForm
	{
		return $this->LoginFormFactory->create();
	}


	protected function createComponentResetPasswordForm(): ResetPasswordForm
	{
		return $this->ResetPasswordFormFactory->create();
	}


	protected function createComponentSetPasswordForm(): SetPasswordForm
	{
		return $this->SetPasswordFormFactory->create($this->resetUserRow);
	}
}
