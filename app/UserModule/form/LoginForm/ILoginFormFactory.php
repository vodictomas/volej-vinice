<?php

namespace User\Form;

interface ILoginFormFactory
{
	function create(): LoginForm;
}
