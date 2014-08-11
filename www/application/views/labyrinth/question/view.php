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
<div class="page-header">
    <div class="pull-right question-btn-container">
        <div class="btn-group"><?php
            if (isset($templateData['question_types']) and count($templateData['question_types']) > 0) { ?>
            <button class="btn btn-primary" data-toggle="dropdown">
                <i class="icon-plus-sign icon-white"></i>Add Question<span class="caret"></span>
            </button>
            <ul class="dropdown-menu"><?php
                foreach ($templateData['question_types'] as $type) { ?>
                <li><a href="<?php echo URL::base().'questionManager/question/'.$templateData['map']->id.'/'.$type->id; ?>"><?php echo $type->title; ?></a></li><?php
                } ?>
            </ul><?php
            } ?>
            <button class="btn" id="copyQuestionBtn"><i class="icon-paste"></i> Paste question</button>
        </div>
    </div>
    <h1><?php echo __('Questions for "') . $templateData['map']->name . '"'; ?></h1>
    </div><?php
    if (isset($templateData['warningMessage'])) {
        echo '<div class="alert alert-error">';
        echo $templateData['warningMessage'];
        if(isset($templateData['listOfUsedReferences']) && count($templateData['listOfUsedReferences']) > 0){
            echo '<ul class="nav nav-tabs nav-stacked">';
            foreach($templateData['listOfUsedReferences'] as $referense){
                list($map, $node) = $referense;
                echo '<li><a href="'.URL::base().'nodeManager/editNode/'.$node['node_id'].'">'.$map['map_name'].' / '.$node['node_title'].'('.$node['node_id'].')'.'</a></li>';
            }
            echo '</ul>';
        }
        echo '</div>';
    } ?>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Embeddable</th>
                <th>Stem</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody><?php
        if (isset($templateData['questions']) and count($templateData['questions']) > 0) {
            foreach ($templateData['questions'] as $question) { ?>
                <tr>
                    <td><label><input class="code" readonly="readonly" type="text" value="[[QU:<?php echo $question->id; ?>]]"></label></td>
                    <td><?php echo $question->stem; ?></td>
                    <td><?php echo $question->type->title; ?></td>
                    <td>
                        <div class="btn-group">
                            <a class="btn btn-info" href="<?php echo URL::base() . 'questionManager/question/' . $templateData['map']->id . '/' . $question->entry_type_id . '/' . $question->id; ?>"><i class="icon-edit"></i>Edit</a>
                            <a class="btn" href="<?php echo URL::base() . 'questionManager/duplicateQuestion/' . $templateData['map']->id . '/' . $question->id; ?>"><i class="icon-th"></i>Duplicate</a>
                            <a class="btn btn-danger" data-toggle="modal" href="javascript:void(0)" data-target="#delete-question-<?php echo $question->id; ?>"><i class="icon-trash"></i>Delete</a>
                            <?php if(isset($templateData['isSuperuser'])) { ?>
                                <a class="btn btn-info" href="<?php echo URL::base().'questionManager/exportQuestion/'.$templateData['map']->id.'/'.$question->id; ?>"><i class="icon-edit"></i>Export</a>
                            <?php } ?>
                        </div>
                        <div class="modal hide alert alert-block alert-error fade in" id="delete-question-<?php echo $question->id; ?>">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="alert-heading"><?php echo __('Caution! Are you sure?'); ?></h4>
                            </div>
                            <div class="modal-body">
                                <p><?php echo __('You have just clicked the delete button, are you certain that you wish to proceed with deleting "' . $question->stem . '" question?'); ?></p>
                                <p>
                                    <a class="btn btn-danger" href="<?php echo URL::base() . 'questionManager/deleteQuestion/' . $templateData['map']->id . '/' . $question->id; ?>"><?php echo __('Delete'); ?></a>
                                    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                                </p>
                            </div>
                        </div>
                    </td>
                </tr><?php
            }
        } else { ?>
            <tr class="info"><td colspan="4">There are no available questions right now. You may add a question using the menu below.</td> </tr>
        <?php }?>
        </tbody>
    </table>

    <div class="modal hide" id="copyQuestionModal">
        <form class="form-horizontal question-copy-form" method="POST" action="<?php echo URL::base(); ?>questionManager/copyQuestion/<?php echo $templateData['map']->id; ?>">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3>Paste question</h3>
            </div>
            <div class="modal-body">
                <div>
                    <div class="control-group">
                        <label class="control-label">Question ID:</label>
                        <div class="controls"><label>
                            [[QU:<input style="width:50px;" type="text" name="questionID" value=""/>]]</label>
                        </div>
                    </div>
                    <?php if(isset($templateData['counters']) && count($templateData['counters']) > 0) { ?>
                    <div class="control-group">
                        <label for="counterID" class="control-label">Assign to counter:</label>
                        <div class="controls">
                            <select name="counterID" id="counterID">
                                <option value="">Select</option>
                                <?php foreach($templateData['counters'] as $counter) { ?>
                                <option value="<?php echo $counter->id; ?>"><?php echo $counter->name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0);" class="btn" data-dismiss="modal">Close</a>
                <button type="submit" class="btn btn-primary">Paste</button>
            </div>
        </form>
    </div>
<?php } ?>