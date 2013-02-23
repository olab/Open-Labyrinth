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

                <h1><?php echo __('Chats "') . $templateData['map']->name . '"'; ?></h1>
                <table class="table table-striped table-bordered" id="my-labyrinths">
                    <thead>
                    <tr>
                        <th>Code</th>
                        <th>Stem</th>
                        <th>Actions</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php if(isset($templateData['chats']) and count($templateData['chats']) > 0) { ?>
                        <?php foreach($templateData['chats'] as $chat) { ?>
                            <tr><td><label><input readonly="readonly" class="span6 code" type="text" value="[[CHAT:<?php echo $chat->id; ?>]]"> </label></td>
                                <td><?php echo $chat->stem; ?></td>
                                <td>
                               <a class="btn btn-primary" href="<?php echo URL::base().'chatManager/editChat/'.$templateData['map']->id.'/'.$chat->id.'/'.count($chat->elements); ?>"><?php echo __('edit'); ?></a>
                               <a class="btn btn-danger" href="<?php echo URL::base().'chatManager/deleteChat/'.$templateData['map']->id.'/'.$chat->id; ?>"><?php echo __('delete'); ?></a></td></tr>
                        <?php } ?>
                    <?php } ?>
                    </tbody>
                    </table>


                             <a class="btn btn-primary" href="<?php echo URL::base().'chatManager/addChat/'.$templateData['map']->id; ?>"><?php echo __('Add Chat'); ?></a>

<?php } ?>