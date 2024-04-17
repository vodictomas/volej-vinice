<?php

namespace User\Mail;

class UserMail extends \Core\Object\MailObject
{
	protected $subject = 'Vytvoření účtu';
	public $namespace = 'user_form';
}
