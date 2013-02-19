<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 14/9/2012
 * Time: 10:10 πμ
 * To change this template use File | Settings | File Templates.
 */
if (isset($templateData['vocabularies'])) {

    ?>
<div style="color: #000000;">
    <h1>Available Vocabularies and Terms</h1>
    <?php  if (count($templateData['vocabularies']) > 0) { ?>
    <?php foreach ($templateData['vocabularies'] as $vocab) { ?>
        <?php echo $vocab->namespace; ?> [<?php echo $vocab->id; ?>] {<a target="_blank" href="<?php echo URL::base() . 'vocabulary/manager/import/?uri='.$vocab->namespace; ?>">Reimport</a>}
            <div style="margin-left: 20px;">
            <?php foreach ($vocab->terms as $term) { ?>
                <a target="_blank" title="<?php echo $term->term_label; ?>" href="<?php echo $term->getFullRepresentation(); ?>"><?php echo $term->name; ?></a> [<?php echo $term->id; ?>],
            <?php } ?>
            </div>
        <?php } ?>
    <?php } ?>

</div>



        <form class="form-horizontal" method="get" action="<?php echo URL::base() . 'vocabulary/manager/import/'; ?>">
            <fieldset class="fieldset">
                <legend>Import new / update existing vocabulary</legend>
                <div class="control-group">
                    <label class="control-label">Universal Resource Identifier</label>

                    <div class="controls">
                        <input name="uri" type="text" />
                    </div>
                </div>
            </fieldset>

            <input class="btn btn-primary" type="submit" value="Import"/>
        </form>



    <a class="btn btn-primary btn-large" href="<?php echo URL::base() . 'vocabulary/mappings/manager/'; ?>">Manage RDF Mappings</a>

<?php } ?>