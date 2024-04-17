<?php

namespace Admin\Dial;

class AttendanceTypeDial
{
	const YES = 'y';
	const NO = 'n';
	const WAITING = 'w';

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
