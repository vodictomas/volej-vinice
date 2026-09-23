<?php

declare(strict_types = 1);

namespace User\Grid;

interface UserGridFactory
{
	function create(): UserGrid;
}
