<?php

namespace User\Form;

use Nette\Application\UI\Form;

class UserForm extends \Core\Form\BaseForm
{
	/**
	 * @var \Core\Manager\MailManager
	 */
	private $MailManager;
	
	/**
	 * @var \Nette\Security\Passwords
	 */
	private $Passwords;

	/**
	 * @var \User\Repository\UserRepository
	 */
	private $UserRepository;

	/**
	 * @var string
	 */
	private $appDir;

	/**
	 * @var int|null
	 */
	private $id;

	public function __construct
	(
		$appDir,
		\User\Repository\UserRepository $UserRepository,
		\Nette\Security\Passwords $Passwords,
		\Core\Manager\MailManager $MailManager
	)
	{
		$this->UserRepository = $UserRepository;
		$this->Passwords = $Passwords;
		$this->appDir = $appDir;
		$this->MailManager = $MailManager;
	}

	public function prepare(): void
	{
		if($this->id)
		{
			$userEntity = $this->UserRepository->getByID($this->id);

			if($userEntity)
			{
				$this['form']->setDefaults($userEntity->toArray());
			}
		}
		else
		{
			$this['form']['password']->setDefaultValue($this->generatePassword());
		}
	}

	public function createComponentForm(): Form
	{
		$form = $this->getForm();

		$form->addText('login', 'Login')
			->setHtmlAttribute('class', 'form-control')
			->setRequired()
			->addRule(function($input)
			{
				$criteria = ['login' => $input->getValue()];

				if($this->id)
				{
					$criteria['id != ?'] = $this->id;
				}

				return !boolval($this->UserRepository->getBy($criteria));
			}, 'Tento login již existuje');

		if(!$this->id)
		{
			$form->addText('password', 'Heslo')
				->setHtmlAttribute('class', 'form-control')
				->setRequired();
		}

		$form->addText('firstname', 'Jméno')
			->setHtmlAttribute('class', 'form-control')
			->setRequired();

		$form->addText('lastname', 'Příjmení')
			->setHtmlAttribute('class', 'form-control')
			->setRequired();

		$form->addEmail('email', 'Email')
			->setHtmlAttribute('class', 'form-control')
			->setRequired()
			->addRule(function($input)
			{
				$criteria = ['email' => $input->getValue()];

				if($this->id)
				{
					$criteria['id != ?'] = $this->id;
				}

				return !boolval($this->UserRepository->getBy($criteria));
			}, 'Uživatel s tímto emailem je již zaregistrovnaý');

		$form->addCheckboxList('role', 'Role', ['admin' => 'Admin', 'customer' => 'Zákazník'])
			->setRequired();

		$form->addSubmit('save', 'Uložit');

		$form->onSuccess[] = [$this, 'successForm'];

		return $form;
	}

	public function successForm(Form $form, \Nette\Utils\ArrayHash $values): void
	{
		if($this->id)
		{
			$userEntity = $this->UserRepository->getByID($this->id);
		}
		else
		{
			$userEntity = new \User\Entity\UserEntity;

			$userEntity->password = $this->Passwords->hash($values->password);
		}

		$userEntity->login = $values->login;
		$userEntity->email = $values->email;
		$userEntity->firstname = $values->firstname;
		$userEntity->lastname = $values->lastname;
		$userEntity->role = $values->role;
		
		$this->UserRepository->save($userEntity);

		if(!$this->id)
		{
			$mail = (new \User\Mail\UserMail)
				->setRecipient($values->email)
				->setParams(['login' => $userEntity->login, 'password' => $values->password, 'create' => true])
				->setObjectId($userEntity->id);

			$this->MailManager->saveAndSendEmail($mail);
		}

		$this->getPresenter()->flashMessage('Úspěšně uloženo', 'success');
		$this->getPresenter()->redirect(':User:User:');
	}

	private function generatePassword(): string
	{
		return substr(bin2hex(openssl_random_pseudo_bytes(10)), 0, 10);
	}

	public function setId(?string $id): self
	{
		$this->id = $id ? intval($id) : null;

		return $this;
	}
}
