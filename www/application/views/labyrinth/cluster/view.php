<?php if (isset($templateData['map'])) { ?>
    <table width="100%" height="100%" cellpadding='6'>
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('data clusters for Labyrinth "') . $templateData['map']->name . '"'; ?></h4>
                <table bgcolor='#ffffff'><tr><td>
                            <p><?php echo __('data clusters act as aggregating and bringing mechanisms between data and media elements'); ?>:</p>
                            <?php if(isset($templateData['dams']) and count($templateData['dams']) > 0) { ?>
                            <?php foreach($templateData['dams'] as $dam) { ?>
                                <p>
                                    <img src='<?php echo URL::base(); ?>images/OL_cluster_wee.gif' alt='clusters' align='absmiddle' border='0' />&nbsp;Name: <?php echo $dam->name; ?> (<?php echo $dam->id; ?>)
                                    - [<a href='<?php echo URL::base(); ?>clusterManager/editCluster/<?php echo $templateData['map']->id; ?>/<?php echo $dam->id; ?>'><?php echo __('edit'); ?></a>] - [<a href='<?php echo URL::base(); ?>clusterManager/deleteDam/<?php echo $templateData['map']->id; ?>/<?php echo $dam->id; ?>'><?php echo __('delete'); ?></a>]</p>
                            <?php } ?>
                            <?php } ?>
                            <hr />
                            <p><a href='<?php echo URL::base(); ?>clusterManager/addDam/<?php echo $templateData['map']->id; ?>'><?php echo __('add data cluster'); ?></a><br /><br /></p>
                        </td></tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>

