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
        <h1><?php echo __('Global avatars for Labyrinth "') . $templateData['map']->name . '"'; ?></h1></div>
    <p>The following avatars have been created for a Labyrinths. Click the [Import] link to add an avatar to this labyrinth.</p>

    <table class="table table-bordered  table-striped">
        <thead>
        <tr>
            <th>Avatar</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (isset($templateData['avatars']) and count($templateData['avatars']) > 0) { ?>

            <?php foreach($templateData['avatars'] as $avatar) {
                if ($avatar != null) {
                    $image = URL::base().'global/avatars/'. $avatar;
                }
                ?>
                <tr>
                    <td>
                        <p><img src="<?php echo $image; ?>" /></p>
                    </td>
                    <td>
                            <a class="btn btn-info" href="<?php echo URL::base().'avatarManager/importAvatar/'.$templateData['map']->id . '/' . base64_encode($avatar); ?>"><i class="icon-edit"></i>Add to labyrinth</a>
                    </td>
                </tr>

            <?php } ?>
        <?php } else { ?>
            <tr class="info">
                <td colspan="2">There are no avatars in this Labyrinth. You may add one, clicking the button above.</td>
            </tr>

        <?php } ?>
        </tbody>

    </table>



<?php } ?>