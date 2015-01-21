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
$scenario = $templateData['scenario']; ?>
<h1><?php echo __("Scenario"); ?> '<?php echo $scenario->title; ?>'</h1>

<form class="form-horizontal" id="addManualForm" name="addManualForm" method="post" action="<?php echo URL::base() ?>"><?php
    if(count($scenario->steps)) {
        if(count(Notice::get('error'))) { ?>
            <div class="alert alert-error">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?php echo Notice::get('error'); ?>
            </div><?php
        }
        foreach($scenario->steps as $scenarioStep) { ?>
        <fieldset class="fieldset">
            <legend><?php
                echo $scenario->current_step == $scenarioStep->id ? ('<b style="color: #0088cc;">'.$scenarioStep->name.'</b>') : $scenarioStep->name; ?>
            </legend><?php
            if(count($scenarioStep->maps)) {
                foreach($scenarioStep->maps as $index => $scenarioMap) { ?>
                <div class="control-group">
                    <label class="control-label" for="title"><?php echo '#'.($index + 1); ?></label>
                    <div style="margin-top: 3px;" class="controls">
                        <span><?php
                            $type = 'labyrinth';
                            if ($scenarioMap->which == 'section') {
                                $type = 'section';
                                echo $scenarioMap->map_node_section->name;
                            } else {
                                echo $scenarioMap->map->name;
                            } ?>
                        </span><?php
                        if ($scenario->current_step == $scenarioStep->id AND
                            isset($templateData['mapsMap'][$scenarioStep->id][$scenarioMap->reference_id]) AND
                            ($templateData['mapsMap'][$scenarioStep->id][$scenarioMap->reference_id] == 0 OR $templateData['mapsMap'][$scenarioStep->id][$scenarioMap->reference_id] == 1)) { ?>
                            <a href="<?php echo URL::base().'webinarManager/play/'.$scenario->id.'/'.$scenario->current_step.'/'.$scenarioMap->reference_id.'/'.$type; ?>" class="btn btn-success btn-small">
                                <i class="icon-play"></i>Play
                            </a><?php
                        } ?>
                    </div>
                </div><?php
                }
            } ?>
        </fieldset><?php
        }
    }

    if($scenario->forum_id) { ?>
    <div class="form-actions">
        <div class="pull-right">
            <a class="btn btn-info" href="<?php echo URL::base().'dforumManager/viewForum/'.$scenario->forum_id;?>">
                <i class="icon-comment icon-white"></i> <?php echo __('Go to the Forum Topic'); ?>
            </a>
        </div>
    </div><?php
    } ?>
</form>