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
if (isset($templateData['map']) and isset($templateData['counter'])) { ?>
    <h3><?php echo __('Edit Counter').' '.$templateData['counter']->id.' "'.$templateData['counter']->name.'"'; ?></h3>
    <form id="form1" name="form1" method="post" action="<?php echo URL::base().'labyrinthManager/caseWizard/5/updateCounter/'.$templateData['map']->id.'/'.$templateData['counter']->id; ?>" class="form-horizontal">
        <fieldset class="fieldset">
            <div class="control-group">
                <label for="cName" class="control-label"><?php echo __('Counter Name'); ?></label>
                <div class="controls">
                    <input type="text" id="cName" name="cName" value="">
                </div>
            </div>

            <div class="control-group">
                <label for="cDesc" class="control-label"><?php echo __('Counter description (optional)'); ?></label>
                <div class="controls">
                    <textarea name="cDesc" id="cDesc" rows="6" cols="40"></textarea>
                </div>
            </div>

            <div class="control-group">
                <label for="cIconId" class="control-label"><?php echo __('Counter image (optional'); ?></label>
                <div class="controls">
                    <select name="cIconId" id="cIconId">
                        <option value="" selected="">no image</option><?php
                        foreach (Arr::get($templateData, 'images', array()) as $image) { ?>
                        <option value="<?php echo $image->id; ?>"><?php echo $image->name.'(ID:'.$image->id; ?>)</option><?php
                        } ?>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label for="cStartV" class="control-label"><?php echo __('Starting value (optional)'); ?></label>
                <div class="controls">
                    <input type="text" name="cStartV" id="cStartV" size="4" value="">
                </div>
            </div>

            <div class="control-group">
                <label for="cVisible" class="control-label"><?php echo __('Visibility'); ?></label>
                <div class="controls">
                    <select id="cVisible" name="cVisible">
                        <option value="1" selected=""><?php echo __('show'); ?></option>
                        <option value="0"><?php echo __('don\'t show'); ?></option>
                    </select>
                </div>
            </div>
        </fieldset>
        <input class="btn-primary btn" type="submit" name="Submit" value="submit"/>
    </form>

    <h4><?php echo __('Counter rules'); ?></h4>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (isset($templateData['rules']) and count($templateData['rules']) > 0) { ?>
            <?php foreach ($templateData['rules'] as $rule) { ?>
                <tr>
                    <td>
                        <img src="<?php echo URL::base(); ?>images/rule.gif" alt="rule"> if
                        '<?php echo $templateData['counter']->name; ?>'
                        is <?php echo $rule->relation->title; ?> <?php echo $rule->value; ?> then go to
                        node <?php echo $rule->redirect_node_id; ?> ('<?php echo $rule->redirect_node->title; ?>')
                    </td>
                    <td><a class="btn btn-primary" href="<?php echo URL::base().'labyrinthManager/caseWizard/5/deleteRule/'.$templateData['map']->id.'/'.$templateData['counter']->id.'/'.$rule->id.'/'.$rule->redirect_node_id; ?>">delete</a>
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
    <h5><?php echo __('Add counter rule'); ?></h5>
    <form class="form-horizontal" id="form2" name="form2" method="post" action="<?php echo URL::base().'labyrinthManager/caseWizard/5/addRule/'.$templateData['map']->id.'/'.$templateData['counter']->id; ?>">
        <fieldset class="fieldset">
            <div class="control-group">
                <label for="relation" class="control-label"><?php echo __('If value of counter'); ?>
                </label>

                <div class="controls">
                    <?php if (isset($templateData['nodes']) and count($templateData['nodes']) > 0) { ?>
                        <select id="relation" name="relation">
                            <?php foreach ($templateData['relations'] as $relation) { ?>
                                <option value="<?php echo $relation->id; ?>"><?php echo $relation->title ?></option>
                            <?php } ?>
                        </select>
                    <?php } ?>
                </div>
            </div>

            <div class="control-group">

                <label for="rulevalue" class="control-label"><?php echo __('Value'); ?></label>

                <div class="controls">

                    <input type="text" id="rulevalue" name="rulevalue"/>
                </div>
            </div>

            <div class="control-group">

                <label for="node" class="control-label"><?php echo __('then go to node');?></label>

                <div class="controls">
                    <?php if (isset($templateData['nodes']) and count($templateData['nodes']) > 0) { ?>
                        <select name="node" id="node">
                            <?php foreach ($templateData['nodes'] as $node) { ?>
                                <option value="<?php echo $node->id; ?>"><?php echo $node->title; ?></option>
                            <?php } ?>
                        </select>
                    <?php } ?>
                </div>
            </div>

            <div class="control-group">

                <label for="ctrval" class="control-label"><?php echo __('reset counter'); ?></label>

                <div class="controls">
                    <?php if (isset($templateData['nodes']) and count($templateData['nodes']) > 0) { ?>
                        <input type="text" name="ctrval" id="ctrval" value=""
                            /><span><?php echo __('type +, - or = an integer - e.g. +1 or =32'); ?></span>
                    <?php } ?>
                </div>
            </div>
            <input class="btn btn-primary" type="submit" name="Submit" value="<?php echo __('submit'); ?>">
        </fieldset>
    </form>
<?php } ?>


