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
    <script language="javascript" type="text/javascript">
        function jumpMenu(targ, selObj, restore) {
            eval(targ + ".location='<?php echo URL::base().'labyrinthManager/caseWizard/5/addNewQuestion/'.$templateData['map']->id; ?>/" + selObj.options[selObj.selectedIndex].value + "'");
            if (restore) selObj.selectedIndex = 0;
        }
    </script>

    <h3><?php echo __('Questions "') . $templateData['map']->name . '"'; ?></h3><?php
    foreach (Arr::get($templateData, 'questions', array()) as $question) { ?>
    <input type="text" value="[[QU:<?php echo $question->id; ?>]]">
    <?php echo $question->stem.' ('.$question->type->value.', '.$question->width.', '.$question->height.')' ?> [
    <a href="<?php echo URL::base().'labyrinthManager/caseWizard/5/editQuestion/'.$templateData['map']->id.'/'.$question->entry_type_id.'/'.$question->id; ?>">edit</a> -
    <a href="<?php echo URL::base().'labyrinthManager/caseWizard/5/deleteQuestion/'.$templateData['map']->id.'/'.$question->id; ?>">delete</a>]<?php
    } ?>

    <p>add question<?php
    if (isset($templateData['question_types']) AND count($templateData['question_types']) > 0) { ?>
        <select onchange="jumpMenu('parent',this,0)" name="qt">
            <option value=""><?php echo __('select'); ?> ...</option>
            <?php foreach ($templateData['question_types'] as $type) { ?>
                <option value="<?php echo $type->id ?>"><?php echo $type->title; ?></option>
            <?php } ?>
        </select><?php
    }
} ?>