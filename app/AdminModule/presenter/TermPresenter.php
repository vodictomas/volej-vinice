<?php

namespace AdminModule;

class TermPresenter extends \Core\Presenter\AuthPresenter
{
	/**
	 * @inject
	 * @var \Admin\Grid\TermGridFactory
	 */
	public $TermGridFactory;

	/**
	 * @inject
	 * @var \Admin\Form\TermFormFactory
	 */
	public $TermFormFactory;


	protected function createComponentTermGrid(): \Admin\Grid\TermGrid
	{
		return $this->TermGridFactory->create();
	}


	protected function createComponentTermForm(): \Admin\Form\TermForm
	{
		return $this->TermFormFactory->create();
	}
}
