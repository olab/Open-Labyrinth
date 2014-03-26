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
$webinarId  = $templateData['webinar']->id;
$userType   = Auth::instance()->get_user()->type->name;
?>
<div class="page-header">
    <h1><?php echo __('Scenario Progress').' - "'.$templateData['webinar']->title; ?>"</h1>
</div>

<div class="report-type">
    <span class="report-type-title"><?php echo __('Repoet type') ?></span>
    <div class="radio_extended btn-group">
        <input type="radio" name="typeReport" id="4R" checked/>
        <label class="btn" for="4R" data-class="btn-info"><?php echo __('4R Report'); ?></label>
        <input type="radio" name="typeReport" id="SCT"/>
        <label class="btn" for="SCT" data-class="btn-info"><?php echo __('SCT Report'); ?></label>
        <input type="radio" name="typeReport" id="Multi"/>
        <label class="btn" for="Multi" data-class="btn-info"><?php echo __('Multi-player'); ?></label>
    </div>
    <select id="sct-webinars" style="display: none;"><?php
        foreach ($templateData['scenario'] as $webinarObj) { ?>
        <option value="<?php echo $webinarObj->id ?>" <?php if($webinarObj->id == $webinarId) echo 'selected'; ?>><?php echo $webinarObj->title ?></option><?php
        } ?>
    </select>

    <div id="discrepancyMap" class="alert alert-error hide">
        <button type="button" class="root-error-close close">×</button>
        You select scenario where is no such labyrinth.
    </div>
</div>

<?php if(isset($templateData['webinarData']) && isset($templateData['usersMap'])) { ?>
    <table class="table table-striped table-bordered" id="my-labyrinths">
        <tbody>
        <tr>
            <td style="text-align: center; font-weight: bold; background: #FFFFFF;font-style: normal; font-size: 14px" rowspan="2" colspan="2">Users</td>
            <td style="text-align: center; font-weight: bold; background: #FFFFFF;font-style: normal; font-size: 14px" rowspan="2">Include in report</td>
            <td style="text-align: center; font-weight: bold; background: #FFFFFF;font-style: normal; font-size: 14px; display: none;" rowspan="2" id="expert-th-js">Expert</td>
            <?php
            $stepsHeaders = array();
            // format publish steps
            $mapSteps = array();
            foreach($templateData['webinarData'] as $userId => $steps)
            {
                foreach($steps as $stepKey => $step)
                {
                        foreach($step as $mapId => $map)
                        {
                            $mapSteps[$stepKey][] = Arr::get($map, 'status');
                        }
                }
            }

            foreach($templateData['webinarData'] as $userId => $steps) {

                foreach($steps as $stepKey => $step) {
                    if(!isset($stepsHeaders[$stepKey]) || $stepsHeaders[$stepKey]['count'] < count($step)) {
                        $isShowReport = false;
                        if(isset($mapSteps[$stepKey]) && count($mapSteps[$stepKey]) > 0) {
                            foreach($mapSteps[$stepKey] as $mapStatus) {
                                if($mapStatus == 2) {
                                    $isShowReport = true;
                                    break;
                                }
                            }
                        }

                        $stepsHeaders[$stepKey]['count'] = count($step);
                        $changeStepLink = '';
                        if($userType != 'learner' && $userType != 'reviewer') {
                            $changeStepLink = ' <a data-toggle="tooltip" data-placement="left" title="" data-original-title="Change Scenario to this step" href="' . URL::base() . 'webinarManager/changeStep/'.$webinarId.'/'.$stepKey.'/1" style="text-decoration: none;font-size: 120%;"><i class="icon-off"></i></a>';
                        }
                        $stepsHeaders[$stepKey]['html']  =
                            '<td colspan="'.$stepsHeaders[$stepKey]['count'].'">'.
                            (isset($templateData['webinarStepMap'][$stepKey]) ?
                                ($templateData['webinar']->current_step == $stepKey)
                                ? '<span style="color:#0088cc;font-weight:bold">'.$templateData['webinarStepMap'][$stepKey]->name.'</span>'
                                : $templateData['webinarStepMap'][$stepKey]->name.$changeStepLink : '-').(
                                    ($isShowReport && $userType != 'learner' && $userType != 'reviewer')
                                    ? ' <a class="reportStepType" data-toggle="tooltip" data-placement="top" title="" data-original-title="Get 4R report for this step" href="'.URL::base().'webinarManager/stepReport4R/'.$webinarId.'/'.$stepKey.'/'.$webinarId.'" style="text-decoration: none;font-size: 130%;"><i class="icon-eye-open"></i></a>
                                        <a data-toggle="tooltip" data-placement="top" title="" data-original-title="Publish 4R report for this step" href="'.URL::base().'webinarManager/publishStep/'.$webinarId.'/'.$stepKey.'" style="text-decoration: none;font-size: 130%;"><i class="icon-upload"></i></a>'
                                    : '') . '</td>';
                    }
                }
            }
            foreach ($stepsHeaders as $stepKey => $stepHeader) echo $stepHeader['html']; ?>
        </tr>
        <tr><?php
        $maps = array();
        foreach ($templateData['webinarData'] as $userId => $steps) {
            foreach ($steps as $stepKey => $step) {
                foreach ($step as $mapId => $map) {
                    $maps[$stepKey][$mapId]['map'] = Arr::get($map, 'map');
                    if (Arr::get($map, 'status') == 2) $maps[$stepKey][$mapId]['showReport'] = true;
                }
            }
        }
        foreach ($maps as $stepKey => $m) {
            foreach($m as $mapId => $v) { ?>
            <td style="text-align: center; font-weight: bold;"><?php
                $map = Arr::get($v, 'map');
                echo $map->name;

                if (isset($v['showReport']) AND $v['showReport'] AND $userType != 'learner' AND $userType != 'reviewer') { ?>
                    <a class="reportMapType" data-toggle="tooltip" data-placement="top" title="" data-original-title="Get 4R report for this labyrinth" href="<?php echo URL::base().'webinarManager/mapReport4R/'.$webinarId.'/'.$v['map']->id.'/'.$webinarId; ?>" style="text-decoration: none;">
                        <i class="icon-eye-open"></i>
                    </a><?php
                } ?>
            </td><?php
            }
        } ?>
        </tr>
        <?php foreach($templateData['webinarData'] as $userId => $steps) { ?>
        <tr><?php
            $icon = (isset($templateData['usersAuthMap'][$userId]) && $templateData['usersAuthMap'][$userId]['icon'] != NULL) ? 'oauth/'.$templateData['usersAuthMap'][$userId]['icon'] : 'openlabyrinth-header.png' ; ?>
            <td style="width: 50px;text-align: center;">
                <img <?php echo (isset($templateData['usersAuthMap'][$userId]) && $templateData['usersAuthMap'][$userId]['icon'] != NULL) ? 'width="32"' : ''; ?> src=" <?php echo URL::base().'images/'.$icon ; ?>" border="0"/>
            </td>
            <td><?php echo isset($templateData['usersMap'][$userId]) ? $templateData['usersMap'][$userId]->nickname : '-'; ?></td>
            <td style="width: 120px; text-align: center;">
                <input
                    type="checkbox"
                    id="check<?php echo $userId; ?>"
                    value="<?php echo $userId; ?>"
                    onclick="ajaxCheck(<?php echo $templateData['includeUsersData'][$userId].', '.$userId; ?>)"
                    <?php if($templateData['includeUsers'][$userId]) echo 'checked'; ?>>
            </td>
            <td style="width: 120px; text-align: center; display: none;" class="expert-td-js"><?php
                if (isset($templateData['experts'][$userId])) { ?>
                <input
                    class="expert-js"
                    type="checkbox"
                    id="expert<?php echo $userId; ?>"
                    onclick="ajaxExpert(<?php echo $templateData['includeUsersData'][$userId].', '.$userId; ?>)"
                    <?php if($templateData['experts'][$userId]) echo 'checked'; ?>><?php
                } ?>
            </td><?php
            foreach($steps as $stepKey => $step)
            {
                foreach($step as $mapId => $map)
                {
                    switch($map['status'])
                    {
                        case 0:
                            echo '<td style="text-align: center;"><i class="icon-remove"></i></td>';
                            break;
                        case 1:
                            echo '<td style="text-align: center; background:#FCF8E3"><i class="icon-time"></i></td>';
                            break;
                        case 2:
                            echo '<td style="text-align: center; background: #DFF0D8"><i style="color:green;" class="icon-ok"></i></td>';
                            break;
                        default:
                            echo '<td></td>';
                    }
                }
            } ?>
        </tr><?php
        } ?>
        </tbody>
    </table><?php
} ?>

<div class="form-actions">
    <a class="btn btn-info pull-right" href="<?php echo URL::base().'dforumManager/viewForum/'.$templateData['webinar']->forum_id;?>">
        <i class="icon-comment icon-white"></i>Go to the Forum Topic
    </a>
</div>

<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/webinar/progress.js'); ?>"></script>