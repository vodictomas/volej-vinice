<?php

declare(strict_types = 1);

namespace Public\Component;

interface ChatFactory
{
	function create(): Chat;
}
