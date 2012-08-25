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
        function jumpMenu(targ,selObj,restore){ 
            eval(targ+".location='<?php echo URL::base(); ?>labyrinthManager/caseWizard/4/addNewQuestion/<?php echo $templateData['map']->id; ?>/"+selObj.options[selObj.selectedIndex].value+"'");
            if (restore) selObj.selectedIndex=0;
        }
    </script>
    <table width="100%" height="100%" cellpadding="6">
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('questions "') . $templateData['map']->name . '"'; ?></h4>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff"><td>
                            <table border="0" width="100%" cellpadding="1">
                                <?php if(isset($templateData['questions']) and count($templateData['questions']) > 0) { ?>
                                    <?php foreach($templateData['questions'] as $question) { ?>
                                        <tr>
                                            <td>
                                                <p>
                                                    <input type="text" value="[[QU:<?php echo $question->id; ?>]]"><?php echo $question->stem; ?> (<?php echo $question->type->value; ?>, <?php echo $question->width; ?>, <?php echo $question->height; ?>) 
                                                    [<a href="<?php echo URL::base().'labyrinthManager/caseWizard/4/editQuestion/'.$templateData['map']->id.'/'.$question->entry_type_id.'/'.$question->id; ?>">edit</a>
                                                    - <a href="<?php echo URL::base().'labyrinthManager/caseWizard/4/deleteQuestion/'.$templateData['map']->id.'/'.$question->id; ?>">delete</a>]
                                                </p>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                                <tr>
                                    <td>
                                        <p>add question 
                                            <?php if(isset($templateData['question_types']) and count($templateData['question_types']) > 0) { ?>
                                            <select onchange="jumpMenu('parent',this,0)" name="qt">
                                                <option value=""><?php echo __('select'); ?> ...</option>
                                                <?php foreach($templateData['question_types'] as $type) { ?>
                                                    <option value="<?php echo $type->id ?>"><?php echo $type->title; ?></option>
                                                <?php } ?>
                                            </select>
                                            <?php } ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td></tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>


