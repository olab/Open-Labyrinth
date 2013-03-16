jQuery(document).ready(function(){
    //------------------Case Wizard--------------------//
    var wizard_button = jQuery('.wizard_body .wizard_button');
    if (wizard_button.length){
        wizard_button.click(function() {
            wizard_button.removeClass('selected');
            jQuery(this).addClass('selected');
        });
    }

    jQuery("#step1_w_button").click(function() {
        var id = jQuery(".wizard_button.selected").attr("id");
        jQuery("#labyrinthType").val(id);
        jQuery("#step1_form").submit();
    });

    jQuery("#step2_w_button").click(function() {
        jQuery("#step2_form").submit();
    });

    jQuery("#dialog-confirm").dialog({
        autoOpen: false,
        draggable: false,
        resizable: false,
        modal: true,
        buttons: {
            "I agree": function() {
                jQuery(this).dialog( "close" );
                jQuery('#upload-form').submit();
            },
            Cancel: function() {
                jQuery(this).dialog( "close" );
            }
        }
    });

    jQuery("#opener").click(function() {
        $("#dialog-confirm").dialog( "open" );
        return false;
    });

    jQuery("#tabs").tabs();

	// Tooltip
	jQuery('a[rel=tooltip]').tooltip();

	// Popovers
	jQuery('[rel=popover]').popover();

	// Datepicker
	jQuery(".datepicker").datepicker();



    jQuery('input:radio#timing-on').change(function(){
        $('#delta_time').prop('disabled', false);
    });
    jQuery('input:radio#timing-off').change(function(){
        $('#delta_time').prop('disabled', true);
    });

    jQuery('input:radio[name=security]').change(function(){
        if(this.value==4)$("#edit_keys").show();
        else $("#edit_keys").hide();
    });

    jQuery('.toggle-all-on').click(function() {
        var id = jQuery(this).attr("id");
        jQuery(".chk_"+id).attr('checked', 'checked');
    });

    jQuery('.toggle-all-off').click(function() {
        var id = jQuery(this).attr("id");
        jQuery(".chk_"+id).removeAttr('checked');
    });

    jQuery('.toggle-reverse').click(function() {
        var id = jQuery(this).attr("id");
        var chk = jQuery(".chk_"+id);
        chk.each(function() {
            var check_value = jQuery(this).attr("checked");
            if (check_value){
                jQuery(this).removeAttr("checked");
            } else {
                jQuery(this).attr("checked", "checked");
            }
        });

    });
    
    function changeClothColor(changedColor) {
        var color = 'FFFFFF';
        var val = changedColor;
        if(val.length > 2) {
            color = val.substr(1, val.length - 1);
        }

        $('#clothcolor').val(color);
        $('#clothcolor').change();
    }
    
    function changeBgColor(changedColor) {
        var color = 'FFFFFF';
        var val = changedColor;
        if(val.length > 2) {
            color = val.substr(1, val.length - 1);
        }
        
        $('#bgcolor').val(color);
        $('#bgcolor').change();
    }

    jQuery('#clothcolor').click(function() {
        $('#clothColorContainer').show();
        $('#clothColorContainer').farbtastic(changeClothColor);
        var val = $(this).val();
        if(val.length > 0) {
            var picker = $.farbtastic('#clothColorContainer');
            picker.setColor('#' + val);
        }
    });

    jQuery('#clothcolor').blur(function() {
        $('#clothColorContainer').hide();
    });

    jQuery('#bgcolor').click(function() {
        $('#avBgPickerContainer').show();
        $('#avBgPickerContainer').farbtastic(changeBgColor);
        var val = $(this).val();
        if(val.length > 0) {
            var picker = $.farbtastic('#avBgPickerContainer');
            picker.setColor('#' + val);
        }
    });

    jQuery('#bgcolor').blur(function() {
        $('#avBgPickerContainer').hide();
    });
    
    var $chatFrom = $('#chatForm');
    var $chatQCont = $('#questionContainer');
    var gQuestionCounter = (typeof questionCount != 'undefined') ? questionCount : 0;
    jQuery('#addNewQuestion').click(function() {
        if (typeof questionCount == 'undefined') return false;
        
        ++questionCount;
        ++gQuestionCounter;
        
        var qHtml = '<fieldset class="fieldset" id="qDiv' + gQuestionCounter + '"><input type="hidden" name="questionIndex' + gQuestionCounter + '" value="' + gQuestionCounter + '"/><legend>Question #' + questionCount + '</legend><div class="control-group"><label for="question' + gQuestionCounter + '" class="control-label">Question</label><div class="controls"><input id="question' + gQuestionCounter + '" type="text" name="question' + gQuestionCounter + '" value=""/></div></div><div class="control-group"><label for="response' + gQuestionCounter + '" class="control-label">Response</label><div class="controls"><input type="text" name="response' + gQuestionCounter + '" id="response' + gQuestionCounter + '" value=""/></div></div><div class="control-group"><label for="counter' + gQuestionCounter + '" class="control-label">Counter</label><div class="controls"><input type="text" name="counter' + gQuestionCounter + '" id="counter' + questionCount + '" value=""/>&nbsp;type +, - or = an integer - e.g. \'+1\' or \'=32\'</div></div><a class="btn btn-primary removeQuestionBtn" removeId="' + gQuestionCounter + '" href="#">Remove</a></fieldset>';
        if($chatFrom != null)
            $chatFrom.attr('action', formAction + questionCount);
        
        if($chatQCont != null)
            $chatQCont.append(qHtml);
        
        return false;
    });
    
    jQuery('.removeQuestionBtn').live('click', function() {
        var id = $(this).attr('removeId');
        if($chatQCont != null && id > 0) 
            $('#qDiv' + id).remove();
        
        --questionCount;
        
        if($chatQCont != null) {
            var i = 1;
            $.each($chatQCont.children('fieldset'), function(index, obj) {
                $(obj).children('legend').text('Question #' + i);
                
                i++;
            });
        }
        
        if($chatFrom != null)
            $chatFrom.attr('action', formAction + questionCount);
        
        return false;
    });
    
    $('#forgot-password-submit').click(function() {
        $('#forgot-password-form').submit();
    });


    $('a.toggles').click(function() {
        $('a.toggles i').toggleClass('icon-chevron-left icon-chevron-right');

        $('#sidebar').animate({
            width: 'toggle'
        }, 0);
        $('#content').toggleClass('span12 span10');
        $('.to-hide').toggleClass('hide');
    });

 /*
    $('#content').toggle(

        function() {

        }, function() {

        });
*/

});
