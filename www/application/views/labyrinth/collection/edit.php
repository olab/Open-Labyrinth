<?php if(isset($templateData['collection'])) { ?>
<table width="100%" height="100%" cellpadding='6'>
    <tr>
        <td valign="top" bgcolor="#bbbbcb">
            <h4><?php echo __('edit Collection'); ?></h4>
            <p><a href="<?php echo URL::base(); ?>collectionManager">Collections</a></p>
            <table width="100%" cellpadding="6">
                <tr bgcolor="#ffffff"><td>
                        <table>
                            <form method="POST" action="<?php echo URL::base(); ?>collectionManager/updateName/<?php echo $templateData['collection']->id; ?>">
                            <tr><td>
                                    <p>colection name</p></td>
                                <td><input type="text" name="colname" value="<?php echo $templateData['collection']->name; ?>">
                                    <input type="submit" value="submit">
                                </td></tr>
                            </form>
                            <tr><td colspan="2"><p><strong>Labyrinths in Collection</strong></p>
                                    <?php if(count($templateData['collection']->maps) > 0) { ?>
                                    <?php foreach($templateData['collection']->maps as $mp) { ?>
                                    <p>
                                        <a href="<?php echo URL::base(); ?>labyrinthManager/editMap/<?php echo $mp->map->id; ?>"><?php echo $mp->map->name; ?></a> 
                                        - [<a href="<?php echo URL::base(); ?>collectionManager/deleteMap/<?php echo $templateData['collection']->id; ?>/<?php echo $mp->map->id; ?>">delete</a>]</p>
                                    <?php } ?>
                                    <?php } ?>
                                    <p></p><form method="POST" action="<?php echo URL::base(); ?>collectionManager/addMap/<?php echo $templateData['collection']->id; ?>">
                                        <select name="mapid">
                                            <?php if(isset($templateData['maps']) and count($templateData['maps']) > 0) { ?>
                                            <?php foreach($templateData['maps'] as $map) { ?>
                                            <option value="<?php echo $map->id; ?>"><?php echo $map->name; ?></option>
                                            <?php } ?>
                                            <?php } ?>
                                           </select>
                                        <input type="submit" value="submit">
                                    </form><p></p>
                                </td></tr>
                        </table>
                    </td></tr>
            </table>
        </td>
    </tr>
</table>
<?php } ?>