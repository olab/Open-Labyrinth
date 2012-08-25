var hideSkinEditor = true;
var editorBodyClick = true;
var color_blur = false;

var current_color_outside = null;
var current_image_outside = null;
var current_color_centre = null;
var current_image_centre = null;

var current_image_path_outside = null;
var current_image_path_centre = null;

var base_path = null;

jQuery(document).ready(function() {
    base_path = jQuery('#base_path').val();
    jQuery("#skinEditor").mouseenter(function() {
        jQuery(this).stop().animate({"height": "200px", "opacity": "1"});
        hideSkinEditor = true;
    }).mouseleave(function() {
        if (hideSkinEditor & editorBodyClick){
            jQuery(this).stop().animate({"height": "50px", "opacity": "0.3"});
        }
    });

    jQuery(".upload_input").click(function() {
        hideSkinEditor = false;
        jQuery(this).parents(".select_image").find(".upload_file").click();
    });

    jQuery(".color").click(function() {
        hideSkinEditor = false;
        editorBodyClick = false;
    });

    jQuery("#skinEditor").click(function() {
        if (color_blur) {
            editorBodyClick = true;
            color_blur = false;
        }
    });

    jQuery(".color").blur(function() {
        color_blur = true;
    });

    jQuery("#outside .upload_radio").click(function() {
        jQuery("#outside .editor_action").css('display', 'none');
        jQuery("#outside .upload_action").css('display', 'block');
        jQuery("body").css({"background-image": current_image_outside, "background-size": jQuery('#outside .change_size').val() + "%", "background-repeat": jQuery("#outside .change_repeat").val()});
    });

    jQuery("#centre .upload_radio").click(function() {
        jQuery("#centre .editor_action").css('display', 'none');
        jQuery("#centre .upload_action").css('display', 'block');
    });

    jQuery("#outside .pick_color_radio").click(function() {
        jQuery("#outside .editor_action").css('display', 'none');
        jQuery("#outside .color_action").css('display', 'block');
        jQuery("body").css("background-color", current_color_outside);
        jQuery("body").css("background-image", "");
    });

    jQuery("#centre .pick_color_radio").click(function() {
        jQuery("#centre .editor_action").css('display', 'none');
        jQuery("#centre .color_action").css('display', 'block');
        jQuery("#centre_table").css("background", "transparent");
        jQuery(".centre_td").css("background", "#FFFFFF");
    });

    jQuery(".transparent").click(function() {
        var parent = jQuery(this).parents(".control");
        parent.find(".editor_action").css('display', 'none');
    });

    jQuery("#outside .transparent").click(function() {
        jQuery("body").css("background", "transparent");
    });

    jQuery("#centre .transparent").click(function() {
        jQuery(".centre_td").css("background", "transparent");
    });

    jQuery("#outside .color").change(function() {
        current_color_outside = "#"+jQuery(this).val();
        jQuery("body").css("background-color", "#"+jQuery(this).val());
    });

    jQuery("#centre .color").change(function() {
        current_color_centre = "#"+jQuery(this).val();
        jQuery(".centre_td").css("background-color", "#"+jQuery(this).val());
    });

    jQuery("#outside .change_size").keyup(function() {
        jQuery("body").css("background-size", jQuery(this).val() + "%");
    });

    jQuery("#outside .change_repeat").change(function() {
        jQuery("body").css("background-repeat", jQuery(this).val());
    });

    jQuery("#centre .change_size").keyup(function() {
        jQuery("#centre_table").css("background-size", jQuery(this).val() + "%");
    });

    jQuery("#centre .change_repeat").change(function() {
        jQuery("#centre_table").css("background-repeat", jQuery(this).val());
    });

    jQuery("#outside .position").click(function() {
        var status = jQuery(this).val();
        if (status == 'on'){
            jQuery("body").css("cursor", "move");
            var mousedown = false;
            var startX = null;
            var startY = null;
            var bPos = jQuery("body").css("background-position");
            bPos = bPos.split(' ');
            var top = parseInt(bPos[0]);
            var left = parseInt(bPos[1]);
            var newTop = null;
            var newLeft = null;
            jQuery('body').bind("mousedown", (function(event) {
                mousedown = true;
                startX = event.pageX;
                startY = event.pageY;
                bPos = jQuery("body").css("background-position");
                bPos = bPos.split(' ');
                top = parseInt(bPos[0]);
                left = parseInt(bPos[1]);
            }));

            jQuery('body').bind("mouseup", (function() {
                mousedown = false;
            }));

            jQuery('body').bind("mousemove", (function(event) {
                if (mousedown){
                    newTop = top + event.pageX - startX;
                    newLeft = left + event.pageY - startY;
                    jQuery("body").css("background-position", newTop + "px " + newLeft + "px");
                    event.preventDefault();
                }
            }));
        }else{
            jQuery('body').unbind('mousedown');
            jQuery('body').unbind('mouseup');
            jQuery('body').unbind('mousemove');
            jQuery("body").css("cursor", "auto");
        }
    });

    jQuery("#centre .position").click(function() {
        var status = jQuery(this).val();
        if (status == 'on'){
            jQuery("#centre_table").css("cursor", "move");
            var mousedown = false;
            var startX = null;
            var startY = null;
            var bPos = jQuery("#centre_table").css("background-position");
            bPos = bPos.split(' ');
            var top = parseInt(bPos[0]);
            var left = parseInt(bPos[1]);
            var newTop = null;
            var newLeft = null;
            jQuery('#centre_table').bind("mousedown", (function(event) {
                mousedown = true;
                startX = event.pageX;
                startY = event.pageY;
                bPos = jQuery("#centre_table").css("background-position");
                bPos = bPos.split(' ');
                top = parseInt(bPos[0]);
                left = parseInt(bPos[1]);
            }));

            jQuery('#centre_table').bind("mouseup", (function() {
                mousedown = false;
            }));

            jQuery('#centre_table').bind("mousemove", (function(event) {
                if (mousedown){
                    newTop = top + event.pageX - startX;
                    newLeft = left + event.pageY - startY;
                    jQuery("#centre_table").css("background-position", newTop + "px " + newLeft + "px");
                    event.preventDefault();
                }
            }));
        }else{
            jQuery('#centre_table').unbind('mousedown');
            jQuery('#centre_table').unbind('mouseup');
            jQuery('#centre_table').unbind('mousemove');
            jQuery("#centre_table").css("cursor", "auto");
        }
    });

    jQuery('#outside .position_reset').click(function(){
        jQuery("body").css("background-position", "0px 0px");
    });

    jQuery('#outside .reset').click(function() {
        jQuery("#outside .select_image").css("display", "block");
        jQuery("#outside .progress_display").css("display", "none");
        jQuery("#outside .progress_display .progress").css("display", "block");
        jQuery("#outside .progress_display .progress .bar").css("width", "0");
        jQuery("#outside .progress_display .status").css("display", "none");
        jQuery("body").css({'background-color': '#EEEEEE', 'background-image': '', 'background-size': '100', 'background-repeat': 'no-repeat', 'cursor': 'auto'});
        jQuery("#outside .upload_input").val('');
        jQuery("#outside .change_size").val('100');
        jQuery("#outside .change_repeat").val('no-repeat');
        jQuery("#outside .position").removeAttr('checked');
        jQuery("#outside .position.off").attr('checked', 'checked');
        current_image_path_outside = null;
    });

    jQuery('#centre .position_reset').click(function(){
        jQuery("#centre_table").css("background-position", "0px 0px");
    });

    jQuery('#centre .reset').click(function() {
        jQuery("#centre .select_image").css("display", "block");
        jQuery("#centre .progress_display").css("display", "none");
        jQuery("#centre .progress_display .progress").css("display", "block");
        jQuery("#centre .progress_display .progress .bar").css("width", "0");
        jQuery("#centre .progress_display .status").css("display", "none");
        jQuery("#centre_table").css({'background-color': '', 'background-image': '', 'background-size': '100', 'background-repeat': 'no-repeat', 'cursor': 'auto'});
        jQuery(".centre_td").css("background", "#FFFFFF");
        jQuery("#centre .upload_input").val('');
        jQuery("#centre .change_size").val('100');
        jQuery("#centre .change_repeat").val('no-repeat');
        jQuery("#centre .position").removeAttr('checked');
        jQuery("#centre .position.off").attr('checked', 'checked');
        current_image_path_centre = null;
    });

    jQuery('#outside_upload').fileupload({
        dataType: 'json',
        autoUpload: false,

        add: function (e, data) {
            data.context = jQuery('#outside .upload_input').val(data.files[0].name);
            jQuery("#outside .upload_button").click(function() {
                jQuery("#outside .select_image").css("display", "none");
                jQuery("#outside .progress_display").css("display", "block");
                data.submit();
            });
        },
        done: function (e, data) {
            jQuery.each(data.result, function (index, file) {
                jQuery("#outside .progress_display .progress").css("display", "none");
                jQuery("#outside .progress_display .status").css("display", "block");
                current_image_path_outside = file.name;
                current_image_outside = 'url("' + base_path + 'fileupload/php/files/' + file.name + '")';
                jQuery("body").css("background", 'url("' + base_path + 'fileupload/php/files/' + file.name + '")');
                jQuery("body").css("background-repeat", "no-repeat");
                jQuery("body").css("background-size", "100%");
            });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#outside .progress_display .progress .bar').css(
                'width',
                progress + '%'
            );
        }
    });

    jQuery('#centre_upload').fileupload({
        dataType: 'json',
        autoUpload: false,

        add: function (e, data) {
            data.context = jQuery('#centre .upload_input').val(data.files[0].name);
            jQuery("#centre .upload_button").click(function() {
                jQuery("#centre .select_image").css("display", "none");
                jQuery("#centre .progress_display").css("display", "block");
                data.submit();
            });
        },
        done: function (e, data) {
            jQuery.each(data.result, function (index, file) {
                jQuery("#centre .progress_display .progress").css("display", "none");
                jQuery("#centre .progress_display .status").css("display", "block");
                current_image_path_centre = file.name;
                current_image_centre = 'url("' + base_path + 'fileupload/php/files/' + file.name + '")';
                jQuery(".centre_td").css("background-color", "transparent");
                jQuery("#centre_table").css("background", 'url("' + base_path + 'fileupload/php/files/' + file.name + '")');
                jQuery("#centre_table").css("background-repeat", "no-repeat");
                jQuery("#centre_table").css("background-size", "100%");
            });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#centre .progress_display .progress .bar').css(
                'width',
                progress + '%'
            );
        }
    });


    jQuery(".save_submit").click(function() {
        jQuery('input[name="outside[b-size]"]').val(jQuery('body').css('background-size'));
        jQuery('input[name="outside[b-repeat]"]').val(jQuery('body').css('background-repeat'));
        jQuery('input[name="outside[b-position]"]').val(jQuery('body').css('background-position'));
        jQuery('input[name="outside[b-color]"]').val(jQuery('body').css('background-color'));
        jQuery('input[name="outside_image"]').val(current_image_path_outside);

        jQuery('input[name="centre[b-size]"]').val(jQuery('#centre_table').css('background-size'));
        jQuery('input[name="centre[b-repeat]"]').val(jQuery('#centre_table').css('background-repeat'));
        jQuery('input[name="centre[b-position]"]').val(jQuery('#centre_table').css('background-position'));
        jQuery('input[name="centre[b-color]"]').val(jQuery('.centre_td').css('background-color'));
        jQuery('input[name="centre_image"]').val(current_image_path_centre);

        jQuery("#submit_form").submit();
    });
});