<?php

declare(strict_types = 1);

namespace User\Form;

use Nette\Database\Table\ActiveRow;

interface SetPasswordFormFactory
{
	function create(ActiveRow $userRow): SetPasswordForm;
}
