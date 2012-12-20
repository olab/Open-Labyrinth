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
<html>
<head>
    <title>Title of your labyrinth</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" type="text/css" href="<?php echo URL::base(); ?>css/skin/basic/layout.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo URL::base(); ?>css/skineditor.css">
    <link rel="stylesheet" type="text/css" href="<?php echo URL::base(); ?>css/jquery-ui-1.9.1.custom.css">
    <script type="text/javascript" src="<?php echo URL::base(); ?>scripts/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="<?php echo URL::base(); ?>scripts/jquery-ui-1.9.1.custom.min.js"></script>
    <script type="text/javascript" src="<?php echo URL::base(); ?>scripts/jscolor.js"></script>
    <script src="<?php echo URL::base(); ?>scripts/fileupload/js/jquery.iframe-transport.js"></script>
    <script src="<?php echo URL::base(); ?>scripts/fileupload/js/jquery.fileupload.js"></script>
    <script type="text/javascript" src="<?php echo URL::base(); ?>scripts/skineditor.js"></script>
    <?php
    if ($templateData['skinData'] != NULL){
        $path = $templateData['skinData']->path;
        $doc_file = DOCROOT.'css/skin/'.$path.'/default.css';
        if (file_exists($doc_file)){
            $css_file = URL::base().'css/skin/'.$path.'/default.css?'.rand();
            echo '<link rel="stylesheet" type="text/css" href="'.$css_file.'" />';
        }
    }
    ?>
</head>

<body>
    <div align="center">
        <table style="padding-top:20px;" id="centre_table" cellpadding="12" cellspacing="2" border="0" width="90%">
            <tbody><tr>
                <td class="centre_td" align="left" bgcolor="#FFFFFF" width="81%">
                    <h4><font color="#000000">Title of your labyrinth</font></h4>
                    
                                                                        <p>Some text of node</p>
                        <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec tincidunt sollicitudin elementum. Vivamus gravida, arcu at ultricies ultrices, velit justo pulvinar lorem, at hendrerit tortor ligula a ante. Integer at erat lorem. In hac habitasse platea dictumst. Morbi laoreet ante a metus pulvinar tincidunt. Praesent dapibus mattis luctus. Sed varius facilisis nulla, sed accumsan odio sodales ac. Aenean fermentum sollicitudin velit in mollis. Aliquam ut nisi aliquet tortor ultrices iaculis ac quis metus. Donec accumsan pretium aliquet. Integer nunc odio, tincidunt ac semper nec, aliquet a libero. Curabitur non sapien quis elit tempus faucibus.

                            Pellentesque non augue quis dui lacinia rhoncus sed a nisl. Maecenas lorem dolor, rhoncus a aliquet sed, sollicitudin vitae orci. Nullam quam nunc, egestas scelerisque fringilla at, iaculis id mauris. Nunc eget orci est. Praesent molestie, ligula ac euismod euismod, massa lorem eleifend ante, nec elementum nisl lorem quis nibh. Morbi at molestie mauris. Vivamus a augue at sapien facilisis tincidunt eget vitae tortor. Fusce vel lorem nisi. Proin viverra sollicitudin luctus. Nulla id placerat dui.</p>
                                        <table cellpadding="0" cellspacing="0" border="0" width="100%">
                        <tbody><tr>
                            <td>
                                <p><a href="#">Link to the next node</a></p>                            </td>
                            <td align="right" valign="bottom">
                                <p><a href="#">health</a>(70) <sup>[-20]</sup></p><p><a href="#">temperature</a>(37) <sup>[+1]</sup></p>                            </td></tr>
                    </tbody></table>
                </td>
                <td class="centre_td" rowspan="2" bgcolor="#FFFFFF" valign="top" width="19%"><p align="center">
                        
                    </p><h5>Map: Your labyrinth (11)<br>
                        Node: 20<br><strong>Score:</strong></h5>
                    <input name="bookmark" value="bookmark" type="button">
                    
                                        <h5>
                                                    <a href="#">turn editing on</a>
                                            </h5>
                                        <p><a href="#">reset</a></p>
                    
                    <a href="#"><img src="<?php echo URL::base(); ?>images/openlabyrinth-powerlogo-wee.jpg" alt="OpenLabyrinth" height="20" border="0" width="118"></a>
                    <h5>OpenLabyrinth is an open source educational pathway system</h5>
                </td></tr>
            <tr>
                <td class="centre_td" bgcolor="#FFFFFF">
                    <a href="#"><p class="style2"><strong>Review your pathway</strong></p></a>

                </td>
            </tr>

        </tbody></table>
</div>
    <div id="skinEditor">
        <div class="header">
            <span>WYSIWYG editor for SKIN</span>
            <div style="position: absolute; right:10px; top:0px; font-size: 14px; width:85px;">
                <div class="show" style="position:absolute; display:none;"><a href="javascript:void(0)" style="text-decoration: underline;">show panel</a></div>
                <div class="hide" style="position:absolute;"><a href="javascript:void(0)" style="text-decoration: underline;">hide panel</a></div>
            </div>
        </div>
        <div style="margin:0 auto; text-align: center; width:1040px;">
            <div class="left">
                <div class="element_header">
                    Change centre area
                </div>
                <div id="centre" class="control">
                    <div class="action">
                        <p><input autocomplete="off" class="upload_radio" type="radio" name="c_action" value="upload" /> Background image</p>
                        <p><input autocomplete="off" class="pick_color_radio" type="radio" name="c_action" value="color" /> Background color</p>
                        <p><input autocomplete="off" class="set_opacity" type="radio" name="c_action" value="set_opacity" /> Set opacity</p>
                        <p><input autocomplete="off" class="transparent" type="radio" name="c_action" value="transparent" /> No background</p>
                    </div>
                    <div class="action_control">
                        <div class="upload_action editor_action">
                            <div class="select_image">
                                <div style="position: relative;">
                                    <input autocomplete="off" class="upload_input" type="text" value="" placeholder="Click here to select file" name="" />
                                    <input name="files[]" data-url="<?php echo URL::base(); ?>scripts/fileupload/php/" autocomplete="off" id="centre_upload" class="upload_file" type="file" />
                                </div>
                                <input autocomplete="off" class="upload_button" type="button" value="Upload" name="upload_button" />
                            </div>
                            <div class="progress_display">
                                <div class="progress">
                                    <div class="bar" style="width: 0;"></div>
                                </div>
                                <div class="status">
                                    <p>Size: <input autocomplete="off" class="change_size" type="text" style="width:40px" value="100" />%</p>
                                    <p>
                                        Repeat:
                                        <select autocomplete="off" class="change_repeat">
                                            <option value="no-repeat">no-repeat</option>
                                            <option value="repeat">repeat</option>
                                            <option value="repeat-x">repeat-x</option>
                                            <option value="repeat-y">repeat-y</option>
                                        </select>
                                    </p>
                                    <p>Change position <input autocomplete="off" type="radio" name="c_change_position" value="on" class="position" /> on <input type="radio" checked="checked" name="c_change_position" value="off" class="position off" /> off | (<a href="javascript:void(0)" class="position_reset">reset</a>)</p>
                                    <p><input class="reset" type="button" value="Delete image" /></p>
                                </div>
                            </div>
                        </div>
                        <div class="color_action editor_action">
                            <p style="margin-top:0px;">Type in the hex code, or select from the color panel, below</p>
                            <input autocomplete="off" id="colorPickerCentre" type="text" style="" value="FFFFFF" />
                        </div>
                        <div class="opacity_action editor_action">
                            <p style="margin-top:0px;">Type in opacity of background, or set by moving slider</p>
                            <p>Opacity: <input autocomplete="off" type="text" class="opacity_value" style="width:30px" value="1" name="opacity_value" /></p>
                            <div id="slider-range-centre" style="margin-left:30px; margin-right: 30px;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="float:left; width:180px;">
                <form id="submit_form" action="<?php echo $templateData['action_url']; ?>" method="post">
                    <div class="save_button">
                        <p>Skin name: <?php echo $templateData['skinData']->name; ?>
                        <input autocomplete="off" id="skinName" type="hidden" name="skin_name" value="<?php echo $templateData['skinData']->name; ?>" />
                        <input autocomplete="off" id="skinId" type="hidden" name="skinId" value="<?php echo $templateData['skinData']->id; ?>" />
                        </p>
                        <p><input type="button" class="save_submit" name="save_changes" value="Save changes" /> <span class="saving_text" style="display: none">Saving...</span></p>
                        <p><input type="button" class="save_submit" name="save_exit" value="Save & Exit" /></p>
                    </div>
                    <input autocomplete="off" type="hidden" name="save" value="" />
                    <input type="hidden" id="redirect_url" name="redirect_url" value="<?php echo URL::base().'skinManager/index/'.$templateData['map']->id; ?>" />

                </form>
            </div>
            <div class="right">
                <div class="element_header">
                    Change outside border
                </div>
                <div id="outside" class="control">
                    <div class="action">
                        <p><input autocomplete="off" class="upload_radio" type="radio" name="o_action" value="upload" /> Background image</p>
                        <p><input autocomplete="off" class="pick_color_radio" type="radio" name="o_action" value="color" /> Background color</p>
                        <p><input autocomplete="off" class="set_opacity" type="radio" name="o_action" value="set_opacity" /> Set opacity</p>
                        <p><input autocomplete="off" class="transparent" type="radio" name="o_action" value="transparent" /> No background</p>
                    </div>
                    <div class="action_control">
                        <div class="upload_action editor_action">
                            <div class="select_image">
                                <div style="position: relative;">
                                    <input autocomplete="off" class="upload_input" type="text" value="" placeholder="Click here to select file" name="" />
                                    <input name="files[]" data-url="<?php echo URL::base(); ?>scripts/fileupload/php/" autocomplete="off" id="outside_upload" class="upload_file" type="file" />
                                </div>
                                <input autocomplete="off" class="upload_button" type="button" value="Upload" name="upload_button" />
                            </div>
                            <div class="progress_display">
                                <div class="progress">
                                    <div class="bar" style="width: 0;"></div>
                                </div>
                                <div class="status">
                                    <p>Size: <input autocomplete="off" class="change_size" type="text" style="width:40px" value="100" />%</p>
                                    <p>
                                        Repeat:
                                        <select autocomplete="off" class="change_repeat">
                                            <option value="no-repeat">no-repeat</option>
                                            <option value="repeat">repeat</option>
                                            <option value="repeat-x">repeat-x</option>
                                            <option value="repeat-y">repeat-y</option>
                                        </select>
                                    </p>
                                    <p>Change position <input autocomplete="off" type="radio" name="change_position" value="on" class="position" /> on <input type="radio" checked="checked" name="change_position" value="off" class="position off" /> off | (<a href="javascript:void(0)" class="position_reset">reset</a>)</p>
                                    <p><input class="reset" type="button" value="Delete image" /></p>
                                </div>
                            </div>
                        </div>
                        <div class="color_action editor_action">
                            <p style="margin-top:0px;">Type in the hex code, or select from the color panel, below</p>
                            <input autocomplete="off" id="colorPickerOutside" type="text" style="" value="EEEEEE" />
                        </div>
                        <div class="opacity_action editor_action">
                            <p style="margin-top:0px;">Type in opacity of background, or set by moving slider</p>
                            <p>Opacity: <input autocomplete="off" type="text" class="opacity_value" style="width:30px" value="1" name="opacity_value" /></p>
                            <div id="slider-range-outside" style="margin-left:30px; margin-right: 30px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="base_path" value="<?php echo URL::base() ?>scripts/" />
    </div>

</body></html>