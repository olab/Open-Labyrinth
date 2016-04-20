<?php
/** @var array $content */
/** @var string $messages */
/** @var bool $has_errors */
?>

<div class="wrap">
    <?php echo $messages ?>

    <?php if (!$has_errors) { ?>
        <h2>
            <?php printf(__('Results for "%s"'), esc_html($content['title'])); ?>
            <a href="<?php print '/h5p/showContent/' . $content['id']; ?>" class="add-new-h2">
                <?php echo __('View'); ?>
            </a>
            <a href="<?php print '/h5p/addContent/' . $content['id']; ?>" class="add-new-h2">
                <?php echo __('Edit'); ?>
            </a>
        </h2>
    <?php } ?>

    <div id="h5p-content-results">
        <?php __('Waiting for JavaScript.'); ?>
    </div>
</div>

<?php echo $settings ?>