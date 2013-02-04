<div style="color: #000000;">
    <h3>Vocabulary import process completed successfully</h3>
    <h4>Terms imported from <?php echo $templateData['uri']; ?>:</h4>

    <?php  if (count($templateData['terms']) > 0) { ?>
    <?php foreach ($templateData['terms'] as $term) { ?>
        <?php echo $term ?><br/>

        <?php } ?>
    <?php } ?>
    <a href="<?php echo URL::base(); ?>vocabulary/manager">Back to the vocabulary manager</a>
    </div>



