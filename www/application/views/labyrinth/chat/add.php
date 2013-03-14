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
if (isset($templateData['map']) and isset($templateData['question_count'])) { ?>
    <script type="text/javascript">
        var questionCount = <?php echo $templateData['question_count'] ?>;
        var formAction = '<?php echo URL::base() . 'chatManager/saveNewChat/' . $templateData['map']->id . '/' ?>';
    </script>
    <h1><?php echo __('Add Chat'); ?></h1>
    <form id="chatForm" class="form-horizontal" name="chatForm" method="post" action="<?php echo URL::base() . 'chatManager/saveNewChat/' . $templateData['map']->id . '/' . $templateData['question_count']; ?>">
        <fieldset class="fieldset">
            <div class="control-group">
                <label for="cstem" class="control-label"><?php echo __('Stem'); ?></label>
                <div class="controls">
                    <textarea id="cstem" name="cStem" rows="3" cols="42"></textarea>
                </div>
            </div>
        </fieldset>
        <div id="questionContainer">
        <?php if (isset($templateData['question_count'])) { ?>
            <?php for ($i = 1; $i <= $templateData['question_count']; $i++) { ?>
                <fieldset class="fieldset" id="qDiv<?php echo $i; ?>">
                    <input type="hidden" name="questionIndex<?php echo $i; ?>" value="<?php echo $i; ?>"/>
                    <legend><?php echo __("Question #") . $i ?></legend>
                    <div class="control-group cQuestion">
                        <label for="question<?php echo $i; ?>" class="control-label"><?php echo __('Question'); ?></label>
                        <div class="controls question">
                            <input id="question<?php echo $i; ?>" type="text" name="question<?php echo $i; ?>" value=""/>
                        </div>
                    </div>
                    <div class="control-group cResponce">
                        <label for="response<?php echo $i; ?>" class="control-label"><?php echo __('Response'); ?></label>
                        <div class="controls responce">
                            <input type="text" name="response<?php echo $i; ?>" id="response<?php echo $i; ?>" value=""/>
                        </div>
                    </div>
                    <div class="control-group cCounter">
                        <label for="counter<?php echo $i; ?>" class="control-label"><?php echo __('Counter'); ?></label>
                        <div class="controls counter">
                            <input type="text" name="counter<?php echo $i; ?>" id="counter<?php echo $i; ?>" value=""/>&nbsp;type +, - or = an integer - e.g. '+1' or '=32'
                        </div>
                    </div>
                    <a class="btn btn-primary removeQuestionBtn" removeId="<?php echo $i; ?>" href="#">Remove</a>
                </fieldset>
            <?php } ?>
        <?php } ?>
        </div>
        
        <a class="btn btn-primary" href="#" id="addNewQuestion">Add new</a>
        
        <fieldset class="fieldset">
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
        <input class="btn btn-primary" type="submit" name="Submit" value="<?php echo __('submit'); ?>">
    </form>
<?php } ?>