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
  <div class="page-header">
       <div class="pull-right">    <a class="btn btn-primary" href="<?php echo URL::base().'avatarManager/addAvatar/'.$templateData['map']->id; ?>"><i class="icon-plus-sign"></i>Add avatar</a>
       </div>
                <h1><?php echo __('Avatars for Labyrinth "') . $templateData['map']->name . '"'; ?></h1></div>
                <p>The following avatars have been created for this Labyrinth. Click the [edit] link to change their appearance. Copy and paste the wiki link (that looks like [[AV:1234567]]) into the content for a node.</p>

                <?php if(isset($templateData['warningMessage'])){ ?>
                <span style ="color:red;"><?php echo $templateData['warningMessage']; ?></span>
                <?php }?>
                                <table class="table table-bordered  table-striped">
                                    <thead>
                                    <tr>
                                        <th>Embeddable</th>
                                        <th>Avatar</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
    <?php if (isset($templateData['avatars']) and count($templateData['avatars']) > 0) { ?>

                                    <?php foreach($templateData['avatars'] as $avatar) {
                                        if ($avatar->image != null) {
                                            $image = URL::base().'avatars/'.$avatar->image;
                                        }else{
                                            $image = URL::base().'avatars/default.png';
                                        }
                                        ?>
                                        <tr>
                                            <td>
                                                <label>
                                                    <input type="text"  class="code" readonly="readonly" value="[[AV:<?php echo $avatar->id; ?>]]"></label>
                                            </td>
                                            <td>
                                                <p><img src="<?php echo $image; ?>" /></p>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                <a class="btn btn-info" href="<?php echo URL::base().'avatarManager/editAvatar/'.$templateData['map']->id.'/'.$avatar->id; ?>"><i class="icon-edit"></i>Edit</a>

                                                <a class="btn" href="<?php echo URL::base().'avatarManager/duplicateAvatar/'.$templateData['map']->id.'/'.$avatar->id; ?>"><i class="icon-copy"></i> Duplicate</a>
                                                <a class="btn btn-danger" href="<?php echo URL::base().'avatarManager/deleteAvatar/'.$templateData['map']->id.'/'.$avatar->id; ?>"><i class="icon-trash"></i>Delete</a></div>
                                            </td>
                                        </tr>

                                    <?php } ?>
    <?php } else { ?>
        <tr class="info">
            <td colspan="3">There are no avatars in this Labyrinth. You may add one, clicking the button above.</td>
            </tr>

    <?php } ?>
                                    </tbody>

                                </table>



<?php } ?>



