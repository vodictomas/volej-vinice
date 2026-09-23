<?php

declare(strict_types = 1);

namespace Public\Enum;

use Latte\Runtime\Html;

enum SmileEnum: string
{
	case Laugh = '1.gif';
	case LaughOpen = '15.gif';
	case BigSmile = '2.gif';
	case Smile = '3.gif';
	case Wink = '4.gif';
	case Tongue = '5.gif';
	case TongueOpen = '16.gif';
	case Crazy = '17.gif';
	case Neutral = '6.gif';
	case Unsure = '7.gif';
	case Sad = '8.gif';
	case Dead = '12.gif';
	case Cry = '9.gif';
	case CryOpen = '19.gif';
	case Surprised = '10.gif';
	case Cool = '11.gif';
	case Drool = '13.gif';
	case Angry = '18.gif';


	/**
	 * Textové zápisy smajlíku, první je výchozí pro vložení do zprávy
	 *
	 * @return string[]
	 */
	public function getCodeArray(): array
	{
		return match($this)
		{
			self::Laugh => [':-D', ':D'],
			self::LaughOpen => [':oD'],
			self::BigSmile => [':-))', ':))', '((:', '((-:'],
			self::Smile => [':-)', ':)', '(:', '(-:'],
			self::Wink => [';-)', ';)', '(;', '(-;'],
			self::Tongue => [':-P', ':P'],
			self::TongueOpen => [':oP'],
			self::Crazy => ['%-)'],
			self::Neutral => [':-|', ':|'],
			// Bez krátkého ":/", jinak by se rozbily odkazy http://
			self::Unsure => [':-/'],
			self::Sad => [':('],
			self::Dead => ['X[]'],
			self::Cry => [':´-('],
			self::CryOpen => [':´o('],
			self::Surprised => [':-O'],
			self::Cool => ['B-]'],
			self::Drool => [':_)'],
			self::Angry => [':-!'],
		};
	}


	public function getCode(): string
	{
		return $this->getCodeArray()[0];
	}


	/**
	 * @return array{int, int} [šířka, výška]
	 */
	public function getSize(): array
	{
		return match($this)
		{
			self::Crazy => [16, 16],
			self::CryOpen => [21, 16],
			self::Cool => [21, 15],
			self::Drool => [50, 15],
			self::Angry => [22, 19],
			default => [15, 15],
		};
	}


	public function getImage(string $imagePath): Html
	{
		[$width, $height] = $this->getSize();

		return new Html(sprintf(
			'<img src="%s/%s" width="%d" height="%d" alt="%s" style="vertical-align: middle;">',
			htmlspecialchars($imagePath),
			$this->value,
			$width,
			$height,
			htmlspecialchars($this->getCode())
		));
	}


	/**
	 * Escapuje text a nahradí v něm smajlíky obrázky
	 */
	public static function replace(string $text, string $imagePath): Html
	{
		$replaceArray = [];

		foreach(self::cases() as $smile)
		{
			foreach($smile->getCodeArray() as $code)
			{
				$replaceArray[htmlspecialchars($code)] = (string) $smile->getImage($imagePath);
			}
		}

		// strtr vždy nahradí nejdelší shodu, takže ":-))" nerozbije ":-)"
		return new Html(strtr(htmlspecialchars($text), $replaceArray));
	}
}
