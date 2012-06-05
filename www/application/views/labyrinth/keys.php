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
if(isset($templateData['map'])) { ?>
<table width="100%" height="100%" cellpadding='6'>
    <tr>
        <td valign="top" bgcolor="#bbbbcb">
            <h4><?php echo __('Keys for Labyrinth'); ?></h4>
            <table width="100%" cellpadding="6">
                <tr bgcolor="#ffffff"><td align="left">
                        <p><?php echo __('note that adding a key here will require any user to enter a key when running this Labyrinth - it will not be able to be run without one of the keys entered here. To disable this feature delete all keys and reset Labyrinth security to something other than requiring keys (click global in the menu)'); ?></p>
                        <form action="<?php if($templateData['keyCount'] > 1){
                                echo URL::base().'labyrinthManager/saveKeys/'.$templateData['map']->id.'/'.$templateData['keyCount'];
                            } else {
                                echo URL::base().'labyrinthManager/saveKeys/'.$templateData['map']->id;
                            }
                            ?>" method="post">
     
                            <p><?php echo __('key value'); ?></p>
                            <?php if(isset($templateData['currentKeys'])) { $i=1; ?>
                            <?php foreach($templateData['currentKeys'] as $key) { ?>
                                <p>Key <?php echo $i++; ?> <input type="text" name="key_<?php echo $key->id; ?>" value="<?php echo $key->key; ?>"> - <a href="<?php echo URL::base().'labyrinthManager/deleteKey/'.$templateData['map']->id.'/'.$key->id; ?>"><?php echo __('delete'); ?></a></p>
                            <?php } ?>
                            <?php } ?>
                            <?php if(isset($templateData['keyCount'])) { ?>
                            <?php for($i = 0; $i < $templateData['keyCount']-1; $i++) { ?>
                                <p>New Key <?php echo $i+1; ?> <input type="text" name="akey_<?php echo $i+1; ?>" value=""></p>
                            <?php } } ?>
                            <input type="submit" name="KeysSubmit" value="<?php echo __('submit'); ?>">
                        </form>
                        <p><a href="<?php if(isset($templateData['keyCount'])) echo URL::base().'labyrinthManager/editKeys/'.$templateData['map']->id.'/'.$templateData['keyCount']; ?>"><?php echo __('add'); ?></a> - <a href="<?php echo URL::base().'labyrinthManager/editKeys/'.$templateData['map']->id; ?>"><?php echo __('reset'); ?></a></p>
                    </td></tr>
            </table>
        </td>
    </tr>
</table>
<?php } ?>