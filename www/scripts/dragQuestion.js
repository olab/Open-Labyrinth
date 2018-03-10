$(function() {
    var $responsesContainer = $('.question-response-draggable-panel-group'),
        parent,
        id,
        button,
        scoreOptions = '';

    $responsesContainer.sortable({
        axis: "y",
        cursor: "move"
    });

    $('#addResponse').click(function() {
        var html  = "<div class=\"response-panel sortable\">"+
                        "<input type=\"hidden\" name=\"responses[]\" value=\"\"/>"+
                        "<label for=\"response\">Response</label>"+
                        "<input type=\"text\" class=\"response-input\" value=\"\"/> "+
                        "<button type=\"button\" class=\"btn-remove-response btn btn-danger btn-small\"><i class=\"icon-trash\"></i></button>"+
                    "</div>";

        $responsesContainer.append(html);
    });

    $('body').on('click', '.question-response-draggable-panel-group .btn-remove-response', function() {
        $(this).parent().remove();

        return false;
    });

    $('.question-save-btn').click(function() {
        $('.question-response-draggable-panel-group .response-panel').each(function(index, value) {
            var $hidden    = $(value).find('input[type="hidden"]'),
                jsonStr    = $hidden.val(),
                jsonObject = (jsonStr.length > 0) ? JSON.parse(jsonStr) : {},
                response   = $(value).find('.response-input').val();

            jsonObject.response = B64.encode($.trim(response.replace(/\0/g,"")));
            jsonObject.order    = index + 1;


            $hidden.val(JSON.stringify(jsonObject));
        });

        $('form').submit();
    });
});