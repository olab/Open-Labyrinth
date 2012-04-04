<?php if (isset($templateData['map'])) { ?>
    <table width="100%" height="100%" cellpadding='6'>
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('Labyrinth Report for "') . $templateData['map']->name . '"'; ?></h4>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff"><td>
                            <p><strong><a href="<?php echo URL::base() ?>reportManager/summaryReport/<?php echo $templateData['map']->id; ?>">aggregate report</a></strong></p>
                            <p><?php echo __('click to view performance by session'); ?></p>
                            <?php if(isset($templateData['sessions']) and count($templateData['sessions']) > 0) { ?>
                            <?php foreach($templateData['sessions'] as $session) { ?>
                            <p><a href="<?php echo URL::base(); ?>reportManager/showReport/<?php echo $session->id; ?>">
                                <?php echo date('Y.m.d H:i:s', $session->start_time); ?></a> user: <?php echo $session->user->nickname; ?> (<?php echo count($session->traces); ?> <?php echo __('clicks'); ?>') (0 bookmarks)</p>
                            <?php } ?>
                            <?php } ?>
                        </td></tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>

