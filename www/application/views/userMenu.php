<table><tr>
        <td bgcolor="#ffffff" align="left" width="60%" valign="top">
            <?php if(isset($presentations) and count($presentations) > 0) { ?>
            <p><img src="<?php echo URL::base(); ?>images/presentl.jpg" border="0" alt="OLPresentations">Presentations</p>
            <?php foreach($presentations as $presentation) { ?>
            <p><a href="<?php echo URL::base(); ?>presentationManager/render/<?php echo $presentation->id; ?>"><?php echo $presentation->title; ?></a></p>
            <?php } ?>
            <hr>
            <?php } ?>
            <p><img src="<?php echo URL::base(); ?>images/olsphere.jpg" border="0" alt="OLMaps">Open Labyrinths</p>
            <?php if (isset($openLabyrinths) and count($openLabyrinths) > 0) { ?>
                <?php foreach ($openLabyrinths as $labyrinth) { ?>
                    <p><a href="<?php echo URL::base(); ?>renderLabyrinth/index/<?php echo $labyrinth->id; ?>"><?php echo $labyrinth->name; ?></a></p>
                <?php } ?>
            <?php } ?>
        </td>
    </tr>
</table>
