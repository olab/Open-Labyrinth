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
<?php if(isset($templateData)) { ?>
    <h1><?php echo __('About'); ?></h1>
    <div class="control-group">
        <label class="control-label">Version: <?php echo Arr::get($templateData, 'version'); ?></label>
    </div>
    <div class="control-group">
        <label class="control-label">Main website: <a href="http://openlabyrinth.ca/">openlabyrinth.ca</a></label>
    </div>
<?php } ?>

<?php if(!empty($templateData['isSuperuser'])) { ?>
    <?php if(!empty($templateData['updaterIsAvailable'])) { ?>
    <iframe src="<?php echo URL::base(); ?>updater" width="100%" height="650" scrolling="auto" frameborder="1"></iframe>
    <?php }else{ ?>
    <div class="alert alert-warning">Updater package not found in the root directory (<?php echo $templateData['updaterDir']?>).</div>
    <?php } ?>
<?php } ?>