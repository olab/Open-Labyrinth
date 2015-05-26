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
<h3><?php echo __('Rolling Back Database'); ?></h3>
<?php echo $templateData['message'] ?>

<?php if(!empty($templateData['versions'])){ ?>
    <form action="<?php echo URL::base(true) ?>updatedatabase/doRollback" method="post" class="form-inline">
        <label for="toVersion">Please, choose version to roll back:</label>
        <select name="toVersion">
            <?php foreach($templateData['versions'] as $version){ ?>
                <option value="<?php echo $version ?>"><?php echo $version ?></option>
            <?php } ?>
        </select>
        <button type="submit" class="btn btn-danger">Roll Back!</button>
    </form>
<?php }else{ ?>
    <div class="alert alert-info">Roll back instructions was not found.</div>
<?php } ?>