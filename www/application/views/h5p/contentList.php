<?php
/** @var string $data_view */
?>

<div class="wrap">
    <h2>
        <?php echo __('All H5P Content'); ?>
        <a class="btn btn-primary" href="<?php echo URL::base() . 'h5p/addContent'; ?>">
            <i class="icon-plus-sign icon-white"></i>
            <?php echo __('Add new'); ?>
        </a>
        <a class="btn btn-primary" href="<?php echo URL::base() . 'h5p/libraries'; ?>">
            <i class="icon-list icon-white"></i>
            <?php echo __('Libraries'); ?>
        </a>
        <a class="btn btn-primary" href="<?php echo URL::base() . 'h5p/deleteTemporaryFiles'; ?>">
            <i class="icon-trash icon-white"></i>
            <?php echo __('Delete temporary files'); ?>
        </a>
    </h2>
    <div id="h5p-contents">
        <?php echo __('Waiting for JavaScript.'); ?>
    </div>
</div>

<?php echo $data_view; ?>