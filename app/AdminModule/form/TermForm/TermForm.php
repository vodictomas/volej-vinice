<?php

declare(strict_types = 1);

namespace Admin\Form;

use Admin\Model\AttendanceModel;
use ModulIS\Form\Form;
use ModulIS\Form\FormComponent;
use Nette\Utils\DateTime;

class TermForm extends FormComponent
{
	private const DayArray = [
		1 => 'Pondělí',
		2 => 'Úterý',
		3 => 'Středa',
		4 => 'Čtvrtek',
		5 => 'Pátek',
		6 => 'Sobota',
		0 => 'Neděle'
	];

	private const DayNameArray = [
		1 => 'monday',
		2 => 'tuesday',
		3 => 'wednesday',
		4 => 'thursday',
		5 => 'friday',
		6 => 'saturday',
		0 => 'sunday'
	];


	public function __construct
	(
		private \Nette\Database\Explorer $Db,
		private AttendanceModel $AttendanceModel
	)
	{

	}


	public function prepare(): void
	{

	}


	public function createComponentForm(): Form
	{
		$form = $this->getForm();

		$form->addDate('date_from', 'Vygenerovat termíny od')
			->setRequired();

		$form->addDate('date_to', 'Vygenerovat termíny do')
			->setRequired();

		$form->addSelect('day', 'Den tréninku', self::DayArray)
			->setDefaultValue(1)
			->setRequired();

		$form->addSubmit('save', 'Generovat');

		$form->onValidate[] = [$this, 'validateForm'];
		$form->onSuccess[] = [$this, 'successForm'];

		return $form;
	}


	public function validateForm(Form $form, \Nette\Utils\ArrayHash $values): void
	{
		if($form->hasErrors())
		{
			return;
		}

		if($values->date_from > $values->date_to)
		{
			$form->addError('Datum do musí být stejné nebo pozdější než datum od');
		}
		elseif(new DateTime($values->date_from)->modify('+1 year') < new DateTime($values->date_to))
		{
			$form->addError('Najednou lze vygenerovat termíny maximálně na rok');
		}
	}


	public function successForm(Form $form, \Nette\Utils\ArrayHash $values): void
	{
		$dateFrom = new DateTime($values->date_from);
		$dateTo = new DateTime($values->date_to);

		/**
		 * První den tréninku v rozsahu
		 */
		if((int) $dateFrom->format('w') !== $values->day)
		{
			$dateFrom->modify('next ' . self::DayNameArray[$values->day]);
		}

		$existingDateArray = $this->Db->table('term')
			->where('date >= ?', $dateFrom->format('Y-m-d'))
			->where('date <= ?', $dateTo->format('Y-m-d'))
			->fetchPairs(null, 'date');

		$existingDateArray = array_map(fn($date) => $date->format('Y-m-d'), $existingDateArray);

		$created = 0;

		$this->Db->transaction(function() use ($dateFrom, $dateTo, $existingDateArray, &$created)
		{
			for($date = $dateFrom; $date <= $dateTo; $date = $date->modifyClone('+1 week'))
			{
				if(in_array($date->format('Y-m-d'), $existingDateArray, true))
				{
					continue;
				}

				$termRow = $this->Db->table('term')
					->insert(['date' => $date->format('Y-m-d')]);

				$this->AttendanceModel->prefillTerm($termRow->id);

				$created++;
			}
		});

		$this->getPresenter()->flashMessage('Vygenerováno termínů: ' . $created, 'success');
		$this->getPresenter()->redirect(':Admin:Term:');
	}
}
