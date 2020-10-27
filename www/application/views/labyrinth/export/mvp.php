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
if (count($templateData['maps'])) { ?>
    <h1><?php echo __('Labyrinths'); ?></h1>
    <table  class="table table-striped table-bordered" id="my-labyrinths">
        <colgroup>
            <col style="width: 70%" />
            <col style="width: 30%" />
        </colgroup>
        <thead>
            <tr>
                <th><?php echo __('Labyrinth Title'); ?></th>
                <th><?php echo __('Actions'); ?></th>
            </tr>
        </thead>
        <tbody><?php
            foreach ($templateData['maps'] as $map) {
                $exportType = $templateData['exportType']; ?>
                <tr>
                    <td><?php echo $map->name; ?></td>
                    <td class="center">
                        <a class="btn btn-info" href="<?php echo URL::base().'exportimportmanager/'.$exportType.'/'.$map->id; ?>">
                            <i class="icon-edit icon-white"></i>Export
                        </a>
                    </td>
                </tr><?php
            } ?>
        </tbody>
    </table><?php
} else { ?>
    <div class="alert alert-info">
        <p class="lead"><?php echo __('You do not appear to have any labyrinths authored at this time.'); ?></p>
        <p><?php echo __('Now is as good-a-time as any to click the Create Labyrinth button above.'); ?></p>
    </div><?php
} ?>

