<table><tr>
        <td bgcolor="#ffffff" align="left" width="60%" valign="top">
            <p><img src="images/presentl.jpg" border="0" alt="OLPresentations">Presentations</p>
            </p><hr>
            <p><img src="images/olsphere.jpg" border="0" alt="OLMaps">Open Labyrinths</p>
            <?php if (isset($openLabyrinths) and count($openLabyrinths) > 0) { ?>
                <?php foreach ($openLabyrinths as $labyrinth) { ?>
                    <p><a href="<?php echo URL::base(); ?>renderLabyrinth/index/<?php echo $labyrinth->id; ?>"><?php echo $labyrinth->name; ?></a></p>
                <?php } ?>
            <?php } ?>
        </td>
    </tr>
</table>
