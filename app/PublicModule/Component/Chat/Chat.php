<?php

declare(strict_types = 1);

namespace Public\Component;

use Core\Component\BaseComponent;
use Nette\Application\UI\Form;
use Nette\Database\Explorer;
use Nette\Utils\ArrayHash;
use Public\Enum\SmileEnum;

class Chat extends BaseComponent
{
	private const MessageLimit = 100;

	private const DefaultColor = '#33CCFF';


	public function __construct
	(
		private readonly Explorer $Database
	)
	{
	}


	public function beforeRender(): void
	{
		$imagePath = $this->getPresenter()->getHttpRequest()->getUrl()->getBasePath() . 'images/smileys';

		$this->template->addFilter('smile', fn(string $text) => SmileEnum::replace($text, $imagePath));
		$this->template->addFilter('color', fn(string $color) => preg_match('~^#[0-9a-fA-F]{6}$~', $color) ? $color : 'inherit');

		$this->template->messageList = $this->Database->table('message')
			->select('player.nick, message.text, message.date, message.color')
			->order('message.date DESC, message.id DESC')
			->limit(self::MessageLimit);
		$this->template->smileList = SmileEnum::cases();
		$this->template->imagePath = $imagePath;
		$this->template->colorArray = $this->getColorArray();
	}


	public function handleRefresh(): void
	{
		$this->redrawControl('messages');
	}


	protected function createComponentForm(): Form
	{
		$form = new Form;

		$playerArray = $this->Database->table('player')
			->where('active', 1)
			->order('nick')
			->fetchPairs('id', 'nick');

		$form->addSelect('player_id', 'Jméno', $playerArray)
			->setPrompt('NEZVOLENO')
			->setRequired('Vyberte jméno');

		$form->addHidden('color', self::DefaultColor)
			->addRule($form::Pattern, 'Neplatná barva', '#[0-9a-fA-F]{6}');

		$form->addText('text', 'Zpráva')
			->setRequired('Napište zprávu')
			->addRule($form::MaxLength, 'Zpráva je příliš dlouhá', 250);

		$form->addSubmit('send', 'Poslat');

		$form->onSuccess[] = [$this, 'successForm'];

		return $form;
	}


	public function successForm(Form $form, ArrayHash $values): void
	{
		$this->Database->table('message')
			->insert([
				'player_id' => $values->player_id,
				'color' => $values->color,
				'text' => $values->text,
			]);

		if($this->getPresenter()->isAjax())
		{
			$this->redrawControl('messages');
		}
		else
		{
			$this->getPresenter()->redirect('this');
		}
	}


	/**
	 * Paleta 216 "web-safe" barev jako ve staré aplikaci
	 *
	 * @return string[]
	 */
	private function getColorArray(): array
	{
		$colorArray = [];
		$stepArray = ['00', '33', '66', '99', 'CC', 'FF'];

		foreach($stepArray as $green)
		{
			foreach($stepArray as $red)
			{
				foreach($stepArray as $blue)
				{
					$colorArray[] = '#' . $red . $green . $blue;
				}
			}
		}

		return $colorArray;
	}
}
