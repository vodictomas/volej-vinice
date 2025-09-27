<?php

declare(strict_types = 1);

namespace Admin\Form;

use ModulIS\Form\Form;

class PlayerForm extends \ModulIS\Form\FormComponent
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
			$playerRow = $this->Database->table('player')
				->where('id', $this->id)
				->fetch();

			$this->getComponent('form')
				->setDefaults($playerRow);
		}
	}


	public function createComponentForm(): Form
	{
		$form = $this->getForm();

		$form->addText('nick', 'Jméno')
				->setRequired();

		$teamArray = $this->Database->table('team')
			->where('active', 1)
			->fetchPairs('id', 'name');

		$form->addSelect('team_id', 'Tým', $teamArray)
			->setPrompt('~ Vyberte ~');

		$form->addCheckbox('active', 'Zobrazovat');

		$form->addCheckbox('prefill', 'Předvyplnit docházku');

		$form->addSubmit('save', 'Uložit');

		$form->onSuccess[] = [$this, 'successForm'];

		return $form;
	}


	public function successForm(Form $form, \Nette\Utils\ArrayHash $values): void
	{
		if($this->id)
		{
			$this->Database->table('player')
				->where('id', $this->id)
				->update($values);
		}
		else
		{
			$this->Database->table('player')
				->insert($values);
		}

		$this->getPresenter()->flashMessage('Uloženo', 'success');
		$this->getPresenter()->redirect(':Admin:Player:');
	}


	public function setId(?int $id): self
	{
		$this->id = $id;

		return $this;
	}
}
