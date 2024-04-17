<?php

namespace Admin\Form;

use Nette\Utils\DateTime;
use Nette\Application\UI\Form;

class TermForm extends \Core\Form\BaseForm
{
	/**
	 * @var \Nette\Database\Explorer
	 */
	protected $Db;


	public function __construct(\Nette\Database\Explorer $Db)
	{

		$this->Db = $Db;
	}


	public function createComponentForm(): Form
	{
		$form = $this->getForm();

		$form->addText('date_from', 'Vygenerovat termíny od')
			->setHtmlAttribute('class', 'form-control datepicker')
			->setHtmlAttribute('autocomplete', 'off')
			->setRequired();

		$form->addText('date_to', 'Vygenerovat termíny do')
			->setHtmlAttribute('class', 'form-control datepicker')
			->setHtmlAttribute('autocomplete', 'off')
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
				->insert([['date' => $dateTerm->format('Y-m-d')]]);

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
