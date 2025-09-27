<?php

declare(strict_types = 1);

namespace AdminModule;

use Admin\Form\TermForm;
use Admin\Form\TermFormFactory;
use Admin\Grid\TermGrid;
use Admin\Grid\TermGridFactory;
use Core\Presenter\AuthPresenter;
use Nette\DI\Attributes\Inject;

class TermPresenter extends AuthPresenter
{
	#[Inject]
	public TermGridFactory $TermGridFactory;

	#[Inject]
	public TermFormFactory $TermFormFactory;


	protected function createComponentTermGrid(): TermGrid
	{
		return $this->TermGridFactory->create();
	}


	protected function createComponentTermForm(): TermForm
	{
		return $this->TermFormFactory->create();
	}
}
