$(function() {
var
    $redirectContainer = $('.submitSettingsContainer');
var
    $colorDefault = $('.submitSettingsContainerColorDefault');
var
    $colorCustom = $('.submitSettingsContainerColorCustom');

$('#node').click(function() {
    if($redirectContainer != null)
        $redirectContainer.removeClass('hide');
});

$('#labyrinth').click(function() {
    if($redirectContainer != null)
        $redirectContainer.addClass('hide');
});

$('#color_code').click(function() {
    $('#font_color_cntr').show();
    $('#font_color_cntr').farbtastic('#color_code');
});

$('#color_code').blur(function() {
    $('#font_color_cntr').hide();
});

$('#color_default').click(function() {
    if($colorDefault != null) {
        $colorDefault.removeClass('hide');
        $colorCustom.addClass('hide');
    }
});

$('#color_custom').click(function() {
    if($colorCustom != null) {
        $colorCustom.removeClass('hide');
        $colorDefault.addClass('hide');
    }
 });

$('input:radio#timing-on').change(function(){
    $('#time_before').prop('disabled', false);
    $('#time_length').prop('disabled', false);
});

$('input:radio#timing-off').change(function(){
    $('#time_before').prop('disabled', true);
    $('#time_length').prop('disabled', true);
});

});

function CheckForm() {
    if(document.getElementById('title').value == '') {
        alert('Please enter you message title');
        return false;
    }

    if(tinyMCE.get("text").getContent() =='') {
        alert('Please enter you message text');
        return false;
    }

    if( (document.getElementById('time_before').value == '' || document.getElementById('time_before').value == 0)
        && document.getElementById('timing-on').checked ) {
        alert('Please enter you Time before appearance interval!');
        return false;
    }

    if(( document.getElementById('time_length').value == '' || document.getElementById('time_length').value == 0)
        && document.getElementById('timing-on').checked ) {
        alert('Please enter you Time length appearance interval!');
        return false;
    }

}


