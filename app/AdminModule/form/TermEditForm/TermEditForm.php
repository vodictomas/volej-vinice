<?php

declare(strict_types = 1);

namespace Admin\Form;

use ModulIS\Form\Form;
use ModulIS\Form\FormComponent;
use Nette\Database\Explorer;
use Nette\Utils\ArrayHash;

class TermEditForm extends FormComponent
{
	public function __construct
	(
		private readonly ?int $id,
		private readonly Explorer $Database
	)
	{
	}


	public function prepare(): void
	{
		$termRow = $this->id
			? $this->Database->table('term')->where('id', $this->id)->fetch()
			: null;

		if(!$termRow)
		{
			$this->getPresenter()->flashMessage('Neexistující záznam', 'warning');
			$this->getPresenter()->redirect(':Admin:Term:');
		}

		$this->getComponent('form')
			->setDefaults([
				'date' => $termRow->date->format('Y-m-d'),
				'available' => (bool) $termRow->available
			]);
	}


	public function createComponentForm(): Form
	{
		$form = $this->getForm();

		$form->addDate('date', 'Datum tréninku')
			->setRequired();

		$form->addCheckbox('available', 'Trénink se koná')
			->setTooltip('Odškrtnutím se trénink v docházce zobrazí jako zrušený, zapsaná docházka zůstane');

		$form->addSubmit('save', 'Uložit');

		$form->onValidate[] = [$this, 'validateForm'];
		$form->onSuccess[] = [$this, 'successForm'];

		return $form;
	}


	/**
	 * Na jeden den patří jediný termín, jinak by se docházka rozpadla do dvou sloupců
	 */
	public function validateForm(Form $form, ArrayHash $values): void
	{
		if($form->hasErrors())
		{
			return;
		}

		$taken = $this->Database->table('term')
			->where('date', $values->date)
			->where('id != ?', $this->id)
			->count('*') > 0;

		if($taken)
		{
			$form->addError('Na tento den už jeden termín existuje');
		}
	}


	public function successForm(Form $form, ArrayHash $values): void
	{
		$this->Database->table('term')
			->where('id', $this->id)
			->update([
				'date' => $values->date,
				'available' => $values->available ? 1 : 0
			]);

		$this->getPresenter()->flashMessage('Uloženo', 'success');
		$this->getPresenter()->redirect(':Admin:Term:');
	}
}
