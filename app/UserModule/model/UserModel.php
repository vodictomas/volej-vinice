<?php

declare(strict_types = 1);

namespace User\Model;

class UserModel
{
	private \Core\Manager\MailManager $MailManager;

	private \Nette\Security\Passwords $Passwords;

	private \User\Repository\UserRepository $UserRepository;

	public function __construct
	(
		//\User\Repository\UserRepository $UserRepository,
		\Nette\Security\Passwords $Passwords,
		\Core\Manager\MailManager $MailManager
	)
	{
		//$this->UserRepository = $UserRepository;
		$this->Passwords = $Passwords;
		$this->MailManager = $MailManager;
	}

	public function resetPassword(string $hash): void
	{
		$hashids = new \Hashids\Hashids(\User\Mail\ResetPasswordMail::SALT, 20);

		$hashArray = $hashids->decode($hash);

		if(!is_array($hashArray) || count($hashArray) != 2)
		{
			throw new \User\Exception\LoginException('Neplatný odkaz');
		}

		if(\Nette\Utils\DateTime::createFromFormat('His', strval($hashArray[1]))->modify('+ 15 minutes') < new \Nette\Utils\DateTime)
		{
			throw new \User\Exception\LoginException('Platnost odkazu vypršela');
		}

		$userEntity = $this->UserRepository->getByID($hashArray[0]);

		$password = substr(bin2hex(openssl_random_pseudo_bytes(10)), 0, 10);

		$userEntity->password = $this->Passwords->hash($password);

		$this->UserRepository->save($userEntity);

		$mail = (new \User\Mail\UserMail)
			->setRecipient($userEntity->email)
			->setParams(['login' => $userEntity->login, 'password' => $password, 'create' => false])
			->setObjectId($userEntity->id);

		$this->MailManager->saveAndSendEmail($mail);
	}
}
