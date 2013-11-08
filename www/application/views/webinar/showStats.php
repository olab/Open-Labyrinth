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
?>
<div class="page-header">
    <h1><?php echo __('Progress for') . ' ' . $templateData['webinar']->title; ?></h1>
</div>

<?php if(isset($templateData['webinar']) && isset($templateData['webinarData']) && isset($templateData['usersMap'])) { ?>
    <table class="table table-striped table-bordered" id="my-labyrinths">
        <tbody>
        <tr>
            <td style="text-align: center; font-weight: bold; background: #FFFFFF;font-style: normal; font-size: 14px" rowspan="2" colspan="2">Users</td>
            <td style="text-align: center; font-weight: bold; background: #FFFFFF;font-style: normal; font-size: 14px" rowspan="2">Include in report</td>
            <?php
            $stepsHeaders = array();

            $mapSteps = array();
            foreach($templateData['webinarData'] as $userId => $steps) {
                foreach($steps as $stepKey => $step) {
                    foreach($step as $mapId => $map) {
                        $mapSteps[$stepKey][] = $map['status'];
                    }
                }
            }

            foreach($templateData['webinarData'] as $userId => $steps) {
                foreach($steps as $stepKey => $step){
                    if(!isset($stepsHeaders[$stepKey]) || $stepsHeaders[$stepKey]['count'] < count($step)) {

                        $isShowReport = false;
                        if (in_array(2, $mapSteps[$stepKey])) {
                            $isShowReport = true;
                        }

                        $stepsHeaders[$stepKey]['count'] = count($step);
                        $stepsHeaders[$stepKey]['html']  = '<td colspan="' . $stepsHeaders[$stepKey]['count'] . '">' .
                            (isset($templateData['webinarStepMap'][$stepKey]) ? ($templateData['step'] == $stepKey) ? '<span style="color:#0088cc;font-weight:bold">' . $templateData['webinarStepMap'][$stepKey]->name . '</span>'
                                : $templateData['webinarStepMap'][$stepKey]->name
                                : '-') .
                                                                ($isShowReport ? ' <a title="" data-original-title="Get 4R report for this step" href="' . URL::base() . 'webinarManager/stepReport/' . $templateData['webinar']->id . '/' . $stepKey . '/' .  $templateData['dateId'].'" style="text-decoration: none;font-size: 130%;"><i class="icon-eye-open"></i></a>
                                                                                   <a title="" data-original-title="Publish 4R report for this step" href="' . URL::base() . 'webinarManager/publishStep/' . $templateData['webinar']->id . '/' . $stepKey . '/' . $templateData['dateId'] .  '" style="text-decoration: none;font-size: 130%;"><i class="icon-upload"></i></a>'
                                                                               : '') .
                                                           '</td>';
                    }
                }
            }
            ?>
            <?php foreach($stepsHeaders as $stepKey => $stepHeader) { echo $stepHeader['html']; } ?>
        </tr>
        <tr>
            <?php
            $maps = array();
            foreach($templateData['webinarData'] as $userId => $steps) {
                foreach($steps as $stepKey => $step) {
                    foreach($step as $mapId => $map) {
                        if(!isset($maps[$stepKey][$mapId])) {
                            $maps[$stepKey][$mapId]['map'] = $map['map'];

                            if(!isset($maps[$stepKey][$mapId]['showReport'])) {
                                $maps[$stepKey][$mapId]['showReport'] = false;
                            }

                            if(!$maps[$stepKey][$mapId]['showReport'] && $map['status'] == 2) {
                                $maps[$stepKey][$mapId]['showReport'] = true;
                            }
                        }
                    }
                }
            }

            foreach($maps as $stepKey => $m) {
                foreach($m as $mapId => $v) {
            ?>
                <td style="text-align: center; font-weight: bold;">
                    <?php echo $v['map']->name; ?>
                    <?php if($v['showReport']) { ?>
                        <a title="" data-original-title="Get 4R report for this labyrinth" href="<?php echo URL::base(); ?>webinarManager/mapReport/<?php echo $templateData['webinar']->id; ?>/<?php echo $v['map']->id; ?>/<?php echo $templateData['dateId']; ?>" style="text-decoration: none;"><i class="icon-eye-open"></i></a>
                    <?php } ?>
                </td>
            <?php }} ?>
        </tr>
        <?php foreach($templateData['webinarData'] as $userId => $steps) { ?>
        <tr>
            <?php $icon = (isset($templateData['usersAuthMap'][$userId]) && $templateData['usersAuthMap'][$userId]['icon'] != NULL) ? 'oauth/'.$templateData['usersAuthMap'][$userId]['icon'] : 'openlabyrinth-header.png' ; ?>
            <td style="width: 50px; text-align: center;"> <img <?php echo (isset($templateData['usersAuthMap'][$userId]) && $templateData['usersAuthMap'][$userId]['icon'] != NULL) ? 'width="32"' : ''; ?> src=" <?php echo URL::base() . 'images/' . $icon ; ?>" border="0"/></td>
            <td><?php echo isset($templateData['usersMap'][$userId]) ? $templateData['usersMap'][$userId]->nickname : '-'; ?></td>
            <td style="width: 120px; text-align: center;"><input type="checkbox" id="check<?php echo $userId; ?>" name="users_include[]" value="<?php echo $userId; ?>" <?php if($templateData['includeUsers'][$userId]) echo 'checked="checked"'; ?> onclick="ajaxCheck(<?php echo $templateData['includeUsersData'][$userId]; ?> , $('#check<?php echo $userId; ?> ').attr('checked') ? 1 : 0 )" /></td>
            <?php
            foreach($steps as $stepKey => $step) {
                foreach($step as $mapId => $map) {
                    switch($map['status']) {
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
            }
            ?>
        </tr>
        <?php } ?>
        </tbody>
    </table>
    <div class="form-actions">
        <div class="pull-right">
            <a class="btn btn-info" href="<?php echo URL::base(); ?>dforumManager/viewForum/<?php echo $templateData['webinar']->forum_id;?>">
                <i class="icon-comment icon-white"></i>
                Go to the Forum Topic
            </a>
        </div>
    </div>
<?php } ?>




<script>
    function ajaxCheck(id,isInclude) {
        var URL = "<?php echo URL::base(); ?>webinarManager/updateInclude4R/" + id + "/" + isInclude ;
        $.get(URL, function(data) {
            if(data != '') {
               return true;
            }
        });
    }

</script>