<?php

declare(strict_types = 1);

namespace User\Model;

use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Security\Passwords;
use Nette\Utils\DateTime;
use Nette\Utils\Random;
use User\Mail\ResetPasswordMail;

class UserModel
{
	private const ResetTokenValidity = '+1 hour';


	public function __construct
	(
		private readonly Explorer $Database,
		private readonly Passwords $Passwords,
		private readonly ResetPasswordMail $ResetPasswordMail
	)
	{
	}


	public function getById(int $id): ?ActiveRow
	{
		return $this->Database->table('user')
			->where('id', $id)
			->fetch();
	}


	/**
	 * Hodnota je volná, pokud ji nemá jiný uživatel než $exceptId
	 */
	public function isUnique(string $column, string $value, ?int $exceptId = null): bool
	{
		$selection = $this->Database->table('user')
			->where($column, $value);

		if($exceptId)
		{
			$selection->where('id != ?', $exceptId);
		}

		return $selection->count('*') === 0;
	}


	/**
	 * Prázdné heslo při úpravě ponechá původní
	 */
	public function save(?int $id, array $data): void
	{
		if($data['password'] ?? null)
		{
			$data['password'] = $this->Passwords->hash($data['password']);
		}
		else
		{
			unset($data['password']);
		}

		if($id)
		{
			$this->Database->table('user')
				->where('id', $id)
				->update($data);
		}
		else
		{
			$this->Database->table('user')
				->insert($data);
		}
	}


	public function delete(int $id): void
	{
		$this->Database->table('user')
			->where('id', $id)
			->delete();
	}


	/**
	 * Pošle odkaz pro nastavení nového hesla, neexistující e-mail tiše ignoruje
	 */
	public function sendResetLink(string $email): void
	{
		$userRow = $this->Database->table('user')
			->where('email', $email)
			->fetch();

		if(!$userRow)
		{
			return;
		}

		$token = Random::generate(40);

		$userRow->update([
			'reset_token' => hash('sha256', $token),
			'reset_expire' => (new DateTime)->modify(self::ResetTokenValidity)
		]);

		$this->ResetPasswordMail->send($userRow->email, $token);
	}


	public function getByResetToken(string $token): ?ActiveRow
	{
		return $this->Database->table('user')
			->where('reset_token', hash('sha256', $token))
			->where('reset_expire > ?', new DateTime)
			->fetch();
	}


	public function resetPassword(ActiveRow $userRow, string $password): void
	{
		$userRow->update([
			'password' => $this->Passwords->hash($password),
			'reset_token' => null,
			'reset_expire' => null
		]);
	}
}
