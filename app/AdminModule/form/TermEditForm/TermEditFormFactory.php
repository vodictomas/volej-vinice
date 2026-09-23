<?php

declare(strict_types = 1);

namespace Admin\Form;

interface TermEditFormFactory
{
	function create(?int $id): TermEditForm;
}
