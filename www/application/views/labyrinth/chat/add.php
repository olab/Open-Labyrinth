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
if (isset($templateData['map']) and isset($templateData['question_count'])) {
    ?>
    <script type="text/javascript">
        var questionCount = <?php echo $templateData['question_count'] ?>;
    </script>
    <div class="page-header">
        <h1><?php echo __('Add Chat'); ?></h1>
    </div>
    <form id="chatForm" class="form-horizontal" name="chatForm" method="post"
          action="<?php echo URL::base() . 'chatManager/saveNewChat/' . $templateData['map']->id; ?>">
        <fieldset class="fieldset">
            <div class="control-group">
                <label for="cstem" class="control-label"><?php echo __('Stem'); ?></label>

                <div class="controls">
                    <textarea id="cstem" name="cStem" rows="3" cols="42"></textarea>
                </div>
            </div>

            <div class="control-group">
                <label for="scount" class="control-label"><?php echo __('Track score with existing counter'); ?></label>

                <div class="controls">
                    <select id="scount" name="scount">
                        <?php if (isset($templateData['counters']) and count($templateData['counters']) > 0) { ?>
                            <option value="0">no counter</option>
                            <?php foreach ($templateData['counters'] as $counter) { ?>
                                <option value="<?php echo $counter->id; ?>"><?php echo $counter->name; ?>
                                    [<?php echo $counter->id; ?>]
                                </option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>
        </fieldset>
        <div id="questionContainer">
            <?php if (isset($templateData['question_count'])) { ?>
                <?php for ($i = 1; $i <= $templateData['question_count']; $i++) { ?>
                    <fieldset class="fieldset" id="qDiv<?php echo $i; ?>">
                        <legend><?php echo __("Question #") . $i ?></legend>
                        <div class="control-group cQuestion">
                            <label for="question<?php echo $i; ?>" class="control-label"><?php echo __('Question'); ?></label>

                            <div class="controls question">
                                <input id="question<?php echo $i; ?>" type="text" name="qarray[<?php echo $i; ?>][question]" value=""/>
                            </div>
                        </div>
                        <div class="control-group cResponce">
                            <label for="response<?php echo $i; ?>" class="control-label"><?php echo __('Response'); ?></label>

                            <div class="controls responce">
                                <input id="response<?php echo $i; ?>" type="text" name="qarray[<?php echo $i; ?>][response]" value=""/>
                            </div>
                        </div>
                        <div class="control-group cCounter">
                            <label for="counter<?php echo $i; ?>" class="control-label"><?php echo __('Counter'); ?></label>
                            <div class="controls counter">
                                <input id="counter<?php echo $i; ?>" type="text" name="qarray[<?php echo $i; ?>][counter]" value=""/>
                                <span class="help-block">type +, - or = an integer - e.g. '+1' or '=32'</span>
                            </div>
                        </div>
                        <div class="form-actions">
                            <a class="btn btn-danger removeQuestionBtn" removeId="<?php echo $i; ?>" href="javascript:void(0);">
                                <i class="icon-minus-sign"></i>Remove</a>
                        </div>

                    </fieldset>
                <?php } ?>
            <?php } ?>
        </div>
        <div class="form-actions">
            <div class="pull-left">
                <a class="btn btn-info" href="javascript:void(0)" id="addNewQuestion"><i class="icon-plus-sign"></i>Add
                    new question</a>
            </div>
            <div class="pull-right">
                <input class="btn btn-primary btn-large" type="submit" name="Submit"
                       value="<?php echo __('Save changes'); ?>">
            </div>
        </div>
    </form>
<?php } ?>