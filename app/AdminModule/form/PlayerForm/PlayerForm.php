<?php

namespace Admin\Form;

use \Nette\Application\UI\Form;

class PlayerForm extends \Core\Form\BaseForm
{
	/**
	 * @var \Nette\Database\Explorer
	 */
	protected $Database;

	/**
	 * @var int|null
	 */
	private $id;


	public function __construct
	(
		\Nette\Database\Explorer $Database
	)
	{
		$this->Database = $Database;
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
			->setHtmlAttribute('class', 'form-control')
			->setRequired();

		$teamArray = $this->Database->table('team')
			->where('active', 1)
			->fetchPairs('id', 'name');

		$form->addSelect('team_id', 'Tým', $teamArray)
			->setPrompt('~ Vyberte ~')
			->setHtmlAttribute('class', 'form-control');

		$form->addCheckbox('active', 'Zobrazovat')
			->setHtmlAttribute('class', 'form-control');

		$form->addCheckbox('prefill', 'Předvyplnit docházku')
			->setHtmlAttribute('class', 'form-control');

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
