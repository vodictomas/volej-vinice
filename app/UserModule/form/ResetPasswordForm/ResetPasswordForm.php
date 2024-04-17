<?php

namespace User\Form;

class ResetPasswordForm extends \Core\Form\BaseForm
{

	/**
	 * @var \Nette\Application\LinkGenerator
	 */
	private $LinkGenerator;

	/**
	 * @var \Core\Manager\MailManager
	 */
	private $MailManager;

	/**
	 * @var \User\Repository\UserRepository
	 */
	private $UserRepository;

	public function __construct
	(
		\User\Repository\UserRepository $UserRepository,
		\Core\Manager\MailManager $MailManager,
		\Nette\Application\LinkGenerator $LinkGenerator
	)
	{
		$this->UserRepository = $UserRepository;
		$this->MailManager = $MailManager;
		$this->LinkGenerator = $LinkGenerator;
	}

	public function createComponentForm(): \Nette\Application\UI\Form
	{
		$form = $this->getForm();

		$form->addEmail('email', 'Zadejte Váš zaregistrovaný email')
			->setHtmlAttribute('class', 'form-control')
			->addRule(function($input)
			{
				return boolval($this->UserRepository->getBy(['email' => $input->getValue()]));
			}, 'Nenalezen žádný uživatel s tímto emailem')
			->setRequired();

		$form->addSubmit('reset', 'Resetovat heslo');

		$form->onSuccess[] = [$this, 'successForm'];

		return $form;
	}

	public function successForm(\Nette\Application\UI\Form $form, \Nette\Utils\ArrayHash $values): void
	{
		$userEntity = $this->UserRepository->getBy(['email' => $values->email]);

		$hashids = new \Hashids\Hashids(\User\Mail\ResetPasswordMail::SALT, 20);

		$hash = $hashids->encode($userEntity->id, (new \Nette\Utils\DateTime)->format('His'));

		$mail = (new \User\Mail\ResetPasswordMail)
			->setRecipient($values->email)
			->setParams(['hash' => $hash])
			->setObjectId($userEntity->id);

		$this->MailManager->saveAndSendEmail($mail);

		$this->getPresenter()->flashMessage('Na zadaný email byly odeslány pokyny pro resetování hesla', 'success');
		$this->getPresenter()->redirect(':User:Login:');
	}
}
