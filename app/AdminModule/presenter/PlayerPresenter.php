<?php

namespace AdminModule;

class PlayerPresenter extends \Core\Presenter\AuthPresenter
{
	/**
	 * @inject
	 * @var \Admin\Form\PlayerFormFactory
	 */
	public $PlayerFormFactory;

	/**
	 * @inject
	 * @var \Admin\Grid\PlayerGridFactory
	 */
	public $PlayerGridFactory;

	/**
	 * @var int|null
	 */
	private $id;


	public function actionEdit(string $id = null)
	{
		if(!$id)
		{
			$this->flashMessage('Neexistující záznam', 'warning');
			$this->redirect('default');
		}

		$this->id = $id;

		$this->getComponent('playerForm')
			->prepare();

		$this->setView('form');
	}


	public function actionAdd()
	{
		$this->id = null;

		$this->setView('form');
	}

	  
	protected function createComponentPlayerForm(): \Admin\Form\PlayerForm
	{
		return $this->PlayerFormFactory->create()
			->setId($this->id);
	}


	protected function createComponentPlayerGrid(): \Admin\Grid\PlayerGrid
	{
		return $this->PlayerGridFactory->create();
	}
}
