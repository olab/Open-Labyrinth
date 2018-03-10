<?php
/** @var object $library */
/** @var string $messages */
?>

<?php echo $messages ?>

<div class="wrap">
    <h2><?php print esc_html($library->title); ?></h2>
    <form method="post" enctype="multipart/form-data" id="h5p-library-form" action="/h5p/libraryDeleteSubmit/<?php echo $library->id ?>">
        <p><?php echo __('Are you sure you wish to delete this H5P library?'); ?></p>
        <input type="submit" name="submit" value="<?php echo __('Do it!') ?>" class="btn btn-danger"/>
    </form>
</div>