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
if (isset($templateData['scenario'])) {
    echo View::factory('webinar/_topMenu')->set('scenario', $templateData['scenario'])->set('webinars', $templateData['webinars']);
    $enabledMaps = Arr::get($templateData, 'enabledMaps', array());
    $steps = Arr::get($templateData, 'steps', array()); ?>

<script>
    var scenarioId = <?php echo $templateData['scenario']->id; ?>;
    var scenarioJSON = '<?php echo Arr::get($templateData,'scenarioJSON', null); ?>';
</script>
    
<div class="block">
    <div class="block" style="position: relative;" id="canvasContainer">
        <!-- left menu -->
        <div id="ve_actionButton" class="canvas-action-buttons-container">
            <p><button type="button" class="round-btn" id="fullScreen" data-toggle="tooltip" data-original-title="Full&nbsp;screen" data-placement="right"><i class="ve-icon-fullscreen"></i></button></p>
            <p><button type="button" class="round-btn update" id="update" data-toggle="tooltip" data-original-title="Save" data-placement="right"><i class="ve-icon-save"></i></button></p>
            <p class="text">Build</p>
            <p><button type="button" class="round-btn" id="addMap" data-toggle="tooltip" data-original-title="Add&nbsp;map" data-placement="right"><i class="ve-icon-add"></i></button></p>
            <p><button type="button" class="round-btn" id="addSection" data-toggle="tooltip" data-original-title="Add&nbsp;section" data-placement="right"><i class="ve-icon-template"></i></button></p>
            <p class="text">Move</p>
            <p><button type="button" class="round-btn active" id="vePan" data-toggle="tooltip" data-original-title="Grab+Pan" data-placement="right"><i class="ve-icon-pan"></i></button></p>
            <p><button type="button" class="round-btn" id="veSelect" data-toggle="tooltip" data-original-title="Select" data-placement="right"><i class="ve-icon-select"></i></button></p>
            <p><button type="button" class="round-btn" id="zoomIn" data-toggle="tooltip" data-original-title="Zoom&nbsp;In" data-placement="right"><i class="ve-icon-zoom-in"></i></button></p>
            <p><button type="button" class="round-btn" id="zoomOut" data-toggle="tooltip" data-original-title="Zoom&nbsp;out" data-placement="right"><i class="ve-icon-zoom-out"></i></button></p>
        </div>

        <!-- left additional menu, display after select some stuff -->
        <div id="ve_additionalActionButton" style="position: absolute; top: 5px; left: 85px; display: none;">
            <p><button type="button" class="round-btn delete" id="deleteSelected" data-toggle="tooltip" data-original-title="Delete&nbsp;selected" data-placement="right"><i class="ve-icon-delete"></i></button></p>
        </div>

        <div style="position: absolute;left:50%;z-index: 1500;" id="ve_message" class="alert alert-success hide"><span id="ve_message_text">Message</span></div>
        <canvas id="canvas" width="100" height="200" style="background-color: #cccccc" tabindex='1'>Not supported</canvas>
        <select class="visual-map-js" size="6">
            <?php foreach($enabledMaps as $map) { ?>
            <option value="<?php echo $map->id; ?>"><?php echo $map->name; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="modal hide block" id="visual_editor_link">
        <div class="modal-header block">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Link Manager</h3>
        </div>

        <div class="modal-body block" align="center">
            <div class="block" data-toggle="buttons-radio" id="linkTypes">
                <button type="button" class="btn" value="direct">Direct</button>
                <button type="button" class="btn" value="back">Back</button>
                <button type="button" class="btn" value="dual">Dual</button>
                <button type="button" class="btn btn-danger" value="delete">Delete</button>
            </div>
            <br/>
            <div class="block" align="left">
                <form class="form-horizontal">
                    <div class="control-group block">
                        <label class="control-label"><?php echo __('Link label'); ?></label>
                        <div class="controls block">
                            <input type="text" id="labelText"/>
                        </div>
                    </div>
                    <div class="control-group block">
                        <label class="control-label" for="mimage"><?php echo __('Link image'); ?></label>
                        <div class="controls block">
                            <?php if (isset($templateData['images']) and count($templateData['images']) > 0) { ?>
                                <select name="linkImage" id="mimage">
                                    <option value="0" <?php if (isset($templateData['editLink']) and $templateData['editLink']->image_id == NULL) echo 'selected=""'; ?>>no image</option>
                                    <?php foreach ($templateData['images'] as $image) { ?>
                                        <option value="<?php echo $image->id; ?>" <?php if (isset($templateData['editLink']) and $image->id == $templateData['editLink']->image_id) echo 'selected=""'; ?>><?php echo $image->name; ?> (<?php echo $image->id; ?>)</option>
                                    <?php } ?>
                                </select>
                            <?php } else { ?>
                                <select name="linkImage" id="mimage">
                                    <option value="0" select="">no image</option>
                                </select>
                            <?php } ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal-footer block">
            <a href="javascript:void(0);" class="btn" data-dismiss="modal" aria-hidden="true">Close</a>
            <a href="javascript:void(0);" class="btn" id="linkApply">Apply</a>
        </div>
    </div>

    <div class="visual-editor-select-right-panel scenario hide" id="veStepPanel">
        <div class="step-header">Step</div>
        <div class="step-body">
            <label>
                <strong>Select step:</strong>
                <br>
                <select id="stepSelect">
                <?php foreach($steps as $step){ ?>
                    <option value="<?php echo $step->id; ?>"><?php echo $step->name; ?></option>
                <?php } ?>
                </select>
            </label>

            <div class="step-settings">
                <label class="control-label"><strong>Step settings:</strong></label>
                <div class="controls">
                    <input type="text" id="stepName" style="margin-bottom: 20px;"/>
                    <div class="btn-group">
                        <button id="stepUpdate" class="btn btn-info">Update</button>
                        <button id="addToStep" class="btn btn-success">Add selected</button>
                        <button id="addStep" class="btn btn-success">Add step</button>
                        <button id="removeStep" class="btn btn-danger"><i class="icon-trash"></i></button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="<?php echo ScriptVersions::get(URL::base().'scripts/visualscenario/utils.js'); ?>"></script>
<script src="<?php echo ScriptVersions::get(URL::base().'scripts/visualscenario/mouse.js'); ?>"></script>
<script src="<?php echo ScriptVersions::get(URL::base().'scripts/visualscenario/transform.js'); ?>"></script>
<script src="<?php echo ScriptVersions::get(URL::base().'scripts/visualscenario/element.js'); ?>"></script>
<script src="<?php echo ScriptVersions::get(URL::base().'scripts/visualscenario/selector.js'); ?>"></script>
<script src="<?php echo ScriptVersions::get(URL::base().'scripts/visualscenario/visualEditor.js'); ?>"></script>
<script src="<?php echo ScriptVersions::get(URL::base().'scripts/visualscenario/application.js'); ?>"></script>

<?php } ?>