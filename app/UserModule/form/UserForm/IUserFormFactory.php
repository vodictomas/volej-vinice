<?php

declare(strict_types = 1);

namespace User\Form;

interface IUserFormFactory
{
	function create(?int $id): UserForm;
}
