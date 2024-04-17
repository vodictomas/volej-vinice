<?php

namespace AdminModule;

class TeamPresenter extends \Core\Presenter\AuthPresenter
{
	/**
	 * @inject
	 * @var \Admin\Form\TeamFormFactory
	 */
	public $TeamFormFactory;

	/**
	 * @inject
	 * @var \Admin\Grid\TeamGridFactory
	 */
	public $TeamGridFactory;

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

		$this->getComponent('teamForm')
			->prepare();

		$this->setView('form');
	}


	public function actionAdd()
	{
		$this->id = null;

		$this->setView('form');
	}

	  
	protected function createComponentTeamForm(): \Admin\Form\TeamForm
	{
		return $this->TeamFormFactory->create()
			->setId($this->id);
	}


	protected function createComponentTeamGrid(): \Admin\Grid\TeamGrid
	{
		return $this->TeamGridFactory->create();
	}
}
