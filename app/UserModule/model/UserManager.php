<?php

namespace User\Model;

class UserManager implements \Nette\Security\IAuthenticator
{
	/**
	 * @var \Nette\Database\Explorer
	 */
	protected $Database;

	/**
	 * @var \Nette\Security\Passwords
	 */
	protected $Passwords;

	public function __construct
	(
		\Nette\Database\Explorer $Database,
		\Nette\Security\Passwords $Passwords
	)
	{
		$this->Database = $Database;
		$this->Passwords = $Passwords;
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
