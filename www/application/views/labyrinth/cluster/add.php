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
    <table width="100%" height="100%" cellpadding='6'>
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('data clusters for Labyrinth "') . $templateData['map']->name . '"'; ?></h4>
                <table bgcolor='#ffffff'>
                    <tr>
                        <td>
                            <form method='post' action='<?php echo URL::base(); ?>clusterManager/saveNewDam/<?php echo $templateData['map']->id; ?>'>
                            <p>Data cluster name: <input type='text' name='damname'><input type='submit' value='add' /></p>
                            </form>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>

