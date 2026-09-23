<?php

declare(strict_types = 1);

namespace Admin\Component;

interface TermCalendarFactory
{
	function create(): TermCalendar;
}
