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

<h1><?php echo __('Collections'); ?></h1>
    <table class="table table-striped table-bordered">



                <?php if (isset($templateData['collections']) and count($templateData['collections']) > 0) { ?>


                            <?php foreach($templateData['collections'] as $collection) { ?>
                <tr><td>
                        <a href="#" onclick="toggle_visibility('<?php echo $collection->id; ?>');"><?php echo $collection->name; ?> (<?php echo $collection->id; ?>)</a> <a class="btn btn-primary" href="<?php echo URL::base() ?>collectionManager/editCollection/<?php echo $collection->id; ?>"><?php echo __('edit'); ?></a>


                        <div id="<?php echo $collection->id; ?>" style="display:none">
                            <?php if(count($collection->maps) > 0) { ?>
                                <table>
                                    <?php foreach($collection->maps as $mp) { ?>
                                        <tr>
                                            <td><a href="<?php echo URL::base() ?>renderLabyrinth/index/<?php echo $mp->map->id; ?>"><?php echo $mp->map->name; ?></a></td>
                                            <td><a class="btn btn-primary" href="<?php echo URL::base(); ?>labyrinthManager/editMap/<?php echo $mp->map_id; ?>"><?php echo __('edit');?></a></td>

                                            <td><?php echo $mp->map->abstract; ?></td>
                                        </tr>
                                    <?php } ?>
                                </table>
                            <?php } ?>
                        </div>


                </td>

                </tr>


                            <?php } ?>


                <?php } ?>

    </table>

<a href="<?php echo URL::base(); ?>collectionManager/addCollection">add Collection</a>