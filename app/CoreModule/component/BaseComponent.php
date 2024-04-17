<?php

namespace Core\Component;

class BaseComponent extends \Nette\Application\UI\Control
{
	public function beforeRender(): void
	{

	}

	public function render()
	{
		$this->beforeRender();

		$this->template->setFile($this->getTemplateName() . '.latte');
		$this->template->render();
	}

	public function renderJs()
	{
		$this->template->setFile($this->getTemplateName() . 'Js.latte');
		$this->template->render();
	}

	private function getTemplateName(): string
	{
		$classArray = explode('\\', static::class);

		$reflection = new \ReflectionClass($this);
		$directory = dirname($reflection->getFileName());

		return $directory . DIRECTORY_SEPARATOR . lcfirst(array_pop($classArray));
	}
}
