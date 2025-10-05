<?php

declare(strict_types = 1);

namespace User\Model;

use Nette\Security\Passwords;
use User\Repository\UserRepository;

class UserModel
{
	public function __construct
	(
		private readonly UserRepository $UserRepository,
        private readonly Passwords $Passwords
	)
	{
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
	}
}
