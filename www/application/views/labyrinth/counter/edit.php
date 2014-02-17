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
if (isset($templateData['map']) AND isset($templateData['counter'])) { ?>
<div class="page-header">
    <h1><?php echo __('Edit Counter').' '.$templateData['counter']->id.' "'.$templateData['counter']->name.'"'; ?></h1>
</div>
<form class="form-horizontal" id="form1" name="form1" method="post" action="<?php echo URL::base().'counterManager/updateCounter/'.$templateData['map']->id.'/'.$templateData['counter']->id; ?>">
    <fieldset class="fieldset">
        <legend><?php echo __('Counter Content'); ?></legend>
        <div class="control-group">
            <label for="cName" class="control-label"><?php echo __('Counter name'); ?></label>
            <div class="controls">
                <input id="cName" type="text" name="cName" value="<?php echo $templateData['counter']->name; ?>" />
            </div>
        </div>
        <div class="control-group">
            <label  for="cDesc" class="control-label"><?php echo __('Counter description (optional)'); ?></label>
            <div class="controls">
                <textarea id="cDesc" name="cDesc" rows="6" cols="40"><?php echo $templateData['counter']->description; ?></textarea>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('Counter status'); ?></label>
            <div class="controls">
                <select name="status">
                    <option value="0">Regular</option>
                    <option value="1" <?php if($templateData['counter']->status == 1) echo 'selected'; ?>>Main</option>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label  for="cIconId" class="control-label"><?php echo __('Counter image (optional)'); ?></label>
            <div class="controls">
                <select id="cIconId" name="cIconId"><?php
                    if($templateData['counter']->icon_id == 0) echo '<option value="0" selected="">no image</option>';
                    if(isset($templateData['images']) and count($templateData['images']) > 0) {
                        foreach($templateData['images'] as $image) { ?>
                            <option value="<?php echo $image->id; ?>" <?php if($templateData['counter']->icon_id == $image->id) echo 'selected=""'; ?>><?php echo $image->name; ?> (ID:<?php echo $image->id; ?>)</option>
                        <?php }
                    } ?>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label  for="cStartV" class="control-label"><?php echo __('Starting value (optional)'); ?></label>
            <div class="controls">
                <input id="cStartV" type="text" name="cStartV" size="4" value="<?php echo $templateData['counter']->start_value; ?>" />
            </div>
        </div>
        <div class="control-group">
            <label  for="cVisible" class="control-label"><?php echo __('Visible'); ?></label>
            <div class="controls">
                <select id="cVisible" name="cVisible">
                    <option value="1" <?php if($templateData['counter']->visible == 1) echo 'selected=""'; ?>><?php echo __('show'); ?></option>
                    <option value="0" <?php if($templateData['counter']->visible == 0) echo 'selected=""'; ?>><?php echo __("don't show"); ?></option>
                    <option value="2" <?php if($templateData['counter']->visible == 2) echo 'selected=""'; ?>><?php echo __('custom'); ?></option>
                </select>
            </div>
        </div>
        <div class="form-actions">
            <input class="btn btn-primary" type="submit" name="Submit" value="Submit" />
        </div>
    </fieldset>
</form>

<h4><?php echo __('Counter rules'); ?></h4>
<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>Rule</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php if(isset($templateData['rules']) and count($templateData['rules']) > 0) { ?>
        <?php foreach($templateData['rules'] as $rule) { ?>
            <tr>

               <td>
                    if '<?php echo $templateData['counter']->name; ?>' is <?php echo $rule->relation->title; ?> <?php echo $rule->value; ?> then go to node <?php echo $rule->redirect_node_id; ?> ('<?php echo $rule->redirect_node->title; ?>') and reset counter '<?php echo $rule->counter_value; ?>' </td>
                <td>
                    <div class="control-group">
                    <a class="btn btn-danger" href="<?php echo URL::base().'counterManager/deleteRule/'.$templateData['map']->id.'/'.$templateData['counter']->id.'/'.$rule->id.'/'.$rule->redirect_node_id; ?>"><i class="icon-trash"></i>Delete</a></div></td>
                </tr>
        <?php } ?>
    <?php } else{?>
        <tr class="info"><td colspan="2">There are no counter rules yet. You may add a rule, using the form below.</td></tr>
    <?php } ?>

    </tbody>

</table>

<form class="form-horizontal" id="form2" name="form1" method="post" action="<?php echo URL::base().'counterManager/addRule/'.$templateData['map']->id.'/'.$templateData['counter']->id; ?>">
    <fieldset class="fieldset">
        <legend><?php echo __('Add counter rule'); ?></legend>
    </fieldset>
    <div class="control-group">
        <label class="control-label" for="relation"><?php echo __('if value of counter'); ?></label>
<div class="controls">
        <?php if(isset($templateData['relations']) and count($templateData['relations']) > 0) { ?>
        <select id="relation" name="relation">
            <?php foreach($templateData['relations'] as $relation) { ?>
                <option value="<?php echo $relation->id; ?>"><?php echo $relation->title ?></option>
            <?php } ?>
        </select>
        <?php } ?>
         <input id="rulevalue" type="text" name="rulevalue"></div>
    </div>
    <div class="control-group">
        <label class="control-label" for="node"><?php echo __('then go to node'); ?></label>
        <div class="controls">
        <?php if(isset($templateData['nodes']) and count($templateData['nodes']) > 0) { ?>
        <select id="node" name="node">
            <?php foreach($templateData['nodes'] as $node) { ?>
                <option value="<?php echo $node->id; ?>"><?php echo $node->title; ?></option>
            <?php } ?>
        </select>
        <?php } ?>
    </div></div>
    <div class="control-group">
        <label class="control-label" for="ctrval"><?php echo __('reset counter'); ?></label>
        <div class="controls">
        <input id="ctrval" type="text" name="ctrval" value="" size="4">
        <?php echo __('type +, - or = an integer - e.g. +1 or =32'); ?><?php echo __(' - you need to change the value of the counter or it will loop'); ?>
        </div>
    </div>
    <div class="form-actions">
        <input class="btn btn-primary" type="submit" name="Submit" value="<?php echo __('Add rule'); ?>">
    </div>
</form>
<?php } ?>