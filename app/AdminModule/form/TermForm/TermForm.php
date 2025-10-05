<?php

declare(strict_types = 1);

namespace Admin\Form;

use ModulIS\Form\Form;
use ModulIS\Form\FormComponent;
use Nette\Utils\DateTime;

class TermForm extends FormComponent
{
	public function __construct
	(
		private \Nette\Database\Explorer $Db
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

		$form->addSubmit('save', 'Generovat');

		$form->onSuccess[] = [$this, 'successForm'];

		return $form;
	}


	public function successForm(Form $form, \Nette\Utils\ArrayHash $values): void
	{
		$dateFrom = new DateTime($values->date_from);
		$dateTo = new DateTime($values->date_to);

		/**
		 * Find Mondays for date range
		 */
		if($dateFrom->format('w') === '1')
		{
			$dateFromMonday = $dateFrom;
		}
		else
		{
			$dateFromMonday = $dateFrom->modify('next monday');
		}

		if($dateTo->format('w') === '1')
		{
			$dateToMonday = $dateTo->modify('+1 day');
		}
		else
		{
			$dateToMonday = $dateTo->modify('next monday');
		}

		$dateRange = new \DatePeriod($dateFromMonday, \DateInterval::createFromDateString('1 week'), $dateToMonday);

		/**
		 * Prefilled attendance
		 */
		$prefillPairs = $this->Db->table('player')
			->select('id')
			->where('prefill', 1)
			->fetchPairs(null, 'id');

		foreach($dateRange as $dateTerm)
		{
			$termRow = $this->Db->table('term')
				->insert(['date' => $dateTerm->format('Y-m-d')]);

			foreach($prefillPairs as $playerId)
			{
				$this->Db->table('attendance')
					->insert(['term_id' => $termRow->id, 'player_id' => $playerId, 'type' => \Admin\Dial\AttendanceTypeDial::YES]);
			}
		}

		$this->getPresenter()->flashMessage('Termíny vygenerovány', 'success');
		$this->getPresenter()->redirect(':Admin:Term:');
	}
}
