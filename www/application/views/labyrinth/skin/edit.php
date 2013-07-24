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
    $haveOne = false;
?>
<h1><?php echo __('Edit skin of Labyrinth "').$templateData['map']->name.'"'; ?></h1>
<div class="member-box round-all">
    <?php echo $templateData['navigation']; ?>
    <form class="form-horizontal" id="form1" name="form1" method="post" action="<?php echo URL::base().'skinManager/skinsSaveChanges/'.$templateData['map']->id; ?>">
        <fieldset class="fieldset">
            <legend><?php echo __('Edit my skins'); ?></legend>
            <?php if ($templateData['skinError'] != NULL){ ?>
            <div class="alert alert-error">
                <button data-dismiss="alert" class="close" type="button">&times;</button>
                <strong><?php echo __('Error!') ?></strong>&nbsp;<?php echo $templateData['skinError']; ?>
            </div>
            <?php } ?>
            <div class="control-group">
                <label class="control-label"><?php echo __('Name:'); ?></label>
                <div class="controls">
                    <input class="not-autocomplete" type="text" name="name" value="<?php echo $templateData['skinData']->name; ?>" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label"><?php echo __('CSS file:'); ?></label>
                <div class="controls">
                    <div class="row-fluid">
                        <div class="span6">
                            <textarea class="not-autocomplete" id="css_file" name="css" style="width:600px; height:300px;"><?php echo $templateData['css_content']; ?></textarea>
                        </div>
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label"><?php echo __('Background color (gradient - from - to)'); ?></label>
                                <div class="controls">
                                    <input type="text" id="background_from" value="#ffffff"/>
                                    <input type="text" id="background_to" value="#e6e6e6"/>
                                    <div id="background_from_cntr" class="farbtastic-container"></div>
                                    <div id="background_to_cntr" class="farbtastic-container"></div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label"><?php echo __('Hover color'); ?></label>
                                <div class="controls">
                                    <input type="text" id="hover_color" value="#e6e6e6"/>
                                    <div id="hover_color_cntr"></div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label"><?php echo __('Color'); ?></label>
                                <div class="controls">
                                    <input type="text" id="font_color" value="#333333"/>
                                    <div id="font_color_cntr"></div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label"><?php echo __('Border size'); ?></label>
                                <div class="controls">
                                    <input type="text" id="border_size" value="1"/>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label"><?php echo __('Border color'); ?></label>
                                <div class="controls">
                                    <input type="text" id="border_color" value="#e6e6e6"/>
                                    <div id="border_color_cntr"></div>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="controls">
                                    <input type="button" class="btn" id="insertButtonStyle" value="Insert"/>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <input class="btn btn-primary" type="submit" name="Submit" value="<?php echo __('Save changes'); ?>" />
                </div>
            </div>
        </fieldset>
        <input type="hidden" name="skinId" value="<?php echo $templateData['skinData']->id; ?>" />
    </form>
</div>

    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/farbtastic/farbtastic.js'); ?>"></script>
    <script>
        $(function() {
            $('#background_from').click(function() {
                $('#background_from_cntr').show();
                $('#background_from_cntr').farbtastic('#background_from');
            });

            $('#background_from').blur(function() {
                $('#background_from_cntr').hide();
            });

            $('#background_to').click(function() {
                $('#background_to_cntr').show();
                $('#background_to_cntr').farbtastic('#background_to');
            });

            $('#background_to').blur(function() {
                $('#background_to_cntr').hide();
            });

            $('#font_color').click(function() {
                $('#font_color_cntr').show();
                $('#font_color_cntr').farbtastic('#font_color');
            });

            $('#font_color').blur(function() {
                $('#font_color_cntr').hide();
            });

            $('#border_color').click(function() {
                $('#border_color_cntr').show();
                $('#border_color_cntr').farbtastic('#border_color');
            });

            $('#border_color').blur(function() {
                $('#border_color_cntr').hide();
            });

            $('#hover_color').click(function() {
                $('#hover_color_cntr').show();
                $('#hover_color_cntr').farbtastic('#hover_color');
            });

            $('#hover_color').blur(function() {
                $('#hover_color_cntr').hide();
            });

            $('#insertButtonStyle').click(function() {
                var style = '\r\na.btn, a.btn:visited, .btn {' +
                                'background-image: -moz-linear-gradient(top, ' + $('#background_from').val() + ', ' + $('#background_to').val() + ');' +
                                'background-image: -webkit-gradient(linear, 0 0, 0 100%, from(' + $('#background_from').val() + '), to(' + $('#background_to').val() + '));' +
                                'background-image: -webkit-linear-gradient(top, ' + $('#background_from').val() + ', ' + $('#background_to').val() + ');' +
                                'background-image: -o-linear-gradient(top, ' + $('#background_from').val() + ', ' + $('#background_to').val() + ');' +
                                'background-image: linear-gradient(to bottom, ' + $('#background_from').val() + ', ' + $('#background_to').val() + ');' +
                                'color: ' + $('#font_color').val() + ';' +
                                'border: ' + $('#border_size').val() + 'px solid ' + $('#border_color').val() + ';' +
                            '}' +
                            '.btn:hover {' +
                                'color: ' + $('#font_color').val() + ';' +
                                'background-image: -moz-linear-gradient(top, ' + $('#hover_color').val() + ', ' + $('#hover_color').val() + ');' +
                                'background-image: -webkit-gradient(linear, 0 0, 0 100%, from(' + $('#hover_color').val() + '), to(' + $('#hover_color').val() + '));' +
                                'background-image: -webkit-linear-gradient(top, ' + $('#hover_color').val() + ', ' + $('#hover_color').val() + ');' +
                                'background-image: -o-linear-gradient(top, ' + $('#hover_color').val() + ', ' + $('#hover_color').val() + ');' +
                                'background-image: linear-gradient(to bottom, ' + $('#hover_color').val() + ', ' + $('#hover_color').val() + ');' +
                            '}';


                $('#css_file').val($('#css_file').val() + style);
            });

        });
    </script>
<?php } ?>