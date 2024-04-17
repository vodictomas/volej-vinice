<?php

namespace Admin\Form;

use \Nette\Application\UI\Form;

class TeamForm extends \Core\Form\BaseForm
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


	public function setId(?int $id): self
	{
		$this->id = $id;

		return $this;
	}
}
