<?php

namespace Admin\Form;

interface TeamFormFactory
{
	function create(?int $id): TeamForm;
}
