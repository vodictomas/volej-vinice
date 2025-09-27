Nette.initOnLoad();

function flashFadeOut()
{
    $('#flashMessagesBox').children('.alert').delay(7000).fadeOut(3000);
}

$(function()
{
    flashFadeOut();

    $.nette.init();
});