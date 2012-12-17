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
if (isset($map)) {
    ?>
    <form action="<?php echo URL::base(); ?>labyrinthManager/updateDevNodes/<?php echo $map->id; ?>" method="post" id="developer-notes-form">
        <input type="hidden" name="mapid" value="21">
        <textarea cols="120" rows="12" id="devnotes" name="devnotes" style="width: 97%"><?php echo $map->dev_notes; ?></textarea>
    </form>
    <script type="text/javascript">
        $('#devnotes').focus();
    </script>
    <?php
}