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
            eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
            if (restore) selObj.selectedIndex=0;
        }

        function ajaxFunction(qid)
        {
            var xmlhttp;
            var qref1 = "qresponse_"+qid;
            var qref2 = document.getElementById(qref1);
            var qresp = qref2.value;
            var labsess = "<%=mysession%>";
            var URL = "questionresponse.asp?r="+qresp+"&s="+labsess+"&q="+qid;
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

        function ajaxMCQ(qid,qqq,qqx,qnts)
        {
            //alert("qid="+qid+", qqq="+qqq+", qqx="+qqx+", qnts="+qnts);
            //qid = questionID
            //qqq = option number
            //qqx = total number of options
            //qnts = number of tries - 0 or 1
            //script should: a) update database of the question submitted, b) update screen of the response given, c) update counter if this has been set

            var xmlhttp;
            var labsess = "<%=mysession%>";
            var URL = "questionresponse.asp?o="+qqq+"&s="+labsess+"&q="+qid;
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
                var hh=1;
                for (hh=1;hh<=qqx;hh++)
                {
                    var hhh = document.getElementById("click"+hh);
                    hhh.style.display = 'none';
                }
            }
        }

        function ajaxBookmark()
        {
            var xmlhttp;
            var labsess = "<%=mysession%>";
            var thisnode = "<%=mnodeid%>";
            var URL = "bookmark.asp?s="+labsess+"&n="+thisnode;
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
            xmlhttp.open("GET",URL,true);
            xmlhttp.send(null);
        }

        function ajaxChatShowAnswer(ChatId, ChatElementId)
        {
            var xmlhttp;
            var labsess = "<%=mysession%>";
            var URL = "chatanswer.asp?ch=" + ChatId +"&ce=" + ChatElementId + "&s=" + labsess;
    
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
    <div align="center">
        <table width="90%" border="0" cellpadding="12" cellspacing="2">
            <tr>
                <td width="81%" bgcolor="#FFFFFF" align="left">
                    <h4><font color="#000000"><?php if (isset($templateData['node_title'])) echo $templateData['node_title']; ?></font></h4>
                    <?php if (isset($templateData['node_text'])) echo $templateData['node_text']; ?>
                    <?php if (isset($templateData['node']) and $templateData['node']->info != '') { ?>
                        <p><a href="#" <?php if (isset($templateData['node'])) { ?> onclick="window.open('<?php echo URL::base(); ?>renderLabyrinth/info/<?php echo $templateData['node']->id; ?>', 'info', 'toolbar=no, directories=no, location=no, status=no, menubat=no, resizable=no, scrollbars=yes, width=500, height=400'); return false;" <?php } ?>><img src="<?php echo URL::base(); ?>images/info_lblu.gif" border="0" alt="info"></a></p>
                    <?php } ?>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <?php if (isset($templateData['links'])) echo $templateData['links']; ?>
                            </td>
                            <td align="right" valign="bottom">
                                <?php if (isset($templateData['counters'])) echo $templateData['counters']; ?>
                            </td></tr>
                    </table>
                </td>
                <td width="19%" rowspan="2" valign="top" bgcolor="#FFFFFF"><p align="center">
                        <?php if (isset($templateData['navigation'])) echo $templateData['navigation']; ?>

                    <h5>Map: <?php if (isset($templateData['map'])) echo $templateData['map']->name; ?> (<?php if (isset($templateData['map'])) echo $templateData['map']->id; ?>)<br />
                        Node: <?php if (isset($templateData['node'])) echo $templateData['node']->id; ?><%=showmapscore%></h5>
                    <input type="button" onclick='ajaxBookmark();' name="bookmark" value="bookmark" />
                    <%=esstring%>
                    <p><a href='/<%=olpath%>mstartnode.asp?mapid=<%=mapid%>'>reset</a></p>
                    </div>
                    <a href="/<%=olpath%>"><img src="<?php echo URL::base(); ?>images/openlabyrinth-powerlogo-wee.jpg" height="20" width="118" alt="OpenLabyrinth"  border="0" /></a>
                    <h5>OpenLabyrinth is an open source educational pathway system</h5>
                </td></tr>
            <tr>
                <td bgcolor="#FFFFFF">
                    <%=traceString%>  
                </td>
            </tr>

        </table>
    </td>
</tr>
</table>
</div>
</body>
</html>