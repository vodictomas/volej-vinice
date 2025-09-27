<?php

namespace User\Form;

interface IUserFormFactory 
{
	function create(?int $id): UserForm;
}
