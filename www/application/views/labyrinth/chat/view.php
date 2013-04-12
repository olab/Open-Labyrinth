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


if (isset($templateData['map'])) {
    ?>

    <script type="text/javascript">

    </script>
    <div class="page-header">
        <div class="pull-right"><a class="btn btn-primary"
                                   href="<?php echo URL::base() . 'chatManager/addChat/' . $templateData['map']->id; ?>"><i
                    class="icon-plus-sign"></i><?php echo __('Add Chat'); ?></a>
        </div>
        <h1><?php echo __('Chats "') . $templateData['map']->name . '"'; ?></h1></div>
    <table class="table table-striped table-bordered" id="my-labyrinths">
        <thead>
        <tr>
            <th>Code</th>
            <th>Stem</th>
            <th>Actions</th>
        </tr>
        </thead>

        <tbody>
        <?php if (isset($templateData['chats']) and count($templateData['chats']) > 0) { ?>
            <?php foreach ($templateData['chats'] as $chat) { ?>
                <tr>
                    <td><label><input readonly="readonly" class="span6 code" type="text"
                                      value="[[CHAT:<?php echo $chat->id; ?>]]"> </label></td>
                    <td><?php echo $chat->stem; ?></td>
                    <td>
                        <div class="btn-group">
                        <a class="btn btn-info"
                           href="<?php echo URL::base() . 'chatManager/editChat/' . $templateData['map']->id . '/' . $chat->id; ?>"><i class="icon-edit"></i><?php echo __('Edit'); ?></a>
                        <a class="btn btn-danger"
                           href="<?php echo URL::base() . 'chatManager/deleteChat/' . $templateData['map']->id . '/' . $chat->id; ?>"><i class="icon-trash"></i><?php echo __('Delete'); ?></a></div>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
<tr class="info">
<td colspan="3">There are no chats yet. Please click the button above to add one.</td>
</tr>


        <?php } ?>
        </tbody>
    </table>



<?php } ?>