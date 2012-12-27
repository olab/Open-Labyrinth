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
                  <h1><?php echo __('edit users of Labyrinth "') . $templateData['map']->name . '"'; ?></h1>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff"><td>
                            <table cellpadding="1">
                                <tr><td colspan="3">
                                        <p><strong><?php echo __('Authors'); ?></strong></p>
                                        <p><?php echo Auth::instance()->get_user()->nickname; ?> (<?php echo __('you cannot remove or add yourself'); ?>)</p>
                                        <?php if(isset($templateData['existUsers']) and count($templateData['existUsers']) > 0) { ?>
                                            <?php foreach($templateData['existUsers'] as $exUser) { ?>
                                                <?php if($exUser->type->name == 'superuser' or $exUser->type->name == 'author') { ?>
                                                    <p><?php echo $exUser->nickname; ?> [<a href="<?php echo URL::base().'mapUserManager/deleteUser/'.$templateData['map']->id.'/'.$exUser->id; ?>"><?php echo __('delete'); ?></a>]</p>
                                                <?php } ?>
                                            <?php } ?>
                                        <?php } ?> 
                                    </td></tr>
                                <form method="POST" action="<?php echo URL::base().'mapUserManager/addUser/'.$templateData['map']->id; ?>">
                                <tr><td><p>add authors</p></td><td>
                                        <select name="mapuserID">
                                            <option value=""><?php echo __('select'); ?> ...</option>
                                            <?php if(isset($templateData['admins']) and count($templateData['admins']) > 0) { ?>
                                                <?php foreach($templateData['admins'] as $admin) { ?>
                                                    <?php if($admin->id != Auth::instance()->get_user()->id) { ?>
                                                    <option value="<?php echo $admin->id; ?>"><?php echo $admin->nickname; ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php if(isset($templateData['authors']) and count($templateData['authors']) > 0) { ?>
                                                <?php foreach($templateData['authors'] as $author) { ?>
                                                    <?php if($author->id != Auth::instance()->get_user()->id) { ?>
                                                    <option value="<?php echo $author->id; ?>"><?php echo $author->nickname; ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                        </select></td>
                                    <td><input type="submit" name="Submit" value="<?php echo __('submit'); ?>"></td>
                                </tr>
                                </form>
                                <tr><td colspan="3"><hr></td></tr>
                                <tr><td colspan="3">
                                        <p><strong>Learners</strong></p>
                                        <?php if(isset($templateData['existUsers']) and count($templateData['existUsers']) > 0) { ?>
                                            <?php foreach($templateData['existUsers'] as $exUser) { ?>
                                                <?php if($exUser->type->name == 'learner') { ?>
                                                    <p><?php echo $exUser->nickname; ?> [<a href="<?php echo URL::base().'mapUserManager/deleteUser/'.$templateData['map']->id.'/'.$exUser->id; ?>">delete</a>]</p>
                                                <?php } ?>
                                            <?php } ?>
                                        <?php } ?> 
                                    </td></tr>
                                <form method="POST" action="<?php echo URL::base().'mapUserManager/addUser/'.$templateData['map']->id; ?>">
                                <tr>
                                    <td>
                                        <p>add learners</p>
                                    </td>                             
                                    <td>
                                        <select name="mapuserID">
                                            <option value=""><?php echo __('select'); ?> ...</option>
                                            <?php if(isset($templateData['learners']) and count($templateData['learners']) > 0) { ?>
                                                <?php foreach($templateData['learners'] as $learner) { ?>
                                                    <?php if($learner->id != Auth::instance()->get_user()->id) { ?>
                                                    <option value="<?php echo $learner->id; ?>"><?php echo $learner->nickname; ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                        </select></td>
                                    <td><input type="submit" name="Submit" value="<?php echo __('submit'); ?>"></td>
                                </tr>
                                </form>
                            </table>
                        </td></tr>
                </table>

<?php } ?>


