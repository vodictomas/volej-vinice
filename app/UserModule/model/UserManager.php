<?php

declare(strict_types = 1);

namespace User\Model;

class UserManager implements \Nette\Security\IAuthenticator
{
	public function __construct
	(
		protected \Nette\Database\Explorer $Database,
		protected \Nette\Security\Passwords $Passwords
	)
	{
	}

	public function authenticate(array $credentials): \Nette\Security\IIdentity
	{
		[$login, $password] = $credentials;

		$userRow = $this->Database->table('user')
			->where('login', $login)
			->fetch();

		if(!$userRow)
		{
			throw new \Nette\Security\AuthenticationException('Login nebo heslo není správné');
		}

		if(!$this->Passwords->verify($password, $userRow->password))
		{
			throw new \Nette\Security\AuthenticationException('Login nebo heslo není správné');
		}

		$identityArray = $userRow->toArray();

		unset($identityArray['password']);

		return new \Nette\Security\Identity($userRow->id, null, $identityArray);
	}
}
