<?php

namespace AdminModule;

use Admin\Form\PlayerForm;
use Admin\Form\PlayerFormFactory;
use Admin\Grid\PlayerGrid;
use Admin\Grid\PlayerGridFactory;
use Core\Presenter\AuthPresenter;
use Nette\DI\Attributes\Inject;

class PlayerPresenter extends AuthPresenter
{
    #[Inject]
	public PlayerFormFactory $PlayerFormFactory;

    #[Inject]
	public PlayerGridFactory $PlayerGridFactory;

	private ?int $id = null;


	public function actionEdit(?int $id = null)
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

	  
	protected function createComponentPlayerForm(): PlayerForm
	{
		return $this->PlayerFormFactory->create($this->id);
	}


	protected function createComponentPlayerGrid(): PlayerGrid
	{
		return $this->PlayerGridFactory->create();
	}
}
