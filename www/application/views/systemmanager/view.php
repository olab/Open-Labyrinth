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
if (isset($templateData)) { ?>
<h1><?php echo __('System Settings'); ?></h1>
<div id="tabs">
    <ul><?php
        foreach ($templateData['tabsName'] as $key => $tabsName) {
            echo '<li><a href="#tabs-'.$key.'">'.$tabsName.'</a></li>';
        } ?>
    </ul><?php
    foreach ($templateData['tabs'] as $key => $tabs) {
        echo '<div id="tabs-'.$key.'">';
        if(count(Notice::get('success'))) { ?>
            <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?php echo Notice::get('success'); ?>
            </div><?php
        }
        if(count(Notice::get('error'))) { ?>
            <div class="alert alert-error">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?php echo Notice::get('error'); ?>
            </div><?php
        }
        echo $tabs.'</div>';
    } ?>
</div><?php
} ?>