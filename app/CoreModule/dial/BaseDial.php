<?php

declare(strict_types = 1);

namespace Core\Dial;

abstract class BaseDial
{
	/** @return array<string, string> */
	abstract protected static function translate(): array;


	/** @return array<string, string> */
	public static function getList(): array
	{
		return static::translate();
	}

	public static function getString(string $value): string
	{
		return static::translate()[$value];
	}
}
