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

    <h1><?php echo __('Feedback editor for Labyrinth: "') . $templateData['map']->name . '"'; ?></h1>





    <form class="form-horizontal"
          action="<?php echo URL::base() . 'feedbackManager/updateGeneral/' . $templateData['map']->id; ?>"
          method="POST">

        <fieldset class="fieldset"><legend>General</legend>
            <div class="control-group">
                <label class="control-label" for="fb">
                    <?php echo __('Feedback irrespective of how user performs'); ?></label>

                <div class="controls">
                    <textarea name="fb" id="fb"><?php echo $templateData['map']->feedback; ?></textarea>
                </div>
            </div>
        </fieldset>

<div class="form-actions">
        <input class="btn btn-primary" type="submit" name="Submit" value="<?php echo __('Update'); ?>"></div>
    </form>


    <h3><?php echo __('Feedback for time taken'); ?></h3>

    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <td><?php echo __('Rule'); ?></td>
            <td>Actions</td>
        </tr>
        </thead>
        <tbody>
        <?php if(isset($templateData['time_feedback_rules']) and count($templateData['time_feedback_rules']) > 0) { ?>
        <?php foreach($templateData['time_feedback_rules'] as $rule) { ?>
        <tr>
            <td>
                <?php echo __('If time taken is'); ?><?php echo $rule->operator->title; ?>
                &nbsp;<?php echo $rule->value; ?>&nbsp;<?php echo __('then give feedback'); ?>
                &nbsp;[<?php echo $rule->message; ?>]
            </td>

            <td><a class="btn btn-danger"
                   href="<?php echo URL::base() . 'feedbackManager/deleteRule/' . $templateData['map']->id . '/' . $rule->id; ?>"><i class="icon-trash"></i><?php echo __('Delete'); ?></a>
            </td>


        </tr>
            <?php } ?>

        <?php }else{ ?>
            <tr class="info"><td colspan="2">There are no rules for time taken, yet. You may add one, using the form below.</td></tr>
<?php } ?>
        </tbody>

    </table>


    <form action="<?php echo URL::base() . 'feedbackManager/addRule/' . $templateData['map']->id . '/time'; ?>"
          class="form-horizontal" method="POST">

        <fieldset class="fieldset">
<legend>Add time-taken rule</legend>
            <div class="control-group">
                <label class="control-label" for="cop"><?php echo __('if time taken in this session is'); ?></label>

                <div class="controls">

                    <select name="cop" id="cop">
                        <?php if (isset($templateData['operators'])) { ?>
                            <option value="">select ...</option>
                            <?php if (count($templateData['operators']) > 0) { ?>
                                <?php foreach ($templateData['operators'] as $operator) { ?>
                                    <option
                                        value="<?php echo $operator->id; ?>"><?php echo $operator->title; ?></option>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </select>
                       <input type="text" name="cval" ><span class="help-inline"><?php echo __('seconds'); ?></span>
                </div>

            </div>
            <div class="control-group">
                <label class="control-label" for="cMess">then feedback</label>

                <div class="controls"><textarea id="cMess" name="cMess" ></textarea>
                </div>

            </div>

        </fieldset>

<div class="form-actions"> <input class="btn btn-primary" type="submit" name="Submit" value="<?php echo __('Create rule'); ?>"></div>

    </form>




    <h3><?php echo __('Feedback for nodes visited'); ?></h3>

    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <td><?php echo __('Rule'); ?></td>
            <td>Action</td>
        </tr>
        </thead>
        <tbody>

        <?php if (isset($templateData['visit_feedback_rules']) and count($templateData['visit_feedback_rules']) > 0) { ?>
            <?php foreach ($templateData['visit_feedback_rules'] as $rule) { ?>
                <tr>
                    <td><?php echo __('if visited node'); ?>&nbsp;<?php echo $rule->value; ?>
                        &nbsp;<?php echo __('then give feedback'); ?>&nbsp;[<?php echo $rule->message; ?>]
                    </td>
                    <td><a class="btn btn-danger"
                           href="<?php echo URL::base() . 'feedbackManager/deleteRule/' . $templateData['map']->id . '/' . $rule->id; ?>"><i class="icon-trash"></i> <?php echo __('Delete'); ?></a>
                    </td>
                </tr>
            <?php } ?>

        <?php }else{ ?>
            <tr class="info"><td colspan="2">There are no rules for nodes visited, yet. You may add one, using the form below.</td></tr>
        <?php } ?>
        </tbody>

    </table>


    <form class="form-horizontal"
          action="<?php echo URL::base() . 'feedbackManager/addRule/' . $templateData['map']->id . '/visit'; ?>"
          method="POST">

        <fieldset class="fieldset">
<legend>Add rule for nodes visited</legend>
            <div class="control-group">
                <label class="control-label" for="cval"><?php echo __('If visited node'); ?></label>

                <div class="controls">
                    <select name="cval" id="cval">
                        <option value="">select ...</option>
                        <?php if (isset($templateData['nodes']) and count($templateData['nodes']) > 0) { ?>
                            <?php foreach ($templateData['nodes'] as $node) { ?>
                                <option value="<?php echo $node->id; ?>"><?php echo $node->id; ?>
                                    : <?php echo $node->title; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>

            </div>
            <div class="control-group">
                <label class="control-label" for="cMess2"><?php echo __('then feedback'); ?></label>

                <div class="controls"><textarea name="cMess" id="cMess2"></textarea>
                </div>

            </div>

        </fieldset>

<div class="form-actions">
        <input class="btn btn-primary" type="submit" name="Submit" value="<?php echo __('Create rule'); ?>"></div>
    </form>

    <h3><?php echo __('Feedback for must visit and must avoid nodes'); ?></h3>

    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <td><?php echo __('Rule'); ?></td>
            <td>Actions</td>
        </tr>
        </thead>
        <tbody>
        <?php if (isset($templateData['must_visit_feedback_rules']) and count($templateData['must_visit_feedback_rules']) > 0) { ?>
            <?php foreach ($templateData['must_visit_feedback_rules'] as $rule) { ?>
                <tr>


                    <td><?php echo __('if visited must visit node'); ?>&nbsp;<?php echo $rule->operator->title; ?>
                        &nbsp;<?php echo $rule->value; ?>&nbsp;<?php echo __('then give feedback'); ?>
                        &nbsp;[<?php echo $rule->message; ?>]
                    </td>
                    <td><a class="btn btn-danger"
                           href="<?php echo URL::base() . 'feedbackManager/deleteRule/' . $templateData['map']->id . '/' . $rule->id; ?>"><i class="icon-trash"></i><?php echo __('Delete'); ?></a>
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>
        <?php if (isset($templateData['must_avoid_feedback_rules']) and count($templateData['must_avoid_feedback_rules']) > 0) { ?>
            <?php foreach ($templateData['must_avoid_feedback_rules'] as $rule) { ?>
                <tr>
                    <td> <?php echo __('if visited must avoid node'); ?>&nbsp;<?php echo $rule->operator->title; ?>
                        &nbsp;<?php echo $rule->value; ?>&nbsp;<?php echo __('then give feedback'); ?>
                        &nbsp;[<?php echo $rule->message; ?>]
                    </td>
                    <td><a class="btn btn-danger"
                           href="<?php echo URL::base() . 'feedbackManager/deleteRule/' . $templateData['map']->id . '/' . $rule->id; ?>"><i class="icon-trash"></i><?php echo __('Delete'); ?></a>
                    </td>
                </tr>

            <?php } ?>
        <?php } ?>
        <?php if (!(isset($templateData['must_visit_feedback_rules']) and count($templateData['must_visit_feedback_rules']) > 0)or !(isset($templateData['must_avoid_feedback_rules']) and count($templateData['must_avoid_feedback_rules']) > 0)) { ?>
<tr class="info"><td colspan="2">There are no rules for visited and avoided nodes, yet. You may add one, using the form below.</td></tr>

        <?php } ?>
        </tbody>

    </table>

    <form class="form-horizontal" action="<?php echo URL::base() . 'feedbackManager/addRule/' . $templateData['map']->id . '/must'; ?>"
          method="POST">
        <fieldset class="fieldset">
            <legend>Add rule for visited / avoided nodes</legend>
            <div class="control-group">
                <label class="control-label" for="crtype"><?php echo __('If the number of nodes of type'); ?></label>

                <div class="controls">
                    <select name="crtype" id="crtype">
                        <option value=""><?php echo __('select'); ?> ...</option>
                        <option value="mustvisit"><?php echo __('must visit'); ?></option>
                        <option value="mustavoid"><?php echo __('must avoid'); ?></option>
                    </select>
                </div>

            </div>
            <div class="control-group">
                <label class="control-label" for="cop2"><?php echo __('is'); ?></label>

                <div class="controls">
                    <select name="cop" id="cop2">
                        <?php if (isset($templateData['operators'])) { ?>
                            <option value="">select ...</option>
                            <?php if (count($templateData['operators']) > 0) { ?>
                                <?php foreach ($templateData['operators'] as $operator) { ?>
                                    <option value="<?php echo $operator->id; ?>"><?php echo $operator->title; ?></option>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </select>
                    <input type="text" name="cval">
                </div>

            </div>
            <div class="control-group">
                <label class="control-label" for="cMess3"><?php echo __('then feedback'); ?></label>

                <div class="controls">
                    <textarea name="cMess" id="cMess3"></textarea>
                </div>

            </div>
        </fieldset>

<div class="form-actions">
            <input class="btn btn-primary" type="submit" name="Submit" value="<?php echo __('Create rule'); ?>"></div>
    </form>

            <h3><?php echo __('Counter Feedback Rules'); ?></h3>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <td>Rule</td>
            <td>Actions</td>
        </tr>
        </thead>
        <tbody>
        <?php if (isset($templateData['counter_feedback_rules']) and count($templateData['counter_feedback_rules']) > 0) { ?>
            <?php foreach ($templateData['counter_feedback_rules'] as $rule) { ?>
                <tr>
                    <td>if counter&nbsp;<?php echo $rule->counter_id; ?>&nbsp;is&nbsp;<?php echo $rule->operator->title; ?>
                        &nbsp;<?php echo $rule->value; ?>&nbsp;then give feedback&nbsp;[<?php echo $rule->message; ?>]</td>
                    <td><a class="btn btn-danger" href="<?php echo URL::base() . 'feedbackManager/deleteRule/' . $templateData['map']->id . '/' . $rule->id; ?>"><i class="icon-trash"></i>Delete</a></td>
                </tr>
            <?php } ?>

        <?php }else{ ?>
            <tr class="info"><td colspan="2">There are no rules for time taken, yet. You may add one, using the form below.</td></tr>
        <?php } ?>

        </tbody>

    </table>

            <form class="form-horizontal"
                action="<?php echo URL::base() . 'feedbackManager/addRule/' . $templateData['map']->id . '/counter'; ?>"
                method="POST">

                <fieldset class="fieldset">
                    <legend>Add counter feedback rule</legend>
                    <div class="control-group">
                        <label class="control-label" for="cid"><?php echo __('If counter'); ?></label>

                        <div class="controls">
                            <select name="cid" id="cid">
                                <option value=""><?php echo __('select'); ?> ...</option>
                                <?php if (isset($templateData['counters']) and count($templateData['counters']) > 0) { ?>
                                    <?php foreach ($templateData['counters'] as $counter) { ?>
                                        <option value="<?php echo $counter->id; ?>"><?php echo $counter->name; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>

                    </div>
                    <div class="control-group">
                        <label class="control-label" for="cop3"><?php echo __('is'); ?></label>

                        <div class="controls">
                            <select name="cop" id="cop3">
                                <?php if (isset($templateData['operators'])) { ?>
                                    <option value=""><?php echo __('select'); ?> ...</option>
                                    <?php if (count($templateData['operators']) > 0) { ?>
                                        <?php foreach ($templateData['operators'] as $operator) { ?>
                                            <option
                                                value="<?php echo $operator->id; ?>"><?php echo $operator->title; ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            <input type="text" name="cval">
                        </div>

                    </div>
                    <div class="control-group">
                        <label class="control-label" for="cmess4"><?php echo __('then feedback'); ?></label>

                        <div class="controls"><textarea name="cMess" id="cmess4"></textarea>
                        </div>

                    </div>

                </fieldset>
<div class="form-actions">
                    <input class="btn btn-primary" type="submit" name="Submit" value="<?php echo __('Create rule'); ?>"></div>
            </form>


<?php } ?>


