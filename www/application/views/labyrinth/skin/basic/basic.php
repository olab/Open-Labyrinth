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
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=10,9,8" />
<title><?php echo (isset($templateData['title']) ? $templateData['title'] : __('OpenLabyrinth')); ?> - <?php if (isset($templateData['node_title'])) echo $templateData['node_title']; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

<link rel="stylesheet" href="<?php echo URL::base(); ?>scripts/jquery-ui/themes/base/jquery.ui.all.css"/>
<link rel="stylesheet" href="<?php echo URL::base(); ?>scripts/bootstrap/css/bootstrap.css"/>
<link rel="stylesheet" href="<?php echo URL::base(); ?>scripts/bootstrap/css/bootstrap-responsive.css"/>
<link rel="stylesheet" href="<?php echo URL::base(); ?>scripts/datepicker/css/datepicker.css"/>
<link rel="stylesheet" href="<?php echo URL::base(); ?>css/basic.css"/>
<link href="<?php echo URL::base(); ?>scripts/bootstrap-modal/css/bootstrap-modal.css" rel="stylesheet"/>
<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<link rel="shortcut icon" href="<?php echo URL::base(); ?>images/ico/favicon.ico"/>
<link rel="apple-touch-icon-precomposed"
      href="<?php echo URL::base(); ?>images/ico/apple-touch-icon-57-precomposed.png"/>
<link rel="apple-touch-icon-precomposed" sizes="72x72"
      href="<?php echo URL::base(); ?>images/ico/apple-touch-icon-72-precomposed.png"/>
<link rel="apple-touch-icon-precomposed" sizes="114x114"
      href="<?php echo URL::base(); ?>images/ico/apple-touch-icon-114-precomposed.png"/>
<link rel="apple-touch-icon-precomposed" sizes="144x144"
      href="<?php echo URL::base(); ?>images/ico/apple-touch-icon-144-precomposed.png"/>

<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/jquery-1.7.2.min.js"></script>
<script language="JavaScript">
    function toggle_visibility(id) {
        var e = document.getElementById(id);
        if (e.style.display == 'none')
            e.style.display = 'block';
        else
            e.style.display = 'none';
    }
</script>

<script language="JavaScript">
    function Populate(form) {
        var myarray = new Array(<?php if(isset($templateData['alinkfil'])) echo $templateData['alinkfil']; ?>);
        var mynodes = new Array(<?php if(isset($templateData['alinknod'])) echo $templateData['alinknod']; ?>);
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
                            form.id.value = <?php if(isset($templateData['node'])) echo $templateData['node']->id; ?>;
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
        var xmlhttp;
        var qref1 = "qresponse_" + qid;
        var qref2 = document.getElementById(qref1);
        var qresp = qref2.value;
        var labsess = <?php if(isset($templateData['sessionId'])) echo $templateData['sessionId']; ?>;
        ;
        var URL = "<?php echo URL::base(); ?>renderLabyrinth/questionResponce/" + qresp + "/" + labsess + "/" + qid;
        if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        }
        else if (window.ActiveXObject) {// code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        else {
            alert("Your browser does not support XMLHTTP for AJAX!");
        }
        xmlhttp.open("GET", URL, false);
        xmlhttp.send(null);
        document.getElementById("AJAXresponse").innerHTML = xmlhttp.responseText;
    }

    function ajaxMCQ(qid, qqq, qqx, qnts, divids) {
        //alert("qid="+qid+", qqq="+qqq+", qqx="+qqx+", qnts="+qnts);
        //qid = questionID
        //qqq = option number
        //qqx = total number of options
        //qnts = number of tries - 0 or 1
        //script should: a) update database of the question submitted, b) update screen of the response given, c) update counter if this has been set

        var xmlhttp;
        var labsess = <?php if(isset($templateData['sessionId'])) echo $templateData['sessionId']; ?>;
        var URL = "<?php echo URL::base(); ?>renderLabyrinth/questionResponce/" + qqq + "/" + labsess + "/" + qid;
        if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
            xmlhttp.open("GET", URL, false);
            xmlhttp.send(null);
        }
        else if (window.ActiveXObject) {// code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            xmlhttp.open("GET", URL, false);
            xmlhttp.send();
        }
        else {
            alert("Your browser does not support XMLHTTP for AJAX!");
        }

        document.getElementById("AJAXresponse" + qqq).innerHTML = xmlhttp.responseText;
        if (qnts == 1) {
            //one try only then hide buttons
            for (hh = 0; hh <= divids.length; hh++) {
                var hhh = document.getElementById("click" + divids[hh]);
                hhh.style.display = 'none';
            }
        }
    }

    function ajaxBookmark() {
        var xmlhttp;
        var labsess = <?php if(isset($templateData['sessionId'])) echo $templateData['sessionId']; ?>;
        var thisnode = "<?php if(isset($templateData['node'])) echo $templateData['node']->id; ?>";
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

    function ajaxChatShowAnswer(ChatId, ChatElementId) {
        var xmlhttp;
        var labsess = <?php if(isset($templateData['sessionId'])) echo $templateData['sessionId']; ?>;
        var URL = "<?php echo URL::base(); ?>renderLabyrinth/chatAnswer/" + ChatId + "/" + ChatElementId + "/" + labsess + <?php if(isset($templateData['node'])) echo '"/" + '.$templateData['node']->map_id; ?>;

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
</script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/timelinejs/js/storyjs-embed.js"></script>

<script type="text/javascript">
    jQuery(document).ready(function ($) {



        //  $("#timeline_link").click(function () {
        /*   $("#timeline-embed").dialog({ title: "Your pathway through the labyrinth", modal: true, height: 350, width: $('.container').width(),open: function(event, ui) {
         $('html').css('overflow', 'hidden');
         $('.ui-widget-overlay').width($(".ui-widget-overlay").width() + 18);
         },
         close: function(event, ui) {
         $('html').css('overflow', 'auto');
         } });*/
        var modal = $('#pathway');
        $('#timeline_link').on('click', function () {

            var dates = <?php if (isset($templateData['trace_links'])) echo $templateData['trace_links']; ?>;
            // dates_lngth = dates.length;
            //dates[dates_lngth-1].endDate = $.datepicker.formatDate('yy,mm,dd,', mydate); Date.now();
            data = {
                "timeline": {

                    "type": "default",

                    "asset": {
                        "credit": "Credit Name Goes Here",
                        "caption": "Caption text goes here"
                    },
                    "date": dates

                }
            };


            modal.modal({width: $('.container').width(), modalOverflow: true});
            setTimeout(function () {

                createStoryJS({
                    type: 'timeline',

                    height: '300',
                    source: data,
                    embed_id: 'timeline-body',
                    start_at_end: true,                          //OPTIONAL START AT LATEST DATE
                    debug: true,                           //OPTIONAL DEBUG TO CONSOLE

                    css: '<?php echo URL::base(); ?>scripts/timelinejs/css/timeline.css',     //OPTIONAL PATH TO CSS
                    js: '<?php echo URL::base(); ?>scripts/timelinejs/js/timeline.js'    //OPTIONAL PATH TO JS
                });
            }, 500);


        });


        if (navigator.platform == 'iPad' || navigator.platform == 'iPhone' || navigator.platform == 'iPod') {
            $("#footer").css("position", "static");
        }
        ;
    });
</script>
<?php
if ($templateData['skin_path'] != NULL) {
    $doc_file = DOCROOT . 'css/skin/' . $templateData['skin_path'] . '/default.css';
    if (file_exists($doc_file)) {
        $css_file = URL::base() . 'css/skin/' . $templateData['skin_path'] . '/default.css';
        echo '<link rel="stylesheet" type="text/css" href="' . $css_file . '" />';
    }
}
?>
<style type="text/css">

        /* Sticky footer styles
        -------------------------------------------------- */

    html, body {
        height: 100%;
    }

    #wrap {
        min-height: 100%;
        padding-bottom: 5em;
    }

    #main {
        overflow: auto;
        padding-bottom: 150px;
    }

        /* must be same height as the footer */

    #footer {
        position: relative;
        position: fixed;
        bottom: 0;

        width: 100%

    }

    #footer-logo {

        width: 32px;
        margin: 10px auto 0;
    }


</style>
<!--[if !IE 7]>
<style type="text/css">
    #wrap {
        display: table;
        height: 100%
    }
</style>
<![endif]-->
</head>

<body class="basic">
<?php if (isset($templateData['editor']) and $templateData['editor'] == TRUE) { ?>
    <script language="javascript" type="text/javascript"
            src="<?php echo URL::base() ?>scripts/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
    <script language="javascript" type="text/javascript">
        tinyMCE.init({
            // General options
            mode: "textareas",
            width: "100%",
            relative_urls: false,
            theme: "advanced",
            plugins: "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,imgmap,autocomplete",
            // Theme options
            theme_advanced_buttons1: "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
            theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
            theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
            theme_advanced_buttons4: "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,|,imgmap",
            theme_advanced_toolbar_location: "top",
            theme_advanced_toolbar_align: "left",
            theme_advanced_statusbar_location: "bottom",
            theme_advanced_resizing: true,
            editor_selector: "mceEditor",
            autocomplete_trigger: "",
            entity_encoding: "raw"
        });
    </script>
<?php } ?>

<div id="wrap">
    <div id="pathway" class="modal hide fade" tabindex="-1">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3>Your pathway through the labyrinth</h3>
        </div>
        <div id="timeline-body" class="modal-body">

        </div>

    </div>
    <div class="navbar  navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container">
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <a href="<?php echo URL::base(); ?>" class="brand"><img
                        src="<?php echo URL::base(); ?>images/openlabyrinth-header.png" alt=""/> <span>Open</span>Labyrinth</a>

                <div class="nav-collapse collapse">

                    <div class="pull-right row">
                        <div class="span3">
                            <div>Map: <?php if (isset($templateData['map'])) echo $templateData['map']->name; ?>
                                (<?php if (isset($templateData['map'])) echo $templateData['map']->id; ?>)
                            </div>
                            <div>Node: <?php if (isset($templateData['node'])) echo $templateData['node']->id; ?></div>
                        </div>

                        <div class="span1">
                            <div>Score</div>
                            <div>0</div>
                        </div>
                    </div>
                </div>
                <!--/.nav-collapse -->
            </div>
        </div>
    </div>

    <div class="container">


        <div id="nodetext">
            <?php if (isset($templateData['editor']) and $templateData['editor'] == TRUE) { ?>
                <?php if (isset($templateData['node_edit'])) { ?>
                    <form method='POST'
                          action='<?php echo URL::base(); ?>renderLabyrinth/updateNode/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['node']->id; ?>'>
                        <h1><input placeholder="Title" id="mnodetitle" type='text' name='mnodetitle'
                                   value='<?php echo $templateData['node']->title; ?>'/></h1>


                        <textarea name='mnodetext' cols='60' rows='20'
                                  class='mceEditor'><?php echo $templateData['node_text']; ?></textarea>
                        <input class="btn btn-primary" type='submit' name='Submit' value='Submit'/>
                    </form>
                    <p><a class="btn btn-primary"
                          href='<?php echo URL::base() . 'linkManager/editLinks/' . $templateData['map']->id ."/". $templateData['node']->id; ?>'>links</a>

                        <a class="btn btn-primary"
                           href='<?php echo URL::base() . 'nodeManager/index/' . $templateData['map']->id; ?>'>nodes</a>

                        <a class="btn btn-primary"
                           href='<?php echo URL::base() . 'fileManager/index/' . $templateData['map']->id; ?>'>files</a>

                        <a class="btn btn-primary"
                           href='<?php echo URL::base() . 'counterManager/index/' . $templateData['map']->id; ?>'>counters</a>

                        <a class="btn btn-primary"
                           href='<?php echo URL::base(); ?>labyrinthManager/editMap/<?php echo $templateData['map']->id; ?>'>main
                            editor</a></p>
                <?php } else { ?>
                    <h1><?php if (isset($templateData['node_title'])) echo $templateData['node_title']; ?></h1>
                    <?php if (isset($templateData['node_text'])) echo $templateData['node_text']; ?>
                <?php } ?>
            <?php } else { ?>
                <h1><?php if (isset($templateData['node_title'])) echo $templateData['node_title']; ?></h1>
                <?php if (isset($templateData['node_text'])) echo $templateData['node_text']; ?>
            <?php } ?>
        </div>
        <div>
            <?php if (isset($templateData['links'])) {
                echo $templateData['links'];
            }?>
        </div>

        <?php if (isset($templateData['counters'])) echo $templateData['counters']; ?>


        <?php if (isset($templateData['navigation'])) echo $templateData['navigation']; ?>

    </div>
    <!-- /container -->
</div>
<div id="footer">

    <div class="navbar">
        <div class="navbar-inner navbar-fixed-bottom">
            <div class="container">
                <div class="row">
                    <div class="span4">
                        <ul class="nav">
                            <li>
                                <div><a class="btn btn-primary" href="#pathway" id="timeline_link">review your
                                        pathway</a></div>
                            </li>
                            <li>
                                <div><a class="btn btn-primary"
                                        href='<?php echo URL::base(); ?>renderLabyrinth/index/<?php echo $templateData['map']->id; ?>'>reset</a>
                                </div>
                            </li>

                        </ul>


                    </div>

                    <div class="span1">
                        <div id="footer-logo">
                            <a href="<?php echo URL::base(); ?>"><img
                                    src="<?php echo URL::base(); ?>images/footer-logo.png"
                                    title="OpenLabyrinth is an open source educational pathway system"
                                    alt="OpenLabyrinth logo"/></a>

                        </div>
                    </div>
                    <div class="span3">
                        <ul class="nav pull-right">
                            <li>
                                <div>
                                    <?php if(Auth::instance()->logged_in()){ if (!isset($templateData['node_edit'])) { ?>
                                        <a class="btn btn-primary"
                                           href="<?php echo URL::base(); ?>renderLabyrinth/go/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['node']->id; ?><?php if (!isset($templateData['node_edit'])) echo '/1'; ?>">edit</a>
                                    <?php } else { ?>
                                        <a class="btn btn-primary"
                                           href="<?php echo URL::base(); ?>renderLabyrinth/go/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['node']->id; ?>">view</a>   <?php }} ?>
                                </div>
                            </li>
                            <li><div><a class="btn btn-primary" href="<?php echo URL::base(); ?>renderLabyrinth/mapinfo/<?php echo $templateData['map']->id; ?>">info</a></div></li>
                            <li><input class="btn btn-primary" type="button" onclick='ajaxBookmark();' name="bookmark"
                                       value="bookmark"/></li>
                        </ul>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/jquery-ui-1.9.1.custom.min.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/application.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/datepicker/js/bootstrap-datepicker.js"></script>
<script src="<?php echo URL::base(); ?>scripts/bootstrap-modal/js/bootstrap-modalmanager.js"></script>
<script src="<?php echo URL::base(); ?>scripts/bootstrap-modal/js/bootstrap-modal.js"></script>

</body>
</html>