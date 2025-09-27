<?php

namespace Admin\Form;

interface PlayerFormFactory
{
	function create(?int $id): PlayerForm;
}
