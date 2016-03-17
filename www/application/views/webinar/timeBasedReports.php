<form action="#" class="form-horizontal left" method="post">
    <input type="hidden" name="referrer" value="<?php echo URL::base() . 'webinarmanager/timeBasedReports' ?>">
    <fieldset>
        <div class="control-group">
            <label for="date_from" class="control-label">Start date</label>

            <div class="controls">
                <input class="datepicker" type="text" name="date_from" id="date_from"
                       value="<?php echo date('m/d/Y', time() - 60 * 60 * 24) ?>"/>
            </div>
        </div>

        <div class="control-group">
            <label for="date_to" class="control-label">End date</label>

            <div class="controls">
                <input class="datepicker" type="text" name="date_to" id="date_to" value="<?php echo date('m/d/Y') ?>"/>
            </div>
        </div>

        <div class="control-group">
            <div class="controls">
                <span class="btn btn-primary js-get-report" data-type="4R">Get 4R Report</span>
                <span class="btn btn-primary js-get-report" data-type="SCT">Get SCT Report</span>
                <span class="btn btn-primary js-get-report" data-type="Poll">Get Poll Report</span>
                <span class="btn btn-primary js-get-report" data-type="SJT">Get SJT Report</span>
                <span class="btn btn-primary js-get-report" data-type="xAPI">Send xAPI Report to all enabled LRS</span>
            </div>
        </div>
    </fieldset>
</form>
<script>
    $(document).ready(function () {
        $('.js-get-report').on('click', function () {
            var button = $(this),
                action = '/webinarmanager/',
                form = button.closest('form');

            switch (button.attr('data-type')) {
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
            form.submit();
        });
    });
</script>