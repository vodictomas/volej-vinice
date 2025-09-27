<?php

declare(strict_types = 1);

namespace Admin\Form;

interface TermFormFactory
{
	function create(): TermForm;
}
