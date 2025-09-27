<?php

declare(strict_types = 1);

namespace User\Mail;

class UserMail extends \Core\Object\MailObject
{
	public $namespace = 'user_form';
	protected $subject = 'Vytvoření účtu';
}
