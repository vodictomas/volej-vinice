<?php

declare(strict_types = 1);

namespace Admin\Form;

interface PlayerFormFactory
{
	function create(?int $id): PlayerForm;
}
