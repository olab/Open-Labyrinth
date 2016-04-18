<?php
/** @var string $data_view */
?>

<div class="wrap">
    <h2><?php echo __('All H5P Content'); ?>
        <a href="/h5p/addContent" class="add-new-h2">
            <?php echo __('Add new'); ?>
        </a>
    </h2>
    <div id="h5p-contents">
        <?php echo __('Waiting for JavaScript.'); ?>
    </div>
</div>

<?php echo $data_view; ?>