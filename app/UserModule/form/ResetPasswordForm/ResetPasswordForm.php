<?php

declare(strict_types = 1);

namespace User\Form;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use User\Model\UserModel;

class ResetPasswordForm extends \Core\Form\BaseForm
{
	public function __construct
	(
		private readonly UserModel $UserModel
	)
	{
	}


	protected function createComponentForm(): Form
	{
		$form = $this->getForm();

		$form->addEmail('email', 'Zadejte e-mail uvedený u Vašeho účtu')
			->setHtmlAttribute('class', 'form-control')
			->setHtmlAttribute('placeholder', 'E-mail')
			->setRequired('Zadejte e-mail');

		$form->addSubmit('reset', 'Odeslat odkaz');

		$form->onSuccess[] = [$this, 'successForm'];

		return $form;
	}


	public function successForm(Form $form, ArrayHash $values): void
	{
		$this->UserModel->sendResetLink($values->email);

		// Stejná hláška i pro neexistující e-mail, aby nešlo zjišťovat registrované adresy
		$this->getPresenter()->flashMessage('Pokud u některého účtu tento e-mail evidujeme, poslali jsme na něj odkaz pro nastavení nového hesla', 'success');
		$this->getPresenter()->redirect(':User:Login:');
	}
}
