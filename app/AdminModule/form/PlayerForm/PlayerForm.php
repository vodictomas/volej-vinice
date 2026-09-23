<?php

declare(strict_types = 1);

namespace Admin\Form;

use Admin\Model\AttendanceModel;
use ModulIS\Form\Form;

class PlayerForm extends \ModulIS\Form\FormComponent
{
	public function __construct
	(
		private ?int $id,
		private \Nette\Database\Explorer $Database,
		private AttendanceModel $AttendanceModel
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

			if(!$playerRow)
			{
				$this->getPresenter()->flashMessage('Neexistující záznam', 'warning');
				$this->getPresenter()->redirect(':Admin:Player:');
			}

			$this->getComponent('form')
				->setDefaults($playerRow->toArray());
		}
	}


	public function createComponentForm(): Form
	{
		$form = $this->getForm();

		$form->addText('nick', 'Jméno', null, 50)
			->setRequired();

		/**
		 * Upravovaný hráč může být v neaktivním týmu
		 */
		$teamSelection = $this->Database->table('team')
			->order('position, name');

		if($this->id)
		{
			$teamSelection->where('team.active = 1 OR :player.id = ?', $this->id);
		}
		else
		{
			$teamSelection->where('active', 1);
		}

		$form->addSelect('team_id', 'Tým', $teamSelection->fetchPairs('id', 'name'))
			->setPrompt('~ Vyberte ~')
			->setRequired();

		$form->addCheckbox('active', 'Zobrazovat')
			->setDefaultValue(true);

		$form->addCheckbox('prefill', 'Předvyplnit docházku');

		$form->addSubmit('save', 'Uložit');

		$form->onSuccess[] = [$this, 'successForm'];

		return $form;
	}


	public function successForm(Form $form, \Nette\Utils\ArrayHash $values): void
	{
		$this->Database->transaction(function() use ($values)
		{
			$prefillBefore = false;

			if($this->id)
			{
				$prefillBefore = (bool) $this->Database->table('player')
					->where('id', $this->id)
					->fetchField('prefill');

				$this->Database->table('player')
					->where('id', $this->id)
					->update($values);

				$playerId = $this->id;
			}
			else
			{
				$playerId = $this->Database->table('player')
					->insert((array) $values)
					->id;
			}

			if($values->prefill && $values->active)
			{
				$this->AttendanceModel->prefillPlayer($playerId);
			}
			elseif(!$values->prefill && $prefillBefore)
			{
				$this->AttendanceModel->clearPlayer($playerId);
			}
		});

		$this->getPresenter()->flashMessage('Uloženo', 'success');
		$this->getPresenter()->redirect(':Admin:Player:');
	}
}
