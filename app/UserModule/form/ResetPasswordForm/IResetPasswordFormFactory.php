<?php

declare(strict_types = 1);

namespace User\Form;

interface IResetPasswordFormFactory
{
	function create(): ResetPasswordForm;
}
