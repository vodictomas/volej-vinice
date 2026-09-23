<?php

declare(strict_types = 1);

namespace Admin\Grid;

interface MessageGridFactory
{
	function create(): MessageGrid;
}
