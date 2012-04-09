<html>
    <title><?php if (isset($templateData['node_title'])) echo $templateData['node_title']; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link rel="stylesheet" type="text/css" href="<?php echo URL::base(); ?>css/skin/basic/layout.css" />

    <SCRIPT LANGUAGE="JavaScript">
        function toggle_visibility(id) {
            var e = document.getElementById(id);
            if(e.style.display == 'none')
                e.style.display = 'block';
            else
                e.style.display = 'none';
        }
    </SCRIPT>

    <script language="JavaScript">
        function Populate(form) {
            var myarray = new Array(<?php if(isset($templateData['alinkfil'])) echo $templateData['alinkfil']; ?>);
            var mynodes = new Array(<?php if(isset($templateData['alinknod'])) echo $templateData['alinknod']; ?>);
            var mycount = 0;
            var mybuffer = form.filler.value;

            if (mybuffer.length >1){
                for (var i = 0; i < myarray.length; i++) {
                    for (var j = 0; j < myarray[i].length; j++) {
                        var ffv = form.filler.value.toLowerCase();
                        if (ffv == myarray[i].substring(0,j)) {
                            for (var k = i+1; k < myarray.length; k++) {
                                var t1 = myarray[i].substring(0,j);
                                var t2 = myarray[k].substring(0,j);
                                if (t1 == t2) {
                                    mycount++;
                                }}
                            if (mycount < 1) {
                                form.filler.value = myarray[i];
                                form.id.value = mynodes[i];
                            }
                            else {
                                form.id.value = <?php if(isset($templateData['node'])) echo $templateData['node']->id; ?>;
                            }
                        }}}}}

        function jumpMenu(targ,selObj,restore){ 
            eval(targ+".location='<?php echo URL::base(); ?>renderLabyrinth/go/<?php echo $templateData['node']->map_id; ?>/"+selObj.options[selObj.selectedIndex].value+"'");
            if (restore) selObj.selectedIndex=0;
        }

        function ajaxFunction(qid)
        {
            var xmlhttp;
            var qref1 = "qresponse_"+qid;
            var qref2 = document.getElementById(qref1);
            var qresp = qref2.value;
            var labsess = <?php if(isset($templateData['sessionId'])) echo $templateData['sessionId']; ?>;;
            var URL = "<?php echo URL::base(); ?>renderLabyrinth/questionResponce/"+qresp+"/"+labsess+"/"+qid;
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();}
            else if (window.ActiveXObject)
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
            else
            {
                alert("Your browser does not support XMLHTTP for AJAX!");
            }
            xmlhttp.open("GET",URL,false);
            xmlhttp.send(null);
            document.getElementById("AJAXresponse").innerHTML=xmlhttp.responseText;
        }

        function ajaxMCQ(qid,qqq,qqx,qnts,divids)
        {
            //alert("qid="+qid+", qqq="+qqq+", qqx="+qqx+", qnts="+qnts);
            //qid = questionID
            //qqq = option number
            //qqx = total number of options
            //qnts = number of tries - 0 or 1
            //script should: a) update database of the question submitted, b) update screen of the response given, c) update counter if this has been set

            var xmlhttp;
            var labsess = <?php if(isset($templateData['sessionId'])) echo $templateData['sessionId']; ?>;
            var URL = "<?php echo URL::base(); ?>renderLabyrinth/questionResponce/"+qqq+"/"+labsess+"/"+qid;
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
                xmlhttp.open("GET",URL,false);
                xmlhttp.send(null);
            }
            else if (window.ActiveXObject)
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
                xmlhttp.open("GET",URL,false);
                xmlhttp.send();
            }
            else
            {
                alert("Your browser does not support XMLHTTP for AJAX!");
            }
            
            document.getElementById("AJAXresponse"+qqq).innerHTML=xmlhttp.responseText;
            if (qnts==1){
                //one try only then hide buttons
                for (hh=0;hh<=divids.length;hh++)
                {
                    var hhh = document.getElementById("click"+divids[hh]);
                    hhh.style.display = 'none';
                }
            }
        }

        function ajaxBookmark()
        {
            var xmlhttp;
            var labsess = <?php if(isset($templateData['sessionId'])) echo $templateData['sessionId']; ?>;
            var thisnode = "<?php if(isset($templateData['node'])) echo $templateData['node']->id; ?>";
            var URL = "<?php echo URL::base(); ?>renderLabyrinth/addBookmark/"+labsess+"/"+thisnode;
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();}
            else if (window.ActiveXObject)
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
            else
            {
                alert("Your browser does not support XMLHTTP for AJAX!");
            }
            xmlhttp.onreadystatechange=function()
            {
            }
            xmlhttp.open("POST",URL,true);
            xmlhttp.send(null);
        }

        function ajaxChatShowAnswer(ChatId, ChatElementId)
        {
            var xmlhttp;
            var labsess = <?php if(isset($templateData['sessionId'])) echo $templateData['sessionId']; ?>;
            var URL = "<?php echo URL::base(); ?>renderLabyrinth/chatAnswer/" + ChatId +"/" + ChatElementId + "/" + labsess + <?php if(isset($templateData['node'])) echo '"/" + '.$templateData['node']->map_id; ?>;
    
            if (window.XMLHttpRequest)
            {
                xmlhttp=new XMLHttpRequest();
                xmlhttp.open("GET", URL, false);
                xmlhttp.send(null);
            }
            else if (window.ActiveXObject)
            {
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
                xmlhttp.open("GET",URL,false);
                xmlhttp.send();
            }
            else
            {
                alert("Your browser does not support XMLHTTP for AJAX!");
            }
    
            document.getElementById("ChatAnswer" + ChatElementId).innerHTML = "<p><b>&nbsp;&nbsp;&nbsp;&nbsp;" + xmlhttp.responseText + "</b></p>";  
            document.getElementById("ChatQuestion" + ChatElementId).style.color = "grey";
        }
    </script>
</head>

<body>
<?php if(isset($templateData['editor']) and $templateData['editor'] == TRUE) { ?>
<script language="javascript" type="text/javascript" src="<?php echo URL::base() ?>scripts/tinymce/jscripts/tiny_mce/tiny_mce.js" /></script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
    // General options
    mode: "textareas",
    theme: "advanced",
    plugins: "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",
    // Theme options
    theme_advanced_buttons1: "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
    theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
    theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
    theme_advanced_buttons4: "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft",
    theme_advanced_toolbar_location: "top",
    theme_advanced_toolbar_align: "left",
    theme_advanced_statusbar_location: "bottom",
    theme_advanced_resizing: true,
    editor_selector: "mceEditor"
});
</script>
<?php } ?>
    <div align="center">
        <table width="90%" border="0" cellpadding="12" cellspacing="2">
            <tr>
                <td width="81%" bgcolor="#FFFFFF" align="left">
                    <h4><font color="#000000"><?php if (isset($templateData['node_title'])) echo $templateData['node_title']; ?></font></h4>
                    
                    <?php if(isset($templateData['editor']) and $templateData['editor'] == TRUE) { ?>
                        <?php if(isset($templateData['node_edit'])) { ?>
                            <form method='POST' action='<?php echo URL::base(); ?>renderLabyrinth/updateNode/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['node']->id; ?>'>
                                <p><input type='text' name='mnodetitle' value='<?php echo $templateData['node']->title; ?>' /></p>
                                <p><textarea name='mnodetext' cols='60' rows='20' class='mceEditor'><?php echo $templateData['node_text']; ?></textarea></p>
                                <input type='submit' name='Submit' value='Submit' />
                            </form>
                            <p><a href='<?php echo URL::base().'linkManager/index/'.$templateData['map']->id; ?>'>links</a> - 
                               <a href='<?php echo URL::base().'nodeManager/index/'.$templateData['map']->id; ?>'>nodes</a> - 
                               <a href='<?php echo URL::base().'fileManager/index/'.$templateData['map']->id; ?>'>files</a> - 
                               <a href='<?php echo URL::base().'counterManager/index/'.$templateData['map']->id; ?>'>counters</a> - 
                               <a href='<?php echo URL::base(); ?>labyrinthManager/editMap/<?php echo $templateData['map']->id; ?>'>main editor</a></p>
                        <?php } else { ?>
                            <?php if (isset($templateData['node_text'])) echo $templateData['node_text']; ?>
                        <?php } ?>
                    <?php } else { ?>
                            <?php if (isset($templateData['node_text'])) echo $templateData['node_text']; ?>
                    <?php } ?>
                    
                    <?php if (isset($templateData['node']) and $templateData['node']->info != '' and $templateData['node']->info != ' ') { ?>
                        <p><a href="#" <?php if (isset($templateData['node'])) { ?> onclick="window.open('<?php echo URL::base(); ?>renderLabyrinth/info/<?php echo $templateData['node']->id; ?>', 'info', 'toolbar=no, directories=no, location=no, status=no, menubat=no, resizable=no, scrollbars=yes, width=500, height=400'); return false;" <?php } ?>><img src="<?php echo URL::base(); ?>images/info_lblu.gif" border="0" alt="info"></a></p>
                    <?php } ?>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <?php if (isset($templateData['links'])) { echo $templateData['links']; }?>
                            </td>
                            <td align="right" valign="bottom">
                                <?php if (isset($templateData['counters'])) echo $templateData['counters']; ?>
                            </td></tr>
                    </table>
                </td>
                <td width="19%" rowspan="2" valign="top" bgcolor="#FFFFFF"><p align="center">
                        <?php if (isset($templateData['navigation'])) echo $templateData['navigation']; ?>

                    <h5>Map: <?php if (isset($templateData['map'])) echo $templateData['map']->name; ?> (<?php if (isset($templateData['map'])) echo $templateData['map']->id; ?>)<br />
                        Node: <?php if (isset($templateData['node'])) echo $templateData['node']->id; ?><br/><strong>Score:</strong></h5>
                    <input type="button" onclick='ajaxBookmark();' name="bookmark" value="bookmark" />
                    
                    <?php if(isset($templateData['editor']) and $templateData['editor'] == TRUE) { ?>
                    <h5>
                        <?php if(!isset($templateData['node_edit'])) { ?>
                            <a href="<?php echo URL::base(); ?>renderLabyrinth/go/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['node']->id; ?><?php if(!isset($templateData['node_edit'])) echo '/1'; ?>">turn editing on</a>
                        <?php } else { ?>
                            <a href="<?php echo URL::base(); ?>renderLabyrinth/go/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['node']->id; ?>">turn editing off</a>
                        <?php } ?>
                    </h5>
                    <?php } ?>
                    <p><a href='<?php echo URL::base(); ?>renderLabyrinth/index/<?php echo $templateData['map']->id; ?>'>reset</a></p>
                    </div>
                    <a href="/<%=olpath%>"><img src="<?php echo URL::base(); ?>images/openlabyrinth-powerlogo-wee.jpg" height="20" width="118" alt="OpenLabyrinth"  border="0" /></a>
                    <h5>OpenLabyrinth is an open source educational pathway system</h5>
                </td></tr>
            <tr>
                <td bgcolor="#FFFFFF">
                    <a href="#" onclick="toggle_visibility('track');"><p class='style2'><strong>Review your pathway</strong></p></a>
                    <div id='track' style='display:none'>
                        <?php if(isset($templateData['trace_links'])) echo $templateData['trace_links']; ?>
                    </div>
                </td>
            </tr>

        </table>
    </td>
</tr>
</table>
</div>
</body>
</html>