<?php if (isset($templateData['maps'])) { ?>
    <table width="100%" height="100%" cellpadding='6'>
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('Keys Labyrinths') . ' (' . count($templateData['maps']) . ')'; ?></h4>
                <table width="100%" cellpadding="0">
                    <tr bgcolor="#ffffff"><td>
                            <table width="100%">
                                <?php foreach($templateData['maps'] as $map) { ?>
                                    <tr bgcolor="#f3f3fa">
                                        <td><p><a href="<?php echo URL::base(); ?>renderLabyrinth/index/<?php echo $map->id; ?>" target="_blank"><?php echo $map->name; ?></a></p></td>
                                        <td><p><a href="<?php echo URL::base().'labyrinthManager/editMap/'.$map->id; ?>"><img src="<?php echo URL::base(); ?>images/editl.jpg" border="0" alt="edit"></a></p></td>
                                        <td><p><a href="<?php echo URL::base().'openLabyrinth/info/'.$map->id; ?>"><img src="<?php echo URL::base(); ?>images/infol.jpg" border="0" alt="get info"></a></p></td>
                                        <td><p>
                                                <?php if(count($map->contributors) > 0) { ?>
                                                <?php foreach($map->contributors as $contributor) { ?>
                                                    <?php echo $contributor->name ?>, (<?php echo $contributor->role->name; ?>)
                                                <?php } ?>
                                                <?php } ?>
                                            </p></td>
                                        <td><p><?php echo $map->abstract; ?></p></td>
                                        <td valign="center"></td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </td></tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>

