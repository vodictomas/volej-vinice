<?php

declare(strict_types = 1);

namespace Admin\Dial;

class AttendanceTypeDial
{
	public const YES = 'y';
	public const NO = 'n';
	public const WAITING = 'w';

	public static function translateIcon(string $icon): ?string
	{
		$translationArray = [
			self::YES => 'check.jpg',
			self::NO => 'cross.gif',
			self::WAITING => 'question.jpg'
		];

		return $translationArray[$icon] ?? null;
	}
}
