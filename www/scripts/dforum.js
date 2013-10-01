$(function() {
    $('.add-quote-btn').click(function() {
        var template  = '<blockquote>' +
                            $('#message-text-' + $(this).attr('msgId')).html() +
                            '<small>posted by <b>' + $(this).attr('msgAuthor') + '</b>, ' + $(this).attr('msgDate') + '</small>'+
                        '</blockquote>';

        tinyMCE.activeEditor.selection.setContent(template);
        tinyMCE.activeEditor.focus();
    });

    // TODO THIS
    $('#mainForum').find('.showMoreTopics').click(function(){
        var id = $(this).attr('attr');
        var icon = $('#icon-'+id);

        $('.showTopic-id-' + id).slideToggle('slow', function() {
        });

        if (icon.hasClass('icon-chevron-right') ) {
            icon.removeClass('icon-chevron-right');
            icon.addClass('icon-chevron-down');
        }
        else {
            icon.removeClass('icon-chevron-down');
            icon.addClass('icon-chevron-right');
        }
    });

    $('.sent-notification-forum-save-btn').click(function() {
        var forumId       = $(this).attr('forumId'),
            checkboxValue = $('#forum-notification-checkbox-' + forumId).is(':checked') ? 1 : 0,
            $this         = $(this),
            $loader       = $('#sent-forum-notification-loader-' + forumId);

        $this.hide();
        $loader.removeClass('hide');
        $.post(updateNotificationURL, {
            forumId: forumId,
            notification: checkboxValue
        }, function(data) {
            $this.parent().parent().modal('hide');
            $this.show();
            $loader.addClass('hide');
        });
    });

    $('.sent-notification-forum-topic-save-btn').click(function() {
        var topicId       = $(this).attr('forumTopicId'),
            checkboxValue = $('#forum-topic-notification-checkbox-' + topicId).is(':checked') ? 1 : 0,
            $this         = $(this),
            $loader       = $('#sent-forum-topic-notification-loader-' + topicId);

        $this.hide();
        $loader.removeClass('hide');
        $.post(updateTopicNotificationURL, {
            topicId: topicId,
            notification: checkboxValue
        }, function(data) {
            $this.parent().parent().modal('hide');
            $this.show();
            $loader.addClass('hide');
        });
    });

});