<?php

namespace Core\Grid;

class BaseGrid extends \Nette\Application\UI\Control
{
	public function getGrid(): \Ublaboo\DataGrid\DataGrid
	{
		$grid = new \Ublaboo\DataGrid\DataGrid;

		$grid::$iconPrefix = 'fas fa-';

		$trans = new \Ublaboo\DataGrid\Localization\SimpleTranslator([
			'ublaboo_datagrid.group actions' => 'Hromadné akce',
			'ublaboo_datagrid.action' => 'Akce',
			'ublaboo_datagrid.here' => 'Zde vymažte filtr',
			'ublaboo_datagrid.no_item_found' => 'Žádné položky k zobrazení.',
			'ublaboo_datagrid.no_item_found_reset' =>  'Žádné položky nenalezeny. Filtr můžete vynulovat',
			'ublaboo_datagrid.items' => 'Položky',
			'ublaboo_datagrid.from' => 'z',
			'ublaboo_datagrid.previous' => 'Předchozí',
			'ublaboo_datagrid.next' => 'Další',
			'ublaboo_datagrid.choose' => 'Vyberte',
			'ublaboo_datagrid.reset_filter' => 'Vymazat filtry',
			'ublaboo_datagrid.all' => 'Vše',
			'ublaboo_datagrid.do' => 'Proveď',
			'ublaboo_datagrid.group_actions' => 'Hromadné akce',
			'ublaboo_datagrid.execute' => 'Provést akci',
			'ublaboo_datagrid.cancel' => 'Zrušit',
			'ublaboo_datagrid.save' => 'Uložit',
			'ublaboo_datagrid.edit' => 'Editovat položku',
			'ublaboo_datagrid.add' => 'Přidat položku',
			'ublaboo_datagrid.multiselect_choose' => 'Vyberte',
			'ublaboo_datagrid.multiselect_selected' => 'Vybrané položky'
        ]);

		$grid->setTranslator($trans);

		return $grid;
	}

	public function render()
	{
		$this->template->setFile(__DIR__ . DIRECTORY_SEPARATOR . 'grid.latte');
		$this->template->render();
	}
}
