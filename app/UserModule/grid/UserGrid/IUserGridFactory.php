<?php

declare(strict_types = 1);

namespace User\Grid;

interface IUserGridFactory
{
	function create(): UserGrid;
}
