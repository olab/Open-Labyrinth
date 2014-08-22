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
 */ ?>
<h1 class="page-header">
    <?php echo __('Assign for ').$templateData['condition']->name; ?>
    <button class="btn btn-primary btn-large pull-right" type="submit"><?php echo __('Save changes'); ?></button>
    <button class="btn btn-primary btn-large pull-right addConditionAssign" type="button"><?php echo __('Add assign'); ?></button>
</h1>
<form action="<?php echo URL::base().'counterManager/updateGrid/'.$templateData['condition']->id; ?>" method="POST">
    <label class="conditionAssignBl">
        Assign to
        <select class="conditionAssign"><?php
            foreach ($templateData['scenarios'] as $scenario) { ?>
            <option value="<?php echo $scenario->id; ?>"><?php echo $scenario->title; ?></option><?php
        } ?>
        </select>
        <button class="btn btn-danger" type="button"><i class="icon-trash"></i></button>
    </label>
    <!-- scenarios block -->
    <label class="conditionAssignBl" style="display: none;">
        Assign to
        <select class="conditionAssign"><?php
            foreach ($templateData['scenarios'] as $scenario) { ?>
                <option value="<?php echo $scenario->id; ?>"><?php echo $scenario->title; ?></option><?php
            } ?>
        </select>
        <button class="btn btn-danger deleteAssign" type="button"><i class="icon-trash"></i></button>
    </label>
    <!-- end scenarios block -->
</form>
<script src="<?php echo ScriptVersions::get(URL::base().'scripts/conditions.js'); ?>"></script>
<!--<form action="--><?php //echo URL::base().'counterManager/updateGrid/'.$templateData['condition']->id; ?><!--" method="POST">-->
<!--    <h2>Nodes</h2>-->
<!--    <table class="table table-striped table-bordered">-->
<!--        <thead>-->
<!--            <tr>-->
<!--                <th></th>-->
<!--                --><?php //foreach(Arr::get($templateData, 'counters', array()) as $counter) { ?>
<!--                <th style="width:155px;">-->
<!--                    --><?php //echo __('Appear on node'); ?>
<!--                    <a href="javascript:void(0)" id="counter_id_--><?php //echo $counter->id; ?><!--" class="btn btn-info btn-mini toggle-all-on">all on</a>-->
<!--                    <a href="javascript:void(0)" id="counter_id_--><?php //echo $counter->id; ?><!--" class="btn btn-info btn-mini toggle-all-off">all off</a>-->
<!--                    <a href="javascript:void(0)" id="counter_id_--><?php //echo $counter->id; ?><!--" class="btn btn-info btn-mini toggle-reverse">reverse</a>-->
<!--                </th>-->
<!--                --><?php //} ?>
<!--            </tr>-->
<!--        </thead>-->
<!--        <tbody>--><?php
//        foreach (Arr::get($templateData, 'nodes', array()) as $node) { ?>
<!--            <tr>-->
<!--                <td><p>--><?php //echo $node->title; ?><!-- [--><?php //echo $node->id; ?><!--]</p></td>-->
<!--                --><?php //foreach(Arr::get($templateData, 'counters', array()) as $counter) { ?>
<!--                <td>-->
<!--                    <div>--><?php //echo $counter->name; ?><!--</div>-->
<!--                    <input class="input-small not-autocomplete" type="text" size="5" name="nc_--><?php //echo $node->id; ?><!--_--><?php //echo $counter->id; ?><!--" value="--><?php //$c = $node->getCounter($counter->id); if($c != NULL) echo $c->function; ?><!--">-->
<!--                    <label>-->
<!--                        <input autocomplete="off" class="chk_counter_id_--><?php //echo $counter->id; ?><!--" type="checkbox" value="1" name="ch_--><?php //echo $node->id; ?><!--_--><?php //echo $counter->id; ?><!--" --><?php //if ($c != NULL) {if($c->display == 1) echo 'checked="checked"';}else{echo 'checked="checked"';} ?><!-- /> --><?php //echo __("appear on node"); ?>
<!--                    </label>-->
<!--                </td>-->
<!--                --><?php //} ?>
<!--            </tr>--><?php
//        } ?>
<!--        </tbody>-->
<!--    </table>-->
<!--    <button class="btn btn-primary btn-large pull-right" type="submit">--><?php //echo __('Save changes'); ?><!--</button>-->
<!--</form>-->
