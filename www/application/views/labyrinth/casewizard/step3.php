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

<script type="text/javascript">
    var sendURL = '<?php echo URL::base(); ?>labyrinthManager/caseWizard/3/updateVisualEditor/<?php echo $templateData['map']; ?>';
    var autoSaveURL = '<?php echo URL::base(); ?>visualManager/autoSave';
    var mapId = <?php echo $templateData['map']; ?>;
    var mapJSON = <?php echo (isset($templateData['mapJSON']) && strlen($templateData['mapJSON']) > 2 ? $templateData['mapJSON'] : 'null') ?>;
    var saveMapJSON = <?php echo (isset($templateData['saveMapJSON']) && strlen($templateData['saveMapJSON']) > 0) ? $templateData['saveMapJSON'] : 'null'; ?>;
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
            <div style="position: relative" id="canvasContainer">
                <div style="position: absolute; top: 5px; left: 5px">
                    <p><input type="button" class="btn" value="Update" id="update" /></p><p><input type="button" class="btn" value="Add Node" id="addNode"/></p>
                    <p style="position:relative; float:left;"><input type="button" class="btn" value="+" id="zoomIn" /> <input type="button" class="btn" value="-" id="zoomOut" /></p>
                </div>
                <div style="position: absolute;left:50%;" id="ve_message" class="alert alert-success hide">
                    <button type="button" class="close" data-dismiss="alert">&times;</button><span id="ve_message_text">Message</span></div>
                <canvas id="canvas" width="1200" height="600" style="background-color: #cccccc;">Not supported</canvas>
            </div>

            <div class="modal hide" id="visual_editor_restore">
                <div class="modal-header">
                    <h3>Auto save</h3>
                </div>

                <div class="modal-body">
                    You have some auto saved data. Do you want to restore this save or use current state.
                </div>

                <div class="modal-footer">
                    <a href="#" class="btn" id="veLastSave">Load last save</a>
                    <a href="#" class="btn" id="veCurrentSave">Load current state</a>
                </div>
            </div>

            <div class="modal hide" id="visual_editor_colorpicker">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3>Color</h3>
                </div>

                <div class="modal-body" align="center">
                    <input type="text" id="colorpicker_input" value="" />
                    <div id="colopicker_container"></div>
                </div>

                <div class="modal-footer">
                    <a href="#" class="btn" data-dismiss="modal">Close</a>
                    <a href="#" class="btn" id="colorpickerApply">Apply</a>
                </div>
            </div>

            <div class="modal hide" id="visual_editor_link">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3>Link Manager</h3>
                </div>

                <div class="modal-body" align="center">
                    <div class="btn-group" data-toggle="buttons-radio" id="linkTypes">
                        <button type="button" class="btn" value="direct">Direct</button>
                        <button type="button" class="btn" value="back">Back</button>
                        <button type="button" class="btn" value="dual">Dual</button>
                        <button type="button" class="btn btn-danger" value="delete">Delete</button>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="#" class="btn" data-dismiss="modal">Close</a>
                    <a href="#" class="btn" id="linkApply">Apply</a>
                </div>
            </div>

            <div class="modal hide alert alert-block alert-error" id="visual_editor_delete">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3>Delete this node</h3>
                </div>

                <div class="modal-body">
                    <p>You have just clicked the delete button, are you certain that you wish to proceed with deleting this node?</p>
                    <a href="#" class="btn btn-danger" id="deleteNode">Delete</a>
                    <a href="#" class="btn" data-dismiss="modal">Close</a>
                </div>
            </div>

            <div class="modal hide" id="visual_editor_node">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3>Edit Node</h3>
                </div>

                <div class="modal-body">
                    <div class="control-group">
                        <label for="nodetitle" class="control-label" style="text-align: left;"><strong>Title</strong></label>
                        <div class="controls">
                            <input type="text" id="nodetitle" value=""/>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="nodecontent" class="control-label" style="text-align: left;"><strong>Node Content</strong></label>
                        <div class="controls">
                            <textarea cols='20' id="nodecontent" rows='10' style="width: 100%;"></textarea>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="nodesupport" class="control-label" style="text-align: left;"><strong>Supporting Information</strong></label>
                        <div class="controls">
                            <textarea cols='20' id="nodesupport" rows='10' style="width: 100%;"></textarea>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="nodesupportkeywords" class="control-label" style="text-align: left;"><strong>Supporting Information Keyword</strong></label>
                        <div class="controls">
                            <input type="text" id="nodesupportkeywords" value="" />
                        </div>
                    </div>
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
                    <div class="row-fluid">
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
                    <div class="row-fluid">
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

                <div class="modal-footer">
                    <a href="#" class="btn" data-dismiss="modal">Close</a>
                    <a href="#" class="btn" id="nodeApply">Save</a>
                </div>
            </div>
        </div>
    </div>
    <div >
        <a href="<?php echo URL::base() . 'labyrinthManager/caseWizard/4/editNode/' . $templateData['map']; ?>"
           style="float:right;" class="wizard_button btn btn-primary">Step 4 - Add other elements</a>
        <a href="<?php echo URL::base(); ?>" style="float:right;" class="wizard_button btn btn-primary">Save & return later</a>
        <a href="<?php echo URL::base() . 'labyrinthManager/caseWizard/2/' . $templateData['map']; ?>"
           style="float:left;" class="btn btn-primary wizard_button">Return to step 2</a>
    </div>
</div>

<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/mouse.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/transform.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/node.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/link.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/linkConnector.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/colorModal.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/linkModal.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/deleteModal.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/nodeModal.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/visualEditor.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/farbtastic/farbtastic.js"></script>

<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/visualeditor/application.js"></script>