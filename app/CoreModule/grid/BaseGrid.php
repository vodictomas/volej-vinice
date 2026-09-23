<?php

declare(strict_types = 1);

namespace Core\Grid;

class BaseGrid extends \Nette\Application\UI\Control
{
	public function getGrid(): \Contributte\Datagrid\Datagrid
	{
		$grid = new \Contributte\Datagrid\Datagrid;

		$grid::$iconPrefix = 'fa-solid fa-';

		$trans = new \Contributte\Datagrid\Localization\SimpleTranslator([
			'contributte_datagrid.action' => 'Akce',
			'contributte_datagrid.here' => 'Zde vymažte filtr',
			'contributte_datagrid.no_item_found' => 'Žádné položky k zobrazení.',
			'contributte_datagrid.no_item_found_reset' =>  'Žádné položky nenalezeny. Filtr můžete vynulovat',
			'contributte_datagrid.items' => 'Položky',
			'contributte_datagrid.from' => 'z',
			'contributte_datagrid.previous' => 'Předchozí',
			'contributte_datagrid.next' => 'Další',
			'contributte_datagrid.choose' => 'Vyberte',
			'contributte_datagrid.choose_input_required' => 'Vyplňte hodnotu',
			'contributte_datagrid.reset_filter' => 'Vymazat filtry',
			'contributte_datagrid.filter_submit_button' => 'Filtrovat',
			'contributte_datagrid.per_page_submit' => 'Změnit',
			'contributte_datagrid.all' => 'Vše',
			'contributte_datagrid.group_actions' => 'Hromadné akce',
			'contributte_datagrid.execute' => 'Provést akci',
			'contributte_datagrid.cancel' => 'Zrušit',
			'contributte_datagrid.save' => 'Uložit',
			'contributte_datagrid.edit' => 'Editovat položku',
			'contributte_datagrid.add' => 'Přidat položku',
			'contributte_datagrid.show' => 'Zobrazit',
			'contributte_datagrid.show_all_columns' => 'Zobrazit všechny sloupce',
			'contributte_datagrid.show_default_columns' => 'Zobrazit výchozí sloupce',
			'contributte_datagrid.show_filter' => 'Zobrazit filtr',
			'contributte_datagrid.hide_column' => 'Skrýt sloupec',
			'contributte_datagrid.multiselect_choose' => 'Vyberte',
			'contributte_datagrid.multiselect_selected' => 'Vybrané položky'
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
