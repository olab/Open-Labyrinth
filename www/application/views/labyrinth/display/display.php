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
    var displayId = <?php echo ((isset($templateData['display'])) ? $templateData['display']->id : 'null'); ?>;
    var displayJSON = <?php echo ((isset($templateData['displayJSON']) && strlen($templateData['displayJSON']) > 0) ? $templateData['displayJSON'] : 'null'); ?>;
    var displayBaseURL = '<?php echo URL::base(); ?>visualdisplaymanager/display/<?php echo $templateData['map']->id; ?>/';
    var baseDisplayImagesPath = '<?php echo URL::base(); ?>files/<?php echo $templateData['map']->id; ?>/vdImages/';
    var displayUploadURL = '<?php echo URL::base(); ?>scripts/fileupload/php';
    var displayMapId = <?php echo $templateData['map']->id; ?>;
    var replaceAction = '<?php echo URL::base(); ?>visualdisplaymanager/replaceDisplayFile';
    var displayDeleteImageURL = '<?php echo URL::base(); ?>visualdisplaymanager/deleteImage/<?php echo $templateData['map']->id; ?>/<?php if(isset($templateData['display'])) echo $templateData['display']->id; ?>';
    var dataURL = '<?php echo URL::base(); ?>scripts/fileupload/php/';
</script>

<div class="control-group">
    <div class="controls">
        <input type="checkbox" name="showOnAllPage" id="showOnAllPage" <?php if(isset($templateData['display']) && $templateData['display']->is_all_page_show) echo 'checked="checked"'; ?>/> Show on all pages
    </div>
</div>

<ul class="nav nav-tabs">
    <li class="active"><a href="#panelsTab" data-toggle="tab">Panels</a></li>
    <li><a href="#imagesTab" data-toggle="tab">Images</a></li>
    <li><a href="#countersTab" data-toggle="tab">Counters</a></li>
</ul>

<div class="tab-content">
<div class="visual-display-panel tab-pane active" id="panelsTab">
    <div class="panel-content">
        <div class="block"><a href="javascript:void(0)" id="createPanelBtn" class="btn">Create Panel</a></div>
        <div class="block">
            <div class="panel-control-group">
                <div class="row">
                    <div class="panel-control-label"><?php echo __('Width'); ?>:</div>
                    <div class="panel-control"><input type="text" id="panelWidth" size="20"/></div>
                    <div class="panel-control-label"><?php echo __('px'); ?></div>
                </div>
                <div class="row">
                    <div class="panel-control-label"><?php echo __('Height'); ?>:</div>
                    <div class="panel-control"><input type="text" id="panelHeight" size="20"/></div>
                    <div class="panel-control-label"><?php echo __('px'); ?></div>
                </div>
            </div>
        </div>
        <div class="block">
            <div class="panel-control-group">
                <div class="row">
                    <div class="panel-control-label"><?php echo __('Z-index'); ?>:</div>
                    <div class="panel-control"><input type="text" id="panelZIndex" size="20"/></div>
                </div>
                <div class="row">
                    <div class="panel-control-label"><?php echo __('Angle'); ?>:</div>
                    <div class="panel-control"><input type="text" id="panelAngle" size="20"/></div>
                </div>
            </div>
        </div>
        <div class="block">
            <div class="panel-control-group">
                <div class="row">
                    <div class="panel-control-label"><?php echo __('Border'); ?>:</div>
                    <div class="panel-control"><input type="text" id="panelBorder" size="20"/></div>
                </div>
                <div class="row">
                    <div class="panel-control-label"><?php echo __('Border color'); ?>:</div>
                    <div class="panel-control">
                        <input type="text" id="panelBorderColor" size="20"/>
                    </div>
                    <div id="borderColorFarbtastic" style="position: absolute; z-index: 2000;"></div>
                </div>
            </div>
        </div>
        <div class="block">
            <div class="panel-control-group">
                <div class="row">
                    <div class="panel-control-label"><?php echo __('Border radius'); ?>:</div>
                    <div class="panel-control"><input type="text" id="panelBorderRadius" size="20"/></div>
                    <div class="panel-control-label"><?php echo __('px'); ?></div>
                </div>
                <div class="row">
                    <div class="panel-control-label"><?php echo __('Background color'); ?>:</div>
                    <div class="panel-control"><input type="text" id="panelBackgroundColor" size="20"/></div>
                    <div id="backgroundColorFarbtastic" style="position: absolute; z-index: 2000;"></div>
                </div>
            </div>
        </div>
        <div class="block"></div>
    </div>
    <div class="clear"></div>
</div>

<div class="visual-display-panel tab-pane" id="imagesTab">
    <div class="panel-content">
        <div class="block file-upload-btn">
                <span class="btn btn-success fileinput-button">
                    <i class="icon-plus icon-white"></i>
                    <span>Add files...</span>
                    <input id="fileupload" type="file" class="file-upload-input" style="width: 108px;" name="files[]" data-url="<?php echo URL::base(); ?>scripts/fileupload/php/" multiple>
                </span>
            <div id="progress" class="progress progress-striped active">
                <div class="bar" style="width: 0%;"></div>
            </div>
        </div>
        <div class="block">
            <div class="panel-control-group">
                <div class="row">
                    <div class="panel-control-label"><?php echo __('Z-index'); ?>:</div>
                    <div class="panel-control"><input type="text" id="panelImageZIndex" size="20"/></div>
                </div>
                <div class="row">
                    <div class="panel-control-label"><?php echo __('Angle'); ?>:</div>
                    <div class="panel-control"><input type="text" id="panelImageAngle" size="20"/></div>
                </div>
            </div>
        </div>
        <div class="block" id="mainDisplayImageContianer">
            <div class="visual-display-images-container">
                <div class="images" id="displayImagesContainer">
                    <?php if(isset($templateData['displayImages']) && count($templateData['displayImages']) > 0) { ?>
                    <?php foreach($templateData['displayImages'] as $image) { ?>
                    <div>
                        <img src="<?php echo URL::base(); ?>files/<?php echo $templateData['map']->id; ?>/vdImages/thumbs/<?php echo $image; ?>" path="<?php echo URL::base(); ?>files/<?php echo $templateData['map']->id; ?>/vdImages/<?php echo $image; ?>" />
                        <div>
                            <form method="POST" action="<?php echo URL::base(); ?>visualdisplaymanager/deleteImage/<?php echo $templateData['map']->id; ?>/<?php if(isset($templateData['display'])) echo $templateData['display']->id; ?>">
                                <input type="hidden" name="imageName" value="<?php echo $image; ?>"/>
                                <input type="submit" class="btn btn-danger btn-small" value="delete"/>
                            </form>
                        </div>
                    </div>
                    <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="block"></div>
    </div>
    <div class="clear"></div>
</div>

<div class="visual-display-panel tab-pane" id="countersTab">
    <div class="panel-content">
        <div class="block">
            <div class="panel-control-group">
                <div class="row">
                    <div class="panel-control-label"><?php echo __('Label font family'); ?>:</div>
                    <div class="panel-control">
                        <select id="counterFontLabelFamily">
                            <option value="Helvetica Neue">Default</option>
                            <option value="Andale Mono">Andale Mono</option>
                            <option value="Arial">Arial</option>
                            <option value="Arial Black">Arial Black</option>
                            <option value="Book Antiqua">Book Antiqua</option>
                            <option value="Comic Sans MS">Comic Sans MS</option>
                            <option value="Courier New">Courier New</option>
                            <option value="Georgia">Georgia</option>
                            <option value="Helvetica">Helvetica</option>
                            <option value="Impact">Impact</option>
                            <option value="Symbol">Symbol</option>
                            <option value="Tahoma">Tahoma</option>
                            <option value="Terminal">Terminal</option>
                            <option value="Times New Roman">Times New Roman</option>
                            <option value="Trebuchet MS">Trebuchet MS</option>
                            <option value="Verdana">Verdana</option>
                            <option value="Webdings">Webdings</option>
                            <option value="Wingdings">Wingdings</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="panel-control-label"><?php echo __('Label font size'); ?>:</div>
                    <div class="panel-control">
                        <select id="counterFontLabelSize">
                            <option value="14px">Default</option>
                            <option value="11px">8(pt)</option>
                            <option value="13px">10(pt)</option>
                            <option value="16px">12(pt)</option>
                            <option value="19px">14(pt)</option>
                            <option value="24px">18(pt)</option>
                            <option value="32px">24(pt)</option>
                            <option value="48px">36(pt)</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="panel-control-label"><?php echo __('Label text'); ?>:</div>
                    <div class="panel-control"><input type="text" id="counterLabelText" size="20"/></div>
                </div>
                <div class="row">
                    <div class="panel-control-label"><?php echo __('Label color'); ?>:</div>
                    <div class="panel-control"><input type="text" id="counterFontLabelColor" size="20"/></div>
                    <div id="labelFontColorFarbtastic" style="position: absolute; z-index: 2000;"></div>
                </div>
                <div class="row">
                    <div class="panel-control-label"><?php echo __('Label z-index'); ?>:</div>
                    <div class="panel-control"><input type="text" id="counterLabelZIndex" size="20"/></div>
                </div>
                <div class="row">
                    <div class="panel-control-label"><?php echo __('Label angle'); ?>:</div>
                    <div class="panel-control"><input type="text" id="counterLabelAngle" size="20"/></div>
                </div>
                <div class="row">
                    <div class="panel-control-label"><?php echo __('Label font settings'); ?>:</div>
                    <div class="panel-control">
                        <div class="btn-group" data-toggle="buttons-checkbox">
                            <button type="button" id="counterLabelBold" class="btn btn-primary">B</button>
                            <button type="button" id="counterLabelItalic" class="btn btn-primary"><i>I</i></button>
                            <button type="button" id="counterLabelUnderline" class="btn btn-primary"><u>U</u></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="block">
            <div class="panel-control-group">
                <div class="row">
                    <div class="panel-control-label"><?php echo __('Value font family'); ?>:</div>
                    <div class="panel-control">
                        <select id="counterFontValueFamily">
                            <option value="Helvetica Neue">Default</option>
                            <option value="Andale Mono">Andale Mono</option>
                            <option value="Arial">Arial</option>
                            <option value="Arial Black">Arial Black</option>
                            <option value="Book Antiqua">Book Antiqua</option>
                            <option value="Comic Sans MS">Comic Sans MS</option>
                            <option value="Courier New">Courier New</option>
                            <option value="Georgia">Georgia</option>
                            <option value="Helvetica">Helvetica</option>
                            <option value="Impact">Impact</option>
                            <option value="Symbol">Symbol</option>
                            <option value="Tahoma">Tahoma</option>
                            <option value="Terminal">Terminal</option>
                            <option value="Times New Roman">Times New Roman</option>
                            <option value="Trebuchet MS">Trebuchet MS</option>
                            <option value="Verdana">Verdana</option>
                            <option value="Webdings">Webdings</option>
                            <option value="Wingdings">Wingdings</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="panel-control-label"><?php echo __('Value font size'); ?>:</div>
                    <div class="panel-control">
                        <select id="counterFontValueSize">
                            <option value="14px">Default</option>
                            <option value="11px">8(pt)</option>
                            <option value="13px">10(pt)</option>
                            <option value="16px">12(pt)</option>
                            <option value="19px">14(pt)</option>
                            <option value="24px">18(pt)</option>
                            <option value="32px">24(pt)</option>
                            <option value="48px">36(pt)</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="panel-control-label"><?php echo __('Value color'); ?>:</div>
                    <div class="panel-control"><input type="text" id="counterFontValueColor" size="20"/></div>
                    <div id="valueFontColorFarbtastic" style="position: absolute; z-index: 2000;"></div>
                </div>
                <div class="row">
                    <div class="panel-control-label"><?php echo __('Value z-index'); ?>:</div>
                    <div class="panel-control"><input type="text" id="counterValueZIndex" size="20"/></div>
                </div>
                <div class="row">
                    <div class="panel-control-label"><?php echo __('Value angle'); ?>:</div>
                    <div class="panel-control"><input type="text" id="counterValueAngle" size="20"/></div>
                </div>
                <div class="row">
                    <div class="panel-control-label"><?php echo __('Value font settings'); ?>:</div>
                    <div class="panel-control">
                        <div class="btn-group" data-toggle="buttons-checkbox">
                            <button type="button" id="counterValueBold" class="btn btn-primary">B</button>
                            <button type="button" id="counterValueItalic" class="btn btn-primary"><i>I</i></button>
                            <button type="button" id="counterValueUnderline" class="btn btn-primary"><u>U</u></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="block" id="mainCounterContainer">
            <div class="visual-display-counter-container">
                <div class="counters">
                    <?php if(isset($templateData['counters']) && count($templateData['counters']) > 0) { ?>
                    <?php foreach($templateData['counters'] as $counter) { ?>
                    <div class="counter-container" 
                        counterId="<?php echo $counter->id; ?>" 
                        counterName="<?php echo $counter->name; ?>"
                        counterValue="<?php echo $counter->start_value; ?>">
                        <?php echo $counter->name; ?>
                    </div>
                    <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="block"></div>
    </div>
    <div class="clear"></div>
</div>
</div>
    <p>&nbsp</p>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span10" id="visualDisplay"></div>
        <div class="span2"><div class="visual-display-layout-panel" id="visualDisplayLayoutContianer"></div></div>
    </div>
</div>

<div class="form-actions">
    <div class="pull-right">
        <input class="btn btn-primary btn-large"
               type="button"
               mapId="<?php echo $templateData['map']->id; ?>"
               postURL="<?php echo URL::base(); ?>visualdisplaymanager/save"
               id="saveVisualDisplayBtn"
               value="Save changes"/>
    </div>
</div>
    
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/farbtastic/farbtastic.js'); ?>"></script>

<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/fileupload/js/vendor/jquery.ui.widget.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/fileupload/js/jquery.iframe-transport.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/fileupload/js/jquery.fileupload.js'); ?>"></script>

<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/visualeditor/base64v1_0.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/visualdisplay/utils.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/visualdisplay/layoutPanel.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/visualdisplay/counter.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/visualdisplay/panelimage.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/visualdisplay/panel.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/visualdisplay/visualDisplay.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/visualdisplay/application.js'); ?>"></script>