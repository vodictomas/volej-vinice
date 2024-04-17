<?php

namespace User\Grid;

interface IUserGridFactory
{
	function create(): UserGrid;
}
