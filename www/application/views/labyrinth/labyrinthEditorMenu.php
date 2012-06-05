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
if(isset($templateData['map'])) { ?>
<p><a href="<?php echo URL::base().'renderLabyrinth/index/'.$templateData['map']->id; ?>" target="_blank"><?php echo __('preview'); ?></a></p>

<p><a href="<?php echo URL::base().'labyrinthManager/editMap/'.$templateData['map']->id; ?>"><?php echo __('editor'); ?></a><br><br>

    - <a href="<?php echo URL::base().'labyrinthManager/global/'.$templateData['map']->id; ?>"><?php echo __('global'); ?></a><br>
    - <a href="<?php echo URL::base().'nodeManager/index/'.$templateData['map']->id; ?>"><?php echo __('nodes'); ?></a><br>
    - <a href="<?php echo URL::base().'nodeManager/grid/'.$templateData['map']->id; ?>"><?php echo __('node grid'); ?></a><br>
    - <a href="<?php echo URL::base().'nodeManager/sections/'.$templateData['map']->id; ?>"><?php echo __('sections'); ?></a><br>
    - <a href="<?php echo URL::base().'linkManager/index/'.$templateData['map']->id; ?>"><?php echo __('links'); ?></a><br>
    - <a href="<?php echo URL::base().'counterManager/index/'.$templateData['map']->id; ?>"><?php echo __('counters'); ?></a><br>
    - <a href="<?php echo URL::base().'counterManager/grid/'.$templateData['map']->id; ?>"><?php echo __('counter grid'); ?></a><br>
    - <a href="<?php echo URL::base().'questionManager/index/'.$templateData['map']->id; ?>"><?php echo __('questions'); ?></a><br>
    - <a href="<?php echo URL::base().'chatManager/index/'.$templateData['map']->id; ?>"><?php echo __('chats'); ?></a><br>
    - <a href="<?php echo URL::base().'fileManager/index/'.$templateData['map']->id; ?>"><?php echo __('files'); ?></a><br>
    - <a href="<?php echo URL::base().'mapUserManager/index/'.$templateData['map']->id; ?>"><?php echo __('users'); ?></a><br>
    - <a href="<?php echo URL::base().'avatarManager/index/'.$templateData['map']->id; ?>"><?php echo __('avatars'); ?></a><br>
    - <a href="<?php echo URL::base().'elementManager/index/'.$templateData['map']->id; ?>"><?php echo __('elements'); ?></a><br>
    - <a href="<?php echo URL::base().'clusterManager/index/'.$templateData['map']->id; ?>"><?php echo __('clusters'); ?></a><br>
    - <a href="<?php echo URL::base().'feedbackManager/index/'.$templateData['map']->id; ?>"><?php echo __('feedback'); ?></a><br>
    - <a href="<?php echo URL::base().'reportManager/index/'.$templateData['map']->id; ?>"><?php echo __('sessions'); ?></a><br>
    - <a href="<?php echo URL::base().'visualManager/index/'.$templateData['map']->id; ?>" target="_blank"><?php echo __('visual editor'); ?></a>
</p>

<p><a href="<?php echo URL::base(); ?>labyrinthManager/showDevNotes/<?php echo $templateData['map']->id; ?>" onClick="window.open('<?php echo URL::base(); ?>labyrinthManager/showDevNotes/<?php echo $templateData['map']->id; ?>', 'notes', 'toolbar=no, directories=no, location=no, status=no, menubar=no, resizable=yes, scrollbars=yes, width=500, height=400'); return false"><img src='<?php echo URL::base(); ?>images/notes.gif' border='0' alt='author notes'></a></p>
<?php } ?>