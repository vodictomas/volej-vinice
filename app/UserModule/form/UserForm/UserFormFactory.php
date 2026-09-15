<?php

declare(strict_types = 1);

namespace User\Form;

interface UserFormFactory
{
	function create(?int $id): UserForm;
}
