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
if(isset($templateData['webinar']) && isset($templateData['webinars'])) {
    echo View::factory('webinar/_topMenu')->set('scenario', $templateData['webinar'])->set('webinars', $templateData['webinars']);
}
$sectionIds = Arr::get($templateData, 'sections', array());
$isScenario = isset($templateData['webinar']);
$scenario   = Arr::get($templateData, 'webinar', false);
$changeStep = $scenario ? $scenario->changeSteps : "manually"; ?>
<script>
    var $labyrinthContainers = [],
        mapsJSON = {<?php
        if(count($templateData['maps'])) {
            echo 'maps: [';
            $mapsJSON = '';
            foreach($templateData['maps'] as $map) {
                $section = (get_class($map) == 'Model_Leap_Map_Node_Section') ? 'section' : '';
                $mapsJSON .= '{id: '.$map->id.', name: "'.base64_encode($map->name).'", section: "'.$section.'"}, ';
            }

            if(strlen($mapsJSON) > 2) {
                echo substr($mapsJSON, 0, strlen($mapsJSON) - 2);
            }
            echo ']';
        } ?>};
</script>
<form class="form-horizontal" id="webinarForm" name="webinarForm" method="post" action="<?php echo URL::base().'webinarmanager/save'; ?>">
    <h1>
        <?php echo $isScenario ? 'Edit' : 'Create'; echo __(" Scenario"); ?>
        <input type="submit" class="btn btn-primary btn-large submit-webinar-btn pull-right" name="submit" value="<?php echo $isScenario ? 'Save Scenario' : 'Create Scenario'; ?>"/>
    </h1>
    <input type="hidden" name="webinarId" value="<?php if($isScenario) echo $scenario->id; ?>">
    <fieldset class="fieldset">
        <legend><?php echo __('Scenario Details'); ?></legend>
        <div class="control-group">
            <label class="control-label" for="title"><?php echo __('Scenario Title'); ?></label>
            <div class="controls">
                <input type="text" class="span6" id="title" name="title" value="<?php if($isScenario) echo $scenario->title; ?>">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('Switching Steps'); ?></label>
            <div class="controls">
                <div class="radio_extended btn-group">
                    <input type="radio" name="switchingSteps" id="manually" value="manually" <?php if($changeStep == 'manually') echo 'checked'; ?>>
                    <label for="manually" data-class="btn-info" class="btn active btn-info">Manually</label>
                    <input type="radio" name="switchingSteps" id="automatic" value="automatic" <?php if($changeStep == 'automatic') echo 'checked'; ?>>
                    <label for="automatic" data-class="btn-info" class="btn">Automatic</label>
                </div>
            </div>
        </div><?php
        if ( ! $isScenario) {
            $forums = Arr::get($templateData, 'forums', array()); ?>
            <div class="control-group">
                <label for="firstmessage" class="control-label"><?php echo __('First message'); ?></label>
                <div class="controls"><textarea name="firstmessage" id="firstmessage" class="mceEditor"></textarea></div>
            </div>
            <div class="control-group">
                <label class="control-label"><?php echo __('Use existing forum') ?></label>
                <div class="controls">
                    <div class="radio_extended btn-group"><?php
                        if ($forums) {?>
                            <input autocomplete="off" type="radio" id="use" name="use" value="1" />
                            <label data-class="btn-info" class="btn" for="use"><?php echo __('Use'); ?></label><?php
                        }?>
                        <input autocomplete="off" type="radio" id="notUse" name="use" value="0" checked="checked" />
                        <label data-class="btn-info" class="btn" for="notUse" rel="tooltip" title="Will be created a new forum"><?php echo __('Do not use'); ?></label>
                    </div>
                </div>
            </div>
            <div class="control-group submitSettingsContainer hide">
                <label for="forum" class="control-label"><?php echo __('Forums') ?></label>
                <div class="controls">
                    <select id="forum" name="forum"><?php
                    foreach ($forums as $forum) { ?>
                        <option value="<?php echo $forum['id']; ?>"><?php echo $forum['name']; ?></option><?php
                    } ?>
                    </select>
                </div>
                <br /><?php
                foreach($forums as $forum) {
                    if (count($forum['topics'])) { ?>
                    <div class="topics hide" id="topics-<?php echo $forum['id'];?>">
                        <label for="topic" class="control-label"><?php echo __('Topics') ?></label>
                        <div class="controls">
                            <select id="topic" name="topic">
                                <option value="0">Please select</option><?php
                                foreach($forum['topics'] as $topic) { ?>
                                <option value="<?php echo $topic['id']; ?>"><?php echo $topic['name']; ?></option><?php
                                } ?>
                            </select>
                        </div>
                    </div><?php
                    }
                } ?>
            </div><?php
        } ?>
    </fieldset>

    <div id="steps-container"><?php
        if(isset($templateData['webinar']) && count($templateData['webinar']->steps)) {
            foreach($templateData['webinar']->steps as $step) { ?>
            <fieldset class="fieldset step-container-<?php echo $step->id; ?>" stepId="<?php echo $step->id; ?>">
                <legend>
                    Step - <input type="text" name="s<?php echo $step->id; ?>_name" value="<?php echo $step->name; ?>">
                    <button class="btn btn-danger btn-remove-step"><i class="icon-trash"></i></button>
                    <input type="hidden" name="stepIDs[]" value="<?php echo $step->id; ?>"/>
                </legend>
                <div id="labyrinth-container-<?php echo $step->id; ?>" containerId="<?php echo $step->id; ?>"><?php
                    if(count($step->maps)) {
                        $index = 1;
                        foreach($step->maps as $stepMap) {
                        $section = ($stepMap->which == 'section'); ?>
                        <div class="control-group labyrinth-item-<?php echo $index; ?>" itemNumber="<?php echo $index ?>">
                            <label for="s<?php echo $step->id; ?>-labyrinth-<?php echo $index; ?>" class="control-label">Labyrinth #<?php echo $index ?></label>
                            <div class="controls">
                                <select id="s<?php echo $step->id; ?>-labyrinth-<?php echo $index; ?>" name="s<?php echo $step->id; ?>_labyrinths[]" class="span6" <?php if ($section) echo 'data-id="'.$stepMap->reference_id.'" data-section="1"'; ?>><?php
                                foreach(Arr::get($templateData, 'maps', array()) as $m) {
                                    $mapId = $section ? Arr::get($sectionIds, $stepMap->reference_id, 0) : $stepMap->reference_id;
                                    $ifSectionClass = get_class($m) == 'Model_Leap_Map_Node_Section'; ?>
                                    <option value="<?php echo $ifSectionClass ? 'section'.$m->id : $m->id; ?>" <?php if($m->id == $mapId) echo 'selected'; if ($ifSectionClass) echo 'style="display:none";' ?>><?php
                                        echo $m->name; ?>
                                    </option><?php
                                } ?>
                                </select>
                                <button class="btn btn-danger remove-map"><i class="icon-trash"></i></button>

                                <div class="poll-node-section">
                                    <label>
                                        <input type="checkbox" class="cumulative-chb" name="cumulative[<?php echo $step->id.'-key-'.$index; ?>]" value="1" <?php if ($stepMap->cumulative) echo 'checked'; ?>>Cumulative
                                        <button type="button" class="cumulative-reset btn btn-info" style="<?php if ( ! $stepMap->cumulative) echo 'display: none;'; ?>" data-scenario="<?php echo $templateData['webinar']->id; ?>" data-map="<?php echo $mapId; ?>" data-loading-text="Loading...">Reset counters</button>
                                    </label>
                                </div>

                                <div class="poll-node-section"><?php
                                    $mapData = Arr::get($templateData, $mapId, array());
                                    foreach (Arr::get($mapData, 'pollNodes', array()) as $selectedNodeId=>$time) { ?>
                                    <select class="node-select-js" name="poll_nodes[]">
                                        <option value="0">Select poll node</option><?php
                                        foreach (Arr::get($mapData, 'mapNodes', array()) as $id=>$title) { ?>
                                        <option value="<?php echo $id; ?>" <?php if ($selectedNodeId == $id) echo 'selected'; ?>>
                                            <?php echo $title.' (id: '.$id.')'; ?>
                                        </option><?php
                                        } ?>
                                    </select>
                                    <input type="text" value="<?php echo $time; ?> sec" class="poll-node-indent" name="poll_nodes[]" required>
                                    <button class="btn btn-danger poll-node-indent delete-node-js" data-id="<?php echo $selectedNodeId; ?>">
                                        <i class="icon-trash"></i>
                                    </button><?php
                                    } ?>
                                    <button type="button" class="btn btn-info poll-node-js" data-id="<?php echo $mapId; ?>" data-loading-text="Loading...">
                                        Add poll node
                                    </button>
                                </div>
                            </div>
                        </div><?php
                        $index++;
                        }
                    } ?>
                </div>

                <div>
                    <button class="btn btn-info add-labyrinth-btn" type="button" containerId="<?php echo $step->id; ?>">
                        <i class="icon-plus-sign"></i>Add map or section
                    </button>
                </div>

                <script>
                    $labyrinthContainers.push($('#labyrinth-container-<?php echo $step->id; ?>'));
                </script>
            </fieldset><?php
            }
        } ?>
    </div>
    <div><button class="btn btn-info add-step-btn" type="button"><i class="icon-plus-sign"></i>Add Step</button></div>

    <hr>

    <h3>List of text macros</h3>
    <table id="macros_list" class="table table-bordered table-striped table-condensed">
        <thead>
            <tr>
                <th>#</th>
                <th>Text</th>
                <th>Hot Keys</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php
        $macros_list = isset($templateData['macros_list']) ? $templateData['macros_list'] : array();
        if(!empty($macros_list) && count($macros_list) > 0){
            foreach($macros_list as $macros) {
                ?>
                <tr>
                    <td>
                        <?php echo isset($macros_counter) ? ++$macros_counter : $macros_counter = 1; ?>
                    </td>
                    <td>
                        <input type="text" name="macros_text[]" value="<?php echo htmlspecialchars($macros->text) ?>">
                    </td>
                    <td>
                        <input type="text" name="macros_hot_keys[]" value="<?php echo htmlspecialchars($macros->hot_keys) ?>">
                    </td>
                    <td>
                        <span class="btn btn-danger remove-macros"><i class="icon-trash"></i></span>
                    </td>
                </tr>
            <?php
            }
        }
        ?>
        </tbody>
    </table>
    <div><span class="btn btn-info add-macros-btn"><i class="icon-plus-sign"></i>Add Macros</span></div>

    <script>
        $(document).ready(function(){
            var macros_list = $('#macros_list').find('tbody'),
                macrosTemplate = '<tr><td></td><td><input type="text" name="macros_text[]"></td><td><input type="text" name="macros_hot_keys[]"></td><td><span class="btn btn-danger remove-macros"><i class="icon-trash"></i></span></td></tr>';

            $(document).on( 'click', '.add-macros-btn', function(e) {
                e.preventDefault();
                macros_list.append(macrosTemplate);
            });

            $(document).on( 'click', '.remove-macros', function(e) {
                e.preventDefault();
                $(this).closest('tr').remove();
            });
        });
    </script>

    <hr>

    <h3>Assign the users</h3>
    <table id="assign-users" class="table table-bordered table-striped">
        <colgroup>
            <col style="width: 5%" />
            <col style="width: 5%" />
            <col style="width: 5%" />
            <col style="width: 85%" />
        </colgroup>
        <thead>
            <tr>
                <th style="text-align: center">Actions</th>
                <th style="text-align: center">Expert</th>
                <th style="text-align: center">Auth type</th>
                <th><a href="javascript:void(0);">User</a></th>
            </tr>
        </thead>
        <tbody><?php
        $loggedUserId = Auth::instance()->get_user()->id;
        if(isset($templateData['webinar']) and count($templateData['webinar']->users)) {
            foreach($templateData['webinar']->users as $existUser) { ?>
            <tr><?php
                $userArray = isset($templateData['usersMap'][$existUser->user_id]) ? $templateData['usersMap'][$existUser->user_id] : false;
                if($existUser->user_id == $loggedUserId) { ?>
                <td style="text-align: center">Author</td><input type="hidden" name="users[]" value="<?php echo $existUser->user_id; ?>"><?php
                } else { ?>
                <td style="text-align: center"><input type="checkbox" name="users[]" value="<?php echo $existUser->user_id; ?>" checked="checked"></td><?php
                } ?>
                <td style="text-align: center"><?php
                    if ($userArray AND $userArray['type_id'] != 1) { ?>
                        <input
                            id="expert<?php echo $userArray['id']; ?>"
                            type="checkbox"
                            name="experts[]"
                            value="<?php echo $userArray['id']; ?>"
                            <?php if (in_array($userArray['id'], $templateData['experts'])) echo 'checked'; ?>><?php
                    } ?>
                </td><?php
                $icon = (isset($templateData['usersMap'][$existUser->user_id]) AND $templateData['usersMap'][$existUser->user_id]['icon'] != NULL)
                    ? 'oauth/'.$templateData['usersMap'][$existUser->user_id]['icon']
                    : 'openlabyrinth-header.png' ; ?>
                <td style="text-align: center;">
                    <img <?php echo (isset($templateData['usersMap'][$existUser->user_id]) && $templateData['usersMap'][$existUser->user_id]['icon'] != NULL) ? 'width="32"' : ''; ?> src=" <?php echo URL::base() . 'images/' . $icon ; ?>" border="0"/>
                </td>
                <td><?php echo $existUser->user->nickname; ?></td>
            </tr><?php
            }
        }
        foreach(Arr::get($templateData, 'users', array()) as $user) { ?>
            <tr>
                <td style="text-align: center"><input type="checkbox" name="users[]" value="<?php echo $user['id']; ?>"></td>
                <?php $icon = ($user['icon'] != NULL) ? 'oauth/'.$user['icon'] : 'openlabyrinth-header.png' ; ?>
                <td style="text-align: center"><?php
                    if ($user['type_id'] != 1) { ?>
                        <input type="checkbox" name="experts[]" value="<?php echo $user['id']; ?>"><?php
                    } ?>
                </td>
                <td style="text-align: center;"> <img <?php echo ($user['icon'] != NULL) ? 'width="32"' : ''; ?> src=" <?php echo URL::base() . 'images/' . $icon ; ?>" border="0"/></td>
                <td><?php echo $user['nickname']; ?></td>
            </tr><?php
        } ?>
        </tbody>
    </table><?php
    if((isset($templateData['groups']) and count($templateData['groups'])) OR (isset($templateData['webinar']) and count($templateData['webinar']->groups))) { ?>
    <h3>Assign the groups</h3>
    <table id="assign-users" class="table table-bordered table-striped">
        <colgroup>
            <col style="width: 5%" />
            <col style="width: 90%" />
        </colgroup>
        <thead>
        <tr>
            <th style="text-align: center">Actions</th>
            <th><a href="javascript:void(0);">Group</a></th>
        </tr>
        </thead>
        <tbody><?php
        if(isset($templateData['webinar']) and count($templateData['webinar']->groups)) {
            foreach($templateData['webinar']->groups as $existGroup) { ?>
                <tr>
                    <td style="text-align: center"><input type="checkbox" name="groups[]" value="<?php echo $existGroup->group_id; ?>" checked="checked"></td>
                    <td><?php echo $existGroup->group->name; ?></td>
                </tr><?php
            }
        }
        if(isset($templateData['groups']) and count($templateData['groups'])) {
            foreach($templateData['groups'] as $group) { ?>
                <tr>
                    <td style="text-align: center"><input type="checkbox" name="groups[]" value="<?php echo $group->id; ?>"></td>
                    <td><?php echo $group->name; ?></td>
                </tr><?php
            }
        } ?>
        </tbody>
    </table><?php
    } ?>

    <div class="form-actions">
        <div class="map-error-empty" style="float: left;margin-top: 12px;color: red;display: none;">Please select labyrinths for each step</div>
        <div class=" pull-right">
            <input type="submit" class="btn btn-primary btn-large submit-webinar-btn" name="submit" value="<?php echo isset($templateData['webinar']) ? 'Save' : 'Create'; ?> <?php echo __('Scenario'); ?>" />
        </div>
    </div>
</form>

<script src="<?php echo ScriptVersions::get(URL::base().'scripts/visualeditor/base64v1_0.js'); ?>"></script>
<script src="<?php echo ScriptVersions::get(URL::base().'scripts/webinar.js'); ?>"></script><?php
if ( ! $isScenario) { ?>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/tinymce/js/tinymce/tinymce.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/tinyMceInit.js'); ?>"></script>
<script type="text/javascript">
    tinyMceInit('textarea', 0);
</script><?php }
?>