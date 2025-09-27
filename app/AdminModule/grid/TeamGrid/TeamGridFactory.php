<?php

declare(strict_types = 1);

namespace Admin\Grid;

interface TeamGridFactory
{
	function create(): TeamGrid;
}
