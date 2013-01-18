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
if (isset($templateData['map']) and isset($templateData['nodes'])) { ?>
<h1><?php echo __('Counter grid'); ?></h1>
<?php if(isset($templateData['oneCounter'])) { ?>
    <form action="<?php echo URL::base().'counterManager/updateGrid/'.$templateData['map']->id.'/'.$templateData['counters'][0]->id; ?>" method="POST">
<?php } else { ?>
    <form action="<?php echo URL::base().'counterManager/updateGrid/'.$templateData['map']->id; ?>" method="POST">
<?php } ?>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th></th>
            <?php if(isset($templateData['counters']) and count($templateData['counters']) > 0) { ?>
                <?php foreach($templateData['counters'] as $counter) { ?>
                    <th style="width:155px;">
                        <p style="text-align: center;"><?php echo __('Appear on node'); ?></p>
                        <a href="javascript:void(0)" id="counter_id_<?php echo $counter->id; ?>" class="btn btn-info btn-mini toggle-all-on">all on</a>
                        <a href="javascript:void(0)" id="counter_id_<?php echo $counter->id; ?>" class="btn btn-info btn-mini toggle-all-off">all off</a>
                        <a href="javascript:void(0)" id="counter_id_<?php echo $counter->id; ?>" class="btn btn-info btn-mini toggle-reverse">reverse</a>
                    </th>
                <?php } ?>
            <?php } ?>
        </tr>
        </thead>
        <?php if (count($templateData['nodes']) > 0) { ?>
            <?php foreach ($templateData['nodes'] as $node) { ?>
                <tr>
                    <td><p><?php echo $node->title; ?> [<?php echo $node->id; ?>]</p></td>
                    <?php if(isset($templateData['counters']) and count($templateData['counters']) > 0) { ?>
                        <?php foreach($templateData['counters'] as $counter) { ?>
                            <td>
                                <p style="margin-bottom: 0px;"><?php echo $counter->name; ?> <input class="input-small" type="text" size="5" name="nc_<?php echo $node->id; ?>_<?php echo $counter->id; ?>" value="<?php $c = $node->getCounter($counter->id); if($c != NULL) echo $c->function; ?>"></p>
                                <p style="margin-top:0px; text-align: center;"><input autocomplete="off" class="chk_counter_id_<?php echo $counter->id; ?>" type="checkbox" value="1" name="ch_<?php echo $node->id; ?>_<?php echo $counter->id; ?>" <?php if ($c != NULL) {if($c->display == 1) echo 'checked="checked"';}else{echo 'checked="checked"';} ?> /> <?php echo __("appear on node"); ?></p>
                            </td>
                        <?php } ?>
                    <?php } ?>
                </tr>
            <?php } ?>
        <?php } ?>
    </table>
    <div class="pull-right">
        <input class="btn btn-primary btn-large" type="submit" name="Submit" value="<?php echo __('Save changes'); ?>">
    </div>
                            </form>
<?php } ?>
