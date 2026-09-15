// Required dependencies
import jquery from 'jquery';
import naja from 'naja';
import netteForms from 'nette-forms';

import 'bootstrap/dist/css/bootstrap.css';
import * as bootstrap from 'bootstrap';

import 'jquery-ui/ui/widgets/sortable';
import 'jquery-ui/ui/disable-selection';

import 'bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css';
import 'bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js';
import 'bootstrap-datepicker/js/locales/bootstrap-datepicker.cs.js';

import 'bootstrap-select/dist/css/bootstrap-select.min.css';
import 'bootstrap-select/dist/js/bootstrap-select.min.js';

import '@fortawesome/fontawesome-free/css/regular.css';
import '@fortawesome/fontawesome-free/css/solid.css';
import '@fortawesome/fontawesome-free/css/fontawesome.css';

import TomSelectLibrary from 'tom-select';
import {
	AutosubmitPlugin,
	CheckboxPlugin,
	ConfirmPlugin,
	createDatagrids,
	DatepickerPlugin,
	EditablePlugin,
	InlinePlugin,
	ItemDetailPlugin,
	NetteFormsPlugin,
	SelectpickerPlugin,
	SortableJS,
	SortablePlugin,
	TomSelect,
	TreeViewPlugin,
	VanillaDatepicker,
} from '@contributte/datagrid/assets/index';
import { NajaAjax } from '@contributte/datagrid/assets/ajax';
import 'vanillajs-datepicker/css/datepicker-bs5.css';
import 'tom-select/dist/css/tom-select.bootstrap5.css';
import '@contributte/datagrid/assets/css/datagrid.css';
import '@contributte/datagrid/assets/css/tom-select.css';

import './js/ColorPicker2.js';

import './css/login.css';
import './css/dashboard.css';

netteForms.initOnLoad();

naja.defaultOptions.history = false;
naja.formsHandler.netteForms = netteForms;
naja.initialize();

window.bootstrap = bootstrap;
window.naja = naja;
window.Nette = netteForms;

document.addEventListener('DOMContentLoaded', () => {
	createDatagrids(new NajaAjax(naja), {
		datagrid: {
			plugins: [
				new AutosubmitPlugin(),
				new CheckboxPlugin(),
				new ConfirmPlugin(),
				new EditablePlugin(),
				new InlinePlugin(),
				new ItemDetailPlugin(),
				new NetteFormsPlugin(netteForms),
				new SortablePlugin(new SortableJS()),
				new DatepickerPlugin(new VanillaDatepicker({ buttonClass: 'btn' })),
				new SelectpickerPlugin(new TomSelect(TomSelectLibrary)),
				new TreeViewPlugin(),
			],
		},
	});
});
