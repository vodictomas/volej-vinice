<?php

declare(strict_types = 1);

namespace Core\Presenter;

class BasePresenter extends \Nette\Application\UI\Presenter
{
	public function startup(): void
	{
		parent::startup();

		$this->setLayout(__DIR__ . '/../../layout/@baseLayout.latte');
	}
}
