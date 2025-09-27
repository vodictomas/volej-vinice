<?php

declare(strict_types = 1);

namespace Admin\Form;

interface TeamFormFactory
{
	function create(?int $id): TeamForm;
}
