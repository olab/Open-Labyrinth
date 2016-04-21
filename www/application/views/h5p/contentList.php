<?php
/** @var string $data_view */
?>

<div class="wrap">
    <h2>
        <?php echo __('All H5P Content'); ?>
        <a class="btn btn-primary pull-right" href="<?php echo URL::base() . 'h5p/addContent'; ?>">
            <i class="icon-plus-sign icon-white"></i>
            <?php echo __('Add new'); ?>
        </a>
    </h2>
    <div id="h5p-contents">
        <?php echo __('Waiting for JavaScript.'); ?>
    </div>
</div>

<?php echo $data_view; ?>