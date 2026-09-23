<?php

declare(strict_types = 1);

namespace User\Form;

use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Security\User;
use Nette\Utils\ArrayHash;

class LoginForm extends \Core\Form\BaseForm
{
	public function __construct
	(
		private readonly User $User
	)
	{
	}


	protected function createComponentForm(): Form
	{
		$form = $this->getForm();

		$form->addText('login', 'Login')
			->setHtmlAttribute('class', 'form-control')
			->setHtmlAttribute('placeholder', 'Přihlašovací jméno')
			->setHtmlAttribute('autocomplete', 'username')
			->setRequired('Zadejte přihlašovací jméno');

		$form->addPassword('password', 'Heslo')
			->setHtmlAttribute('class', 'form-control')
			->setHtmlAttribute('placeholder', 'Heslo')
			->setHtmlAttribute('autocomplete', 'current-password')
			->setRequired('Zadejte heslo');

		$form->addSubmit('submit', 'Přihlásit');

		$form->onSuccess[] = [$this, 'formSuccess'];

		return $form;
	}


	public function formSuccess(Form $form, ArrayHash $values): void
	{
		try
		{
			$this->User->login($values->login, $values->password);
		}
		catch(AuthenticationException $exc)
		{
			$form->addError($exc->getMessage());

			return;
		}

		$this->getPresenter()->flashMessage('Úspěšně přihlášeno', 'success');
		$this->getPresenter()->redirect(':Admin:Admin:');
	}
}
