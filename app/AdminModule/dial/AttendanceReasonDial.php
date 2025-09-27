<?php

declare(strict_types = 1);

namespace Admin\Dial;

class AttendanceReasonDial
{
	public const BEER = 'be';
	public const WINE = 'wi';
	public const KOFOLA = 'ko';
	public const HOUBA = 'ho';
	public const CHIPS = 'ch';
	public const INJURY_BEER = 'ib';
	public const CELEBRATION = 'ce';
	public const NONSTOP = 'no';
	public const HOUBA_NONSTOP = 'hn';
	public const SLEEP = 'sl';
	public const LEARN = 'le';
	public const SKI = 'ski';
	public const COLD = 'co';
	public const INJURY = 'in';
	public const BABY = 'ba';
	public const WORK = 'wo';
	public const WAITING = 'w';


	public static function getPubArray(): array
	{
		return [
			self::BEER => 'Jo na jedno klidně zajdu',
			self::WINE => 'Du na víno',
			self::KOFOLA => 'Du jen na kofolu',
			self::HOUBA => 'Já jdu houbařit (;',
			self::CHIPS => 'A já si dám hranolky, dvojitý',
			self::INJURY_BEER => 'Sem zraněnej, ale na pivo mohu',
			self::CELEBRATION => 'Slavíííím',
			self::NONSTOP => 'Pojďte do nonstopu! @ Bělka',
			self::HOUBA_NONSTOP => 'Na houbu, pak do nonstopu'
		];
	}


	public static function getExcuseArray(): array
	{
		return [
			self::SLEEP => 'Musím jít brzo spát',
			self::LEARN => 'Musím se učit',
			self::SKI => 'Sem na horách',
			self::COLD => 'Sem nachcípanej',
			self::INJURY => 'Sem zraněnej',
			self::BABY => 'Mateřské povinnosti',
			self::WORK => 'Musím být v práci'
		];
	}


	public static function translateIcon(string $icon): ?string
	{
		$translationArray = [
			self::BEER => 'pivo3b.jpg',
			self::WINE => 'vino.png',
			self::KOFOLA => 'kofola3r.jpg',
			self::HOUBA => 'moch2.jpg',
			self::CHIPS => 'hranolky4.jpg',
			self::INJURY_BEER => 'invalidaSPivem3.jpg',
			self::CELEBRATION => 'dortik.jpg',
			self::NONSTOP => 'nonstop.jpg',
			self::HOUBA_NONSTOP => 'mochnon.gif',
			self::SLEEP => 'postel12.jpg',
			self::LEARN => 'kniha3.jpg',
			self::SKI => 'ski2.jpg',
			self::COLD => 'nemoc.jpg',
			self::INJURY => 'invalida3.png',
			self::BABY => 'kocarek2.png',
			self::WORK => 'prace3.jpg',
			self::WAITING => 'question.jpg'
		];

		return $translationArray[$icon] ?? null;
	}
}
