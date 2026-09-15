<?php

declare(strict_types = 1);

namespace User\Form;

use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\ArrayHash;
use User\Model\UserModel;

class SetPasswordForm extends \Core\Form\BaseForm
{
	public const PasswordMinLength = 8;


	public function __construct
	(
		private readonly ActiveRow $userRow,
		private readonly UserModel $UserModel
	)
	{
	}


	protected function createComponentForm(): Form
	{
		$form = $this->getForm();

		$form->addPassword('password', 'Nové heslo')
			->setHtmlAttribute('class', 'form-control')
			->setHtmlAttribute('placeholder', 'Nové heslo')
			->setHtmlAttribute('autocomplete', 'new-password')
			->setRequired('Zadejte nové heslo')
			->addRule($form::MinLength, 'Heslo musí mít alespoň %d znaků', self::PasswordMinLength);

		$form->addPassword('password_confirm', 'Heslo znovu')
			->setHtmlAttribute('class', 'form-control')
			->setHtmlAttribute('placeholder', 'Heslo znovu')
			->setHtmlAttribute('autocomplete', 'new-password')
			->setRequired('Zadejte heslo znovu')
			->addRule($form::Equal, 'Hesla se neshodují', $form['password'])
			->setOmitted();

		$form->addSubmit('save', 'Nastavit heslo');

		$form->onSuccess[] = [$this, 'successForm'];

		return $form;
	}


	public function successForm(Form $form, ArrayHash $values): void
	{
		$this->UserModel->resetPassword($this->userRow, $values->password);

		$this->getPresenter()->flashMessage('Heslo bylo nastaveno, můžete se přihlásit', 'success');
		$this->getPresenter()->redirect(':User:Login:');
	}
}
