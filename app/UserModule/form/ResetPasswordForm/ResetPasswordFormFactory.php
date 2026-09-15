<?php

declare(strict_types = 1);

namespace User\Form;

interface ResetPasswordFormFactory
{
	function create(): ResetPasswordForm;
}
