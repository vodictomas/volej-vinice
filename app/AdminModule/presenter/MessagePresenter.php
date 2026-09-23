<?php

declare(strict_types = 1);

namespace AdminModule;

use Admin\Grid\MessageGrid;
use Admin\Grid\MessageGridFactory;
use Core\Presenter\AuthPresenter;
use Nette\DI\Attributes\Inject;

class MessagePresenter extends AuthPresenter
{
	#[Inject]
	public MessageGridFactory $MessageGridFactory;


	protected function createComponentMessageGrid(): MessageGrid
	{
		return $this->MessageGridFactory->create();
	}
}
