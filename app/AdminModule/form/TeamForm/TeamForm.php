<?php

declare(strict_types = 1);

namespace Admin\Form;

use ModulIS\Form\Form;

class TeamForm extends \ModulIS\Form\FormComponent
{
	public function __construct
	(
		private ?int $id,
		private \Nette\Database\Explorer $Database
	)
	{

	}


	public function prepare(): void
	{
		if($this->id)
		{
			$teamRow = $this->Database->table('team')
				->where('id', $this->id)
				->fetch();

			$this->getComponent('form')
				->setDefaults($teamRow);
		}
	}


	public function createComponentForm(): Form
	{
		$form = $this->getForm();

		$form->addText('name', 'Název')
			->setHtmlAttribute('class', 'form-control')
			->setRequired();

		$form->addText('color', 'Barva')
			->setRequired();

		$form->addCheckbox('active', 'Zobrazovat')
			->setHtmlAttribute('class', 'form-control');

		$form->addSubmit('save', 'Uložit');

		$form->onSuccess[] = [$this, 'successForm'];

		return $form;
	}


	public function successForm(Form $form, \Nette\Utils\ArrayHash $values): void
	{
		if($this->id)
		{
			$this->Database->table('team')
				->where('id', $this->id)
				->update($values);
		}
		else
		{
			$this->Database->table('team')
				->insert($values);
		}

		$this->getPresenter()->flashMessage('Uloženo', 'success');
		$this->getPresenter()->redirect(':Admin:Team:');
	}
}
