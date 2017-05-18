var urlBase = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port : '') + '/';
var reportFilename = 'report_' + (new Date).getTime();

jQuery(document).ready(function () {

    showWaitPopup('._xapi-report');

    $('.js-get-report').on('click', function (e) {
        var button = $(this),
            action = '/webinarmanager/',
            form = button.closest('form'),
            type = button.attr('data-type');

        switch (type) {
            case '4R':
                action += 'report4RTimeBased';
                break;

            case 'SCT':
                action += 'reportSCTTimeBased';
                break;

            case 'Poll':
                action += 'reportPollTimeBased';
                break;

            case 'SJT':
                action += 'reportSJTTimeBased';
                break;

            case 'xAPI':
                action = '/lrs/sendReportSubmit';
                break;
        }

        form.attr('action', action);

        if (type === 'xAPI') {
            sendReport(action, form.serializeObject());
        } else {
            e.preventDefault();

            handleAjaxReport(action, form.serializeObject());
        }
    });

    var th = $('#expert-th-js'),
        td = $('.expert-td-js'),
        webinar = $('#sct-webinars'),
        reportMapType = $('.reportMapType'),
        reportStepType = $('.reportStepType'),
        selectedWeb = webinar.val(),
        href = null,
        reportByFirstOrLastAttempt = $('#reportByFirstOrLastAttempt');

    showWaitPopup('.sendXAPIStepReport');
    showWaitPopup('.reportStepType');

    $(document).on('click', '.sendXAPIStepReport', function (e) {
        e.preventDefault();
        sendReport($(this).attr('href'), {'is_initial_request': 1});
    });


    $(document).on('click', '.reportStepType', function (e) {

        if ($(this).hasClass('sendXAPIStepReport')) {
            return;
        }

        e.preventDefault();

        handleAjaxReport($(this).attr('href'));
    });

    $('#4R').change(function () {
        clickOnButton($(this));
    });
    $('#SCT').change(function () {
        clickOnButton($(this));
    });
    $('#Poll').change(function () {
        clickOnButton($(this));
    });
    $('#SJT').change(function () {
        clickOnButton($(this));
    });
    $('#xAPI').change(function () {
        clickOnButton($(this));
    });

    $('#reportByLatest').change(function () {
        changeSort($(this));
    });
    $('#reportByFirst').change(function () {
        changeSort($(this));
    });

    function changeSort(button) {
        var value = button.attr('value');
        $.get(urlBase + 'webinarManager/reportByLatestSession/' + value);
    }

    function clickOnButton(button) {
        var typeId = button.attr('id');

        if (typeId == 'xAPI') {
            reportStepType.addClass('sendXAPIStepReport');
        } else {
            reportStepType.removeClass('sendXAPIStepReport');
        }

        if (typeId == '4R' || typeId == 'xAPI') {
            reportByFirstOrLastAttempt.hide();
        } else {
            reportByFirstOrLastAttempt.show();
        }

        if (typeId == '4R' || typeId == 'Poll' || typeId == 'xAPI') {
            th.hide();
            td.hide();
            webinar.hide();
        } else if (typeId == 'SCT' || typeId == 'SJT') {
            th.show();
            td.show();
            webinar.show();
        }

        changeLinkType(reportMapType, typeId, 'mapReport');
        changeLinkType(reportStepType, typeId, 'stepReport');

        window.location.hash = typeId;
    }

    function changeLinkType(objs, typeId, stepOrMap) {
        objs.each(function () {
            var href = $(this).attr('href'),
                regexHref = new RegExp(stepOrMap + '[^\/]*');

            href = href.replace(regexHref, stepOrMap + typeId);
            $(this).attr('href', href);
        });
    }

    function checkForMap(maps) {
        reportMapType.each(function () {
            var getMapId = $(this).attr('href').split('/').reverse()[1];

            if ($.inArray(parseInt(getMapId), maps) == -1) $(this).addClass('discrepancyMap');
            else $(this).removeClass('discrepancyMap');
        });
    }

    webinar.change(function () {
        // change visual expert check box
        var expertBox = $('.expert-js');

        if ($(this).val() != selectedWeb) expertBox.prop('disabled', true);
        else expertBox.prop('disabled', false);

        // change href of all SCT report
        var selectedScenarioId = $(this).val();

        $.getJSON(urlBase + 'webinarManager/getMapByWebinar/' + selectedScenarioId, function (data) {
            checkForMap(data);
        });

        var changeScenarioId = function (obj) {
            var currentScenarioId = obj.attr('href').split('/').pop(),
                objHref = obj.attr('href'),
                newHref = objHref.substr(0, objHref.length - currentScenarioId.length) + selectedScenarioId;

            obj.attr('href', newHref);
        };

        reportMapType.each(function () {
            changeScenarioId($(this));
        });

        reportStepType.each(function () {
            changeScenarioId($(this));
        });
    });

    // ----------- discrepancyMap error massage ---------- //
    var Message = $('#discrepancyMap');
    $('.discrepancyMap').live('click', function (e) {
        e.preventDefault();

        Message.removeClass('hide');
        setTimeout(function () {
            Message.addClass('hide');
        }, 5000);
    });

    $('.root-error-close').click(function () {
        if (Message != null) Message.addClass('hide');
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
        function (data) {
        }
    );
}

function ajaxExpert(idWebinarUser, idUser) {
    var isExpert = $('#expert' + idUser).attr('checked') ? 1 : 0;
    var URL = urlBase + 'webinarManager/updateExpert/' + idWebinarUser + "/" + isExpert;
    $.get(
        URL,
        function (data) {
        }
    );
}

var sendReportFailedAttempts = 0;

function sendReport(action, data) {
    $.post(action, data)
        .done(function (response) {
            var result = JSON.parse(response);
            if (!result.completed) {

                $('#please_wait_additional_info').html('Sent ' + result.sent + ' of ' + result.total + ' user sessions.');

                data['is_initial_request'] = 0;
                sendReport(action, data);
            } else {
                location.reload();
            }
        })
        .fail(function () {
            sendReportFailedAttempts++;
            if (sendReportFailedAttempts > 6) {
                alert('Something went wrong. Please try again.');
            } else {
                setTimeout(function () {
                    sendReport(action, data);
                }, 1000);
            }
        })
}

function getReportProgress() {
    $.post('/webinarManager/getReportProgress', {'filename': reportFilename}).done(function (response) {
        var result = JSON.parse(response);
        if (result.is_done) {
            $('#please_wait_additional_info').html('Processed ' + result.session_counter + ' items.');
            var currentUrl = location.href;
            location.href = '/webinarManager/downloadReport/' + reportFilename;
            setTimeout(function () {
                location.href = currentUrl;
                location.reload();
            }, 7000);
        } else {
            $('#please_wait_additional_info').html('Processed ' + result.session_counter + ' items.');
            setTimeout(function () {
                getReportProgress();
            }, 3000);
        }
    });
}

function handleAjaxReport(action, data) {
    var data = data || {};
    data['filename'] = reportFilename;
    data['is_ajax'] = 1;

    $.post(action, data).done(function (response) {
        if (empty(response)) {
            return;
        }
        var result = JSON.parse(response);
        if (result.reload) {
            location.reload();
        }
    });

    setTimeout(function () {
        getReportProgress();
    }, 3000);
}
