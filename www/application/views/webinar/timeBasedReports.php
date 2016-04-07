<form action="#" class="form-inline left" method="post">
    <input type="hidden" name="referrer" value="<?php echo Request::current()->url(true) . URL::query() ?>">
    <input type="hidden" name="is_initial_request" value="1">
    <fieldset>
        <div class="control-group">
            <input class="datepicker" type="text" name="date_from" id="date_from"
                   value="<?php echo date('m/d/Y') ?>"/>
            -
            <input class="datepicker" type="text" name="date_to" id="date_to" value="<?php echo date('m/d/Y') ?>"/>
        </div>

        <div class="control-group">
            <div class="controls">
                <span class="btn btn-primary js-get-report" data-type="4R">Get 4R Report</span>
                <span class="btn btn-primary js-get-report" data-type="SCT">Get SCT Report</span>
                <span class="btn btn-primary js-get-report" data-type="Poll">Get Poll Report</span>
                <span class="btn btn-primary js-get-report" data-type="SJT">Get SJT Report</span>
                <span class="btn btn-primary js-get-report _xapi-report" data-type="xAPI">Send xAPI Report to all enabled LRS</span>
            </div>
        </div>
    </fieldset>
</form>
<script>
    showWaitPopup('._xapi-report');

    $(document).ready(function () {
        $('.js-get-report').on('click', function () {
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
                form.submit();
            }
        });
    });

    var sendReportFailedAttempts = 0;

    function sendReport(action, data) {
        $.post(action, data)
            .done(function(response){
                var result = JSON.parse(response);
                console.log(result);
                if (!result.completed) {

                    $('#please_wait_additional_info').html('Sent ' + result.sent + ' of ' + result.total + ' user sessions.');

                    data['is_initial_request'] = 0;
                    console.log(data);
                    sendReport(action, data);
                } else {
                    location.reload();
                }
            })
            .fail(function(){
                sendReportFailedAttempts++;
                if (sendReportFailedAttempts > 6) {
                    alert('Something went wrong. Please try again.');
                } else {
                    sendReport(action, data);
                }
            })
    }
</script>