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

			if(!$teamRow)
			{
				$this->getPresenter()->flashMessage('Neexistující záznam', 'warning');
				$this->getPresenter()->redirect(':Admin:Team:');
			}

			$this->getComponent('form')
				->setDefaults($teamRow->toArray());
		}
	}


	public function createComponentForm(): Form
	{
		$form = $this->getForm();

		$form->addText('name', 'Název', null, 50)
			->setRequired();

		$form->addText('color', 'Barva')
			->setHtmlType('color')
			->setDefaultValue('#33ccff')
			->setRequired()
			->addRule($form::Pattern, 'Zadejte barvu ve formátu #rrggbb', '#[0-9a-fA-F]{6}');

		$form->addInteger('position', 'Pozice')
			/* nativní bublina prohlížeče, HTML by se vypsalo doslova – Bootstrap Tooltip v bundlu není */
			->setTooltip('Pořadí ve výpisu docházky, od nejmenšího čísla. Domácí tým nahoru, hosté dolů; při shodě rozhoduje abeceda.')
			->setDefaultValue($this->id ? 0 : $this->getNextPosition())
			->setRequired()
			->addRule($form::Range, 'Pozice musí být mezi %d a %d', [0, 999]);

		$form->addCheckbox('active', 'Zobrazovat')
			->setDefaultValue(true);

		$form->addSubmit('save', 'Uložit');

		$form->onSuccess[] = [$this, 'successForm'];

		return $form;
	}


	/**
	 * Nový tým se zařadí na konec, ať nepřebije stávající pořadí
	 */
	private function getNextPosition(): int
	{
		return (int) $this->Database->table('team')
			->max('position') + 1;
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
