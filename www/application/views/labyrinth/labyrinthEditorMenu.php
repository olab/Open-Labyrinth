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
if (isset($templateData['map'])) {
    ?>
    <div class="well" style="padding: 8px 0;">
        <ul class="nav nav-list">
            <li class="nav-header">Labyrinth</li>
            <li><a href="<?php echo URL::base() . 'renderLabyrinth/index/' . $templateData['map']->id; ?>" target="_blank"><i class="icon-play"></i> <?php echo __('Play'); ?></a></li>
            <li><a href="<?php echo URL::base() . 'labyrinthManager/global/' . $templateData['map']->id; ?>"><i class="icon-edit"></i> <?php echo __('Edit'); ?></a></li>
            <li><a data-toggle="modal" href="#" data-target="#delete-labyrinth"><i class="icon-trash"></i> <?php echo __('Delete'); ?></a></li>
            <li><a href="<?php echo URL::base() . 'labyrinthManager/info/' . $templateData['map']->id; ?>"><i class="icon-info-sign"></i> <?php echo __('Information'); ?></a></li>
            <li><a data-toggle="modal" href="<?php echo URL::base(); ?>labyrinthManager/showDevNotes/<?php echo $templateData['map']->id; ?>" data-target="#developer-notes"><i class="icon-edit"></i> Notes</a></li>

            <li class="nav-header">Core Layout</li>
            <li><a href="<?php echo URL::base() . 'visualManager/index/' . $templateData['map']->id; ?>"><i class="icon-eye-open"></i> <?php echo __('Visual Editor'); ?></a></li>
            <li><a href="<?php echo URL::base() . 'nodeManager/index/' . $templateData['map']->id; ?>"><i class="icon-th-large"></i> <?php echo __('Nodes'); ?></a></li>
            <li><a href="<?php echo URL::base() . 'nodeManager/grid/' . $templateData['map']->id; ?>"><i class="icon-th"></i> <?php echo __('Node Grid'); ?></a></li>

            <li class="nav-header">Sub-Options</li>
            <li><a href="<?php echo URL::base() . 'nodeManager/sections/' . $templateData['map']->id; ?>"><i class="icon-th-list"></i> <?php echo __('Sections'); ?></a></li>
            <li><a href="<?php echo URL::base() . 'linkManager/index/' . $templateData['map']->id; ?>"><i class="icon-share"></i> <?php echo __('Links'); ?></a></li>
            <li><a href="<?php echo URL::base() . 'chatManager/index/' . $templateData['map']->id; ?>"><i class="icon-facetime-video"></i> <?php echo __('Chats'); ?></a></li>
            <li><a href="<?php echo URL::base() . 'questionManager/index/' . $templateData['map']->id; ?>"><i class="icon-question-sign"></i> <?php echo __('Questions'); ?></a></li>
            <li><a href="<?php echo URL::base() . 'avatarManager/index/' . $templateData['map']->id; ?>"><i class="icon-user"></i> <?php echo __('Avatars'); ?></a></li>
            <li><a href="<?php echo URL::base() . 'counterManager/index/' . $templateData['map']->id; ?>"><i class="icon-chevron-right"></i> <?php echo __('Counters'); ?></a></li>
            <li><a href="<?php echo URL::base() . 'counterManager/grid/' . $templateData['map']->id; ?>"><i class="icon-chevron-right"></i> <?php echo __('Counter Grid'); ?></a></li>
            <li><a href="<?php echo URL::base() . 'counterManager/rules/' . $templateData['map']->id; ?>"><i class="icon-chevron-right"></i> <?php echo __('Counters Rules'); ?></a></li>
            <li><a href="<?php echo URL::base() . 'elementManager/index/' . $templateData['map']->id; ?>"><i class="icon-fire"></i> <?php echo __('Elements'); ?></a></li>
            <li><a href="<?php echo URL::base() . 'clusterManager/index/' . $templateData['map']->id; ?>"><i class="icon-tags"></i> <?php echo __('Clusters'); ?></a></li>

            <li class="nav-header">Case Design</li>
            <li><a href="<?php echo URL::base() . 'feedbackManager/index/' . $templateData['map']->id; ?>"><i class="icon-comment"></i> <?php echo __('Feedback'); ?></a></li>
            <li><a href="<?php echo URL::base() . 'skinManager/index/' . $templateData['map']->id; ?>"><i class="icon-book"></i> <?php echo __('Skin'); ?></a></li>
            <li><a href="<?php echo URL::base() . 'fileManager/index/' . $templateData['map']->id; ?>"><i class="icon-file"></i> <?php echo __('Files'); ?></a></li>

            <li class="nav-header">Control</li>
            <li><a href="<?php echo URL::base() . 'mapUserManager/index/' . $templateData['map']->id; ?>"><i class="icon-user"></i> <?php echo __('Users'); ?></a></li>
            <li><a href="<?php echo URL::base() . 'reportManager/index/' . $templateData['map']->id; ?>"><i class="icon-calendar"></i> <?php echo __('Sessions'); ?></a></li>
        </ul>
    </div>
    <div class="modal hide fade" id="developer-notes">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Labyrinth Developer Notes</h3>
        </div>
        <div class="modal-body">
            <p>&nbsp;</p>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
            <a href="#" class="btn btn-primary">Save changes</a>
        </div>
    </div>
    <div class="modal hide alert alert-block alert-error fade in" id="delete-labyrinth">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="alert-heading"><?php echo __('Caution! Are you sure?'); ?></h4>
        </div>
        <div class="modal-body">
            <p><?php echo __('You have just clicked the delete button, are you certain that you wish to proceed with deleting this labyrinth from OpenLabyrinth?'); ?></p>
            <p>
                <a class="btn btn-danger" href="<?php echo URL::base() . 'labyrinthManager/disableMap/' . $templateData['map']->id; ?>"><?php echo __('Delete Labyrinth'); ?></a> <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
            </p>
        </div>
    </div>
    <?php
}
?>