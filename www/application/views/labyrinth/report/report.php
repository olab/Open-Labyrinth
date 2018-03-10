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

$isFeedbackExists = (
    !empty($templateData['feedbacks']['general']) ||
    !empty($templateData['feedbacks']['timeTaken']) ||
    !empty($templateData['feedbacks']['nodeVisit']) ||
    !empty($templateData['feedbacks']['mustVisit']) ||
    !empty($templateData['feedbacks']['counters'])
);

function getRandomColor()
{
    mt_srand((double)microtime() * 1000000);
    $c = '';
    while (strlen($c) < 6) {
        $c .= sprintf("%02X", mt_rand(0, 255));
    }

    return $c;
}

if (isset($templateData['session'])) {
    if (isset($templateData['nextCase']) || isset($templateData['webinarForum']) || isset($templateData['webinarID'])) { ?>
        <h3>Scenario actions</h3>
        <div>
            <?php if (isset($templateData['nextCase'])) { ?>
                <a href="<?php echo URL::base() . 'webinarManager/play/' . $templateData['nextCase']['webinarId'] . '/' . $templateData['nextCase']['webinarStep'] . '/' . $templateData['nextCase']['webinarMap']; ?>"
                   class="btn btn-success"><i class="icon-play"></i><?php echo __("Play the next labyrinth"); ?></a>
            <?php }
            if (isset($templateData['webinarForum'])) { ?>
            <a class="btn btn-info"
               href="<?php echo URL::base(); ?>dforumManager/viewForum/<?php echo $templateData['webinarForum']; ?>">
                <i class="icon-comment icon-white"></i>
                <?php echo __('Go to the Forum Topic'); ?>
                </a><?php
            }
            if (isset($templateData['webinarForum'])) { ?>
            <a class="btn"
               href="<?php echo URL::base(); ?>webinarManager/render/<?php echo $templateData['webinarID']; ?>">
                <i class="icon-folder-open icon-white"></i>
                <?php echo __('Go to the Scenario steps'); ?>
                </a><?php
            } ?>
        </div>
        <hr/><?php
    } ?>

    <div class="pull-right">
        <a href="<?php echo URL::base() . 'reportManager/exportToExcel/' . $templateData['session']->id; ?>"
           class="btn btn-primary"><?php echo __('Export to Excel'); ?></a>
    </div>
    <h1 class="report-title"><?php echo __('Labyrinth session "') . $templateData['session']->map->name . '"' . ' user ' . $templateData['session']->user->nickname; ?></h1>

    <?php
    $flash = Session::instance()->get_once('finalSubmit', null);
    if (!empty($flash)) {
        ?>
        <div class="alert alert-error">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?php echo $flash; ?>
        </div>
        <?php
    }
    ?>

    <table class="table table-striped table-bordered">
    <tr>
        <td><?php echo __('user'); ?></td>
        <td><?php echo $templateData['session']->user->nickname; ?></td>
    </tr>
    <tr>
        <td>session</td>
        <td><?php echo $templateData['session']->id; ?></td>
    </tr>
    <tr>
        <td>Labyrinth</td>
        <td><?php echo $templateData['session']->map->name; ?> (<?php echo $templateData['session']->map->id; ?>)</td>
    </tr>
    <tr>
        <td>start time</td>
        <td><?php echo date('Y.m.d H:i:s', round($templateData['session']->start_time)); ?></td>
    </tr>
    <tr>
        <td>time taken</td>
        <td><?php
            if (count($templateData['session']->traces) > 0) {
                $max = $templateData['session']->start_time;
                foreach ($templateData['session']->traces as $val) {
                    if ($val->end_date_stamp != null && $val->end_date_stamp > $max) {
                        $max = $val->end_date_stamp;
                    } else {
                        if ($val->date_stamp > $max) {
                            $max = $val->date_stamp;
                        }
                    }
                }
                $t = $max - $templateData['session']->start_time;
                echo gmdate('H:i:s', round($t));
            } ?>
        </td>
    </tr>
    <tr>
        <td>nodes visited</td>
        <td><?php echo count($templateData['session']->traces); ?>
            &nbsp;<?php echo __('nodes visited altogether of which'); ?>&nbsp;<?php
            $mustVisited = 0;
            $mustAvoid = 0;
            if (count($templateData['session']->traces) > 0) {
                foreach ($templateData['session']->traces as $trace) {
                    if ($trace->node->priority_id == 3) {
                        $mustVisited++;
                    }
                    if ($trace->node->priority_id == 2) {
                        $mustAvoid++;
                    }
                }
            }
            echo $mustVisited; ?>&nbsp;<?php echo __('required nodes and'); ?>&nbsp;
            <?php echo $mustAvoid; ?>&nbsp;<?php echo __('avoid nodes visited'); ?></td>
    </tr>
    <?php

    if (isset($templateData['session']->traces['0'])) {
        $counterPoints = $templateData['session']->traces['0']->counters;
    } else {
        $counterPoints = '';
    }

    if ($progress = DB_ORM::model('Map_Counter')->progress($counterPoints, $templateData['session']->map->id)): ?>
        <tr>
            <td>your score</td>
            <td><?php echo $progress; ?></td>
        </tr>
    <?php endif; ?>
    </table>

    <?php if ($isFeedbackExists) { ?>
    <table class="table table-striped table-bordered">
    <?php if (isset($templateData['feedbacks']['general'])) { ?>
        <tr>
            <td><?php echo __('general feedback'); ?></td>
            <td><?php echo $templateData['feedbacks']['general']; ?></td>
        </tr>
    <?php }
    if (isset($templateData['feedbacks']['timeTaken']) and count($templateData['feedbacks']['timeTaken']) > 0) { ?>
        <tr>
            <td><?php echo __('feedback for time taken'); ?></td>
            <td><?php foreach ($templateData['feedbacks']['timeTaken'] as $msg) {
                    echo $msg . '<br/>';
                } ?></td>
        </tr>
    <?php }
    if (isset($templateData['feedbacks']['nodeVisit']) and count($templateData['feedbacks']['nodeVisit']) > 0) { ?>
        <tr>
            <td><?php echo __('feedback for nodes visit'); ?></td>
            <td> <?php foreach ($templateData['feedbacks']['nodeVisit'] as $msg) {
                    echo $msg . '<br/>';
                } ?></td>
        </tr>
    <?php }
    if (isset($templateData['feedbacks']['mustVisit']) and count($templateData['feedbacks']['mustVisit']) > 0) { ?>
        <tr>
            <td><?php echo __('feedback for must visit'); ?></td>
            <td><?php foreach ($templateData['feedbacks']['mustVisit'] as $msg) {
                    echo $msg . '<br/>';
                } ?></td>
        </tr>
    <?php }
    if (isset($templateData['feedbacks']['mustAvoid']) and count($templateData['feedbacks']['mustAvoid']) > 0) { ?>
        <tr>
            <td><?php echo __('feedback for must avoid'); ?></td>
            <td><?php foreach ($templateData['feedbacks']['mustAvoid'] as $msg) {
                    echo $msg . '<br/>';
                } ?></td>
        </tr>
    <?php }
    if (isset($templateData['feedbacks']['counters']) and count($templateData['feedbacks']['counters']) > 0) { ?>
        <tr>
            <td><?php echo __('feedback for counters'); ?></td>
            <td><?php foreach ($templateData['feedbacks']['counters'] as $msg) {
                    echo $msg . '<br/>';
                } ?></td>
        </tr>
        <?php
    }
    ?>
    </table>
        <?php } ?>
    
    <?php
    if (count($templateData['responses'])) { ?>
        <h3><?php echo __('Questions'); ?></h3>
        <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <td>ID</td>
            <td>stem</td>
            <td>response</td>
            <td>correct</td>
            <td><?php echo __('feedback'); ?></td>
        </tr>
        </thead>
        <tbody><?php
        foreach ($templateData['responses'] as $response) {
            /** @var Model_Leap_Map_Question $question */
            $question = $templateData['questions'][$response->question_id];
            $responseMap = array();
            $questionType = $question->type->value;
            $isGrid = ($questionType === 'mcq-grid' || $questionType === 'pcq-grid');

            if ($isGrid) {
                $user_response = json_decode($response->response, true);
                $userResponses = [];
                foreach ($user_response as $array) {
                    $userResponses[$array['subQuestionId']][$array['subQuestionResponse']['parent_id']] = DB_ORM::select('Map_Question_Response')
                        //->from(Model_Leap_Map_Question_Response::table())
                        //->column('is_correct')
                        //->column('feedback')
                        ->where('id', '=', $array['subQuestionResponse']['id'])
                        ->query()
                        ->fetch(0);
                }
            } else {
                $user_response = html_entity_decode($response->response);
            }

            if ($questionType == 'dd' && count($question->responses) > 0) {
                foreach ($question->responses as $r) {
                    $responseMap[$r->id] = $r;
                }
            } ?>
            <tr>
            <td><?php echo $question->id; ?></td>
            <td><?php echo $question->stem; ?></td>
            <td><?php
                if ($questionType == 'dd') {
                    $jsonObj = json_decode($response->response, true);
                    if (count($jsonObj)) {
                        foreach ($jsonObj as $o) {
                            if (isset($responseMap[$o])) {
                                echo '<p>' . $responseMap[$o]->response . '</p>';
                            }
                        }
                    }
                } elseif ($isGrid) {
                    $subQuestions = $question->subQuestions;
                    $responses = $question->responses;
                    $isPCQ = ($questionType === 'pcq-grid');
                    ?>
                    <table class="table table-condensed table-bordered">
                        <thead>
                        <tr>
                            <th></th>
                            <?php foreach ($responses as $responseGrid) { ?>
                                <th>
                                    <?php echo $responseGrid->response ?>
                                </th>
                            <?php } ?>
                        </tr>
                        </thead>
                        <tbody>

                        <?php foreach ($subQuestions as $subQuestion) { ?>
                            <tr>
                                <td>
                                    <?php echo $subQuestion->stem ?>
                                </td>

                                <?php foreach ($responses as $responseGrid) { ?>
                                    <td>
                                        <div class="control-group">
                                            <input
                                                class="mcqResponse"
                                                type="<?php echo($isPCQ ? 'radio' : 'checkbox') ?>"
                                                name="userResponses[<?php echo $subQuestion->id ?>][<?php echo $responseGrid->id ?>]"
                                                value="<?php echo $responseGrid->response ?>"
                                                disabled="disabled"
                                                <?php if (isset($userResponses[$subQuestion->id][$responseGrid->id])) echo 'checked' ?>
                                            >
                                            <?php
                                            if (!empty($userResponses[$subQuestion->id][$responseGrid->id])) {
                                                /** @var Model_Leap_Map_Question_Response $responseObj */
                                                $responseObj = $userResponses[$subQuestion->id][$responseGrid->id];
                                                echo $responseObj->getIsCorrectHTML();
                                                echo $responseObj->getFeedbackHTML();
                                            }
                                            ?>
                                        </div>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php
                } else {
                    echo $user_response;
                } ?>
            </td>
            <td><?php
                $responseFeedback = '';
                if (($questionType == 'pcq' OR $questionType == 'mcq') AND count($question->responses) > 0) {
                    foreach ($question->getResponses() as $resp) {
                        if ($user_response == $resp->response) {
                            $responseFeedback = $resp->feedback;
                        }
                        if ($resp->is_correct == 1) {
                            echo '<p>' . $resp->response . '</p>';
                        }
                    }
                } else {
                    echo 'n/a';
                } ?>
            </td>
            <td><?php
                if ($question->feedback) {
                    echo $question->feedback . '<br>';
                }
                if ($responseFeedback) {
                    echo html_entity_decode($responseFeedback);
                } ?>
            </td>
            </tr><?php
        } ?>
        </tbody>
        </table><?php
    } ?>

    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <td>node</td>
            <td><?php echo __('time elapsed (in seconds)'); ?></td>
            <td><?php echo __('time spent on node'); ?></td>
        </tr>
        </thead>
        <tbody><?php
        if (count($templateData['session']->traces) > 0) {
            $flag = true;
            for ($i = 0; $i < count($templateData['session']->traces); $i++) { ?>
                <tr>
                <td><?php echo $templateData['session']->traces[$i]->node->title; ?>
                    (<?php echo $templateData['session']->traces[$i]->node_id; ?>)
                </td>
                <td><?php echo $templateData['session']->traces[$i]->date_stamp - $templateData['session']->start_time; ?></td>
                <td><?php if ($templateData['session']->traces[$i]->end_date_stamp != null) {
                        echo $templateData['session']->traces[$i]->end_date_stamp - $templateData['session']->traces[$i]->date_stamp;
                    } else {
                        if (isset($templateData['session']->traces[$i + 1])) {
                            echo $templateData['session']->traces[$i + 1]->date_stamp - $templateData['session']->traces[$i]->date_stamp;
                        } else {
                            echo '-';
                        }
                    } ?>
                </td>
                </tr><?php
            }
        } ?>
        </tbody>
    </table>

    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
            codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="565"
            height="420">
        <param name="FlashVars" value="&dataXML=<graph bgcolor='FFFFFF' canvasbgcolor='FFFFFF' xaxisname='node path (node IDs)' yaxisname='time on node (s)' caption='Node Path Analysis'>
                        <?php if (count($templateData['session']->traces) > 0) {
            $flag = true; ?>
                                            <?php for ($i = 0; $i < count($templateData['session']->traces); $i++) { ?>
                                                    <set name='<?php echo $templateData['session']->traces[$i]->node_id; ?>' value='<?php if ($templateData['session']->traces[$i]->end_date_stamp != null) {
                echo $templateData['session']->traces[$i]->end_date_stamp - $templateData['session']->traces[$i]->date_stamp;
            } else {
                if ($i > 0) {
                    echo $templateData['session']->traces[$i]->date_stamp - $templateData['session']->traces[$i - 1]->date_stamp;
                } else {
                    echo 0;
                }
            } ?>' color='<?php if ($flag) {
                echo '#666696';
                $flag = false;
            } else {
                echo '#8888A8';
                $flag = true;
            } ?>'>
                            <?php } ?>
                        <?php } ?>
                           </graph>">
        <param name="movie" value="<?php echo URL::base(); ?>documents/FC_2_3_Column3D.swf">
        <param name="quality" value="high">
        <param name="bgcolor" value="#FFFFFF">
        <embed src="<?php echo URL::base(); ?>documents/FC_2_3_Column3D.swf" flashvars="&dataXML=<graph bgcolor='FFFFFF' canvasbgcolor='FFFFFF' xaxisname='node path (node IDs)' yaxisname='time on node (s)' caption='Node Path Analysis'>
                        <?php if (count($templateData['session']->traces) > 0) {
            $flag = true; ?>
                            <?php for ($i = 0; $i < count($templateData['session']->traces); $i++) { ?>
                                                    <set name='<?php echo $templateData['session']->traces[$i]->node_id; ?>' value='<?php if ($templateData['session']->traces[$i]->end_date_stamp != null) {
                echo $templateData['session']->traces[$i]->end_date_stamp - $templateData['session']->traces[$i]->date_stamp;
            } else {
                if ($i > 0) {
                    echo $templateData['session']->traces[$i]->date_stamp - $templateData['session']->traces[$i - 1]->date_stamp;
                } else {
                    echo 0;
                }
            } ?>' color='<?php if ($flag) {
                echo '#666696';
                $flag = false;
            } else {
                echo '#8888A8';
                $flag = true;
            } ?>' />
                            <?php } ?>
                        <?php } ?>
                           </graph>" quality="high" bgcolor="#FFFFFF" name="Line" align="" width="565" height="420"
               type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">
    </object>

    <h3>Counters Track</h3>
    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
            codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="565"
            height="420">
        <param name="FlashVars" value="&dataXML=<graph caption='Counters' lineThickness='2' showValues='1' formatNumberScale='1' rotateNames='1' decimalPrecision='2' anchorRadius='2' anchorBgAlpha='50' showAlternateVGridColor='1' anchorAlpha='100' animation='1' limitsDecimalPrecision='0' divLineDecimalPrecision='1'>
                           <categories>
                           <?php if (count($templateData['session']->traces) > 0) { ?>
                           <?php foreach ($templateData['session']->traces as $trace) { ?>
                           <c n='<?php echo $trace->node_id; ?>' />
                           <?php } ?>
						   <c n='<?php echo $templateData['session']->traces[0]->node_id; ?>' />
                           <?php } ?>
                           </categories>
                           <?php if (count($templateData['counters']) > 0) { ?>
                           <?php foreach ($templateData['counters'] as $counter) { ?>
                           <dataset seriesName='Counter: <?php echo $counter[0]; ?>' color='<?php $v[$counter[0]] = getRandomColor();
            echo $v[$counter[0]]; ?>' anchorBorderColor='<?php echo $v[$counter[0]]; ?>' >
                                    <?php if (isset($templateData['startValueCounters']) && (isset($templateData['startValueCounters'][$counter[2]]))) { ?>
                                    <s v='<?php echo $templateData['startValueCounters'][$counter[2]]; ?>' />
                                    <?php } ?>
                                    <?php if (isset($counter[1])) {
                if (count($counter[1]) > 0) { ?>
                                        <?php for ($i = 1; $i < count($counter[1]); $i++) { ?>
                                            <s v='<?php echo $counter[1][$i]; ?>' />
                                        <?php } ?>
					<s v='<?php echo $counter[1][0]; ?>' />	
                                    <?php } ?>
                                    <?php } ?>
                           </dataset>
                           <?php } ?>
                           <?php } ?>
                           </graph>">
        <param name="movie" value="<?php echo URL::base(); ?>documents/FC_2_3_MSLine.swf">
        <param name="quality" value="high">
        <param name="bgcolor" value="#FFFFFF">
        <embed src="<?php echo URL::base(); ?>documents/FC_2_3_MSLine.swf" flashvars="&dataXML=<graph caption='Counters' lineThickness='2' showValues='1' formatNumberScale='1' rotateNames='1' decimalPrecision='2' anchorRadius='2' anchorBgAlpha='50' showAlternateVGridColor='1' anchorAlpha='100' animation='1' limitsDecimalPrecision='0' divLineDecimalPrecision='1'>
                           <categories>
                           <?php if (count($templateData['session']->traces) > 0) { ?>
                           <?php foreach ($templateData['session']->traces as $trace) { ?>
                           <c n='<?php echo $trace->node_id; ?>' />
                           <?php } ?>
						   <c n='<?php echo $templateData['session']->traces[0]->node_id; ?>' />
                           <?php } ?>
                           </categories>
                           <?php if (count($templateData['counters']) > 0) { ?>
                           <?php foreach ($templateData['counters'] as $counter) { ?>
                                <dataset seriesName='Counter: <?php echo $counter[0]; ?>' color='<?php echo $v[$counter[0]]; ?>' anchorBorderColor='<?php echo $v[$counter[0]]; ?>' >
                                    <?php if (isset($templateData['startValueCounters']) && (isset($templateData['startValueCounters'][$counter[2]]))) { ?>
                                    <s v='<?php echo $templateData['startValueCounters'][$counter[2]]; ?>' />
                                    <?php } ?>
                                    <?php if (isset($counter[1])) {
            if (count($counter[1]) > 0) { ?>
                                        <?php for ($i = 1; $i < count($counter[1]); $i++) { ?>
                                            <s v='<?php echo $counter[1][$i]; ?>' />
                                        <?php } ?>
					<s v='<?php echo $counter[1][0]; ?>' />
                                    <?php } ?>
                                    <?php } ?>
                                </dataset>
                           <?php } ?>
                           <?php } ?>
                           </graph>" name="Line" width="565" height="420" type="application/x-shockwave-flash"
               pluginspage="http://www.macromedia.com/go/getflashplayer">
    </object>
<?php } ?>