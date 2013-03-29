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
    <script language="javascript" type="text/javascript">
        function jumpMenu(targ, selObj, restore) {
            if(selObj.options[selObj.selectedIndex].value == '7') {
                eval(targ + ".location='<?php echo URL::base(); ?>questionManager/addPick/<?php echo $templateData['map']->id; ?>/" + selObj.options[selObj.selectedIndex].value + "'");
            } else {
            eval(targ + ".location='<?php echo URL::base(); ?>questionManager/addQuestion/<?php echo $templateData['map']->id; ?>/" + selObj.options[selObj.selectedIndex].value + "'");
            }
            if (restore) selObj.selectedIndex = 0;
        }
    </script>
<div class="page-header">
    <h1><?php echo __('Questions for "') . $templateData['map']->name . '"'; ?></h1></div>

    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Embeddable</th>
            <th>Content</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (isset($templateData['questions']) and count($templateData['questions']) > 0) { ?>
            <?php foreach ($templateData['questions'] as $question) { ?>
                <tr>
                    <td>
<label>
                        <input class="code" readonly="readonly" type="text" value="[[QU:<?php echo $question->id; ?>]]"></label></td>
                    <td>

                        <?php echo $question->stem; ?> (<?php echo $question->type->value; ?>
                        , <?php echo $question->width; ?>, <?php echo $question->height; ?>)
                    </td>
                    <td>
                        <div class="btn-group">
                        <a class="btn btn-info"
                           href="<?php echo URL::base() . 'questionManager/editQuestion/' . $templateData['map']->id . '/' . $question->entry_type_id . '/' . $question->id; ?>"><i class="icon-edit"></i>Edit</a>
                        <a class="btn btn-danger"
                           href="<?php echo URL::base() . 'questionManager/deleteQuestion/' . $templateData['map']->id . '/' . $question->id; ?>"><i class="icon-trash"></i>Delete</a>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr class="info"><td colspan="3">There are no available questions right now. You may add a question using the menu below.</td> </tr>

        <?php }?>


        </tbody>
    </table>


    <form class="form-horizontal" action="#">
        <fieldset class="fieldset">
            <legend>Add question</legend>
            <?php if (isset($templateData['question_types']) and count($templateData['question_types']) > 0) { ?>
                <div class="control-group">
                    <label for="qt" class="control-label"><?php echo __('Question type'); ?>
                    </label>

                    <div class="controls">
                        <select class="span4" onchange="jumpMenu('parent',this,0)" id="qt" name="qt">
                            <option value=""><?php echo __('select'); ?> ...</option>
                            <?php foreach ($templateData['question_types'] as $type) { ?>
                                <option value="<?php echo $type->id ?>"><?php echo $type->title; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

            <?php } ?>
        </fieldset>
    </form>

<?php } ?>


