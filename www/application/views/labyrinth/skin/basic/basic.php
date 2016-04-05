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

<link rel="stylesheet" type="text/css" href="<?php echo ScriptVersions::get(URL::base().'css/skin/basic/layout_basic.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo ScriptVersions::get(URL::base().'scripts/bootstrap-modal/css/bootstrap-modal.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo ScriptVersions::get(URL::base().'css/font.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo ScriptVersions::get(URL::base().'scripts/dhtmlxSlider/codebase/dhtmlxslider.css'); ?>">

<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/jquery-1.7.2.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/jquery-ui-1.9.1.custom.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/jquery-ui-touch-punch.min.js'); ?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/jquery.cookie.js'); ?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/tinymce/js/tinymce/tinymce.min.js'); ?>"></script>
<script language="javascript" type="text/javascript">
    /*tinymce.init({
        selector: ".mceText",
        theme: "modern",
        content_css: "<?php echo URL::base().'scripts/tinymce/js/tinymce/plugins/rdface/css/rdface.css'; ?>",
        entity_encoding: "raw",
        contextmenu: "link image inserttable | cell row column",
        closed: /^(br|hr|input|meta|img|link|param|area|source)$/,
        valid_elements : "+*[*]",
        plugins: ["compat3x",
            "advlist autolink lists link image charmap hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime nonbreaking save table contextmenu directionality",
            "template paste textcolor layer advtextcolor rdface"
        ],
        toolbar1: "insertfile undo redo | styleselect | bold italic | fontselect fontsizeselect | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
        toolbar2: " link image | forecolor backcolor layer restoredraft",
        image_advtab: true,
        templates: [],
        setup: function(editor) {
            editor.on('init', function(e) {
                valToTextarea();
            });
        }
    });*/
    tinymce.init({
        selector: ".mceEditor, .mceText",
        theme: "modern",
        content_css: "<?php echo URL::base().'scripts/tinymce/js/tinymce/plugins/rdface/css/rdface.css'; ?>",
        entity_encoding: "raw",
        contextmenu: "link image inserttable | cell row column",
        menubar : false,
        closed: /^(br|hr|input|meta|img|link|param|area|source)$/,
        valid_elements : "+*[*]",
        plugins: ["compat3x",
            "advlist autolink lists link image charmap hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime nonbreaking save table contextmenu directionality",
            "template paste textcolor layer advtextcolor rdface"
        ],
        toolbar1: "undo redo | styleselect | bold italic | fontselect fontsizeselect | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
        image_advtab: true,
        templates: [],
        setup: function(editor) {
            editor.on('init', function() {
                valToTextarea();
            });
        }
    });

    // use in basic.js & turkTalk.js
    var urlBase = '<?php echo URL::base(); ?>';
</script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/basic.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/helper.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/turkTalk.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/dhtmlxSlider/codebase/dhtmlxcommon.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/dhtmlxSlider/codebase/dhtmlxslider.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/dhtmlxSlider/codebase/ext/dhtmlxslider_start.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/visualeditor/base64v1_0.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/bootstrap-modal/js/bootstrap-modal.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/bootstrap-modal/js/bootstrap-modalmanager.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/validator.min.js'); ?>"></script>
<script language="javascript">
    var idNode      = <?php echo $templateData['node']->id; ?>,
        idPatients  = '<?php
                    $ids = array();
                    foreach (Arr::get($templateData, 'patients', array()) as $id=>$patient)
                    {
                        $ids[] = $id;
                    }
                    echo json_encode($ids); ?>',
        pollTime    = <?php echo Arr::get($templateData, 'time', 0); ?>,
        jsonRule    = '<?php echo Arr::get($templateData, 'jsonRule', '[]'); ?>';

    $(document).ready(function(){
        var rem = '',
            remMessage = '',
            session = '<?php if (isset($templateData['session'])) echo $templateData['session']; else echo ''; ?>';

        // Timer
        <?php
        if ( ($templateData['map']->timing) && isset($templateData['session']) ) {
            if ( isset($templateData['timer_start']) && $templateData['timer_start'] != 0) { ?>

        var sec = <?php echo $templateData['map']->delta_time - $templateData['timeForNode']; ?> ;

        <?php
        if ( $templateData['map']->reminder_time > 0 && ( $templateData['map']->reminder_time < ($templateData['map']->delta_time - $templateData['timeForNode']) ) ) { ?>
        rem = <?php echo $templateData['map']->reminder_time; ?> ;
        remMessage = '<?php echo $templateData['map']->reminder_msg; ?>' ;
        <?php
        }
    } else { ?>

        var sec = <?php echo $templateData['map']->delta_time; ?> ;

        <?php
        if ( $templateData['map']->reminder_time > 0 )  {  ?>
        rem = <?php echo $templateData['map']->reminder_time; ?> ;
        remMessage = '<?php echo $templateData['map']->reminder_msg; ?>' ;
        <?php
        }
    }?>

        start_countdown(sec,rem,remMessage,session);
        <?php
        }?>
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
    });
</script>
<?php
if ($templateData['skin_path'] != NULL) {
    $doc_file = DOCROOT.'css/skin/'.$templateData['skin_path'].'/default.css';
    if (file_exists($doc_file)) {
        $css_file = URL::base().'css/skin/'.$templateData['skin_path'].'/default.css';
        echo '<link rel="stylesheet" type="text/css" href="'.ScriptVersions::get($css_file).'" />';
    }
}

$id_map  = $templateData['map']->id;
$id_node = $templateData['node']->id; ?>
</head>
<body>
<?php if(!empty($templateData['wasRedirected'])) { ?>
    <div style="position: fixed; left: 50%;top:20px; z-index: 1500; margin-left: -89px;" id="wasRedirected" class="alert alert-success">
        <span>You have been redirected.</span>
    </div>
    <script>
        setTimeout(function(){
            $('#wasRedirected').hide();
        }, 8000);
    </script>
<?php } ?>
<div align="center" class="popup-outside-container">
    <table style="padding-top:20px;" id="centre_table" width="90%" border="0" cellpadding="12" cellspacing="2">
        <tr>
            <td class="centre_td popup-inside-container" width="81%" bgcolor="#FFFFFF" align="left">
                <h4><?php echo Arr::get($templateData, 'node_title'); ?></h4><?php
                if (Arr::get($templateData, 'editor') == TRUE) {
                    if (isset($templateData['node_edit'])) { ?>
                        <form method='POST' action='<?php echo URL::base(); ?>renderLabyrinth/updateNode/<?php echo $id_map.'/'.$id_node; ?>'>
                            <p><input type='text' name='mnodetitle' value='<?php echo $templateData['node']->title; ?>'/></p>
                            <p><textarea name='mnodetext' cols='60' rows='20' class='mceEditor'><?php echo $templateData['node_text']; ?></textarea></p>
                            <?php if (isset($templateData['node_annotation'])) { ?>
                                <div class="annotation" style="width: 99%;"><?php echo $templateData['node_annotation']; ?></div>
                            <?php } ?>
                            <input type='submit' name='Submit' value='Submit'/>
                        </form>
                        <p></p><?php
                    } else {
                        echo Arr::get($templateData, 'node_text');
                        if (isset($templateData['node_annotation']) && $templateData['node_annotation'] != null) echo '<div class="annotation">' . $templateData['node_annotation'] . '</div>';
                    }
                } else {
                    echo Arr::get($templateData, 'node_text');
                    if (isset($templateData['node_annotation']) && $templateData['node_annotation'] != null) echo '<div class="annotation">' . $templateData['node_annotation'] . '</div>';
                } ?>
                <table width="100%">
                    <tr>
                        <td><?php
                            echo Arr::get($templateData, 'links');
                            echo Arr::get($templateData, 'undoLinks'); ?>
                        </td>
                        <td align="right" valign="bottom">
                            <?php echo Arr::get($templateData, 'counters'); ?>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="centre_td" width="19%" rowspan="2" valign="top" bgcolor="#FFFFFF"><p align="center"><?php
                    if ($templateData['map']->timing) { ?>
                <h4>Timer: <div id="timer"></div><br /><br /><?php
                    }
                    echo Arr::get($templateData, 'navigation', ''); ?>
                    <h5>Map: <?php if (isset($templateData['map'])) echo $templateData['map']->name; ?>
                        (<?php if (isset($templateData['map'])) echo $id_map; ?>)<br/>
                        Node: <?php if (isset($templateData['node'])) echo $id_node; ?>
                        <br/><strong>Score:</strong>
                    </h5>

                    <input type="button" onclick='ajaxBookmark();' name="bookmark" value="suspend"/>
                    <?php if (isset($templateData['editor']) and $templateData['editor'] == TRUE) { ?>
                        <h5>
                            <a href="<?php echo URL::base().'renderLabyrinth/go/'.$id_map.'/'.$id_node; ?><?php if (!isset($templateData['node_edit'])) echo '/1'; ?>">
                                <button type="button"><?php echo !isset($templateData['node_edit']) ? __('turn editing on') : __('turn editing off'); ?></button>
                            </a>
                        </h5>
                    <?php } ?>
                    <p>
                        <a href='<?php echo URL::base(); ?>renderLabyrinth/reset/<?php echo $id_map; ?><?php if(isset($templateData['webinarId']) && isset($templateData['webinarStep'])) echo '/' . $templateData['webinarId'] . '/' . $templateData['webinarStep']; ?>'>reset</a>
                    </p>

                    <p>
                        <label for="user_notepad"></label>
                        <textarea class="user_notepad" id="user_notepad">
                            <?php if(!empty($templateData['user_notepad_text'])) echo $templateData['user_notepad_text']; ?>
                        </textarea>
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
$section = false;
foreach (Arr::get($templateData, 'map_popups', array()) as $mapPopup) {
    $assign  = null;
    foreach ($mapPopup->assign as $a){
        foreach (Arr::get($templateData,'sections', array()) as $s){
            if ($a->assign_to_id == $s->id){
                $section = true;
                break;
            }
        }
        if ($a->assign_to_id == $id_node OR
            $a->assign_to_id == $id_map OR
            $section) $assign = $a;
    }
    if ( ! is_null($assign)) { ?>
    <div class="popup hide <?php echo Popup_Positions::toString($mapPopup->position_id); ?>"
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
         style="<?php if ( ! $mapPopup->style->font_color) echo 'color:'.$mapPopup->style->font_color.';'; ?>">

        <?php
        $user = Auth::instance()->get_user();
        if ($user)
        {
            $status = $user->type_id;
            if ($status == 2 OR $status == 4) { ?>
                <div class="info_for_admin node_id"><?php
                    echo '#:'.$mapPopup->id;?>
                </div>
                <div class="info_for_admin redirect_to"><?php
                    if ($assign->redirect_type_id == 3) echo 'to report';
                    if ($assign->redirect_type_id == 2) echo 'to #'.$assign->redirect_to_id;?>
                </div>
            <?php
            }
        }
        ?>
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

<script>
    var reportRedirectType  = <?php echo Popup_Redirect_Types::REPORT; ?>,
        popupsAction        = '<?php echo URL::base().'renderLabyrinth/popupAction/'.$id_map; ?>',
        showReport          = '<?php echo URL::base().'reportManager/showReport/'.Session::instance()->get('session_id'); ?>',
        redirectURL         = '<?php echo URL::base().'renderLabyrinth/go/'.$id_map; ?>/#node#',
        timeForNode         = <?php echo isset($templateData['timeForNode']) ? $templateData['timeForNode'] : 0; ?>,
        nodeId              = <?php echo $id_node; ?>,
        popupStart          = <?php echo (isset($templateData['popup_start']) AND $templateData['popup_start'] != 0) ? $templateData['popup_start'] : 0; ?>,
        sections            = [<?php if(count($templateData['node']->sections) > 0) {
                                         $sections = array();
                                         foreach($templateData['node']->sections as $nodeSection) {
                                            $sections[] = $nodeSection->section_id;
                                         }
                                         echo implode(',', $sections);
                                     } ?>];
</script>
<script src="<?php echo URL::base().'scripts/popupRender.js'; ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/user-notepad.js'); ?>"></script>
</body>
</html>