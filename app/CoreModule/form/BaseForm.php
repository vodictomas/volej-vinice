<?php

namespace Core\Form;

use Nette\Application\UI\Form;

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
				if($control->hasErrors())
				{
					$currentClass = $control->getControl()->attrs['class'] ?? null;

					$control->setHtmlAttribute('class', $currentClass . ' is-invalid');
				}
			}
		};

		return $form;
	}
}
