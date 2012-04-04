<?php if (isset($templateData['map']) and isset($templateData['node'])) { ?>
    <script language="javascript" type="text/javascript" src="<?php echo URL::base(); ?>scripts/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>    
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
    <table width="100%" height="100%" cellpadding="6">
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('edit "') . $templateData['node']->title . __('" in Labyrinth ') . '"' . $templateData['map']->name . '"'; ?></h4>
                <table bgcolor="#ffffff"><tr><td>
                            <p><a href="<?php echo URL::base() . 'nodeManager/editNode/' . $templateData['node']->id . '/h'; ?>">HTML</a> - <a href="<?php echo URL::base() . 'nodeManager/editNode/' . $templateData['node']->id . '/w'; ?>">WYSIWYG</a></p>
                            <form id="form1" name="form1" method="post" action="<?php echo URL::base() . 'nodeManager/updateNode/' . $templateData['node']->id; ?>">
                                <table width="100%" border="0" cellspacing="0" cellpadding="4">
                                    <tr>
                                        <td width="40%" align="right"><p>title</p></td>
                                        <td width="40%"><p><textarea name="mnodetitle" cols="60" rows="2"><?php echo $templateData['node']->title; ?></textarea></p></td>
                                    </tr>
                                    <tr>
                                        <td align="right"><p><?php echo __('node content'); ?></p></td>
                                        <td><p>
                                                <textarea name="mnodetext" cols='60' rows='10' <?php if (isset($templateData['editMode']) && $templateData['editMode'] == 'w') echo 'class="mceEditor"'; ?>><?php echo $templateData['node']->text; ?></textarea>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right"><p><img src="<?php echo URL::base(); ?>images/info_blak.gif"><?php echo __('supporting information'); ?></p></td>
                                        <td><textarea name="mnodeinfo" cols='60' rows='10' <?php if (isset($templateData['editMode']) && $templateData['editMode'] == 'w') echo 'class="mceEditor"'; ?>><?php echo $templateData['node']->info; ?></textarea></td>
                                    </tr>
                                    <tr>
                                        <td><p>&nbsp;</p></td>
                                        <td><p><input type="submit" name="Submit" value="<?php echo __('submit'); ?>"></p></td>
                                    </tr>
                                    <?php if(isset($templateData['counters']) and count($templateData['counters']) > 0) { ?>
                                    <?php foreach($templateData['counters'] as $counter) { ?>
                                    <tr>
                                        <td align="right">
                                            <p><?php echo __('counter function for'); ?>: "<?php echo $counter->name; ?>"</p>
                                        </td>
                                        <td>
                                            <hr>
                                            <p><input type="text" name="cfunc_<?php echo $counter->id; ?>" size="5" 
                                                      value="<?php $c = $templateData['node']->getCounter($counter->id); if($c != NULL) echo $c->function; ?>">&nbsp;type +, - or = an integer - e.g. '+1' or '=32'</p>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    <?php } ?>
                                    <tr>
                                        <td align="right"><p><?php echo __('exit Node Probability'); ?></p></td>
                                        <td><hr><p>[&nbsp;<?php echo __('on'); ?>&nbsp;<input name="mnodeprobability" type="radio" value="1" <?php if ($templateData['node']->probability) echo 'checked=""'; ?>>&nbsp;]&nbsp;&nbsp;&nbsp;[&nbsp;<?php echo __('off'); ?>&nbsp;<input name="mnodeprobability" type="radio" value="0" <?php if (!$templateData['node']->probability) echo 'checked=""'; ?>>&nbsp;]</p><hr></td>
                                    </tr>
                                    <tr>
                                        <td align="right"><p><?php echo __('node conditional'); ?></p></td>
                                        <td><p>[<?php echo $templateData['node']->conditional; ?>] - [message:<?php echo $templateData['node']->conditional_message; ?>&nbsp;<i></i>] - [<a href="<?php echo URL::base().'nodeManager/editConditional/'.$templateData['node']->id; ?>"><?php echo __('click to reset'); ?></a>]</p><hr></td>
                                    </tr>
                                    <tr>
                                        <td align="right"><p><?php echo __('link function style'); ?></p></td>
                                        <td><p>
                                                <?php if (isset($templateData['linkStyles'])) { ?>
                                                    <?php foreach ($templateData['linkStyles'] as $linkStyle) { ?>
                                                        <input type="radio" name="linkstyle" value="<?php echo $linkStyle->id ?>" <?php if ($linkStyle->id == $templateData['node']->link_style_id) echo 'checked=""'; ?>><?php echo __($linkStyle->name); ?> |
                                                    <?php } ?>
                                                <?php } ?>
                                            </p>
                                            <hr>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right"><p><?php echo __('node priority'); ?></p></td>
                                        <td><p>
                                                <?php if (isset($templateData['priorities'])) { ?>
                                                    <?php foreach ($templateData['priorities'] as $priority) { ?>
                                                        <input type="radio" name="priority" value="<?php echo $priority->id ?>" <?php if ($priority->id == $templateData['node']->priority_id) echo 'checked=""'; ?>><?php echo $priority->name; ?> |
                                                    <?php } ?>
                                                <?php } ?>
                                            </p><hr></td>
                                    </tr>
                                    <tr>
                                        <td align="right"><p><?php echo __('root node'); ?></p></td>
                                        <td><p>
                                                <a href="<?php echo URL::base().'nodeManager/setRootNode/'.$templateData['map']->id.'/'.$templateData['node']->id; ?>"><?php echo __('click to set this node as root'); ?></a></p>
                                            <hr></td>
                                    </tr>
                                    <tr>
                                        <td align="right"><p><?php echo __('enable undo links'); ?></p></td>
                                        <td><p>[&nbsp;<?php echo __('on'); ?>&nbsp;<input name="mnodeUndo" type="radio" value="1" <?php if ($templateData['node']->undo) echo 'checked=""'; ?>>&nbsp;]&nbsp;&nbsp;&nbsp;[&nbsp;<?php echo __('off'); ?>&nbsp;<input name="mnodeUndo" type="radio" value="0" <?php if (!$templateData['node']->undo) echo 'checked=""'; ?>>&nbsp;]
                                            </p><hr></td>
                                    </tr>
                                    <tr>
                                        <td align="right"><p><?php echo __('link to end and report from this node'); ?></p></td>
                                        <td><p><input type="radio" name="ender" value="0" <?php if (!$templateData['node']->end) echo 'checked=""'; ?>><?php echo __('off'); ?> (<?php echo __('default'); ?>) | <input type="radio" name="ender" value="1" <?php if ($templateData['node']->end) echo 'checked=""'; ?>><?php echo __('on'); ?></p><hr></td>
                                    </tr>
                                    <tr>
                                        <td><p>&nbsp;</p></td>
                                        <td><p><input type="submit" name="Submit" value="<?php echo __('submit'); ?>"></p></td>
                                    </tr>
                                </table>
                            </form>
                            <br>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>