<?php

declare(strict_types = 1);

namespace User\Model;

use Nette\Database\Explorer;
use Nette\Security\AuthenticationException;
use Nette\Security\Authenticator;
use Nette\Security\Passwords;
use Nette\Security\SimpleIdentity;

class UserManager implements Authenticator
{
	public function __construct
	(
		private readonly Explorer $Database,
		private readonly Passwords $Passwords
	)
	{
	}


	public function authenticate(string $username, string $password): SimpleIdentity
	{
		$userRow = $this->Database->table('user')
			->where('login', $username)
			->fetch();

		if(!$userRow)
		{
			throw new AuthenticationException('Login nebo heslo není správné', self::IdentityNotFound);
		}

		if(!$this->Passwords->verify($password, $userRow->password))
		{
			throw new AuthenticationException('Login nebo heslo není správné', self::InvalidCredential);
		}

		if($this->Passwords->needsRehash($userRow->password))
		{
			$userRow->update(['password' => $this->Passwords->hash($password)]);
		}

		return new SimpleIdentity($userRow->id, null, [
			'login' => $userRow->login,
			'firstname' => $userRow->firstname,
			'lastname' => $userRow->lastname,
		]);
	}
}
