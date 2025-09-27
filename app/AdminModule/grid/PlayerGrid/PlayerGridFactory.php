<?php

declare(strict_types = 1);

namespace Admin\Grid;

interface PlayerGridFactory
{
	function create(): PlayerGrid;
}
