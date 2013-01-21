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
if (isset($templateData['map']) and isset($templateData['question_count']) and isset($templateData['chat'])) { ?>

                <h1><?php echo __('Edit Chat').' '.$templateData['chat']->id.' "'.$templateData['chat']->stem.'"'; ?></h1>

                            <form class="form-horizontal" id="chatForm" name="chatForm" method="post" action="<?php echo URL::base().'chatManager/updateChat/'.$templateData['map']->id.'/'.$templateData['chat']->id.'/'.$templateData['question_count']; ?>">

                                <fieldset class="fieldset">

                                    <div class="control-group">
                                        <label for="cstem" class="control-label"><?php echo __('Stem'); ?></label>

                                        <div class="controls">
                                            <textarea id="cstem" name="cStem" rows="3" cols="42"><?php echo $templateData['chat']->stem; ?></textarea>
                                        </div>
                                    </div>

                                </fieldset>


                                <?php if (isset($templateData['question_count'])) { ?>
                                    <?php for ($i = 1; $i <= $templateData['question_count']; $i++) { ?>
                                        <fieldset class="fieldset" id="qDiv<?php echo $i; ?>">
                                            <legend><?php echo __("Question #") . $i ?></legend>

                                            <div class="control-group">
                                                <label for="question<?php echo $i; ?>"
                                                       class="control-label"><?php echo __('Question'); ?></label>

                                                <div class="controls">
                                                    <input id="question<?php echo $i; ?>" type="text" name="question<?php echo $i; ?>"
                                                           value="<?php if(($i-1) < count($templateData['chat']->elements)) echo $templateData['chat']->elements[$i-1]->question; ?>"/>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label for="response<?php echo $i; ?>"
                                                       class="control-label"><?php echo __('Response'); ?></label>

                                                <div class="controls">
                                                    <input type="text" name="response<?php echo $i; ?>" id="response<?php echo $i; ?>"
                                                           value="<?php if(($i-1) < count($templateData['chat']->elements)) echo $templateData['chat']->elements[$i-1]->response; ?>"/>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label for="counter<?php echo $i; ?>"
                                                       class="control-label"><?php echo __('Counter'); ?></label>

                                                <div class="controls">
                                                    <input type="text" name="counter<?php echo $i; ?>" id="counter<?php echo $i; ?>"
                                                            value="<?php if(($i-1) < count($templateData['chat']->elements)) echo $templateData['chat']->elements[$i-1]->function; ?>"/>&nbsp;type +, - or = an integer - e.g. '+1' or '=32'
                                                </div>
                                            </div>


                                            <a class="btn btn-primary"
                                               href="<?php echo URL::base().'chatManager/removeEditChatQuestion/'.$templateData['map']->id.'/'.$templateData['chat']->id.'/'.$templateData['question_count'].'/'.$i; ?>">Remove</a>

                                        </fieldset>
                                    <?php } ?>
                                <?php } ?>



                                                <a class="btn btn-primary" href="<?php if(isset($templateData['question_count'])) { echo URL::base().'chatManager/addEditChatQuestion/'.$templateData['map']->id.'/'.$templateData['chat']->id.'/'.($templateData['question_count'] + 1); }
                                                else { echo URL::base().'chatManager/addEditChatQuestion/'.$templateData['map']->id.'/'.$templateData['chat']->id.'/3'; }?>">Add new</a>




                                    <fieldset class="fieldset">
                                        <div class="control-group">
                                            <label for="scount"
                                                   class="control-label"><?php echo __('Track score with existing counter'); ?></label>

                                            <div class="controls">
                                                <select id="scount" name="scount">
                                                    <?php if (isset($templateData['counters']) and count($templateData['counters']) > 0) { ?>

                                                        <option value="0">no counter</option>
                                                        <?php foreach ($templateData['counters'] as $counter) { ?>
                                                            <option value="<?php echo $counter->id; ?>" <?php if($counter->id == $templateData['chat']->counter_id) echo 'selected=""'; ?>><?php echo $counter->name; ?>
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