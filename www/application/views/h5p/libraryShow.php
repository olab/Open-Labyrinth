<?php
/** @var object $library */
/** @var string $messages */
?>
<div class="wrap">
    <h2><?php print esc_html($library->title); ?></h2>
    <?php print $messages ?>
    <div id="h5p-admin-container"></div>
</div>

<?php echo $settings ?>