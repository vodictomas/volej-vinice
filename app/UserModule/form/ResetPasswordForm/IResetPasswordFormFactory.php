<?php

namespace User\Form;

interface IResetPasswordFormFactory
{
	function create(): ResetPasswordForm;
}
