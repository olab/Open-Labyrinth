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
if (isset($openLabyrinths)) { ?>
    <h3><?php echo __('Open Labyrinths'); ?></h3>
    <ul class="unstyled"><?php
        foreach ($openLabyrinths as $labyrinth) { ?>
        <li>
            <i class="icon-arrow-right"></i>
            <a href="<?php echo URL::base().'renderLabyrinth/index/'.$labyrinth->id; ?>"><?php echo $labyrinth->name; ?></a><?php
            if (isset($templateData['bookmarks'][$labyrinth->id])) { ?>
            <a class="btn btn-warning" href="<?php echo URL::base().'renderLabyrinth/resume/'.$labyrinth->id; ?>">
                <i class="icon-refresh icon-white"></i>Resume
            </a><?php
            }
            ?>
        </li><?php
        } ?>
    </ul><?php
} ?>