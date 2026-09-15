<?php

declare(strict_types = 1);

namespace User\Form;

interface LoginFormFactory
{
	function create(): LoginForm;
}
