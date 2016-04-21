<?php
/** @var int $update_available */
/** @var int $updates_available */
/** @var int $current_update */
/** @var string $H5PAdminIntegration */
/** @var string $messages */
?>

<?php echo $messages ?>

    <div class="wrap">
        <?php if ($updates_available): ?>
            <form method="post" enctype="multipart/form-data">
                <h3 class="h5p-admin-header"><?php echo __('Update All Libraries'); ?></h3>
                <div class="h5p postbox">
                    <div class="h5p-text-holder" id="h5p-download-update">
                        <p><?php echo __('There are updates available for your H5P content types.') ?></p>
                        <p><?php printf(__('You can read about why it\'s important to update and the benefits from doing so on the <a href="https://h5p.org/why-update" target="_blank">Why Update H5P</a> page.')); ?>
                            <br/><?php echo __('The page also list the different changelogs, where you can read about the new features introduced and the issues that have been fixed.') ?>
                        </p>
                        <p>
                            <?php if ($current_update > 1): ?>
                                <?php printf(__('The version you\'re running is from <strong>%s</strong>.'), date('Y-m-d', $current_update)); ?>
                                <br/>
                            <?php endif; ?>
                            <?php printf(__('The most recent version was released on <strong>%s</strong>.'), date('Y-m-d', $update_available)); ?>
                        </p>
                        <p><?php echo __('You can use the button below to automatically download and update all of your content types.') ?></p>
                    </div>
                    <div class="h5p-button-holder">
                        <input type="submit" name="submit" value="<?php echo __('Download & Update') ?>"
                               class="button button-primary button-large"/>
                    </div>
                </div>
            </form>
        <?php endif; ?>
        <h3 class="h5p-admin-header"><?php echo __('Upload Libraries'); ?></h3>
        <div class="alert alert-info">
            <?php echo __('Note: Value of php.ini directives post_max_size and upload_max_filesize should be greater than the size of the package.') ?>
            <?php echo __('Current') ?> post_max_size = <?php echo ini_get('post_max_size') ?>,
            <?php echo __('current') ?> upload_max_filesize = <?php echo ini_get('upload_max_filesize') ?>
        </div>
        <form method="post" enctype="multipart/form-data" id="h5p-library-form" action="/h5p/libraryUpload">
            <div class="h5p postbox">
                <div class="h5p-text-holder">
                    <p><?php print __('Here you can upload new libraries or upload updates to existing libraries. Files uploaded here must be in the .h5p file format.') ?></p>
                    <input type="file" name="h5p_file" id="h5p-file"/>
                    <label for="h5p-upgrade-only">
                        <input type="checkbox" name="h5p_upgrade_only" id="h5p-upgrade-only"/>
                        <?php print __('Only update existing libraries'); ?>
                    </label>
                    <div class="h5p-disable-file-check">
                        <label><input type="checkbox" name="h5p_disable_file_check"
                                      id="h5p-disable-file-check"/> <?php echo __('Disable file extension check'); ?>
                        </label>
                        <div
                            class="h5p-warning"><?php echo __("Warning! This may have security implications as it allows for uploading php files. That in turn could make it possible for attackers to execute malicious code on your site. Please make sure you know exactly what you're uploading."); ?></div>
                    </div>
                </div>
                <div class="h5p-button-holder">
                    <input type="submit" name="submit" value="<?php echo __('Upload') ?>" class="btn btn-primary"/>
                </div>
            </div>
        </form>
        <h3 class="h5p-admin-header"><?php echo __('Installed Libraries'); ?></h3>
        <div id="h5p-admin-container"><?php echo __('Waiting for JavaScript.'); ?></div>
    </div>

<?php echo $H5PAdminIntegration ?>