<?php

namespace Core\Dial;

abstract class BaseDial
{
	public static function getList(): array
	{
		return static::translate();
	}

	public static function getString(string $value)
	{
		return static::translate()[$value];
	}
}
