<?php

declare(strict_types = 1);

namespace User\Mail;

use Nette\Application\LinkGenerator;
use Nette\Bridges\ApplicationLatte\LatteFactory;
use Nette\Mail\Mailer;
use Nette\Mail\Message;

class ResetPasswordMail
{
	public function __construct
	(
		private readonly string $from,
		private readonly Mailer $Mailer,
		private readonly LinkGenerator $LinkGenerator,
		private readonly LatteFactory $LatteFactory
	)
	{
	}


	public function send(string $email, string $token): void
	{
		$html = $this->LatteFactory->create()
			->renderToString(__DIR__ . '/resetPasswordMail.latte', [
				'link' => $this->LinkGenerator->link('User:Login:setPassword', ['token' => $token])
			]);

		$message = (new Message)
			->setFrom($this->from)
			->addTo($email)
			->setSubject('Volejbálek - obnovení hesla')
			->setHtmlBody($html);

		$this->Mailer->send($message);
	}
}
