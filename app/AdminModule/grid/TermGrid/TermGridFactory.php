<?php

declare(strict_types = 1);

namespace Admin\Grid;

interface TermGridFactory
{
	function create(): TermGrid;
}
