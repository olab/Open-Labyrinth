jQuery(document).ready(function() {
    jQuery('#check_rule_button').click(function() {
        checkRule();
    });
});

function checkRule(submit){
    if (submit != 1) {
        jQuery('#check_rule_button').button('loading');
    }
    var ruleText = jQuery('#code').val(),
        mapId = jQuery('#mapId').val(),
        URL = jQuery('#url').val();
    jQuery.post(URL, { mapId: mapId, ruleText: ruleText }, function(data) {
        if(data != '') {
            if (data == 1){
                checkSuccess();
            } else {
                checkFailed();
            }
        } else {
            checkFailed();
        }
        jQuery('#check_rule_button').button('reset');
        if (submit == 1) {
            $('#submit_button').click();
        }
    }).fail(function() {
            checkFailed();
            if (submit == 1) {
                $('#submit_button').click();
            }
        });

    return false;
}

function checkFailed(){
    jQuery('#check_rule_button').button('reset');
    jQuery('#error-alert').removeClass('hide');
    jQuery('.status-label .label').addClass('hide');
    jQuery('.status-label .label-important').removeClass('hide');
    jQuery('#isCorrect').val('0');
}

function checkSuccess(){
    jQuery('.status-label .label').addClass('hide');
    jQuery('.status-label .label-success').removeClass('hide');
    //jQuery('#check_rule_button').addClass('hide');
    //jQuery('#rule_submit_button').removeClass('hide');
    jQuery('#isCorrect').val('1');
}

function resetCheck(){
    //jQuery('#check_rule_button').button('reset').removeClass('hide');
    jQuery('.status-label .label').addClass('hide');
    jQuery('.status-label .label-warning').removeClass('hide');
    //jQuery('#rule_submit_button').addClass('hide');
}