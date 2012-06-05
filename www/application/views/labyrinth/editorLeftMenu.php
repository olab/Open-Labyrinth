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
<p><a href="<?php echo URL::base(); ?>collectionManager"><strong><?php echo __('Collections'); ?></strong></a><br>
<p><a href="<?php echo URL::base(); ?>authoredLabyrinth"><strong><?php echo __('Labyrinths I am Authoring'); ?></strong></a><br><br><br>

<a href="<?php echo URL::base(); ?>labyrinthManager/showDevNotes/<?php echo $templateData['map']->id; ?>" onclick="window.open('<?php echo URL::base(); ?>labyrinthManager/showDevNotes/<?php echo $templateData['map']->id; ?>', 'notes', 'toolbar=no, directories=no, location=no, status=no, menubar=no, resizable=yes, scrollbars=yes, width=500, height=400'); return false"><img src="<?php echo URL::base(); ?>images/notes.gif" border="0" alt="author notes"></a></p>