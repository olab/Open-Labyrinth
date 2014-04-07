var urlBase = window.location.origin + '/';

jQuery(document).ready(function()
{
    var th              = $('#expert-th-js'),
        td              = $('.expert-td-js'),
        webinar         = $('#sct-webinars'),
        reportMapType   = $('.reportMapType'),
        reportStepType  = $('.reportStepType'),
        selectedWeb     = webinar.val(),
        href            = null;

    $('#4R').change(function() {
        clickOnButton($(this));
    });
    $('#SCT').change(function() {
        clickOnButton($(this));
    });
    $('#Poll').change(function() {
        clickOnButton($(this));
    });

    function clickOnButton (button){
        var typeId = button.attr('id');

        if (typeId == '4R' || typeId == 'Poll') {
            th.hide();
            td.hide();
            webinar.hide();
        } else if (typeId == 'SCT') {
            th.show();
            td.show();
            webinar.show();
        }

        changeLinkType(reportMapType, typeId, 'mapReport');
        changeLinkType(reportStepType, typeId, 'stepReport');

        window.location.hash = typeId;
    }

    function changeLinkType (objs, typeId, stepOrMap) {
        objs.each(function(){
            var href        = $(this).attr('href'),
                regexHref   = new RegExp(stepOrMap + '[^\/]*');

            href = href.replace(regexHref, stepOrMap + typeId);
            $(this).attr('href', href);
        });
    }

    function checkForMap(maps) {
        reportMapType.each(function(){
            var getMapId = $(this).attr('href').split('/').reverse()[1];

            if($.inArray(parseInt(getMapId), maps) == -1) $(this).addClass('discrepancyMap');
            else $(this).removeClass('discrepancyMap');
        });
    }

    webinar.change(function(){
        // change visual expert check box
        var expertBox = $('.expert-js');

        if ($(this).val() != selectedWeb) expertBox.prop('disabled', true);
        else expertBox.prop('disabled', false);

        // change href of all SCT report
        var selectedScenarioId  = $(this).val();

        $.getJSON(urlBase + 'webinarManager/getMapByWebinar/' + selectedScenarioId, function(data){
            checkForMap(data);
        });

        var changeScenarioId = function (obj){
            var currentScenarioId   = obj.attr('href').split('/').pop(),
                objHref             = obj.attr('href'),
                newHref             = objHref.substr(0, objHref.length - currentScenarioId.length) + selectedScenarioId;

            obj.attr('href', newHref);
        };

        reportMapType.each(function(){
            changeScenarioId($(this));
        });

        reportStepType.each(function(){
            changeScenarioId($(this));
        });
    });

    // ----------- discrepancyMap error massage ---------- //
    var Message = $('#discrepancyMap');
    $('.discrepancyMap').live('click', function(e){
        e.preventDefault();

        Message.removeClass('hide');
        setTimeout(function() { Message.addClass('hide'); }, 5000);
    });

    $('.root-error-close').click(function() {
        if(Message != null) Message.addClass('hide');
    });
    // ----------- end discrepancyMap error massage ---------- //

    var hash = window.location.hash;
    if (hash != '') {
        $(hash).click();
    }

});

function ajaxCheck(id, idUser) {
    var isInclude = $('#check' + idUser).attr('checked') ? 1 : 0;
    var URL = urlBase + 'webinarManager/updateInclude4R/' + id + "/" + isInclude;
    $.get(
        URL,
        function(data) {}
    );
}

function ajaxExpert(idWebinarUser, idUser) {
    var isExpert = $('#expert' + idUser).attr('checked') ? 1 : 0;
    var URL = urlBase + 'webinarManager/updateExpert/' + idWebinarUser + "/" + isExpert;
    $.get(
        URL,
        function(data){}
    );
}
