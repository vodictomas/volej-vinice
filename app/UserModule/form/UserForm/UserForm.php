<?php

declare(strict_types = 1);

namespace User\Form;

use ModulIS\Form\Form;

class UserForm extends \ModulIS\Form\FormComponent
{
	public function __construct
	(
		private ?int $id,
		private \Nette\Database\Explorer $Explorer
	)
	{

	}

	public function prepare(): void
	{
		if($this->id)
		{
			$userRow = $this->Database->table('user')
					->where('id', $this->id)
					->fetch();

			$this->getComponent('form')
					->setDefaults($userRow);
		}
		else
		{
			$this['form']['password']->setDefaultValue($this->generatePassword());
		}
	}

	public function createComponentForm(): Form
	{
		$form = $this->getForm();

		$form->addText('login', 'Login')
				->setRequired();

		if(!$this->id)
		{
			$form->addText('password', 'Heslo')
					->setRequired();
		}

		$form->addText('firstname', 'Jméno')
				->setRequired();

		$form->addText('lastname', 'Příjmení')
				->setRequired();

		$form->addSubmit('save', 'Uložit');

		$form->onSuccess[] = [$this, 'successForm'];

		return $form;
	}

	public function successForm(Form $form, \Nette\Utils\ArrayHash $values): void
	{
		if($this->id)
		{
			$this->Database->table('user')
					->where('id', $this->id)
					->update($values);
		}
		else
		{
			$this->Database->table('user')
					->insert($values);
		}

		$this->getPresenter()->flashMessage('Úspěšně uloženo', 'success');
		$this->getPresenter()->redirect(':User:User:');
	}

	private function generatePassword(): string
	{
		return substr(bin2hex(openssl_random_pseudo_bytes(10)), 0, 10);
	}
}
