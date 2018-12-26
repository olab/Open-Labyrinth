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
<h1><?php echo __('Search on term "'); ?><?php if(isset($term)) echo $term; ?>"</h1>
<div>Found <?php if(isset($maps)) { echo count($maps); } else { echo 0; } ?> labyrinths</div>
<?php if(isset($maps) and count($maps) > 0) { ?>
<table  class="table table-striped table-bordered" id="my-labyrinths">
    <colgroup>
        <col style="width: 30%" />
        <col style="width: 30%" />
        <col style="width: 20%" />
        <col style="width: 20%" />
    </colgroup>
    <thead>
    <tr>
        <th><?php echo __('Labyrinth Title'); ?></th>
        <th><?php echo __('Description'); ?></th>
        <th><?php echo __('Contributors'); ?></th>
        <th><?php echo __('Actions'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($maps as $map) {
        ?>
        <tr>
            <td>
                <a href="<?php echo URL::base(); ?>labyrinthManager/info/<?php echo $map->id; ?>"><?php echo $map->name; ?></a>

            </td>
            <td><?php echo $map->abstract; ?></td>
            <td>
                <?php
                if (count($map->contributors) > 0) {
                    $contributors = array();
                    foreach ($map->contributors as $contributor) {
                        $contributors[] = '<a href="#" rel="tooltip" title="' . ucwords($contributor->role->name) . '">' . $contributor->name . '</a>';
                    }
                    echo implode(', ', $contributors);
                }
                ?>
            </td>
            <td class="center">
                <div class="btn-group">
                    <?php if((isset($rootNodes[$map->id])) && ($rootNodes[$map->id] != null)) { ?>
                        <a class="btn btn-success" href="<?php echo URL::base(); ?>renderLabyrinth/index/<?php echo $map->id; ?>">
                            <i class="icon-play icon-white"></i>
                            <span class="visible-desktop">Play</span>
                        </a>
                    <?php } else { ?>
                        <a class="btn btn-success show-root-error" href="javascript:void(0)">
                            <i class="icon-play icon-white"></i>
                            <span class="visible-desktop">Play</span>
                        </a>
                    <?php } ?>
                    <a class="btn btn-info" href="<?php echo URL::base() . 'labyrinthManager/global/' . $map->id; ?>">
                        <i class="icon-edit icon-white"></i>
                        Edit
                    </a>
                </div>
            </td>
        </tr>
    <?php
    }
    ?>
    </tbody>
</table>
<?php } ?>
