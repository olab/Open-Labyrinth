$(function() {
    $('.add-quote-btn').click(function() {
        var template  = '<blockquote>' +
                            $('#message-text-' + $(this).attr('msgId')).html() +
                            '<small>posted by <b>' + $(this).attr('msgAuthor') + '</b>, ' + $(this).attr('msgDate') + '</small>'+
                        '</blockquote>';

        tinyMCE.activeEditor.selection.setContent(template);
        tinyMCE.activeEditor.focus();
    });
});