<?php

namespace User\Mail;

class ResetPasswordMail extends \Core\Object\MailObject
{
	const SALT = 'nebilovskyborek22';
	protected $subject = 'Resetování hesla';
	public $namespace = 'reset_password';
}
