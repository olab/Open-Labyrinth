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
?>
    <script language="JavaScript">
        function toggle_visibility(id) {
            var e = document.getElementById(id);
            if(e.style.display == 'none')
                e.style.display = 'block';
            else
                e.style.display = 'none';
        }
    </script>    
    <table width="100%" height="100%" cellpadding='6'>
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('Collections'); ?></h4>
                <p><a href="<?php echo URL::base(); ?>collectionManager/addCollection">add Collection</a></p>
                <?php if (isset($templateData['collections']) and count($templateData['collections']) > 0) { ?>
                <table width="100%" cellpadding="0">
                    <tr bgcolor="#ffffff"><td>
                            <?php foreach($templateData['collections'] as $collection) { ?>
                            <p><a href="#" onclick="toggle_visibility('<?php echo $collection->id; ?>');"><strong><?php echo $collection->name; ?></strong> (<?php echo $collection->id; ?>)</a> - [<a href="<?php echo URL::base() ?>collectionManager/editCollection/<?php echo $collection->id; ?>"><?php echo __('edit'); ?></a>]</p>
                            <div id="<?php echo $collection->id; ?>" style="display:none">
                                <?php if(count($collection->maps) > 0) { ?>
                                <table width="100%">
                                    <?php foreach($collection->maps as $mp) { ?>
                                    <tr bgcolor="#f3f3fa">
                                        <td width="30%"><p><a href="<?php echo URL::base() ?>renderLabyrinth/index/<?php echo $mp->map->id; ?>"><?php echo $mp->map->name; ?></a></p></td>
                                        <td width="10%"><p><a href="<?php echo URL::base(); ?>labyrinthManager/editMap/<?php echo $mp->map_id; ?>"><img src="<?php echo URL::base(); ?>images/editl.jpg" border="0" alt="edit"></a></p></td>
                                        <td width="30%"><p></p></td>
                                        <td width="30%"><p><?php echo $mp->map->abstract; ?></p></td>
                                    </tr>
                                    <?php } ?>
                                </table>
                                <?php } ?> 
                            </div>
                            <?php } ?>
                        </td></tr>
                </table>
                <?php } ?>
            </td>
        </tr>
    </table>