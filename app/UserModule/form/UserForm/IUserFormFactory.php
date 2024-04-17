<?php

namespace User\Form;

interface IUserFormFactory 
{
	function create(): UserForm;
}
