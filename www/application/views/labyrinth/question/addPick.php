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
    ?>

    <div class="pull-right"><a class="btn btn-info" href="#" id="addNewQuestion"><i class="icon-plus-sign"></i>Add new question</a></div>
    <div class="page-header"><h1><?php echo __('New question for "') . $templateData['map']->name . '"'; ?></h1></div>

    <form class="form-horizontal" method="POST" action="<?php echo URL::base() . 'questionManager/addNewPick/' . $templateData['map']->id; ?>">
        <input type="hidden" name="questionsIDs" id="questionsIDs" value=""/>
        <fieldset class="fieldset">
            <div class="control-group">
                <label for="qstem" class="control-label"><?php echo __('Stem'); ?></label>
                <div class="controls"><textarea id="qstem" name="qstem"><?php if (isset($templateData['question'])) echo $templateData['question']->stem; ?></textarea></div>
            </div>

            <div class="control-group">
                <label class="control-label"><?php echo __('Show answer to user'); ?></label>

                <div class="controls">
                    <label class="radio">
                        <input type="radio" name="qshow"value="1" <?php if (isset($templateData['question']) and $templateData['question']->show_answer == 1) echo 'checked=""'; ?>> show
                    </label>
                    <label class="radio">
                        <input type="radio" name="qshow" value="0" <?php if (isset($templateData['question']) and $templateData['question']->show_answer == 0) echo 'checked=""'; ?>> do not show
                    </label>
                </div>
            </div>
            <div class="control-group">
                <label for="scount" class="control-label"><?php echo __('Track score with existing counter'); ?></label>

                <div class="controls">
                    <select id="scount" name="scount">
                        <option value="0">no counter</option>
                        <?php if (isset($templateData['counters']) and count($templateData['counters']) > 0) { ?>
                            <?php foreach ($templateData['counters'] as $counter) { ?>
                                <option value="<?php echo $counter->id; ?>" <?php if (isset($templateData['question']) and $counter->id == $templateData['question']->counter_id) echo 'selected=""'; ?>><?php echo $counter->name; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label for="fback" class="control-label"><?php echo __('Number of tries allowed'); ?></label>

                <div class="controls">
                    <textarea id="fback" name="fback"><?php if (isset($templateData['question'])) echo $templateData['question']->feedback; ?></textarea>
                </div>
            </div>
        </fieldset>

        <div id="pickQuestionsContainer"></div>

        <div class="form-actions">
            <div class="pull-right"><input class="btn btn-primary btn-large" type="submit" name="Submit" value="Save"></div>
        </div>
    </form>

    <script type="text/javascript">
        var currentCount = 1;
        var globalIndex = 1;
        var $pickContainer = jQuery('#pickQuestionsContainer');
        var $questionIDs = jQuery('#questionsIDs');
                        
        var scoreOptions = '';
        for(var i = -10; i <= 10; i++) {
            scoreOptions += '<option value="' + i + '" ' + ((i == 0) ? 'selected' : '') + '>' + i + '</option>'
        }
                        
        addEmptyQuestion();
                        
        jQuery('#addNewQuestion').click(function() {
            addEmptyQuestion();
        });
        
        jQuery('.removePickQuestionBtn').live('click', function() {
            currentCount--;
            var id = $(this).attr('removeid');
            if(id > 0) $('#fieldset' + id).remove();
            
            var val = $questionIDs.val();
            $questionIDs.val(val.replace(' ' + id, ''));
            
            $.each($('#pickQuestionsContainer > .fieldset'), function(index, object) {
                $(object).children('legend').text('Response #' + (index + 1));
            });
        });
                        
        function addEmptyQuestion() {
            $questionIDs.val($questionIDs.val() + ' ' + globalIndex);
            var html = '<fieldset class="fieldset" id="fieldset' + globalIndex + '"><legend id="legend' + globalIndex + '">Response #' + currentCount + '</legend>' +
                '<div class="control-group">' + 
                '<label for="qresp' + globalIndex + 't" class="control-label">Response</label>' + 
                '<div class="controls"><input type="text" id="qresp' + globalIndex + 't" name="qresp' + globalIndex + 't" value=""/></div>' +
                '</div>' +
                '<div class="control-group">' +
                '<label for="qfeed' + currentCount + '" class="control-label">Feedback</label>' + 
                '<div class="controls"><input id="qfeed' + globalIndex + '" type="text" name="qfeed' + globalIndex + '" value=""></div>' +
                '</div>' +
                '<div class="control-group">' +
                '<label class="control-label">Correctness</label>' + 

                '<div class="controls">' +
                '<label class="radio"><input type="radio" name="qresp' + globalIndex + 'y" value="1"> correct</label>' +
                '<label class="radio"><input type="radio" name="qresp' + globalIndex + 'y" value="0"> incorrect</label>' +
                '</div>' +
                '</div>' +
                '<div class="control-group">' +
                '<label for="qresp' + globalIndex + 's" class="control-label">Score</label>' +

                '<div class="controls">' +
                '<select id="" name="qresp' + globalIndex + 's">' + scoreOptions + '</select>' +
                '</div>' + 
                '</div>' +
                '<div class="form-actions"><a class="btn btn-danger removePickQuestionBtn" removeid="' + globalIndex + '" href="#"><i class="icon-minus-sign"></i>Remove</a></div>' +
                '</fieldset>';
                                    
            $pickContainer.append(html);      
            currentCount++;
            globalIndex++;
        }  
    </script>

<?php } ?>