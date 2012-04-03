<table cellpadding="2" width="100%" cellpadding='6'>

    <tr bgcolor="#ddddee"><td align="right" colspan="5"><p><strong><font color="white"><?php echo __('Labyrinths'); ?>&nbsp;&nbsp;&nbsp;</font></strong></p></td></tr>

    <tr>
        <td valign="center" nowrap=""><a href="mylabyrinth.asp"><img src="<?php echo URL::base(); ?>images/olsphere.jpg" border="0" alt="OLSphere"></a></td>
        <td valign="center" nowrap=""><a href="<?php echo URL::base() . 'playedLabyrinth'; ?>"></a><p><a href="<?php echo URL::base() . 'playedLabyrinth'; ?>"><strong><?php echo __('Labyrinths I have played'); ?></strong></a></p></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td valign="center" nowrap=""><a href="labyrinthlist.asp?t=a"><img src="<?php echo URL::base(); ?>images/olsphere.jpg" border="0" alt="OLSphere"></a></td>
        <td valign="center" nowrap=""><a href="<?php echo URL::base() . 'authoredLabyrinth'; ?>"></a><p><a href="<?php echo URL::base() . 'authoredLabyrinth'; ?>"><strong><?php echo __('Labyrinths I am Authoring'); ?></strong></a></p></td>
    </tr>

    <tr>
        <td valign="center" nowrap=""><a href="labyrinthlist.asp?t=p"><img src="<?php echo URL::base(); ?>images/olsphere.jpg" border="0" alt="OLSphere"></a></td>
        <td valign="center" nowrap=""><a href="<?php echo URL::base() . 'collectionManager'; ?>"></a><p><a href="<?php echo URL::base() . 'collectionManager'; ?>"><strong><?php echo __('Collections'); ?></strong></a></p></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td valign="center" nowrap=""><a href="labyrinthlist.asp?t=c"><img src="<?php echo URL::base(); ?>images/olsphere.jpg" border="0" alt="OLSphere"></a></td>
        <td valign="center" nowrap=""><a href="<?php echo URL::base() . 'closeLabyrinth'; ?>"></a><p><a href="<?php echo URL::base() . 'closeLabyrinth'; ?>"><strong><?php echo __('closed Labyrinths'); ?></strong></a></p></td>
    </tr>

    <tr>
        <td valign="center" nowrap=""><a href="<?php echo URL::base() . 'openLabyrinth'; ?>"><img src="<?php echo URL::base(); ?>images/olsphere.jpg" border="0" alt="OLSphere"></a></td>
        <td valign="center" nowrap=""><a href="<?php echo URL::base() . 'openLabyrinth'; ?>"></a><p><a href="<?php echo URL::base() . 'openLabyrinth'; ?>"><strong><?php echo __('open Labyrinths'); ?></strong></a></p></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td valign="center" nowrap=""><a href="labyrinthlist.asp?t=k"><img src="<?php echo URL::base(); ?>images/olsphere.jpg" border="0" alt="OLSphere"></a></td>
        <td valign="center" nowrap=""><a href="<?php echo URL::base() . 'keyLabyrinth'; ?>"></a><p><a href="<?php echo URL::base() . 'keyLabyrinth'; ?>"><strong><?php echo __('key Labyrinths'); ?></strong></a></p></td>
    </tr>

    <tr bgcolor="#ddddee"><td align="right" colspan="5"><p><strong><font color="white"><?php echo __('Tools'); ?>&nbsp;&nbsp;&nbsp;</font></strong></p></td></tr>

    <tr>
        <td nowrap=""><a href="<?php echo URL::base(); ?>presentationManager"><img src="<?php echo URL::base(); ?>images/presentl.jpg" border="0" alt="OLPresentations"></a></td>
        <td nowrap=""><p><a href="<?php echo URL::base(); ?>presentationManager"><strong><?php echo __('presentations'); ?></strong></a></p></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td nowrap=""><a href="<?php echo URL::base(); ?>remoteServiceManager"><img src="<?php echo URL::base(); ?>images/remotel.jpg" border="0" alt="OLRemote"></a></td>
        <td nowrap=""><p><a href="<?php echo URL::base(); ?>remoteServiceManager"><strong><?php echo __('remote services'); ?></strong></a></p></td>
    </tr>

    <tr>
        <td nowrap=""><a href=<?php echo URL::base() . 'labyrinthManager/createLabyrinth'; ?>><img src="<?php echo URL::base(); ?>images/addl.jpg" border="0" alt="OLCreate"></a></td>
        <td nowrap=""><p><a href=<?php echo URL::base() . 'labyrinthManager/createLabyrinth'; ?>><strong><?php echo __('create Labyrinth'); ?></strong></strong></a></p></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>

        <td nowrap=""><a href=<?php echo URL::base() . 'usermanager'; ?>><img src="<?php echo URL::base(); ?>images/usersl.jpg" border="0" alt="OLRemote"></a></td>
        <td><p><a href=<?php echo URL::base() . 'usermanager'; ?>><strong><?php echo __('users'); ?></strong></a></p></td>


    </tr>
</table>