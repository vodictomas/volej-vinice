<?php

declare(strict_types = 1);

namespace User\Mail;

class ResetPasswordMail extends \Core\Object\MailObject
{
	public const SALT = 'nebilovskyborek22';
	public $namespace = 'reset_password';
	protected $subject = 'Resetování hesla';
}
