<?php

namespace User\Form;

class LoginForm extends \Core\Form\BaseForm
{
	/**
	 * @var \Nette\Security\User
	 */
	protected $User;

	public function __construct
	(
		\Nette\Security\User $User
	)
	{
		$this->User = $User;
	}
	
	public function createComponentForm()
	{
		$form = $this->getForm();
		
		$form->addText('login', 'Login')
			->setHtmlAttribute('class', 'form-control')
			->setRequired()
			->setHtmlAttribute('placeholder', 'Zadejte přihlašovací jméno');
		
		$form->addPassword('password', 'Heslo')
			->setHtmlAttribute('class', 'form-control')
			->setRequired()
			->setHtmlAttribute('placeholder', 'Zadejte heslo');
		
		$form->addSubmit('submit', 'Přihlásit');
		
		$form->onSuccess[] = [$this, 'formSuccess'];
		
		return $form;
	}
	
	public function formSuccess($form, $values)
	{
		try 
		{
			$this->User->login($values->login, $values->password);
		}
		catch(\Nette\Security\AuthenticationException $exc)
		{
			$this->getPresenter()->flashMessage($exc->getMessage(), 'warning');
			$this->getPresenter()->redirect('this');
		}

		$this->getPresenter()->flashMessage('Úspěšně přihlášeno', 'success');
		$this->getPresenter()->redirect(':Admin:Homepage:');
	}
}
