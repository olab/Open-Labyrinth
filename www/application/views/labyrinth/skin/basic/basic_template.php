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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Open Labyrinth. If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright Copyright 2012 Open Labyrinth. All Rights Reserved.
 *
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo Arr::get($templateData, 'node_title'); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

    <link rel="stylesheet" type="text/css" href="<?php echo ScriptVersions::get(URL::base().'css/skin/basic/layout.css'); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php echo ScriptVersions::get(URL::base().'scripts/bootstrap-modal/css/bootstrap-modal.css'); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php echo ScriptVersions::get(URL::base().'css/font.css'); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php echo ScriptVersions::get(URL::base().'scripts/dhtmlxSlider/codebase/dhtmlxslider.css'); ?>">

    <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/jquery-1.7.2.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/jquery-ui-1.9.1.custom.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/jquery-ui-touch-punch.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/basic.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/dhtmlxSlider/codebase/dhtmlxcommon.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/dhtmlxSlider/codebase/dhtmlxslider.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/dhtmlxSlider/codebase/ext/dhtmlxslider_start.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/visualeditor/base64v1_0.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/bootstrap-modal/js/bootstrap-modal.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/bootstrap-modal/js/bootstrap-modalmanager.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/validator.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/popupRender.js'); ?>"></script>
    <script language="javascript">
        var idNode              = <?php echo $templateData['node']->id; ?>,
            pollTime            = <?php echo Arr::get($templateData, 'time', 0); ?>,
            jsonRule            = '<?php echo Arr::get($templateData, 'jsonRule', '[]'); ?>',
            reportRedirectType  = <?php echo Popup_Redirect_Types::REPORT; ?>,
            popupsAction        = '<?php echo URL::base().'renderLabyrinth/popupAction/'.$templateData['map']->id; ?>',
            showReport          = '<?php echo URL::base().'reportManager/showReport/'.Session::instance()->get('session_id'); ?>',
            redirectURL         = '<?php echo URL::base().'renderLabyrinth/go/'.$templateData['map']->id; ?>/#node#',
            timeForNode         = <?php echo Arr::get($templateData, 'timeForNode', 0); ?>,
            nodeId              = <?php echo $templateData['node']->id; ?>,
            popupStart          = <?php echo (isset($templateData['popup_start']) && $templateData['popup_start'] != 0) ? $templateData['popup_start'] : 0; ?>,
            idPatients = '<?php
                $ids = array();
                foreach (Arr::get($templateData, 'patients', array()) as $id=>$patient)
                {
                    $ids[] = $id;
                }
                echo json_encode($ids); ?>',
            sections = [<?php
            if(count($templateData['node']->sections) > 0) {
                $sections = array();
                foreach($templateData['node']->sections as $nodeSection) {
                    $sections[] = $nodeSection->section_id;
                }
                echo implode(',', $sections);
            } ?>];

        $(document).ready(function()
        {
            var sec,
                rem,
                remMessage,
                session = '<?php echo Arr::get($templateData, 'session', ''); ?>';

            // Timer
            <?php
            if (($templateData['map']->timing) AND isset($templateData['session'])) {
                if (isset($templateData['timer_start']) AND $templateData['timer_start'] != 0) { ?>
                    sec = <?php echo $templateData['map']->delta_time - $templateData['timeForNode']; ?> ;
                    <?php if ($templateData['map']->reminder_time > 0 && ($templateData['map']->reminder_time < ($templateData['map']->delta_time - $templateData['timeForNode']))) { ?>
                        rem = <?php echo $templateData['map']->reminder_time; ?> ;
                        remMessage = '<?php echo $templateData['map']->reminder_msg; ?>' ;
                    <?php }
                } else {?>
                    sec = <?php echo $templateData['map']->delta_time; ?> ;<?php
                    if ( $templateData['map']->reminder_time > 0 )  {  ?>
                        rem = <?php echo $templateData['map']->reminder_time; ?> ;
                        remMessage = '<?php echo $templateData['map']->reminder_msg; ?>' ;<?php
                    }
                }?>
                start_countdown(sec,rem,remMessage,session);<?php
            }?>
        });

        function Populate(form) {
            var myarray = new Array(<?php echo Arr::get($templateData,'alinkfil'); ?>);
            var mynodes = new Array(<?php echo Arr::get($templateData, 'alinknod'); ?>);
            var mycount = 0;
            var mybuffer = form.filler.value;

            if (mybuffer.length > 1) {
                for (var i = 0; i < myarray.length; i++) {
                    for (var j = 0; j < myarray[i].length; j++) {
                        var ffv = form.filler.value.toLowerCase();
                        if (ffv == myarray[i].substring(0, j)) {
                            for (var k = i + 1; k < myarray.length; k++) {
                                var t1 = myarray[i].substring(0, j);
                                var t2 = myarray[k].substring(0, j);
                                if (t1 == t2) {
                                    mycount++;
                                }
                            }
                            if (mycount < 1) {
                                form.filler.value = myarray[i];
                                form.id.value = mynodes[i];
                            }
                            else form.id.value = idNode;
                        }
                    }
                }
            }
        }

        function jumpMenu(targ, selObj, restore) {
            eval(targ + ".location='<?php echo URL::base().'renderLabyrinth/go/'.$templateData['node']->map_id; ?>/" + selObj.options[selObj.selectedIndex].value + "'");
            if (restore) selObj.selectedIndex = 0;
        }

        function ajaxDrag(id) {
            $('#questionSubmit'+id).show();

            var response = $('#qresponse_'+id);
            response.sortable( "option", "cancel", "li" );

            var responsesObject = [];

            response.children('.sortable').each(function(index, value) {
                responsesObject.push($(value).attr('responseId'));
                $(value).css('color','gray');
            });

            $.post('<?php echo URL::base().'renderLabyrinth/ajaxDraggingQuestionResponse'; ?>', {
                questionId: id,
                responsesJSON: JSON.stringify(responsesObject)
            }, function(data) {});
        }

        function timer(sec, block,reminder, check, remMsg, session) {
            var time            = sec,
                remTime         = reminder,
                checkReminder   = check,
                hour            = parseInt(time / 3600);
            if ( hour < 1 ) hour = 0;
            time = parseInt(time - hour * 3600);
            if ( hour < 10 ) hour = '0'+hour;

            var minutes = parseInt(time / 60);
            if ( minutes < 1 ) minutes = 0;
            time = parseInt(time - minutes * 60);
            if ( minutes < 10 ) minutes = '0'+minutes;

            var seconds = time;
            if ( seconds < 10 ) seconds = '0'+seconds;

            block.innerHTML = hour+':'+minutes+':'+seconds;

            sec--;

            if (checkReminder && remTime != '' && remTime > sec){
                document.getElementsByClassName("demo btn btn-primary btn-large")[0].click();
                checkReminder = false;
            }

            if (sec > 0) setTimeout(function(){ timer(sec, block,remTime, checkReminder,remMsg,session); }, 1000);
            else {
                document.getElementsByClassName("demo btn btn-primary btn-large")[1].click();
                setTimeout(function(){ window.location.assign('/reportManager/showReport/' + session); }, 2000);
            }
        }

        function start_countdown(seconds,reminderTime,remMsg,session) {
            var block = document.getElementById('timer');
            if (reminderTime != '') timer(seconds, block, reminderTime, true, remMsg, session);
            else timer(seconds, block, '', '', '', session);
        }


        function ajaxChatShowAnswer(ChatId, ChatElementId) {
            var xmlhttp,
                labsess = <?php echo Arr::get($templateData, 'sessionId', ''); ?>,
                URL = "<?php echo URL::base(); ?>renderLabyrinth/chatAnswer/" + ChatId + "/" + ChatElementId + "/" + labsess + <?php if (isset($templateData['node'])) echo '"/" + '.$templateData['node']->map_id; ?>;
            if (window.XMLHttpRequest) {
                xmlhttp = new XMLHttpRequest();
                xmlhttp.open("GET", URL, false);
                xmlhttp.send(null);
            }
            else if (window.ActiveXObject) {
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                xmlhttp.open("GET", URL, false);
                xmlhttp.send();
            }
            else alert("Your browser does not support XMLHTTP for AJAX!");
            document.getElementById("ChatAnswer" + ChatElementId).innerHTML = "<p><b>&nbsp;&nbsp;&nbsp;&nbsp;" + xmlhttp.responseText + "</b></p>";
            document.getElementById("ChatQuestion" + ChatElementId).style.color = "grey";
        }

        $(function() {
            $.each($('.visual-display-container'), function(index, object) {
                var $object   = $(object),
                    maxHeight = 0,
                    maxWidth  = 0;

                $.each($object.children(), function(index, child) {
                    var top     = parseInt($(child).css('top').replace('px', '')),
                        height  = parseInt($(child).css('height').replace('px', '')),
                        left    = parseInt($(child).css('left').replace('px', '')),
                        width   = parseInt($(child).css('width').replace('px', ''));

                    if (maxHeight < (top + height)) maxHeight = top + height;
                    if (maxWidth < (left + width)) maxWidth = left + width;
                });

                $object.css({'width': maxWidth, 'height': maxHeight});
                $object.parent().css('width', maxWidth);
            });
        });
    </script>
</head>

    <body <?php if(isset($templateData['bodyStyle'])) echo 'style="'.$templateData['bodyStyle'].'"'; ?>><?php
        echo Arr::get($templateData, 'skin'); ?>

        <div id="reminder" class="modal hide fade" tabindex="-1" data-width="760">
            <div class="modal-header">
                <h3><?php echo __('Reminder'); ?></h3>
            </div>
            <div class="modal-body">
                <?php echo $templateData['map']->reminder_msg; ?>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn"><?php echo __('Close'); ?></button>
            </div>
        </div>

        <div id="finish" class="modal hide fade" tabindex="-1" data-width="760">
            <div class="modal-header"><h3><?php echo __('FINISH'); ?></h3></div>
            <div class="modal-body"><?php echo __('Time is up'); ?></div>
            <div class="modal-footer"><button type="button" data-dismiss="modal" class="btn"><?php echo __('Close'); ?></button></div>
        </div>

        <button id="remButton" class="demo btn btn-primary btn-large" href="#reminder" data-toggle="modal" style="display: none" type="submit"></button>
        <button id="finishButton" class="demo btn btn-primary btn-large" href="#finish" data-toggle="modal" style="display: none" type="submit"></button>

        <?php
        $assign  = null;
        $section = false;
        foreach (Arr::get($templateData, 'map_popups', array()) as $mapPopup) {
            foreach ($mapPopup->assign as $a){
                foreach (Arr::get($templateData,'sections', array()) as $s){
                    if ($a->assign_to_id == $s->id){
                        $section = true;
                        break;
                    }
                }
                if ($a->assign_to_id == $templateData['node']->id OR $a->assign_to_id == $templateData['map']->id OR $section) $assign = $a;
            }
            if (isset($assign)) { ?>
            <div
                class="popup hide <?php echo Popup_Positions::toString($mapPopup->position_id); ?>"
                popup-position-type="<?php echo Popup_Position_Types::toString($mapPopup->position_type); ?>"
                time-before="<?php echo $mapPopup->time_before; ?>"
                time-length="<?php echo $mapPopup->time_length; ?>"
                assign-type="<?php echo Popup_Assign_Types::toString($assign->assign_type_id); ?>"
                assign-to-id="<?php echo $assign->assign_to_id; ?>"
                popup-id="<?php echo $mapPopup->id; ?>"
                redirect-type="<?php echo $assign->redirect_type_id; ?>"
                redirect-id="<?php echo $assign->redirect_to_id; ?>"
                title-hide="<?php echo $mapPopup->title_hide; ?>"
                background-color="<?php echo $mapPopup->style->background_color ?>"
                border-color="<?php echo $mapPopup->style->border_color ?>"
                is-background-transparent="<?php echo $mapPopup->style->is_background_transparent ?>"
                background-transparent="<?php echo $mapPopup->style->background_transparent ?>"
                is-border-transparent="<?php echo $mapPopup->style->is_border_transparent ?>"
                border-transparent="<?php echo $mapPopup->style->border_transparent ?>"
                style="<?php if ( ! $mapPopup->style->font_color) echo 'color:'.$mapPopup->style->font_color.';'; ?>"><?php
                $user = Auth::instance()->get_user();
                if ($user)
                {
                    $status = $user->type_id;
                    if ($status == 2 OR $status == 4)
                    { ?>
                    <div class="info_for_admin node_id"><?php echo '#:'.$mapPopup->id; ?></div>
                    <div class="info_for_admin redirect_to"><?php
                        if ($assign->redirect_type_id == 3) echo 'to report';
                        if ($assign->redirect_type_id == 2) echo 'to #'.$assign->redirect_to_id;?>
                    </div><?php
                    }
                } ?>
                <div class="header"><?php echo $mapPopup->title; ?></div>
                <div class="text"><?php echo $mapPopup->text; ?></div>
            </div><?php
            }
        } ?>

        <div class="modal hide fade" id="counter-debug">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="alert-heading"><?php echo __('Debbuger window'); ?></h4>
            </div>
            <div class="modal-body modal-body-scroll"><?php
                foreach (Arr::get($templateData, 'c_debug', array()) as $data){?>
                    <h2><?php echo Arr::get($data, 'title'); ?></h2>
                    <p class="c_description"><?php echo Arr::get($data, 'description');; ?></p>
                    <p class="c_info"><?php echo Arr::get($data, 'info');; ?></p><?php
                }; ?>
            </div>
        </div>
    </body>
</html>
