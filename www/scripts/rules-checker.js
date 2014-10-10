$(document).ready(function() {
    $('#rule_submit_check').click(function() {
        $(this).button('loading');
        checkRule(1);
    });
    $('#check_button').click(function() {
        checkRule(1);
    });
    $('#check_rule_button').click(function() {
        checkRule(0);
    });
});

function checkRule(submit){
    if (submit != 1) {
        $('#check_rule_button').button('loading');
    }

    var ruleText = $('#code').val(),
        mapId = $('#mapId').val(),
        URL = $('#url').val();

    $.post(
        URL,
        { mapId: mapId, ruleText: ruleText },
        function(data){
            (data == 1) ? checkSuccess() : checkFailed();
            $('#check_rule_button').button('reset');
            if (submit == 1) {
                $('#submit_button').click();
            }
        }
    ).fail(
        function(){
            checkFailed();
            if (submit == 1) {
                $('#submit_button').click();
            }
        }
    );
}

function checkFailed(){
    $('#check_rule_button').button('reset');
    $('#error-alert').removeClass('hide');
    $('.status-label .label').addClass('hide');
    $('.status-label .label-important').removeClass('hide');
    $('#isCorrect').val('0');
}

function checkSuccess(){
    $('.status-label .label').addClass('hide');
    $('.status-label .label-success').removeClass('hide');
    $('#isCorrect').val('1');
}

function resetCheck(){
    $('.status-label .label').addClass('hide');
    $('.status-label .label-warning').removeClass('hide');
}