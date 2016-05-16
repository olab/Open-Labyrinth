<?php
$googleServiceAccountCredentials = get_option('google_service_account_credentials');
?>

<?php if (empty($googleServiceAccountCredentials)){ ?>
    <?php if (Auth::instance()->get_user()->isSuperuser()) { ?>
        <div class="alert alert-warning">
        <?php echo __('Please set Google service account credentials to use Google Spreadsheets.') ?>
        </div>
    <?php } else { ?>
    <div class="alert alert-warning">
        <?php echo __('Please ask superuser to set Google service account credentials to use Google Spreadsheets.') ?>
    </div>
    <?php } ?>
<?php } ?>

<div class="control-group">
    <label for="external_source_id" class="control-label"><?php echo __('Google Spreadsheet ID'); ?></label>
    <div class="controls">
        <input
            id="external_source_id"
            name="external_source_id"
            value="<?php if(isset($templateData['question'])) echo $templateData['question']->external_source_id ?>"
            maxlength="255"
            style="width:400px;"
        >
        <?php if (Auth::instance()->get_user()->isSuperuser()) { ?>
            <div class="help-inline">
                <a href="<?php echo URL::base() . 'options/all'?>">
                    <?php echo __('Set Google service account credentials') ?>
                </a>
            </div>
        <?php } ?>
    </div>
</div>
