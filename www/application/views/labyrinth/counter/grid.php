<?php if (isset($templateData['map']) and isset($templateData['nodes'])) { ?>
    <table width="100%" height="100%" cellpadding="6">
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('counter grid'); ?></h4>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff">
                        <td>
                            <?php if(isset($templateData['oneCounter'])) { ?>
                                <form action="<?php echo URL::base().'counterManager/updateGrid/'.$templateData['map']->id.'/'.$templateData['counters'][0]->id; ?>" method="POST">
                            <?php } else { ?>
                                <form action="<?php echo URL::base().'counterManager/updateGrid/'.$templateData['map']->id; ?>" method="POST">
                            <?php } ?>
                                <table border="0" width="50%" cellpadding="1">
                                    <?php if (count($templateData['nodes']) > 0) { ?>
                                        <?php foreach ($templateData['nodes'] as $node) { ?>
                                            <tr>
                                                <td><p><?php echo $node->title; ?> [<?php echo $node->id; ?>]</p></td>
                                                <?php if(isset($templateData['counters']) and count($templateData['counters']) > 0) { ?>
                                                    <?php foreach($templateData['counters'] as $counter) { ?>
                                                        <td>
                                                            <p><?php echo $counter->name; ?> <input type="text" size="5" name="nc_<?php echo $node->id; ?>_<?php echo $counter->id; ?>" 
                                                                                                    value="<?php $c = $node->getCounter($counter->id); if($c != NULL) echo $c->function; ?>"></p>
                                                        </td>
                                                    <?php } ?>
                                                <?php } ?>
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>
                                    <tr><td colspan="1"><input type="submit" name="Submit" value="submit"></td></tr>
                                </table>
                            </form>
                        </td></tr>
                </table> 
            </td>
        </tr>
    </table>
<?php } ?>
