<?php

namespace Core\Presenter;

class BasePresenter extends \Nette\Application\UI\Presenter
{
	public function startup(): void
	{
		parent::startup();

		$this->template->js = $this->getFile('js');
		$this->template->css = $this->getFile('css');
		$this->setLayout(__DIR__ . '/../../layout/@baseLayout.latte');
	}

	private function getFile(string $type): ?string
	{
		foreach(\Nette\Utils\Finder::findFiles('*.' . $type)->in(__DIR__ . '/../../../www/dist/') as $file)
		{
			return $file->getFilename();
		}

		return null;
	}
}
