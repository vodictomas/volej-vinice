<?php

namespace AdminModule;

use Admin\Form\TeamForm;
use Admin\Form\TeamFormFactory;
use Admin\Grid\TeamGrid;
use Admin\Grid\TeamGridFactory;
use Core\Presenter\AuthPresenter;
use Nette\DI\Attributes\Inject;

class TeamPresenter extends AuthPresenter
{
    #[Inject]
	public TeamFormFactory $TeamFormFactory;

    #[Inject]
	public TeamGridFactory $TeamGridFactory;

	private ?int $id = null;


	public function actionEdit(?int $id = null)
	{
		if(!$id)
		{
			$this->flashMessage('Neexistující záznam', 'warning');
			$this->redirect('default');
		}

		$this->id = $id;

		$this->getComponent('teamForm')
			->prepare();

		$this->setView('form');
	}


	public function actionAdd()
	{
		$this->id = null;

		$this->setView('form');
	}

	  
	protected function createComponentTeamForm(): TeamForm
	{
		return $this->TeamFormFactory->create($this->id);
	}


	protected function createComponentTeamGrid(): TeamGrid
	{
		return $this->TeamGridFactory->create();
	}
}
