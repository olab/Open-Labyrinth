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
<hr />

<form action="<?php echo URL::base(); ?>home/search" method="POST">
    <p><input type="text" name="searchterm" size="10" />
        <input id="SeachSubmit" type="submit" value="Search" /><br />
        <?php echo __('title'); ?>:<input name="scope" type="radio" value="t" checked />&nbsp;&nbsp;<?php echo __('all'); ?>:<input name="scope" type="radio" value="a" />
    </p>
</form>

<hr />

<p><?php echo __('logged in as'); ?>&nbsp;<?php if(isset($templateData['username'])) echo $templateData['username']; ?>
    <br /><a href="<?php echo URL::base(); ?>home/changePassword"><?php echo __('change password'); ?></a>
    <br /><a href="<?php echo URL::base(); ?>home/logout"><?php echo __('logout'); ?></a></p>