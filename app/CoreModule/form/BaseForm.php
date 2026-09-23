<?php

declare(strict_types = 1);

namespace Core\Form;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\BaseControl;

class BaseForm extends \Core\Component\BaseComponent
{
	protected function getForm(): Form
	{
		$form = new Form;

		$form->getElementPrototype()->novalidate('novalidate');

		$form->onError[] = function(Form $form)
		{
			foreach($form->getControls() as $control)
			{
				// getControls() vrací Nette\Forms\Control, ale hasErrors()
				// a setHtmlAttribute() jsou až na BaseControl
				if(!$control instanceof BaseControl || !$control->hasErrors())
				{
					continue;
				}

				// prototyp místo getControl() – ten klonuje a navíc si značí 'rendered'
				$currentClass = $control->getControlPrototype()->class;

				$control->setHtmlAttribute('class', $currentClass . ' is-invalid');
			}
		};

		return $form;
	}
}
