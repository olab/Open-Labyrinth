<?php
/**
 * Open Labyrinth [ http://www.openlabyrinth.ca ]
 *
 * Open Labyrinth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Open Labyrinth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Open Labyrinth.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright Copyright 2012 Open Labyrinth. All Rights Reserved.
 *
 */
if (isset($templateData['map'])) { ?>
    <table width="100%" height="100%" cellpadding="6">
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('avatars for Labyrinth "') . $templateData['map']->name . '"'; ?></h4>
                <p>The following avatars have been created for this Labyrinth. Click the [edit] link to change their appearance. Copy and paste the wiki link (that looks like [[AV:1234567]]) into the content for a node.</p>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff">
                        <td>
                            <?php if (isset($templateData['avatars']) and count($templateData['avatars']) > 0) { ?>
                                <table>
                                    <?php foreach($templateData['avatars'] as $avatar) {
                                    if ($avatar->image != null) {
                                        $image = URL::base().'avatars/'.$avatar->image;
                                    }else{
                                        $image = URL::base().'avatars/default.png';
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <input type="text" size="20" value="[[AV:<?php echo $avatar->id; ?>]]">
                                        </td>
                                        <td align="center" valign="middle">
                                            <p><img src="<?php echo $image; ?>" /></p>
                                        </td>
                                        <td>
                                            <p>[<a href="<?php echo URL::base().'avatarManager/editAvatar/'.$templateData['map']->id.'/'.$avatar->id; ?>">edit</a>] [<a href="<?php echo URL::base().'avatarManager/duplicateAvatar/'.$templateData['map']->id.'/'.$avatar->id; ?>">duplicate</a>] [<a href="<?php echo URL::base().'avatarManager/deleteAvatar/'.$templateData['map']->id.'/'.$avatar->id; ?>">delete</a>]</p></td>
                                    </tr>
                                    <tr><td colspan="3"><hr></td></tr>
                                    <?php } ?>
                                </table>
                            <?php } else { ?>
                                <p>there are no avatars in this Labyrinth</p>
                            <?php } ?>
                            <p><a href="<?php echo URL::base().'avatarManager/addAvatar/'.$templateData['map']->id; ?>">add an avatar</a></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>



