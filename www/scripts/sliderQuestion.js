$(function() {
    var $container,
        $redirectContainer,
        currentCount,
        currentIndex;

    $container = $('#responsesContainer');
    $redirectContainer = $('.submitSettingsContainer');
    currentCount = $container.children('fieldset').length;
    currentIndex = 0;

    $('#showSubmit').click(function() {
        if($redirectContainer != null)
            $redirectContainer.removeClass('hide');
    });

    $('#hideSubmit').click(function() {
        if($redirectContainer != null)
            $redirectContainer.addClass('hide');
    });

    $('#addResponse').click(function() {
        currentCount += 1;
        currentIndex += 1;
        var html = '<fieldset class="fieldset" id="fieldset_' + currentIndex + '_n">'+
                        '<legend class="legend-title">Response #' + currentCount + '</legend>'+
                        '<div class="control-group">'+
                            '<label for="response_' + currentIndex + '" class="control-label">Interval</label>'+
                            '<div class="controls">'+
                                'From: <input autocomplete="off" type="text" id="interval_from_' + currentIndex + '_n" name="interval_from_' + currentIndex + '_n" value=""/>'+
                                'To: <input autocomplete="off" type="text" id="interval_to_' + currentIndex + '_n" name="interval_to_' + currentIndex + '_n" value=""/>'+
                            '</div>'+
                        '</div>'+

                        '<div class="control-group">'+
                            '<label for="score_' + currentIndex + '" class="control-label">Score</label>'+
                            '<div class="controls">'+
                                '<input type="text" name="score_' + currentIndex + '_n" id="score_' + currentIndex + '" value="0" selectBoxOptions="-10;-9;-8;-7;-6;-5;-4;-3;-2;-1;0;1;2;3;4;5;6;7;8;9;10">'+
                            '</div>'+
                        '</div>'+

                        '<div class="form-actions">'+
                            '<a class="btn btn-danger removeBtn" removeid="fieldset_' + currentIndex + '_n" href="#"><i class="icon-minus-sign"></i>Remove</a>'+
                        '</div>'+

                        '<script>'+
                            'createEditableSelect(document.getElementById(\'score_' + currentIndex + '\'));'+
                        '</script>'+
                    '</fieldset>';

        $container.append(html);

        return false;
    });

    $('.removeBtn').live('click', function() {
        var id = $(this).attr('removeid');
        if(id != null && id.length > 0) {
            currentCount -= 1;
            $('#' + id).remove();
            RecalculateLegends($container);
        }

        return false;
    });

    function RecalculateLegends($responsesContinaer) {
        var index = 1;
        $responsesContinaer.children('fieldset').children('legend.legend-title').each(function(i) {
            $(this).text('Response #' + index);
            index += 1;
        });
    }

    var parent;
    var id;
    var button;
    $(".radio_extended input[type=radio]").each(function(){
        if ($(this).is(':checked')){
            parent = $(this).parent('.radio_extended');
            id = $(this).attr('id');
            button = $(parent).find('label[for=' + id + ']');
            changeRadioBootstrap(button);
        }
    });

    $(".radio_extended .btn").live("click", function() {
        changeRadioBootstrap(this);
    });

    function changeRadioBootstrap(obj){
        $(obj).parent(".radio_extended").find(".btn").removeAttr('class').addClass('btn');
        $(obj).addClass('active');
        var additionClass = $(obj).attr('data-class');
        if (additionClass !== null){
            $(obj).addClass(additionClass);
        }
    }
});