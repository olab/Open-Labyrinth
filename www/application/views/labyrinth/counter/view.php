<?php if (isset($templateData['map'])) { ?>
    <table width="100%" height="100%" cellpadding="6">
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('counters') . ' "' . $templateData['map']->name . '"'; ?></h4>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff">
                        <td>
                            <table border="0" width="100%" cellpadding="1">
                                <tr>
                                    <td>
                                        <?php if(isset($templateData['counters']) and count($templateData['counters']) > 0) { ?>
                                            <?php foreach($templateData['counters'] as $counter) { ?>
                                                <p>
                                                    <?php echo $counter->name; ?> 
                                                    [<a href="<?php echo URL::base().'counterManager/editCounter/'.$templateData['map']->id.'/'.$counter->id; ?>"><?php echo __('edit'); ?></a> 
                                                    - <a href="<?php echo URL::base().'counterManager/previewCounter/'.$templateData['map']->id.'/'.$counter->id; ?>"><?php echo __('preview'); ?></a> 
                                                    - <a href="<?php echo URL::base().'counterManager/grid/'.$templateData['map']->id.'/'.$counter->id; ?>">grid</a>  
                                                    - <a href="<?php echo URL::base().'counterManager/deleteCounter/'.$templateData['map']->id.'/'.$counter->id; ?>"><?php echo __('delete'); ?></a>]
                                                </p>
                                            <?php } ?>
                                        <?php } ?>
                                        <hr>
                                        <p><a href="<?php echo URL::base().'counterManager/addCounter/'.$templateData['map']->id; ?>"><?php echo __('add counter'); ?></a></p>
                                        <p><a href="<?php echo URL::base().'counterManager/grid/'.$templateData['map']->id; ?>"><?php echo __('counter grid'); ?></a></p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>