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
                <span class="btn btn-primary js-get-report _xapi-report" data-type="4R">Get 4R Report</span>
                <span class="btn btn-primary js-get-report _xapi-report" data-type="SCT">Get SCT Report</span>
                <span class="btn btn-primary js-get-report _xapi-report" data-type="Poll">Get Poll Report</span>
                <span class="btn btn-primary js-get-report _xapi-report" data-type="SJT">Get SJT Report</span>
                <span class="btn btn-primary js-get-report _xapi-report" data-type="xAPI">Send xAPI Report to all enabled LRS</span>
            </div>
        </div>
    </fieldset>
</form>