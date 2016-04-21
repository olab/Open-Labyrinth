<?php
/** @var array $content */
/** @var string $embed_code */
/** @var string $messages */
/** @var bool $has_errors */
?>

<div class="wrap">
    <?php echo $messages ?>

    <?php if (!$has_errors) { ?>
        <h2>
            <?php print esc_html($content['title']); ?>
            <a href="<?php print '/h5p/results/' . $content['id']; ?>" class="btn btn-primary">
                <?php echo __('Results'); ?>
            </a>
            <a href="<?php print '/h5p/addContent/' . $content['id']; ?>" class="btn btn-primary">
                <?php echo __('Edit'); ?>
            </a>
        </h2>
        <div class="h5p-wp-admin-wrapper">

            <div class="h5p-content-wrap">
                <?php print $embed_code; ?>
            </div>

            <?php if (false): ?>
                <div class="postbox h5p-sidebar">
                    <h2><?php echo __('Shortcode'); ?></h2>
                    <div class="h5p-action-bar-settings h5p-panel">
                        <p><?php echo __("What's next?"); ?></p>
                        <p><?php echo __('You can use the following shortcode to insert this interactive content into nodes'); ?></p>
                        <code>[h5p id="<?php print $content['id'] ?>"]</code>
                    </div>
                </div>
            <?php endif; ?>

            <div class="postbox h5p-sidebar">
                <h2><?php echo __('Tags'); ?></h2>
                <div class="h5p-action-bar-settings h5p-panel">
                    <?php if (empty($content['tags'])): ?>
                        <p style="font-style: italic;"><?php echo __('No tags'); ?></p>
                    <?php else: ?>
                        <p><?php echo $content['tags']; ?></p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    <?php } ?>
</div>

<?php echo $settings ?>
