<?php if (isset($templateData['map'])) { ?>
    <table width="100%" height="100%" cellpadding='6'>
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('data elements for Labyrinth "') . $templateData['map']->name . '"'; ?></h4>
                <table bgcolor="#ffffff" width="100%"><tr><td align="left">
                            <?php if(isset($templateData['vpds']) and count($templateData['vpds']) > 0) { ?>
                            <p>data elements:</p>
                            <?php foreach($templateData['vpds'] as $vpd) { ?>
                                <p>
                                    <strong><img src="<?php echo URL::base(); ?>images/OL_element_wee.gif" alt="elements" align="absmiddle" border="0">&nbsp;<?php echo $vpd->type->label; ?> (<?php echo $vpd->id; ?>)</strong> 
                                    - <a href="<?php echo URL::base(); ?>elementManager/editVpd/<?php echo $templateData['map']->id; ?>/<?php echo $vpd->id; ?>">edit</a> 
                                    - <a href="<?php echo URL::base(); ?>elementManager/deleteVpd/<?php echo $templateData['map']->id; ?>/<?php echo $vpd->id; ?>">delete</a>
                                    <br>
                                    <?php if(count($vpd->elements) > 0) { ?>
                                        <?php foreach($vpd->elements as $element) { ?>
                                            <?php echo $element->key; ?> = <?php echo $element->value; ?><br>
                                        <?php } ?>
                                    <?php } ?>
                                </p>
                                <hr>
                            <?php } ?>
                            <?php } ?>
                            <hr>
                            <p><a href="<?php echo URL::base(); ?>elementManager/addNewElement/<?php echo $templateData['map']->id; ?>">add a new data element</a></p>
                        </td></tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>