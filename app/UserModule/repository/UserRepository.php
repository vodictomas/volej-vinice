<?php

namespace User\Repository;

class UserRepository extends \ModulIS\Repository
{
	protected $table = 'user';
	protected $entity = '\User\Entity\UserEntity';
	
	public function getBy(array $criteria): ?\User\Entity\UserEntity
	{
		return parent::getBy($criteria);
	}
	
	public function getByID($id): ?\User\Entity\UserEntity
	{
		return parent::getByID($id);
	}
}
