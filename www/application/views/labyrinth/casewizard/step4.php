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
<script language="javascript" type="text/javascript"
            src="<?php echo URL::base(); ?>scripts/tinymce/jscripts/tiny_mce/tiny_mce.js"
            xmlns="http://www.w3.org/1999/html"></script>
    <script type="text/javascript">
        var sendURL = '<?php echo URL::base(); ?>labyrinthManager/caseWizard/3/updateVisualEditor/<?php echo $templateData['map']; ?>';
        var autoSaveURL = '<?php echo URL::base(); ?>visualManager/autoSave';
        var bufferCopy = '<?php echo URL::base(); ?>visualManager/bufferCopy';
        var bufferPaste = '<?php echo URL::base(); ?>visualManager/bufferPaste';
        var mapId = <?php echo $templateData['map']; ?>;
        var mapJSON = <?php echo (isset($templateData['mapJSON']) && strlen($templateData['mapJSON']) > 0) ? $templateData['mapJSON'] : 'null'; ?>;
        var saveMapJSON = <?php echo (isset($templateData['saveMapJSON']) && strlen($templateData['saveMapJSON']) > 0) ? $templateData['saveMapJSON'] : 'null'; ?>;
        var mapType = <?php if(isset($templateData['mapModel'])) echo $templateData['mapModel']->type_id; ?>;
    </script>
<h1><?php echo __('Step 3. Add your story'); ?></h1>
<div>
    <div class="wizard_body">
        <div class="instractions">
            <p class="header">Instructions:</p>

            <p class="li">This is hight level editing. Just get the main points of your story down one idea per
                node.</p>
        </div>
        <div class="visual-editor">
            <div class="block" style="position: relative;" id="canvasContainer">
            <div id="ve_actionButton" style="position: absolute; top: 5px; left: 5px">
                <p><button type="button" class="round-btn" id="update" data-toggle="tooltip" data-original-title="Update" data-placement="right"><i class="ve-icon-save"></i></button></p>
                <p><button type="button" class="round-btn" id="addNode" data-toggle="tooltip" data-original-title="<div style='width: 50px'>Add node</div>" data-placement="right"><i class="ve-icon-add"></i></button></p>
                <p><button type="button" class="round-btn active" id="vePan" data-toggle="tooltip" data-original-title="<div style='width: 50px'>Pan mode</div>" data-placement="right"><i class="ve-icon-pan"></i></button></p>
                <p><button type="button" class="round-btn" id="veSelect" data-toggle="tooltip" data-original-title="Select&nbsp;mode" data-placement="right"><i class="ve-icon-select"></i></button></p>
                <p><button type="button" class="round-btn" id="veTemplate" data-toggle="tooltip" data-original-title="<div style='width: 90px'>Insert&nbsp;pre-template</div>" data-placement="right"><i class="ve-icon-template"></i></button></p>
                <p><button type="button" class="round-btn" id="zoomIn" data-toggle="tooltip" data-original-title="Zoom&nbsp;In" data-placement="right"><i class="ve-icon-zoom-in"></i></button></p>
                <p><button type="button" class="round-btn" id="zoomOut" data-toggle="tooltip" data-original-title="Zoom&nbsp;out" data-placement="right"><i class="ve-icon-zoom-out"></i></button></p>
            </div>
            
            <div style="position: absolute;left:50%;z-index: 1500;" id="ve_message" class="alert alert-success hide"><button type="button" class="close" data-dismiss="alert">&times;</button><span id="ve_message_text">Message</span></div>
            <canvas id="canvas" width="100" height="800" style="background-color: #cccccc" tabindex='1'>Not supported</canvas>
            <div class="visual-editor-right-panel hide" id="veRightPanel">
                <div class="pull-right"><button type="button" class="close veRightPanelCloseBtn">&times;</button></div>
                <div class="block" style="width: 480px;">
                    <div class="visual-editor-right-panel-tabs">
                        <ul class="nav nav-tabs ">
                            <li><a href="#actions" data-toggle="tab">Actions</a></li>
                            <li class="active"><a href="#content" data-toggle="tab">Node Content</a></li>
                        </ul>
                    </div>
                    
                    <div class="tab-content block">
                        <div class="tab-pane" id="actions">
                            <div class="accordion-inner block" align="center">
                                <button id="veNodeRootBtn" type="button" class="btn" data-toggle="button">Set as Root</button>
                                <button id="veDeleteNodeBtn" type="button" class="btn btn-danger">Delete Node</button>
                            </div>
                            <legend>Background color</legend>
                            <div class="block" align="center">
                                <input type="text" id="colorpickerInput" value="" />
                                <div class="child-block" id="colopickerContainer"></div>
                            </div>
                        </div>
                        <div class="tab-pane active" id="content">
                            <div class="accordion-inner block" style="margin-left: 0">
                                <div class="block" style="max-height: 585px;width: 430px; overflow: auto;padding-right: 25px;">
                                <div class="control-group block">
                                    <label for="nodetitle" class="control-label" style="text-align: left;"><strong>Title</strong></label>
                                    <div class="controls">
                                        <input type="text" id="nodetitle" value=""/>
                                    </div>
                                </div>
                                <div class="control-group block">
                                    <label for="nodecontent" class="control-label" style="text-align: left;"><strong>Node Content</strong></label>
                                    <div class="controls block">
                                        <textarea cols='20' class="mceEditor" id="nodecontent" rows='10' style="width: 100%;"></textarea>
                                    </div>
                                </div>
                                <div class="control-group block">
                                    <label for="nodesupport" class="control-label" style="text-align: left;"><strong>Supporting Information</strong></label>
                                    <div class="controls block">
                                        <textarea cols='20' class="mceEditor" id="nodesupport" rows='10' style="width: 100%;"></textarea>
                                    </div>
                                </div>
                                <div class="control-group block">
                                    <label for="nodesupportkeywords" class="control-label" style="text-align: left;"><strong>Supporting Information Keyword</strong></label>
                                    <div class="controls block">
                                        <input type="text" id="nodesupportkeywords" value="" />
                                    </div>
                                </div>
                                <div>
                                    <?php if (isset($templateData['counters']) and count($templateData['counters']) > 0) { ?>
                                        <div>
                                            <div class="control-group">
                                                <?php
                                                $countersData = '';
                                                foreach ($templateData['counters'] as $counter) {
                                                    $countersData .= "{id: '" . $counter->id . "', func: '#nodecounter_function_" . $counter->id . "', show: '#nodecounter_show_" . $counter->id . "'}, ";
                                                    ?>
                                                    <?php echo $counter->name; ?>
                                                    <label for="nodesupportkeywords" class="control-label" style="text-align: left;"><strong>Counter function</strong></label>
                                                    <div class="controls">
                                                        <input type="text" id="nodecounter_function_<?php echo $counter->id; ?>" value="" />
                                                    </div>

                                                    <label for="nodesupportkeywords" class="control-label" style="text-align: left;"><strong>Appear on node</strong></label>
                                                    <div class="controls">
                                                        <input type="checkbox" id="nodecounter_show_<?php echo $counter->id; ?>" value="1" />
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <div id="counters" data="[<?php
                                        if (strlen($countersData) > 2) {
                                            echo substr($countersData, 0, strlen($countersData) - 2);
                                        }
                                        ?>]"></div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="row-fluid block">
                                    <div class="span6">
                                        <div class="control-group">
                                            <label class="control-label"><strong>Exit Node Probability</strong></label>
                                            <div class="controls" id="exitNodeOptions">
                                                <label class="radio">On<input name="exit" type="radio" value="1"></label>
                                                <label class="radio">Off<input name="exit" type="radio" value="0" checked=""></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span6">
                                        <div class="control-group">
                                            <label class="control-label"><strong>Link Function Style</strong></label>

                                            <div class="controls" id="linkStyleOptions">
                                                <label class="radio"><input name="style" type="radio" value="1" checked="">text (default)</label>
                                                <label class="radio"><input name="style" type="radio" value="2">dropdown</label>
                                                <label class="radio"><input name="style" type="radio" value="3">dropdown + confidence</label>
                                                <label class="radio"><input name="style" type="radio" value="4">type in text</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row-fluid block">
                                    <div class="span6">
                                        <div class="control-group">
                                            <label class="control-label"><strong>Node Priorities</strong></label>

                                            <div class="controls" id="nodePriorities">
                                                <label class="radio"><input name="priority" type="radio" value="1" checked="">normal (default)</label>
                                                <label class="radio"><input name="priority" type="radio" value="2">must avoid</label>
                                                <label class="radio"><input name="priority" type="radio" value="3">must visit</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span6">
                                        <div class="control-group">
                                            <label class="control-label"><strong>Undo Links</strong></label>

                                            <div class="controls" id="nodeUndoLinks">
                                                <label class="radio">Enabled<input name="undo" type="radio" value="1"></label>
                                                <label class="radio">Disabled<input name="undo" type="radio" value="0" checked=""></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label"><strong>Link to end and report from this node</strong></label>

                                    <div class="controls" id="nodeEndAndReport">
                                        <label class="radio">Off (default)<input name="end" type="radio" value="0" checked=""></label>
                                        <label class="radio">On<input name="end" type="radio" value="1"></label>
                                    </div>
                                </div>
                            </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <br/>
                <div class="footer block">
                    <button class="btn" id="veRightPanelSaveBtn">Save</button>
                    <button class="btn btn-danger veRightPanelCloseBtn">Close</button>
                </div>
            </div>
        </div>
        
        <div class="modal hide block" id="visual_editor_template">
            <div class="modal-header block">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3>Insert pre-template</h3>
            </div>
            
            <div class="modal-body block">
                <div class="block" align="center">
                    <div class="block" data-toggle="buttons-radio" id="veTypeContainer">
                        <button type="button" class="btn" value="linear">Linear</button>
                        <button type="button" class="btn" value="branched">Branched</button>
                        <button type="button" class="btn" value="dandelion">Dandelion</button>
                    </div>
                </div>
                
                <div class="block" align="center">
                    <div class="block" data-toggle="buttons-radio" id="veCountContainer">
                        <button type="button" class="btn" value="6">6</button>
                        <button type="button" class="btn" value="8">8</button>
                        <button type="button" class="btn" value="18">18</button>
                        <button type="button" class="btn" value="24">24</button>
                        <button type="button" class="btn" value="Custom" id="veCustom">Custom</button>
                        <input type="text" style="margin-top: 10px; width: 40px;" id="veCount" disabled/>
                    </div>
                </div>
            </div>

            <div class="modal-footer block">
                <a href="#" class="btn" data-dismiss="modal">Close</a>
                <a href="#" class="btn" id="veTemplateSaveBtn">Save</a>
            </div>
        </div>

        <div class="modal hide block" id="visual_editor_dandelion">
            <div class="modal-header block">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3>Dandelion</h3>
            </div>
            
            <div class="modal-body block">
                <div class="block" align="center">
                    <div class="block" data-toggle="buttons-radio" id="veDandelionCountContainer">
                        <button type="button" class="btn" value="6">6</button>
                        <button type="button" class="btn" value="8">8</button>
                        <button type="button" class="btn" value="18">18</button>
                        <button type="button" class="btn" value="24">24</button>
                        <button type="button" class="btn" value="Custom" id="veDandelionCustom">Custom</button>
                        <input type="text" style="margin-top: 10px; width: 40px;" id="veDandelionCount" disabled/>
                    </div>
                </div>
            </div>

            <div class="modal-footer block">
                <a href="#" class="btn" data-dismiss="modal">Close</a>
                <a href="#" class="btn" id="veDandelionSaveBtn">Save</a>
            </div>
        </div>
        
        <div class="modal hide block" id="visual_editor_background_color">
            <div class="modal-header block">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3>Background color</h3>
            </div>
            
            <div class="modal-body block">
                <div class="block" align="center">
                    <div class="defined-color-picker">
                        <div style="background-color: #FFFFFF;"></div>
                        <div style="background-color: #CCCCCC;"></div>
                        <div style="background-color: #000000;"></div>
                    </div>
                </div>
            </div>

            <div class="modal-footer block">
                <a href="#" class="btn" data-dismiss="modal">Close</a>
                <a href="#" class="btn" id="veBgColorSaveBtn">Save</a>
            </div>
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
                <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Close</a>
                <a href="#" class="btn" id="linkApply">Apply</a>
            </div>
        </div>

        <div class="modal hide alert alert-block alert-error block" id="visual_editor_delete">
            <div class="modal-header block">
                <a class="close" data-dismiss="alert" href="#">&times;</a>
                <h3 class="deleteModalHeaderNode">Delete this node</h3>
                <h3 class="deleteModalHeaderNodes">Delete this nodes</h3>
            </div>

            <div class="modal-body block">
                <p class="deleteModalContentNode">You have just clicked the delete button, are you certain that you wish to proceed with deleting this node?</p>
                <p class="deleteModalContentNodes">You have just clicked the delete button, are you certain that you wish to proceed with deleting this nodes?</p>
                <a href="#" class="btn btn-danger" id="deleteNode">Delete</a>
                <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Close</a>
            </div>
        </div>
        
        <div class="modal hide alert alert-block alert-error block" id="visual_editor_set_root">
            <div class="modal-header block">
                <a class="close" data-dismiss="alert" href="#">&times;</a>
                <h3>Set as Root</h3>
            </div>

            <div class="modal-body block">
                <p>You have just clicked the set as root button, are you certain that you wish to proceed with set this node as root?</p>
                <a href="#" class="btn btn-danger" id="setAsRootNodeBtn">Set</a>
                <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Close</a>
            </div>
        </div>
    </div>
    <div >
        <a href="<?php echo URL::base() . 'labyrinthManager/caseWizard/5/editNode/' . $templateData['map']; ?>"
           style="float:right;" class="wizard_button btn btn-primary">Step 5 - Add other elements</a>
        <a href="<?php echo URL::base(); ?>" style="float:right;" class="wizard_button btn btn-primary">Save & return later</a>
    </div>
</div>
</div>

<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/utils.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/colorModal.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/base64v1_0.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/mouse.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/transform.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/node.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/link.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/linkConnector.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/linkModal.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/deleteModal.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/nodeModal.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/selector.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/rightPanel.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/visualEditor.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/farbtastic/farbtastic.js"></script>

<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/application.js"></script>