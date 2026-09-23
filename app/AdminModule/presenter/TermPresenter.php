<?php

declare(strict_types = 1);

namespace AdminModule;

use Admin\Component\TermCalendar;
use Admin\Component\TermCalendarFactory;
use Admin\Form\TermEditForm;
use Admin\Form\TermEditFormFactory;
use Admin\Form\TermForm;
use Admin\Form\TermFormFactory;
use Admin\Grid\TermGrid;
use Admin\Grid\TermGridFactory;
use Core\Presenter\AuthPresenter;
use Nette\Application\Attributes\Persistent;
use Nette\DI\Attributes\Inject;

/**
 * Kalendář je persistentní komponenta, aby si zobrazený měsíc udržel i při
 * odskoku do úpravy termínu a zpět.
 */
#[Persistent('termCalendar')]
class TermPresenter extends AuthPresenter
{
	public const TabGrid = 'grid';

	public const TabCalendar = 'calendar';

	/**
	 * Vybraná záložka přehledu. Nejmenuje se 'view', to už Presenter obsadil.
	 */
	#[Persistent]
	public string $tab = self::TabGrid;

	#[Inject]
	public TermGridFactory $TermGridFactory;

	#[Inject]
	public TermFormFactory $TermFormFactory;

	#[Inject]
	public TermEditFormFactory $TermEditFormFactory;

	#[Inject]
	public TermCalendarFactory $TermCalendarFactory;

	private ?int $id = null;


	public function actionDefault(): void
	{
		/* nesmysl v URL nesmí shodit přehled */
		if(!in_array($this->tab, [self::TabGrid, self::TabCalendar], true))
		{
			$this->tab = self::TabGrid;
		}
	}


	public function renderDefault(): void
	{
		$this->template->tab = $this->tab;
		$this->template->tabGrid = self::TabGrid;
		$this->template->tabCalendar = self::TabCalendar;
	}


	public function actionEdit(?int $id = null): void
	{
		if(!$id)
		{
			$this->flashMessage('Neexistující záznam', 'warning');
			$this->redirect('default');
		}

		$this->id = $id;

		$this->getComponent('termEditForm')
			->prepare();
	}


	protected function createComponentTermGrid(): TermGrid
	{
		return $this->TermGridFactory->create();
	}


	protected function createComponentTermForm(): TermForm
	{
		return $this->TermFormFactory->create();
	}


	protected function createComponentTermEditForm(): TermEditForm
	{
		return $this->TermEditFormFactory->create($this->id);
	}


	protected function createComponentTermCalendar(): TermCalendar
	{
		return $this->TermCalendarFactory->create();
	}
}
