<?php

declare(strict_types=1);

namespace App;

use Nette\Configurator;


class Bootstrap
{
	public static function boot(): Configurator
	{
		$configurator = new Configurator;

		$configurator->setDebugMode(true);
		$configurator->enableTracy(__DIR__ . '/../log');

		$configurator->setTimeZone('Europe/Prague');
		$configurator->setTempDirectory(__DIR__ . '/../temp');

		$configurator->addConfig(__DIR__ . '/config/common.neon');
		$configurator->addConfig(__DIR__ . '/config/local.neon');
		$configurator->addConfig(__DIR__ . '/CoreModule/config.neon');
		$configurator->addConfig(__DIR__ . '/AdminModule/config.neon');
		$configurator->addConfig(__DIR__ . '/UserModule/config.neon');

		$configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();

		\Kravcik\Macros\FontAwesomeMacro::$defaultStyle = 'fas';

		return $configurator;
	}
}
