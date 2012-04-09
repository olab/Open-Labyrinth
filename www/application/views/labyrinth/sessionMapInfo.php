<?php if (isset($templateData['sessions'])) { ?>
    <table width="100%" height="100%" cellpadding='6'>
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('my Labyrinth '); ?></h4>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff"><td>
                            <p><?php echo __('the following are your Labyrinth sessions organised by Labyrinth - click to view your performance within a session'); ?> ...</p>
                            <p><?php echo __('note that these are sessions with more than three nodes browsed within them'); ?></p>
                            <table width="100%">
                                <?php if(count($templateData['sessions']) > 0) { ?>
                                <?php foreach($templateData['sessions'] as $session) { ?>
                                <tr>
                                    <td><p><a href="<?php echo URL::base(); ?>reportManager/showReport/<?php echo $session->id; ?>">session at '<?php echo date('Y:m:d H:i:s', $session->start_time); ?> (<?php echo __('clicks'); ?> <?php echo count($session->traces); ?>)</a></p></td>
									<td><p><?php if(isset($templateData['bookmarks']) and isset($templateData['bookmarks'][$session->id])) { ?>
										<a href='<?php echo URL::base(); ?>renderLabyrinth/go/<?php echo $session->map_id; ?>/<?php echo $templateData['bookmarks'][$session->id]->node_id; ?>/b/<?php echo $templateData['bookmarks'][$session->id]->id; ?>'>bookmark at <?php echo $templateData['bookmarks'][$session->id]->node->title; ?></a>
									<?php } ?>
									</p></td>
                                </tr>
                                <?php } ?>
                                <?php } ?>
                            </table>
                        </td></tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>

