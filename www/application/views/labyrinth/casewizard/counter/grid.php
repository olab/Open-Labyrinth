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
<h3><?php echo __('counter grid'); ?></h3>

<?php $id_counter = (isset($templateData['oneCounter'])) ? $templateData['counters'][0]->id : ''; ?>
<form class="form-horizontal" action="<?php echo URL::base().'labyrinthManager/caseWizard/5/updateGrid/'.$templateData['map']->id.'/'.$id_counter; ?>" method="POST">
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Title</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody><?php
            foreach (Arr::get($templateData, 'nodes', array()) as $node) { ?>
            <tr>
                <td><?php echo $node->title; ?></p></td>
                <td><?php
                    foreach (Arr::get($templateData, 'counters', array()) as $counter) { ?>
                        <fieldset>
                            <div class="control-group">
                                <label for="nc_<?php echo $node->id.'_'.$counter->id; ?>" class="control-label"><?php echo $counter->name; ?></label>
                                <div class="controls">
                                    <input type="text" id="nc_<?php echo $node->id.'_'.$counter->id; ?>"  name="nc_<?php echo $node->id.'_'.$counter->id; ?>" value="<?php $c = $node->getCounter($counter->id); if ($c != NULL) echo $c->function; ?>"/>
                                </div>
                            </div>
                        </fieldset><?php
                    } ?>
                </td>
            </tr><?php
            } ?>
        </tbody>
    </table>
    <input class="btn btn-primary" type="submit" name="Submit" value="<?php echo __('submit'); ?>">
</form><?php
} ?>
