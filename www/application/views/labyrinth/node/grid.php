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
if (isset($templateData['map'])) { ?>
    <div class="page-header">
    <h1><?php echo __('NodeGrid "') . $templateData['map']->name . '"'; ?></h1></div>

    <div class="main-edit-panel">
        <div class="header"></div>
        <div class="edit-content">
            <div class="edit-controls">
                <div class="edit-row">
                    <div class="edit-cell">Find:</div>
                    <div class="edit-cell"><input type="text" id="findWhat" value=""/></div>
                    <div class="edit-cell">
                        <button class="btn previous-btn"><i class="icon-arrow-left icon-white"></i>Previous</button>
                        <button id="tipsForNextButton" class="btn next-btn" data-placement="bottom" data-trigger="manual" title="Then click on this button to find something."><i class="icon-arrow-right icon-white"></i>Next</button>
                    </div>
                </div>
                <div class="edit-row">
                    <div class="edit-cell">Replace:</div>
                    <div class="edit-cell"><input type="text" id="replaceWith" value=""/></div>
                    <div class="edit-cell">
                        <button class="btn replace-btn">Replace</button>
                        <button class="btn replace-all-btn">Replace All</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal hide block" id="leaveBox">
        <div class="modal-header block">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeLeaveBox">&times;</button>
            <h3>Unsaved data</h3>
        </div>

        <div class="modal-body block" align="center">
            <p>You have unsaved data</p>
        </div>

        <div class="modal-footer block">
            <a href="javascript:void(0);" class="btn" id="uploadUnsaved">Save</a>
            <a href="javascript:void(0);" class="btn" id="leave">Leave without saving</a>
        </div>
    </div>

    <form id="grid_from" class="form-horizontal" action="<?php echo URL::base() . 'nodeManager/saveGrid/' . $templateData['map']->id; ?>" method="POST">
        <input type="hidden" id="orderBy" name="orderBy" value="<?php if(isset($templateData['orderBy'])) echo $templateData['orderBy']; ?>"/>
        <input type="hidden" id="logicSort" name="logicSort" value="<?php if(isset($templateData['logicSort'])) echo $templateData['logicSort']; ?>"/>

        <div class="control-group">
            <label class="control-label" style="text-align: left;width: 80px;"><b>Logic sort:</b></label>
            <div class="controls" style="margin-left: 80px;">
                <div class="radio_extended btn-group">
                    <input type="radio" id="logicSortRadioOn" name="logicSortRadio" value="1" <?php if(isset($templateData['logicSort']) && $templateData['logicSort'] == 1) echo 'checked="checked"'; ?>>
                    <label data-class="btn-info" class="btn logic-btn" on="on" for="logicSortRadioOn">On</label>
                    <input type="radio" id="logicSortRadioOff" name="logicSortRadio" value="0" <?php if(!isset($templateData['logicSort']) || $templateData['logicSort'] == 0) echo 'checked="checked"'; ?>>
                    <label data-class="btn-danger" class="btn logic-btn off" on="off" for="logicSortRadioOff">Off</label>
                </div>
            </div>
        </div>

        <table class="table table-striped table-bordered persist-area">
            <thead>
                <tr class="persist-header">
                    <th>
                        <a href="javascript:void(0)" class="sort-btn" orderBy="<?php echo isset($templateData['orderBy']) && $templateData['orderBy'] == 1 ? '2' : '1'; ?>">
                            Node ID <div class="pull-right"><i class="icon-chevron-<?php if(isset($templateData['orderBy']) && $templateData['orderBy'] == 1) echo 'down';  else  echo 'up'; ?> icon-white"></i></div>
                        </a>
                    </th>
                    <th>
                        <a href="javascript:void(0)" class="sort-btn" orderBy="<?php echo isset($templateData['orderBy']) && $templateData['orderBy'] == 3 ? '4' : '3'; ?>">
                            Title <div class="pull-right"><i class="icon-chevron-<?php if(isset($templateData['orderBy']) && $templateData['orderBy'] == 3) echo 'down';  else  echo 'up'; ?> icon-white"></i></div>
                        </a>
                    </th>
                    <th>Text</th>
                    <th>Info</th>
                    <th>
                        <a href="javascript:void(0)" class="sort-btn" orderBy="<?php echo isset($templateData['orderBy']) && $templateData['orderBy'] == 5 ? '6' : '5'; ?>">
                            X <div class="pull-right"><i class="icon-chevron-<?php if(isset($templateData['orderBy']) && $templateData['orderBy'] == 5) echo 'down';  else  echo 'up'; ?> icon-white"></i></div>
                        </a>
                    </th>
                    <th>
                        <a href="javascript:void(0)" class="sort-btn" orderBy="<?php echo isset($templateData['orderBy']) && $templateData['orderBy'] == 7 ? '8' : '7'; ?>">
                            Y <div class="pull-right"><i class="icon-chevron-<?php if(isset($templateData['orderBy']) && $templateData['orderBy'] == 7) echo 'down';  else  echo 'up'; ?> icon-white"></i></div>
                        </a>
                    </th>
                    <th>
                        <a href="javascript:void(0)" class="sort-btn" orderBy="<?php echo isset($templateData['orderBy']) && $templateData['orderBy'] == 9 ? '10' : '9'; ?>">
                            Section ID <div class="pull-right"><i class="icon-chevron-<?php if(isset($templateData['orderBy']) && $templateData['orderBy'] == 9) echo 'down';  else  echo 'up'; ?> icon-white"></i></div>
                        </a>
                    </th>
                </tr>
            </thead>
            
            <tbody>
                <?php if (isset($templateData['nodes']) and count($templateData['nodes']) > 0) { ?>
                <?php foreach ($templateData['nodes'] as $node) { ?>
                <tr>
                    <td><?php echo $node->id; ?> <?php if ($node->type->name == 'root') echo __('(root)'); ?></td>
                    <td><textarea class="search-textarea" id="title_<?php echo $node->id; ?>" name="title_<?php echo $node->id; ?>"><?php echo $node->title; ?></textarea></td>
                    <td><textarea class="search-textarea" id="text_<?php echo $node->id; ?>" name="text_<?php echo $node->id; ?>"><?php echo $node->text; ?></textarea></td>
                    <td><textarea class="search-textarea" id="info_<?php echo $node->id; ?>" name="info_<?php echo $node->id; ?>"><?php echo $node->info; ?></textarea></td>
                    <td><?php echo $node->x; ?></td>
                    <td><?php echo $node->y; ?></td>
                    <td>
                        <?php if(count($node->sections) > 0) { ?>
                        <?php foreach($node->sections as $section) { ?>
                        <div><?php echo $section->section_id; ?> </div>
                        <?php } ?>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
                <?php } ?>
            </tbody>
        </table>
        
        
        <div class="form-actions">
            <div class="pull-right">
                <input class="btn btn-primary btn-large" type="submit" name="Submit" value="<?php echo __('Save changes'); ?>">
            </div>
        </div>
    </form>

<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/replacer.js'); ?>"></script>
<?php } ?>