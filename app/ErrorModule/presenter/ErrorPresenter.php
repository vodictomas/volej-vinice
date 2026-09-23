<?php

declare(strict_types = 1);

namespace ErrorModule;

use Nette\Application\BadRequestException;
use Nette\Application\UI\Presenter;
use Tracy\ILogger;

/**
 * Chybová stránka. Záměrně nedědí z BasePresenter ani nesahá do databáze,
 * aby se vykreslila i ve chvíli, kdy je rozbité to, co chybu způsobilo.
 */
final class ErrorPresenter extends Presenter
{
	private const MessageArray = [
		400 => ['Neplatný požadavek', 'Odkaz je poškozený nebo v něm chybí údaje. Zkuste se vrátit na docházku a akci zopakovat.'],
		403 => ['Sem se nedostanete', 'Tahle část je jen pro přihlášené správce.'],
		404 => ['Stránka nenalezena', 'Adresa neexistuje nebo se přesunula jinam.'],
		405 => ['Takhle to nejde', 'Akce se musí vyvolat ze stránky, ne přímo z adresního řádku.'],
		410 => ['Stránka už neexistuje', 'Obsah byl odstraněn a zpátky se nevrátí.'],
		500 => ['Něco se pokazilo', 'Chybu jsme zaznamenali a podíváme se na ni.']
	];


	public function __construct
	(
		private readonly ILogger $Logger
	)
	{
		parent::__construct();
	}


	public function renderDefault(\Throwable $exception): void
	{
		if($exception instanceof BadRequestException)
		{
			$code = isset(self::MessageArray[$exception->getCode()]) ? $exception->getCode() : 404;
		}
		else
		{
			$code = 500;

			$this->Logger->log($exception, ILogger::EXCEPTION);
		}

		$this->getHttpResponse()->setCode($code);

		[$title, $note] = self::MessageArray[$code];

		/**
		 * Požadavky z naja čekají JSON, celá HTML stránka by je jen zmátla.
		 * Frontend si z payloadu vezme hlášku pro uživatele.
		 */
		if($this->isAjax())
		{
			$this->payload->error = true;
			$this->payload->errorCode = $code;
			$this->payload->errorMessage = $title;

			$this->sendPayload();
		}

		$this->setLayout(__DIR__ . '/../../layout/@baseLayout.latte');

		$this->template->errorCode = $code;
		$this->template->errorTitle = $title;
		$this->template->errorNote = $note;
	}
}
