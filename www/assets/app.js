import naja from 'naja';
import netteForms from 'nette-forms';

import 'bootstrap/dist/css/bootstrap.css';

/**
 * Z datagridu bereme jen to, co aplikace používá. Import z 'assets/index' by
 * přitáhl i integrace na tom-select, sortablejs a vanillajs-datepicker,
 * které nikde nepoužíváme, proto sahame rovnou do konkrétních modulů.
 */
import { createDatagrids } from '@contributte/datagrid/assets/datagrid';
import { AutosubmitPlugin } from '@contributte/datagrid/assets/plugins/features/autosubmit';
import { CheckboxPlugin } from '@contributte/datagrid/assets/plugins/features/checkboxes';
import { ConfirmPlugin } from '@contributte/datagrid/assets/plugins/features/confirm';
import { NetteFormsPlugin } from '@contributte/datagrid/assets/plugins/integrations/nette-forms';
import { NajaAjax } from '@contributte/datagrid/assets/ajax/naja';
import '@contributte/datagrid/assets/css/datagrid.css';

import './css/icons.css';
import './css/admin.css';

netteForms.initOnLoad();

naja.defaultOptions.history = false;
naja.formsHandler.netteForms = netteForms;

initAjaxErrors();

naja.initialize();

/* veřejná část volá naja.makeRequest() přímo ze šablony */
window.naja = naja;
window.Nette = netteForms;

document.addEventListener('DOMContentLoaded', () =>
{
	createDatagrids(new NajaAjax(naja), {
		datagrid: {
			plugins: [
				new AutosubmitPlugin(),
				/* zaškrtávátka pro hromadné mazání zpráv */
				new CheckboxPlugin(),
				new ConfirmPlugin(),
				new NetteFormsPlugin(netteForms),
			],
		},
	});

	initMenuToggle();
	initFlashMessages();
});

/**
 * Zobrazí bublinu v pravém horním rohu. Vlastní kvůli tomu, že veřejná část
 * a administrace mají každá jiné flash zprávy.
 */
function showToast(message, timeout = 6000)
{
	let area = document.querySelector('.vv-toast-area');

	if(!area)
	{
		area = document.createElement('div');
		area.className = 'vv-toast-area';
		/**
		 * Styly nastavujeme přímo. Veřejná část si vystačí s vlastními
		 * inline styly v layoutu a bundle s admin.css vůbec nenačítá,
		 * takže bublina se o stylopis opřít nemůže.
		 */
		Object.assign(area.style, {
			position: 'fixed',
			top: '1rem',
			right: '1rem',
			zIndex: '2147483000',
			display: 'flex',
			flexDirection: 'column',
			gap: '0.5rem',
			maxWidth: 'min(24rem, calc(100vw - 2rem))',
		});
		document.body.appendChild(area);
	}

	const toast = document.createElement('div');

	toast.className = 'vv-toast';
	toast.setAttribute('role', 'alert');
	toast.textContent = message;
	Object.assign(toast.style, {
		padding: '0.7rem 0.9rem',
		font: '500 14px/1.4 system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
		color: '#fff',
		textAlign: 'left',
		background: '#b02a37',
		borderRadius: '0.45rem',
		boxShadow: '0 0.3rem 0.9rem rgba(24, 28, 40, 0.25)',
		transition: 'opacity 0.3s ease',
	});
	area.appendChild(toast);

	setTimeout(() =>
	{
		toast.style.opacity = '0';
		setTimeout(() => toast.remove(), 300);
	}, timeout);
}

/**
 * Neúspěšný AJAX se dřív nijak neprojevil – docházka prostě zůstala beze změny
 * a uživatel nevěděl proč. Nejčastější případ je zavřený termín u stránky,
 * která je otevřená delší dobu, proto ji po odmítnutí obnovíme.
 */
function initAjaxErrors()
{
	naja.addEventListener('error', event =>
	{
		const status = event.detail.response ? event.detail.response.status : 0;
		const url = String(event.detail.request ? event.detail.request.url : '');
		const isSave = url.includes('do=saveAttendance') || url.includes('do=saveReason');

		if(status === 0)
		{
			showToast('Nepodařilo se spojit se serverem. Zkontrolujte připojení.');

			return;
		}

		if(isSave && (status === 400 || status === 403))
		{
			showToast('Změnu už nejde uložit – termín je nejspíš uzavřený nebo zrušený. Obnovuji stránku…');
			setTimeout(() => location.reload(), 2500);

			return;
		}

		showToast(event.detail.payload?.errorMessage ?? 'Akce se nezdařila, zkuste to prosím znovu.');
	});
}

/**
 * Vysouvací menu na úzkém displeji. Bootstrap JS kvůli tomuhle jedinému
 * chování nenačítáme.
 */
function initMenuToggle()
{
	const toggle = document.querySelector('[data-menu-toggle]');
	const menu = document.querySelector('[data-menu]');

	if(!toggle || !menu)
	{
		return;
	}

	const setOpen = open =>
	{
		menu.classList.toggle('is-open', open);
		toggle.setAttribute('aria-expanded', String(open));
	};

	toggle.addEventListener('click', () => setOpen(!menu.classList.contains('is-open')));

	/* klik mimo menu a Esc ho zavřou */
	document.addEventListener('click', event =>
	{
		if(!menu.contains(event.target) && !toggle.contains(event.target))
		{
			setOpen(false);
		}
	});

	document.addEventListener('keydown', event =>
	{
		if(event.key === 'Escape')
		{
			setOpen(false);
		}
	});

	/* proklik v menu ho zavře, ať se po navigaci nepřekrývá obsah */
	menu.addEventListener('click', event =>
	{
		if(event.target.closest('a'))
		{
			setOpen(false);
		}
	});
}

/**
 * Zavírání flash hlášek – ručně křížkem, jinak samo po chvíli.
 */
function initFlashMessages()
{
	const container = document.querySelector('.flash-messages');

	if(!container)
	{
		return;
	}

	const dismiss = alert =>
	{
		alert.style.transition = 'opacity .3s ease';
		alert.style.opacity = '0';
		setTimeout(() => alert.remove(), 300);
	};

	const scheduleDismiss = () => container
		.querySelectorAll('.alert:not([data-dismiss-scheduled])')
		.forEach(alert =>
		{
			alert.dataset.dismissScheduled = '1';
			setTimeout(() => dismiss(alert), 6000);
		});

	container.addEventListener('click', event =>
	{
		const button = event.target.closest('[data-bs-dismiss="alert"]');

		if(button)
		{
			dismiss(button.closest('.alert'));
		}
	});

	/* hlášky chodí i ze snippetu po AJAXovém požadavku */
	naja.snippetHandler.addEventListener('afterUpdate', scheduleDismiss);

	scheduleDismiss();
}
