Nette.initOnLoad();

function flashFadeOut()
{
    $('#flashMessagesBox').children('.alert').delay(7000).fadeOut(3000);
}

$(function()
{
    flashFadeOut();

    $.nette.init();
	$(".datepicker,input[data-provide='datepicker']").datepicker({format: "d.m.yyyy",language: "cs"});
});

$.nette.ext({
	load: function()
	{
		$(".datepicker,input[data-provide='datepicker']").datepicker({format: "d.m.yyyy",language: "cs"});
	}
});

