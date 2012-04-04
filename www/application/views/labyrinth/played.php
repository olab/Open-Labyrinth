<?php if (isset($templateData['maps'])) { ?>
    <table width="100%" height="100%" cellpadding='6'>
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('my Labyrinth') . ' (' . count($templateData['maps']) . ')'; ?></h4>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff"><td>
                            <p><?php echo __('the following are your Labyrinth sessions organised by Labyrinth - click to view your performance within a session'); ?> ...</p>
                            <p><?php echo __('note that these are sessions with more than three nodes browsed within them'); ?></p>
                            <table width="100%">
                                <?php if(count($templateData['maps']) > 0) { ?>
                                <?php foreach($templateData['maps'] as $map) { ?>
                                <tr>
                                    <td><p><a href="<?php echo URL::base(); ?>playedLabyrinth/showMapInfo/<?php echo $map->id; ?>">Labyrinth: '<?php echo $map->name; ?>' (<?php echo $map->id; ?>)</a></p></td>
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

