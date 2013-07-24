jQuery(document).ready(function() {
    jQuery('#check_rule_button').click(function() {
        checkRule();
    });
});

function checkRule(){
    jQuery('#check_rule_button').button('loading');
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
    }).fail(function() {
            checkFailed();
        })
}

function checkFailed(){
    jQuery('#check_rule_button').button('reset');
    jQuery('#error-alert').removeClass('hide');
    jQuery('.status-label .label').addClass('hide');
    jQuery('.status-label .label-important').removeClass('hide');
}

function checkSuccess(){
    jQuery('.status-label .label').addClass('hide');
    jQuery('.status-label .label-success').removeClass('hide');
    jQuery('#check_rule_button').addClass('hide');
    jQuery('#rule_submit_button').removeClass('hide');
}

function resetCheck(){
    jQuery('#check_rule_button').button('reset').removeClass('hide');
    jQuery('.status-label .label').addClass('hide');
    jQuery('.status-label .label-warning').removeClass('hide');
    jQuery('#rule_submit_button').addClass('hide');
}