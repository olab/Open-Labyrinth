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
    <h3>Available Vocabularies and Terms</h3>
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

    <div>
        <h4>Import new / update existing vocabulary</h4>
        <form method="get" action="<?php echo URL::base() . 'vocabulary/manager/import/'; ?>">
            <fieldset>
            <label>Universal Resource Identifier</label>
            <input name="uri" type="text" size="100"/><br/>

            </fieldset>
            <input type="submit" value="Import"/>
        </form>

    </div>

    <div><a href="<?php echo URL::base() . 'vocabulary/mappings/manager/'; ?>"><h4>Manage RDF Mappings</h4></a></div>

<?php } ?>