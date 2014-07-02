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
    $id_map = $templateData['map']->id; ?>
    <div class="well" style="padding: 8px 0;">
        <ul class="nav nav-list">
            <li class="nav-header">Labyrinth</li>
            <li class="top"><?php
                if(isset($templateData['rootNodeMap']) AND isset($templateData['rootNodeMap'][$id_map]) AND $templateData['rootNodeMap'][$id_map] != null) { ?>
                <a href="<?php echo URL::base().'renderLabyrinth/index/'.$id_map; ?>" target="_blank"><?php
                } else { ?>
                <a class="show-root-error" href="javascript:void(0)"><?php
                } ?>
                    <i class="icon-play icon-white"></i> <?php echo __('Play'); ?>
                </a>
                <div class="pull-right arrow"></div>
            </li>
            <li><a href="<?php echo URL::base().'labyrinthManager/global/'.$id_map; ?>"><i class="icon-edit"></i> <?php echo __('Details'); ?></a></li>
            <li><a data-toggle="modal" href="#" data-target="#delete-labyrinth"><i class="icon-trash"></i> <?php echo __('Delete'); ?></a></li>
            <li class="nav-header">Core Layout</li>
            <li><a href="<?php echo URL::base().'visualManager/index/'.$id_map; ?>"><i class="icon-eye-open"></i> <?php echo __('Visual Editor'); ?></a></li>
            <li><a href="<?php echo URL::base().'nodeManager/index/'.$id_map; ?>"><i class="icon-circle-blank"></i> <?php echo __('Nodes'); ?></a></li>
            <li><a href="<?php echo URL::base().'nodeManager/grid/'.$id_map.'/1'; ?>"><i class="icon-th"></i> <?php echo __('Node Grid'); ?></a></li>
            <li><a href="<?php echo URL::base().'linkManager/index/'.$id_map; ?>"><i class="icon-link"></i> <?php echo __('Links'); ?></a></li>
            <li class="nav-header">Sub-Options</li>
            <li><a href="<?php echo URL::base().'nodeManager/sections/'.$id_map; ?>"><i class="icon-th-list"></i> <?php echo __('Sections'); ?></a></li>
            <li><a href="<?php echo URL::base().'chatManager/index/'.$id_map; ?>"><i class="icon-comments-alt"></i> <?php echo __('Chats'); ?></a></li>
            <li><a href="<?php echo URL::base().'questionManager/index/'.$id_map; ?>"><i class="icon-question-sign"></i> <?php echo __('Questions'); ?></a></li>
            <li><a href="<?php echo URL::base().'avatarManager/index/'.$id_map; ?>"><i class="icon-user"></i> <?php echo __('Avatars'); ?></a></li>
            <li><a href="<?php echo URL::base().'counterManager/index/'.$id_map; ?>"><i class="icon-dashboard"></i> <?php echo __('Counters'); ?></a></li>
            <li><a href="<?php echo URL::base().'counterManager/grid/'.$id_map; ?>"><i class="icon-th-large"></i> <?php echo __('Counter Grid'); ?></a></li>
            <li><a href="<?php echo URL::base().'visualdisplaymanager/index/'.$id_map; ?>"><i class="icon-eye-open icon-white"></i> <?php echo __('Counter Displays'); ?></a><div class="pull-right arrow"></div></li>
            <li><a href="<?php echo URL::base().'counterManager/rules/'.$id_map; ?>"><i class="icon-check"></i> <?php echo __('Rules'); ?></a></li>
            <li><a href="<?php echo URL::base().'popupManager/index/'.$id_map; ?>"><i class="icon-envelope"></i> <?php echo __('Pop-up messages'); ?></a></li>
            <li><a href="<?php echo URL::base().'elementManager/index/'.$id_map; ?>"><i class="icon-stethoscope"></i> <?php echo __('Elements'); ?></a></li>
            <li><a href="<?php echo URL::base().'clusterManager/index/'.$id_map; ?>"><i class="icon-tags"></i> <?php echo __('Clusters'); ?></a></li>
            <li><a href="<?php echo URL::base().'patient/labyrinth/'.$id_map; ?>"><i class="icon-user"></i> <?php echo __('Virtual patient'); ?></a></li>
            <li class="nav-header">Case Design</li>
            <li><a href="<?php echo URL::base().'feedbackManager/index/'.$id_map; ?>"><i class="icon-comment"></i> <?php echo __('Feedback'); ?></a></li>
            <li><a href="<?php echo URL::base().'skinManager/index/'.$id_map; ?>"><i class="icon-book"></i> <?php echo __('Skin'); ?></a></li>
            <li><a href="<?php echo URL::base().'fileManager/index/'.$id_map; ?>"><i class="icon-file"></i> <?php echo __('Files'); ?></a></li>
            <li class="nav-header">Control</li>
            <li><a href="<?php echo URL::base().'mapUserManager/index/'.$id_map; ?>"><i class="icon-user"></i> <?php echo __('Users'); ?></a></li>
            <li><a href="<?php echo URL::base().'reportManager/index/'.$id_map; ?>"><i class="icon-calendar"></i> <?php echo __('Sessions'); ?></a></li>
            <li class="nav-header">Global elements</li>
            <li><a href="<?php echo URL::base().'fileManager/globalFiles/'.$id_map; ?>"><i class="icon-file"></i> <?php echo __('Global files'); ?></a></li>
            <li><a href="<?php echo URL::base().'avatarManager/globalAvatars/'.$id_map; ?>"><i class="icon-user"></i> <?php echo __('Global avatars'); ?></a></li>
            <li><a href="<?php echo URL::base().'questionManager/globalQuestions/'.$id_map; ?>"><i class="icon-question-sign"></i> <?php echo __('Global questions'); ?></a></li>
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
                <a class="btn btn-danger" href="<?php echo URL::base().'labyrinthManager/disableMap/'.$id_map; ?>"><?php echo __('Delete Labyrinth'); ?></a>
                <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
            </p>
        </div>
    </div>
    <div class="modal hide fade in" id="readonly-notice">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="alert-heading"><?php echo __('Are you sure?'); ?></h4>
        </div>
        <div class="modal-body">
            <p><?php echo __('You try go to the page on which already another author is working, you can go there with "Read-only" permission.'); ?></p>
            <p>
                <a class="btn btn-primary" href="javascript:void(0);"><?php echo __('Enter with Read-only'); ?></a>
                <button class="btn btn-primary" id="discard">Discard</button>
                <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
            </p>
        </div>
    </div>

    <div class="modal hide fade" id="discardWarning">
        <div class="modal-header">
            <h4 class="alert-heading">Superuser discard you.</h4>
        </div>
        <button class="btn btn-primary" id="discardReload">Reload</button>
    </div><?php
}; ?>