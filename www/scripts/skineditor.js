var isPickerOutside = false,
    isPickerCentre = false;

var current_color_outside = "#EEEEEE",
    current_image_outside = null,
    current_image_outside_position = null,
    current_color_centre = "#FFFFFF",
    current_image_centre = null,
    current_image_centre_position = null,
    color_opacity_centre = 1,
    color_opacity_outside = 1;

var current_image_path_outside = null,
    current_image_path_centre = null;

var base_path = null,
    colorPickerOutside,
    colorPickerCentre;

$(document).ready(function() {
    var colorPicker = new ColorPicker();
	colorPickerOutside = colorPicker.createPicker('colorPickerOutside');
    var colorPicker2 = new ColorPicker();
	colorPickerCentre = colorPicker2.createPicker('colorPickerCentre');
    base_path = jQuery('#base_path').val();

    jQuery("#skinEditor .show").click(function() {
        jQuery("#skinEditor").stop().animate({"height": "300px"}, function() {
            if (isPickerOutside){
                colorPickerOutside.showPicker();
            }
            if (isPickerCentre){
                colorPickerCentre.showPicker();
            }
        });
        $(this).css('display', 'none');
        jQuery("#skinEditor .hide").css('display', 'block');
    });

    jQuery("#skinEditor .hide").click(function() {
        colorPickerCentre.hidePicker();
        colorPickerOutside.hidePicker();
        jQuery("#skinEditor").stop().animate({"height": "50px"});
        $(this).css('display', 'none');
        jQuery("#skinEditor .show").css('display', 'block');
    });

    jQuery("#outside .upload_radio").click(function() {
        colorPickerOutside.hidePicker();
        isPickerOutside = false;
        jQuery("#outside .editor_action").css('display', 'none');
        jQuery("#outside .upload_action").css('display', 'block');
        jQuery("body").css("background", "");
        jQuery("body").css({"background-image": current_image_outside, "background-size": jQuery('#outside .change_size').val() + "%", "background-repeat": jQuery("#outside .change_repeat").val(), "background-position": current_image_outside_position});
    });

    jQuery("#centre .upload_radio").click(function() {
        colorPickerCentre.hidePicker();
        isPickerCentre = false;
        jQuery("#centre .editor_action").css('display', 'none');
        jQuery("#centre .upload_action").css('display', 'block');
        jQuery(".centre_td").css("background-color", "transparent");
        jQuery("#centre_table").css({"background-image": current_image_centre, "background-size": jQuery('#centre .change_size').val() + "%", "background-repeat": jQuery("#centre .change_repeat").val(), "background-position": current_image_centre_position});
    });

    jQuery("#outside .pick_color_radio").click(function() {
        jQuery("#outside .editor_action").css('display', 'none');
        jQuery("#outside .color_action").css('display', 'block');
        var rgb = hexToRgb(current_color_outside);
        jQuery("body").css("background", "rgba("+rgb.r+","+rgb.g+","+rgb.b+","+color_opacity_outside+")");
        colorPickerOutside.showPicker();
        isPickerOutside = true;
    });

    jQuery("#centre .pick_color_radio").click(function() {
        jQuery("#centre .editor_action").css('display', 'none');
        jQuery("#centre .color_action").css('display', 'block');
        jQuery("#centre_table").css("background", "transparent");
        var rgb = hexToRgb(current_color_centre);
        jQuery(".centre_td").css("background", "rgba("+rgb.r+","+rgb.g+","+rgb.b+","+color_opacity_centre+")");
        colorPickerCentre.showPicker();
        isPickerCentre = true;
    });

    jQuery("#centre .set_opacity").click(function() {
        colorPickerCentre.hidePicker();
        isPickerCentre = false;
        jQuery("#centre .editor_action").css('display', 'none');
        jQuery("#centre .opacity_action").css('display', 'block');
        jQuery("#centre_table").css("background", "transparent");
        var rgb = hexToRgb(current_color_centre);
        jQuery(".centre_td").css("background", "rgba("+rgb.r+","+rgb.g+","+rgb.b+","+color_opacity_centre+")");
    });

    jQuery("#outside .set_opacity").click(function() {
        colorPickerOutside.hidePicker();
        isPickerOutside = false;
        jQuery("#outside .editor_action").css('display', 'none');
        jQuery("#outside .opacity_action").css('display', 'block');
        var rgb = hexToRgb(current_color_outside);
        jQuery("body").css("background", "rgba("+rgb.r+","+rgb.g+","+rgb.b+","+color_opacity_outside+")");
    });

    jQuery(".transparent").click(function() {
        var parent = jQuery(this).parents(".control");
        parent.find(".editor_action").css('display', 'none');
    });

    jQuery("#outside .transparent").click(function() {
        colorPickerOutside.hidePicker();
        isPickerOutside = false;
        jQuery("body").css("background", "transparent");
    });

    jQuery("#centre .transparent").click(function() {
        colorPickerCentre.hidePicker();
        isPickerOutside = false;
        jQuery(".centre_td").css("background", "transparent");
    });

    jQuery("#colorPickerOutside").change(function() {
        var rgb = hexToRgb("#"+jQuery(this).val());
        current_color_outside = "#"+jQuery(this).val();
        jQuery("body").css("background-color", "rgba("+rgb.r+","+rgb.g+","+rgb.b+","+color_opacity_outside+")");
    });

    jQuery("#colorPickerCentre").change(function() {
        var rgb = hexToRgb("#"+jQuery(this).val());
        current_color_centre = "#"+jQuery(this).val();
        jQuery(".centre_td").css("background-color", "rgba("+rgb.r+","+rgb.g+","+rgb.b+","+color_opacity_centre+")");
    });

    jQuery("#outside .opacity_value").change(function(){
        sliderOutside.slider( "value", this.value );
        changeOpacityOutside(this.value);
    });

    jQuery("#centre .opacity_value").change(function(){
        sliderCentre.slider( "value", this.value );
        changeOpacityCentre(this.value);
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
            var position = null;
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
                    position = newTop + "px " + newLeft + "px";
                    current_image_outside_position = position;
                    jQuery("body").css("background-position", position);
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
            var position = null;
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
                    position = newTop + "px " + newLeft + "px";
                    current_image_centre_position = position;
                    jQuery("#centre_table").css("background-position", position);
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
        current_image_outside = null;
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
        current_image_centre = null;
    });

    jQuery('#outside_upload').fileupload({
        dataType: 'json',
        autoUpload: false,

        add: function (e, data) {
            data.context = jQuery('#outside .upload_input').val(data.files[0].name);
            jQuery("#outside .upload_button").click(function() {
                jQuery("#outside .select_image").css("display", "none");
                jQuery("#outside .progress_display").css("display", "block");
                jQuery('#outside .upload_input').val("");
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
                jQuery('#centre .upload_input').val("");
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

    jQuery('.save_submit').click(function() {
        var type = jQuery(this).attr("name");
        var skinName = jQuery('#skinName').val();
        if (skinName == ''){
            alert('Please enter skin name');
            jQuery('#skinName').focus();
        }else{
            jQuery('.saving_text').css('display', 'inline');
            jQuery('.save_submit').css('display', 'none');
            var outside = {}
            outside['b-size'] = jQuery('body').css('background-size');
            outside['b-repeat'] = jQuery('body').css('background-repeat');
            outside['b-position'] = jQuery('body').css('background-position');
            outside['b-color'] = jQuery('body').css('background-color')
            var outside_image = current_image_path_outside;

            var centre = {}
            centre['b-size'] = jQuery('#centre_table').css('background-size');
            centre['b-repeat'] = jQuery('#centre_table').css('background-repeat');
            centre['b-position'] = jQuery('#centre_table').css('background-position');
            centre['b-color'] = jQuery('.centre_td').css('background-color');
            var centre_image = current_image_path_centre;
            var save = jQuery(this).attr('name');
            var skinId = jQuery('#skinId').val();
            var action = jQuery('#submit_form').attr('action');
            $.post(action, {outside: outside, centre: centre, outside_image: outside_image, centre_image: centre_image, save: save, skinId: skinId}, function(){
                if (type == 'save_changes'){
                    alert('Skin successfully saved.');
                    jQuery('.saving_text').css('display', 'none');
                    jQuery('.save_submit').css('display', 'inline');
                }else{
                    window.location.href = jQuery('#redirect_url').val();
                }
            });
        }

    });

    var sliderCentre = $("#slider-range-centre").slider({
        range: "min",
        value: color_opacity_centre,
        min: 0,
        max: 1,
        step: 0.1,
        slide: function( event, ui ) {
            $( "#centre .opacity_value" ).val(ui.value);
            changeOpacityCentre(ui.value);
        }
    });

    var sliderOutside = $("#slider-range-outside").slider({
        range: "min",
        value: color_opacity_outside,
        min: 0,
        max: 1,
        step: 0.1,
        slide: function( event, ui ) {
            $( "#outside .opacity_value" ).val(ui.value);
            changeOpacityOutside(ui.value);
        }
    });

    function changeOpacityCentre(value){
        color_opacity_centre = value;
        var rgb = hexToRgb(current_color_centre);
        jQuery(".centre_td").css("background", "rgba("+rgb.r+","+rgb.g+","+rgb.b+","+color_opacity_centre+")");
    }

    function changeOpacityOutside(value){
        color_opacity_outside = value;
        var rgb = hexToRgb(current_color_outside);
        jQuery("body").css("background", "rgba("+rgb.r+","+rgb.g+","+rgb.b+","+color_opacity_outside+")");
    }
});

function hexToRgb(hex) {
    // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
    var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
    hex = hex.replace(shorthandRegex, function(m, r, g, b) {
        return r + r + g + g + b + b;
    });

    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

function rgbToHex(c) {
    if (c == 'transparent'){
        return 'FFFFFF';
    }else{
        var m = /rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/.exec(c);
        return m ? (1 << 24 | m[1] << 16 | m[2] << 8 | m[3]).toString(16).substr(1) : c;
    }
}

function getOpacity(rgba){
    var alpha = rgba.replace(/^.*,(.+)\)/,'$1')+'';
    if (parseFloat(alpha)){
        return parseFloat(alpha);
    }else{
        return 1;
    }
}