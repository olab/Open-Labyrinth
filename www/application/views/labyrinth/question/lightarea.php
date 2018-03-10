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
    $type = Arr::get($templateData, 'type');
?>

    <div class="page-header">
        <h1><?php
            echo (isset($templateData['question']))
                ? __('Edit question "').$templateData['question']->stem.'"'
                : __('New question for "').$templateData['map']->name.'"'; ?>
        </h1>
    </div>

    <form class="form-horizontal" method="POST" action="<?php echo URL::base().'questionManager/questionPOST/'.$templateData['map']->id.'/'.$templateData['type']->id; ?><?php echo (isset($templateData['question']) ? ('/'.$templateData['question']->id) : ''); ?>">
        <fieldset class="fieldset">
            <div class="control-group">
                <label for="qstem" class="control-label"><?php echo __('Stem'); ?>
                </label>
                <div class="controls">
                    <textarea id="qstem" name="qstem"><?php if(isset($templateData['question'])) echo $templateData['question']->stem; ?></textarea>
                </div>
            </div>

            <div class="control-group">
                <label for="qwidth" class="control-label"><?php echo __('Width'); ?>
                </label>
                <div class="controls">
                    <select  id="qwidth" name="qwidth"><?php
                        for($i = 10; $i <= 60; $i += 10) { ?>
                            <option value="<?php echo $i; ?>" <?php if(isset($templateData['question']) and $templateData['question']->width == $i) echo 'selected=""'; ?>><?php echo $i; ?></option><?php
                        } ?>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label for="qheight" class="control-label"><?php echo __('Height'); ?>
                </label>
                <div class="controls">
                    <select id="qheight" name="qheight"><?php
                        for($i = 2; $i <= 8; $i += 2) { ?>
                            <option value="<?php echo $i; ?>" <?php if(isset($templateData['question']) and $templateData['question']->height == $i) echo 'selected=""'; ?>><?php echo $i; ?></option><?php
                        } ?>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label for="feedback" class="control-label">Feedback</label>
                <div class="controls"><textarea id="feedback" name="fback"><?php if(isset($templateData['question'])) echo $templateData['question']->feedback; ?></textarea></div>
            </div>

            <div class="control-group">
                <label for="fback" class="control-label">
                    <?php echo __('Prompt text'); ?>
                    <p class="question-info-box"><?php echo __('Text will automatically appear in response area. Use to give learner a hint or further instruction.'); ?></p>
                </label>
                <div class="controls">
                    <textarea id="fback" name="prompt"><?php if(isset($templateData['question'])) echo $templateData['question']->prompt; ?></textarea>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="v"><?php echo __('Private'); ?></label>
                <div class="controls">
                    <input type="checkbox" name="is_private" <?php if(isset($templateData['question'])) { echo $templateData['question']->is_private ? 'checked=""' : '"checked"';} ?>>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label"><?php echo __('Used'); ?></label>
                <div class="controls">
                    <input type="text" readonly value="<?php if(isset($templateData['used'])) { echo $templateData['used']; } ?>"/>
                </div>
            </div>

        </fieldset>

        <div class="form-actions">
            <div class="pull-right">
                <input style="float:right;" id="submit_button" class="btn btn-primary btn-large" type="submit" name="Submit" value="<?php echo __('Save question'); ?>">
            </div>
        </div>

        <input type="hidden" name="mapId" id="mapId" value="<?php echo $templateData['map']->id; ?>" />
        <input type="hidden" name="isCorrect" id="isCorrect" value="1" />
    </form>
<?php } ?>


