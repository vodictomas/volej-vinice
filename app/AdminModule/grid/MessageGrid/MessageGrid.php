<?php

declare(strict_types = 1);

namespace Admin\Grid;

use Contributte\Datagrid\Column\Action\Confirmation\StringConfirmation;
use Contributte\Datagrid\Datagrid;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\Html;

class MessageGrid extends \Core\Grid\BaseGrid
{
	public function __construct
	(
		private readonly Explorer $Database
	)
	{
	}


	public function createComponentGrid(): Datagrid
	{
		$grid = $this->getGrid();

		$grid->setDataSource($this->Database->table('message'));

		$grid->setDefaultSort(['date' => 'DESC']);

		$grid->addColumnDateTime('date', 'Odesláno')
			->setFormat('j. n. Y H:i')
			->setAlign('center')
			->setSortable();

		$grid->addColumnText('player', 'Autor', 'player.nick')
			->setAlign('center')
			->setSortable()
			->setFilterText('player.nick');

		/**
		 * Barvu si pisatel volí sám, tak ji ukážeme stejně jako v chatu –
		 * jinak by v administraci nešlo poznat, co uživatel vlastně viděl.
		 */
		$grid->addColumnText('text', 'Zpráva')
			->setRenderer(fn(ActiveRow $item) => Html::el('span')
				->setAttribute('style', 'color: ' . $this->sanitizeColor($item->color))
				->setText($item->text))
			->setFilterText();

		$grid->addActionCallback('delete', '')
			->setClass('btn btn-outline-danger btn-sm')
			->setTitle('Smazat zprávu')
			->setIcon('trash')
			->setConfirmation(new StringConfirmation('Opravdu smazat tuto zprávu?'))
			->onClick[] = [$this, 'delete'];

		$grid->addGroupButtonAction('Smazat vybrané', 'btn btn-sm btn-danger')
			->onClick[] = [$this, 'deleteGroup'];

		return $grid;
	}


	public function delete(int|string $id): void
	{
		$this->Database->table('message')
			->where('id', $id)
			->delete();

		$this->getPresenter()->flashMessage('Zpráva smazána', 'success');
		$this->getPresenter()->redirect('this');
	}


	/**
	 * @param array<int|string> $idArray
	 */
	public function deleteGroup(array $idArray): void
	{
		$count = $this->Database->table('message')
			->where('id', $idArray)
			->delete();

		$this->getPresenter()->flashMessage('Smazáno zpráv: ' . $count, 'success');
		$this->getPresenter()->redirect('this');
	}


	/**
	 * Do sloupce se ukládá cokoli, co přišlo z formuláře, takže hodnotu nelze pustit do stylu naslepo
	 */
	private function sanitizeColor(?string $color): string
	{
		return $color !== null && preg_match('~^#[0-9a-fA-F]{6}$~', $color) === 1
			? $color
			: 'inherit';
	}
}
