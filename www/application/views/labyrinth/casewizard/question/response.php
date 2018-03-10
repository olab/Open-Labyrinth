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

                <h4><?php echo __('Questions for "') . $templateData['map']->name . '"'; ?></h4>

                            <?php if(isset($templateData['question'])) { ?>
                            <form class="form-horizontal" method="POST" action="<?php echo URL::base().'labyrinthManager/caseWizard/4/updateQuestion/'.$templateData['map']->id.'/'.$templateData['questionType'].'/'.$templateData['question']->id; ?>">
                            <?php } else { ?>
                            <form method="POST" class="form-horizontal" action="<?php echo URL::base().'labyrinthManager/caseWizard/4/saveNewQuestion/'.$templateData['map']->id.'/'.$templateData['questionType']; ?>">
                            <?php } ?>

                            <fieldset class="fieldset">
                                <div class="control-group">
                                    <label for="qstem" class="control-label"><?php echo __('Stem');?></label>
                                    <div class="controls">
                                        <textarea cols="50" rows="3" id="qstem"
                                                  name="qstem"><?php if (isset($templateData['question'])) echo $templateData['question']->stem; ?></textarea>
                                    </div>
                                </div>
                            </fieldset>
    <?php if(isset($templateData['args'])) { ?>
        <?php for($i = 1; $i <= (int)$templateData['args']; $i++) { ?>
    <fieldset class="fieldset">
        <legend><?php echo __('Response').' '.$i; ?></legend>
        <div class="control-group">
            <label for="qresp<?php echo $i; ?>t" class="control-label"><?php echo __('Response text');?></label>
            <div class="controls">
                <input type="text" name="qresp<?php echo $i; ?>t"  id="qresp<?php echo $i; ?>t"
                       value="<?php if(isset($templateData['question']) and count($templateData['question']->responses) > 0) echo $templateData['question']->responses[$i-1]->response; ?>">
            </div>
        </div>

        <div class="control-group">
            <label for="qfeed<?php echo $i; ?>"  class="control-label"><?php echo __('Feedback');?></label>
            <div class="controls">
                <input type="text" name="qfeed<?php echo $i; ?>" id="qfeed<?php echo $i; ?>"
                       value="<?php if(isset($templateData['question']) and count($templateData['question']->responses) > 0) echo $templateData['question']->responses[$i-1]->feedback; ?>"/>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"><?php echo __('Correctness');?></label>
            <div class="controls">
                <label class="radio">
                    <input type="radio" name="qresp<?php echo $i ?>y" value="1" <?php if(isset($templateData['question']) and count($templateData['question']->responses) > 0 and $templateData['question']->responses[$i-1]->is_correct == 1) echo 'checked=""'; ?>/> correct
                </label>
              <label class="radio">
                  <input type="radio" name="qresp<?php echo $i ?>y" value="0" <?php if(isset($templateData['question']) and count($templateData['question']->responses) > 0 and $templateData['question']->responses[$i-1]->is_correct == 0) echo 'checked=""'; ?>>incorrect
              </label>

            </div>
        </div>

        <div class="control-group">
            <label for="qresp<?php echo $i; ?>s"  class="control-label"><?php echo __('Score');?></label>
            <div class="controls">
                <select name="qresp<?php echo $i; ?>s" id="qresp<?php echo $i; ?>s">
                    <?php for($j = -10; $j <= 10; $j++) { ?>
                        <option value="<?php echo $j; ?>"
                            <?php if(isset($templateData['question']) and count($templateData['question']->responses) > 0 and $templateData['question']->responses[$i-1]->score == $j) { echo 'selected=""'; } ?>
                            <?php if(!isset($templateData['question']) and $j == 0) echo 'selected=""'; ?>><?php echo $j; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

    </fieldset>
        <?php } ?>
    <?php } ?>

    <fieldset class="fieldset">
        <legend><?php echo __('Options'); ?></legend>

        <div class="control-group">
            <label class="control-label"><?php echo __('Show answer to user');?></label>
            <div class="controls">
                <label class="radio">
                    <input type="radio" name="qshow" value="1" <?php if(isset($templateData['question']) and $templateData['question']->show_answer == 1) echo 'checked=""'; ?>/>Show
                </label>
                <label class="radio">
                    <input type="radio" name="qshow" value="0" <?php if(isset($templateData['question']) and $templateData['question']->show_answer == 0) echo 'checked=""'; ?>/>Do not show
                </label>

            </div>
        </div>


        <div class="control-group">
            <label for="numtries"  class="control-label"><?php echo __('Number of tries allowed'); ?>
            </label>
            <div class="controls">
                <select name="numtries" id="numtries"><option value="-1" <?php if(isset($templateData['question']) and $templateData['question']->num_tries == -1) echo 'selected=""'; ?>>no limit</option><option value="1" <?php if(isset($templateData['question']) and $templateData['question']->num_tries == 1) echo 'selected=""'; ?>>1 try</select>
            </div>
        </div>

        <div class="control-group">
            <label for="scount"  class="control-label"><?php echo __('Track score with existing counter'); ?>
            </label>
            <div class="controls">
                <select id="scount" name="scount">
                    <option value="0">no counter</option>
                    <?php if(isset($templateData['counters']) and count($templateData['counters']) > 0) { ?>
                        <?php foreach($templateData['counters'] as $counter) { ?>
                            <option value="<?php echo $counter->id; ?>" <?php if(isset($templateData['question']) and $counter->id == $templateData['question']->counter_id) echo 'selected=""'; ?>><?php echo $counter->name; ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label for="fback"  class="control-label"><?php echo __('Feedback'); ?>
            </label>
            <div class="controls">
                <textarea cols="60" rows="3" id="fback" name="fback"><?php if(isset($templateData['question'])) echo $templateData['question']->feedback; ?></textarea>
            </div>
        </div>

    </fieldset>



                                  <input class="btn btn-primary" type="submit" name="Submit" value="submit">

                            </form>

<?php } ?>