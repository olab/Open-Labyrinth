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

});
