<?php

declare(strict_types = 1);

namespace User\Form;

use ModulIS\Form\Form;
use ModulIS\Form\FormComponent;
use Nette\Utils\ArrayHash;
use User\Model\UserModel;

class UserForm extends FormComponent
{
	public function __construct
	(
		private ?int $id,
		private readonly UserModel $UserModel
	)
	{
	}


	public function prepare(): void
	{
		if($this->id)
		{
			$userRow = $this->UserModel->getById($this->id);

			if(!$userRow)
			{
				$this->getPresenter()->flashMessage('Neexistující záznam', 'warning');
				$this->getPresenter()->redirect(':User:User:');
			}

			$this->getComponent('form')
				->setDefaults([
					'login' => $userRow->login,
					'email' => $userRow->email,
					'firstname' => $userRow->firstname,
					'lastname' => $userRow->lastname,
				]);
		}
	}


	public function createComponentForm(): Form
	{
		$form = $this->getForm();

		$form->addText('login', 'Login', null, 50)
			->setRequired();

		$form->addEmail('email', 'E-mail', 100)
			->setRequired()
			->setTooltip('Slouží pro obnovení zapomenutého hesla');

		$form->addText('firstname', 'Jméno', null, 50)
			->setRequired();

		$form->addText('lastname', 'Příjmení', null, 50)
			->setRequired();

		$password = $form->addPassword('password', $this->id ? 'Nové heslo' : 'Heslo')
			->setHtmlAttribute('autocomplete', 'new-password')
			->setRequired(!$this->id);

		$password->addCondition($form::Filled)
			->addRule($form::MinLength, 'Heslo musí mít alespoň %d znaků', SetPasswordForm::PasswordMinLength);

		if($this->id)
		{
			$password->setTooltip('Vyplňte jen pokud chcete heslo změnit');
		}

		$form->addSubmit('save', 'Uložit');

		$form->onValidate[] = [$this, 'validateForm'];
		$form->onSuccess[] = [$this, 'successForm'];

		return $form;
	}


	public function validateForm(Form $form, ArrayHash $values): void
	{
		if(!$this->UserModel->isUnique('login', $values->login, $this->id))
		{
			$form->addError('Tento login už používá jiný uživatel');
		}

		if(!$this->UserModel->isUnique('email', $values->email, $this->id))
		{
			$form->addError('Tento e-mail už používá jiný uživatel');
		}
	}


	public function successForm(Form $form, ArrayHash $values): void
	{
		$this->UserModel->save($this->id, (array) $values);

		$this->getPresenter()->flashMessage('Uloženo', 'success');
		$this->getPresenter()->redirect(':User:User:');
	}
}
