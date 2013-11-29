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
<html>
<title><?php if (isset($templateData['node_title'])) echo $templateData['node_title']; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<link rel="stylesheet" type="text/css" href="<?php echo URL::base(); ?>css/skin/basic/layout.css"/>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/jquery-ui-1.9.1.custom.min.js"></script>

<script  src="<?php echo URL::base(); ?>scripts/dhtmlxSlider/codebase/dhtmlxcommon.js"></script>
<script  src="<?php echo URL::base(); ?>scripts/dhtmlxSlider/codebase/dhtmlxslider.js"></script>
<script  src="<?php echo URL::base(); ?>scripts/dhtmlxSlider/codebase/ext/dhtmlxslider_start.js"></script>
<script  src="<?php echo URL::base(); ?>scripts/visualeditor/base64v1_0.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo URL::base(); ?>scripts/bootstrap-modal/css/bootstrap-modal.css"/>

<script  src="<?php echo URL::base(); ?>scripts/bootstrap-modal/js/bootstrap-modal.js"></script>
<!--<script  src="--><?php //echo URL::base(); ?><!--scripts/bootstrap/js/bootstrap.js"></script>-->
<script  src="<?php echo URL::base(); ?>scripts/bootstrap-modal/js/bootstrap-modalmanager.js"></script>


<link rel="stylesheet" type="text/css" href="<?php echo URL::base(); ?>scripts/dhtmlxSlider/codebase/dhtmlxslider.css">


<script language="JavaScript">
    window.dhx_globalImgPath = "<?php echo URL::base(); ?>scripts/dhtmlxSlider/codebase/imgs/";

    function toggle_visibility(id) {
        var e = document.getElementById(id);
        if (e.style.display == 'none')
            e.style.display = 'block';
        else
            e.style.display = 'none';
    }
</script>

<script language="javascript">
    $(document).ready(function(){
        var rem = '';
        var remMessage = '';
        var session = <?php if (isset($templateData['session'])) echo $templateData['session']; else echo ''; ?> ;

        // Timer
        <?php  if ( ($templateData['map']->timing) && isset($templateData['session']) ) { ?>
                <?php if ( isset($templateData['timer_start']) && $templateData['timer_start'] != 0) {?>
                    var sec = <?php echo $templateData['map']->delta_time - $templateData['timeForNode']; ?> ;

                    <?php if ( $templateData['map']->reminder_time > 0 && ( $templateData['map']->reminder_time < ($templateData['map']->delta_time - $templateData['timeForNode']) ) ) { ?>
                        rem = <?php echo $templateData['map']->reminder_time; ?> ;
                        remMessage = '<?php echo $templateData['map']->reminder_msg; ?>' ;
                    <?php } ?>

        <?php } else {?>

        var sec = <?php echo $templateData['map']->delta_time; ?> ;

            <?php if ( $templateData['map']->reminder_time > 0 )  {  ?>
                rem = <?php echo $templateData['map']->reminder_time; ?> ;
                remMessage = '<?php echo $templateData['map']->reminder_msg; ?>' ;
          <?php } }?>

        start_countdown(sec,rem,remMessage,session);

    <?php }?>

        $(".clearQuestionPrompt").focus(function(){
            if (!$(this).hasClass('cleared')){
                $(this).val('');
                $(this).text('');
                $(this).addClass('cleared');
            }
        });
    });

    function Populate(form) {
        var myarray = new Array(<?php if (isset($templateData['alinkfil'])) echo $templateData['alinkfil']; ?>);
        var mynodes = new Array(<?php if (isset($templateData['alinknod'])) echo $templateData['alinknod']; ?>);
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
                        else {
                            form.id.value = <?php if (isset($templateData['node'])) echo $templateData['node']->id; ?>;
                        }
                    }
                }
            }
        }
    }

    function jumpMenu(targ, selObj, restore) {
        eval(targ + ".location='<?php echo URL::base(); ?>renderLabyrinth/go/<?php echo $templateData['node']->map_id; ?>/" + selObj.options[selObj.selectedIndex].value + "'");
        if (restore) selObj.selectedIndex = 0;
    }

    function ajaxFunction(qid) {
        var qresp = $("#qresponse_" + qid).val();
        if (qresp != ''){
            qresp = B64.encode(qresp);
            var URL = "<?php echo URL::base(); ?>renderLabyrinth/questionResponse/" + qresp + "/" + qid + "/" + <?php echo $templateData['node']->id; ?>;

            var $response = $('#AJAXresponse' + qid);
            $.get(URL, function(data) {
                if(data != '') {
                    $response.html(data);
                }
            });
        }
    }

    function ajaxQU(obj, qid, qresp, qnts) {
        var URL = '<?php echo URL::base(); ?>renderLabyrinth/questionResponse/' + qresp + '/' + qid + '/' + <?php echo $templateData['node']->id; ?>;
        var check = $(obj).is(':checked');
        if (check){
            URL += '/1';
        } else {
            URL += '/0';
        }

        var $response = $('#AJAXresponse' + qresp);
        if (qnts == 1){
            $('.questionForm_'+qid+' .click').remove();
        }

        $.get(URL, function(data) {
            if(data != '') {
                $response.html(data);
            }
        });

    }

    function sendSliderValue(qid, value) {
        var URL = '<?php echo URL::base(); ?>renderLabyrinth/saveSliderQuestionResponse/' + qid;
        $.post(URL, {value: value}, function(data) {});
    }

    function ajaxBookmark() {
        var xmlhttp;
        var labsess = <?php if (isset($templateData['sessionId'])) echo $templateData['sessionId']; ?>;
        var thisnode = "<?php if (isset($templateData['node'])) echo $templateData['node']->id; ?>";
        var URL = "<?php echo URL::base(); ?>renderLabyrinth/addBookmark/" + labsess + "/" + thisnode;
        if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        }
        else if (window.ActiveXObject) {// code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        else {
            alert("Your browser does not support XMLHTTP for AJAX!");
        }
        xmlhttp.onreadystatechange = function () {
        }
        xmlhttp.open("POST", URL, true);
        xmlhttp.send(null);
    }

    function timer(sec, block,reminder, check, remMsg, session) {
        var time    = sec;

        var remTime = reminder;
        var checkReminder = check;
        var remMessage = remMsg;
        var sessionID = session;

        var hour    = parseInt(time / 3600);
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

        if ( sec > 0 ) {
            setTimeout(function(){ timer(sec, block,remTime, checkReminder,remMessage,sessionID); }, 1000);
        }
        else {
            document.getElementsByClassName("demo btn btn-primary btn-large")[1].click();
            setTimeout(function(){ window.location.assign('/reportManager/showReport/' + sessionID); }, 2000);
        }
    }

    function start_countdown(seconds,reminderTime,remMsg,session) {
        var time = seconds;
        var remTime = reminderTime;
        var remMessage = remMsg;
        var sessionID = session;
        var block = document.getElementById('timer');
        if (reminderTime != '') {
            timer(time, block,reminderTime, true, remMessage,sessionID);
        }
        else {
            timer(time,block,'','','',sessionID);
        }
    }


    function ajaxChatShowAnswer(ChatId, ChatElementId) {
        var xmlhttp;
        var labsess = <?php if (isset($templateData['sessionId'])) echo $templateData['sessionId']; ?>;
        var URL = "<?php echo URL::base(); ?>renderLabyrinth/chatAnswer/" + ChatId + "/" + ChatElementId + "/" + labsess + <?php if (isset($templateData['node'])) echo '"/" + ' . $templateData['node']->map_id; ?>;
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
        else {
            alert("Your browser does not support XMLHTTP for AJAX!");
        }
        document.getElementById("ChatAnswer" + ChatElementId).innerHTML = "<p><b>&nbsp;&nbsp;&nbsp;&nbsp;" + xmlhttp.responseText + "</b></p>";
        document.getElementById("ChatQuestion" + ChatElementId).style.color = "grey";
    }

    $(function() {
        $.each($('.visual-display-container'), function(index, object) {
            var maxHeight = 0,
                maxWidth  = 0,
                children = $(object).children(),
                top = 0,
                height = 0,
                width = 0,
                left = 0;
            $.each(children, function(index, child) {
                top = parseInt($(child).css('top').replace('px', ''));
                height = parseInt($(child).css('height').replace('px', ''));
                if(maxHeight < (top + height)) {
                    maxHeight = top + height;
                }

                left = parseInt($(child).css('left').replace('px', ''));
                width = parseInt($(child).css('width').replace('px', ''));
                if(maxWidth < (left + width)) {
                    maxWidth = left + width;
                }
            });

            $(object).css('width', maxWidth);
            $(object).parent().css('width', maxWidth);
            $(object).css('height', maxHeight);
        });

        $('.drag-question-container').sortable({
            axis: "y",
            cursor: "move",
            stop: function(event, ui) {
                var questionId      = ui.item.parent().attr('questionId'),
                    responsesObject = [];
                ui.item.parent().children().each(function(index, value) {
                    responsesObject.push($(value).attr('responseId'));
                });

                $.post('<?php echo URL::base(); ?>renderLabyrinth/ajaxDraggingQuestionResponse', {
                    questionId: questionId,
                    responsesJSON: JSON.stringify(responsesObject)
                }, function(data) {});
            }
        });
    });
</script>
<?php
if ($templateData['skin_path'] != NULL) {
    $doc_file = DOCROOT . 'css/skin/' . $templateData['skin_path'] . '/default.css';
    if (file_exists($doc_file)) {
        $css_file = URL::base() . 'css/skin/' . $templateData['skin_path'] . '/default.css';
        echo '<link rel="stylesheet" type="text/css" href="' . ScriptVersions::get($css_file) . '" />';
    }
}
?>
</head>

    <body>
        <?php if (isset($templateData['editor']) and $templateData['editor'] == TRUE) { ?>
            <script language="javascript" type="text/javascript"
                    src="<?php echo URL::base(); ?>scripts/tinymce/js/tinymce/tinymce.min.js"></script>
            <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/jquery.cookie.js'); ?>"></script>
            <script language="javascript" type="text/javascript">
                tinymce.init({
                    selector: "textarea",
                    theme: "modern",
                    content_css: "<?php echo URL::base(); ?>scripts/tinymce/js/tinymce/plugins/rdface/css/rdface.css,<?php echo URL::base(); ?>scripts/tinymce/js/tinymce/plugins/rdface/schema_creator/schema_colors.css",
                    entity_encoding: "raw",
                    contextmenu: "link image inserttable | cell row column rdfaceMain",
                    closed: /^(br|hr|input|meta|img|link|param|area|source)$/,
                    valid_elements : "+*[*]",
                    plugins: ["compat3x",
                        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                        "searchreplace wordcount visualblocks visualchars code fullscreen",
                        "insertdatetime media nonbreaking save table contextmenu directionality",
                        "emoticons template paste textcolor layer advtextcolor rdface imgmap"
                    ],
                    toolbar1: "insertfile undo redo | styleselect | bold italic | fontselect fontsizeselect | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
                    toolbar2: " link image imgmap|print preview media | forecolor backcolor emoticons ltr rtl layer restoredraft | rdfaceMain",
                    image_advtab: true,
                    templates: [

                    ]
                });
            </script>
        <?php } ?>
        <div align="center" class="popup-outside-container">
            <table style="padding-top:20px;" id="centre_table" width="90%" border="0" cellpadding="12" cellspacing="2">
                <tr>
                    <td class="centre_td popup-inside-container" width="81%" bgcolor="#FFFFFF" align="left">
                        <h4><font color="#000000"><?php if (isset($templateData['node_title'])) echo $templateData['node_title']; ?></font></h4>
                        <?php if (isset($templateData['editor']) and $templateData['editor'] == TRUE) { ?>
                            <?php if (isset($templateData['node_edit'])) { ?>
                                <form method='POST'
                                      action='<?php echo URL::base(); ?>renderLabyrinth/updateNode/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['node']->id; ?>'>
                                    <p><input type='text' name='mnodetitle' value='<?php echo $templateData['node']->title; ?>'/></p>

                                    <p><textarea name='mnodetext' cols='60' rows='20'
                                                 class='mceEditor'><?php echo $templateData['node_text']; ?></textarea></p>
                                    <input type='submit' name='Submit' value='Submit'/>
                                </form>
                                <p>
                                    - <a href='<?php echo URL::base() . 'linkManager/index/' . $templateData['map']->id; ?>'><?php echo __('links'); ?></a>
                                    - <a href='<?php echo URL::base() . 'nodeManager/index/' . $templateData['map']->id; ?>'><?php echo __('nodes'); ?></a>
                                    - <a href='<?php echo URL::base() . 'fileManager/index/' . $templateData['map']->id; ?>'><?php echo __('files'); ?></a>
                                    - <a href='<?php echo URL::base() . 'counterManager/index/' . $templateData['map']->id; ?>'><?php echo __('counters'); ?></a>
                                    - <a href='<?php echo URL::base(); ?>labyrinthManager/editMap/<?php echo $templateData['map']->id; ?>'><?php echo __('main editor'); ?></a>
                                </p>
                            <?php } else {
                                if (isset($templateData['node_text'])) echo $templateData['node_text'];
                                if (isset($templateData['node_annotation']) && $templateData['node_annotation'] != null) echo '<div class="annotation">' . $templateData['node_annotation'] . '</div>';
                            }
                        } else {
                            if (isset($templateData['node_text'])) echo $templateData['node_text'];
                            if (isset($templateData['node_annotation']) && $templateData['node_annotation'] != null) echo '<div class="annotation">' . $templateData['node_annotation'] . '</div>';
                        } ?>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <?php
                                        if (isset($templateData['links']))     { echo $templateData['links'];     }
                                        if (isset($templateData['undoLinks'])) { echo $templateData['undoLinks']; }
                                    ?>
                                </td>
                                <td align="right" valign="bottom">
                                    <?php if (isset($templateData['counters'])) echo $templateData['counters']; ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="centre_td" width="19%" rowspan="2" valign="top" bgcolor="#FFFFFF"><p align="center">
                        <?php if ($templateData['map']->timing) { ?>
                             <h4>Timer: <div id="timer"></div><br /><br />
                        <?php }?>
                        <?php if (isset($templateData['navigation'])) echo $templateData['navigation']; ?>

                        <h5>Map: <?php if (isset($templateData['map'])) echo $templateData['map']->name; ?>
                            (<?php if (isset($templateData['map'])) echo $templateData['map']->id; ?>)<br/>
                            Node: <?php if (isset($templateData['node'])) echo $templateData['node']->id; ?>
                            <br/><strong>Score:</strong>
                        </h5>

                        <input type="button" onclick='ajaxBookmark();' name="bookmark" value="bookmark"/>
                        <?php if (isset($templateData['editor']) and $templateData['editor'] == TRUE) { ?>
                            <h5>
                                <a href="<?php echo URL::base(); ?>renderLabyrinth/go/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['node']->id; ?><?php if (!isset($templateData['node_edit'])) echo '/1'; ?>">
                                    <?php echo !isset($templateData['node_edit']) ? __('turn editing on') : __('turn editing off'); ?>
                                </a>
                            </h5>
                        <?php } ?>
                        <p>
                            <a href='<?php echo URL::base(); ?>renderLabyrinth/reset/<?php echo $templateData['map']->id; ?><?php if(isset($templateData['webinarId']) && isset($templateData['webinarStep'])) echo '/' . $templateData['webinarId'] . '/' . $templateData['webinarStep']; ?>'>reset</a>
                        </p>

                        <a href="<?php echo URL::base(); ?>">
                            <img src="<?php echo URL::base(); ?>images/openlabyrinth-powerlogo-wee.jpg" height="20" width="118" alt="OpenLabyrinth" border="0"/>
                        </a>
                        <h5><?php echo __('OpenLabyrinth is an open source educational pathway system'); ?></h5>
                    </td>
                </tr>
                <tr>
                    <td class="centre_td" bgcolor="#FFFFFF">
                        <a href="#" onclick="toggle_visibility('track');"><p class='style2'><strong>Review your pathway</strong></p></a>
                        <div id='track' style='display:none'><?php if (isset($templateData['trace_links'])) { echo $templateData['trace_links']; }?></div>
                    </td>
                </tr>
            </table>
        </div>

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
            if (isset($templateData['map_popups']) && count($templateData['map_popups']) > 0) {
                $shownMapPopups = Session::instance()->get('shownMapPopups');
        ?>
            <?php foreach($templateData['map_popups'] as $mapPopup) { ?>
                <?php if(!isset($shownMapPopups) || (!in_array($mapPopup->id, $shownMapPopups))) { ?>
                    <div class="popup hide <?php echo Popup_Positions::toString($mapPopup->position_id); ?>"
                         popup-position-type="<?php echo Popup_Position_Types::toString($mapPopup->position_type); ?>"
                         time-before="<?php echo $mapPopup->time_before; ?>"
                         time-length="<?php echo $mapPopup->time_length; ?>"
                         assign-type="<?php echo Popup_Assign_Types::toString($mapPopup->assign->assign_type_id); ?>"
                         assign-to-id="<?php echo $mapPopup->assign->assign_to_id; ?>"
                         popup-id="<?php echo $mapPopup->id; ?>"
                         redirect-type="<?php echo $mapPopup->assign->redirect_type_id; ?>"
                         redirect-id="<?php echo $mapPopup->assign->redirect_to_id; ?>"
                         title-hide="<?php echo $mapPopup->title_hide; ?>"
                         background-color="<?php echo $mapPopup->style->background_color ?>"
                         border-color="<?php echo $mapPopup->style->border_color ?>"
                         is-background-transparent="<?php echo $mapPopup->style->is_background_transparent ?>"
                         background-transparent="<?php echo $mapPopup->style->background_transparent ?>"
                         is-border-transparent="<?php echo $mapPopup->style->is_border_transparent ?>"
                         border-transparent="<?php echo $mapPopup->style->border_transparent ?>"

                         style="<?php if(!$mapPopup->style->font_color) echo 'color:'.$mapPopup->style->font_color.';'; ?>">
                        <div class="header"><?php echo $mapPopup->title; ?></div>
                        <div class="text"><?php echo $mapPopup->text; ?></div>
                    </div>
                <?php } ?>
            <?php } ?>
        <?php } ?>

        <script>
            var reportRedirectType = <?php echo Popup_Redirect_Types::REPORT; ?>,
                shownPopups = '<?php echo URL::base(); ?>renderLabyrinth/shownPopup',
                showReport  = '<?php echo URL::base(); ?>reportManager/showReport/<?php echo Session::instance()->get('session_id'); ?>',
                redirectURL = '<?php echo URL::base(); ?>renderLabyrinth/go/<?php echo $templateData['map']->id; ?>/#node#',
                timeForNode = <?php echo isset($templateData['timeForNode']) ? $templateData['timeForNode'] : 0; ?>,
                nodeId      = <?php echo $templateData['node']->id; ?>,
                popupStart  = <?php echo (isset($templateData['popup_start']) && $templateData['popup_start'] != 0) ? $templateData['popup_start'] : 0; ?>;
                sections    = [<?php if(count($templateData['node']->sections) > 0) {
                                         $sections = array();
                                         foreach($templateData['node']->sections as $nodeSection) {
                                            $sections[] = $nodeSection->section_id;
                                         }

                                         echo implode(',', $sections);
                                     } ?>];
        </script>
        <script src="<?php echo URL::base(); ?>scripts/popupRender.js"></script>
    </body>
</html>
