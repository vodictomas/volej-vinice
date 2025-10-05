<?php

declare(strict_types = 1);

namespace User\Model;

class UserManager implements \Nette\Security\Authenticator
{
	public function __construct
	(
		protected \Nette\Database\Explorer $Database,
		protected \Nette\Security\Passwords $Passwords
	)
	{
	}

	public function authenticate(string $username, string $password): \Nette\Security\IIdentity
	{
		$userRow = $this->Database->table('user')
			->where('login', $username)
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
