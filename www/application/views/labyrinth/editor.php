<?php if (isset($templateData['map'])) { ?>
    <table width="100%" height="100%" cellpadding='6'>
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('editor for Labyrinth') . ' "' . $templateData['map']->name . '"'; ?></h4>
                <p><?php echo __('you can access all of this Labyrinth&#039;s editing features from this page.'); ?></p>
                <table width="100%" border="0" cellspacing="0" cellpadding="4" bgcolor="#ffffff">
                    <tr>
                        <td align="left">
                            <table width="100%" border="0">
                                <tr>
                                    <td width="25%" align="left" nowrap="">
                                        <p><a href=<?php echo URL::base().'labyrinthManager/global/'.$templateData['map']->id; ?>><img src="<?php echo URL::base(); ?>images/OL_global_wee.gif" alt="global" align="absmiddle" border="0">&nbsp;&nbsp;<strong><?php echo __('global'); ?></strong></a></p>
                                        <p><a href="<?php echo URL::base().'nodeManager/index/'.$templateData['map']->id; ?>"><img src="<?php echo URL::base(); ?>images/OL_nodes_wee.gif" alt="nodes" align="absmiddle" border="0">&nbsp;&nbsp;<strong><?php echo __('nodes'); ?></strong></a></p>
                                        <p><a href="<?php echo URL::base().'nodeManager/grid/'.$templateData['map']->id; ?>"><img src="<?php echo URL::base(); ?>images/OL_nodegrid_wee.gif" alt="nodegrid" align="absmiddle" border="0">&nbsp;&nbsp;<strong><?php echo __('nodegrid'); ?></strong></a></p>
                                        <p><a href="<?php echo URL::base().'nodeManager/sections/'.$templateData['map']->id; ?>"><img src="<?php echo URL::base(); ?>images/OL_section_wee.gif" alt="sections" align="absmiddle" border="0">&nbsp;&nbsp;<strong><?php echo __('sections'); ?></strong></a></p>
                                        <p><a href="<?php echo URL::base().'linkManager/index/'.$templateData['map']->id; ?>"><img src="<?php echo URL::base(); ?>images/OL_links_wee.gif" alt="links" align="absmiddle" border="0">&nbsp;&nbsp;<strong><?php echo __('links'); ?></strong></a></p>
                                        <p><a href="<?php echo URL::base().'chatManager/index/'.$templateData['map']->id; ?>"><img src="<?php echo URL::base(); ?>images/OL_chats_wee.gif" alt="chats" align="absmiddle" border="0">&nbsp;&nbsp;<strong><?php echo __('chats'); ?></strong></a></p>
                                    </td>
                                    <td width="25%" align="left" nowrap="">
                                        <p><a href="/openlabyrinth/mstartnode.asp?mapid=6" target="_blank"><img src="<?php echo URL::base(); ?>images/OL_preview_wee.gif" alt="preview" align="absmiddle" border="0">&nbsp;&nbsp;<strong><?php echo __('preview'); ?></strong></a></p>
                                        <p><a href="mapview.asp?mapid=6" target="_blank"><img src="<?php echo URL::base(); ?>images/OL_visualeditor_wee.gif" alt="visual editor" align="absmiddle" border="0">&nbsp;&nbsp;<strong><?php echo __('visual editor'); ?></strong></a></p><a href="mapview.asp?mapid=6" target="_blank">
                                        </a><p><a href="<?php echo URL::base().'feedbackManager/index/'.$templateData['map']->id; ?>" target="_blank"></a><a href="feedback.asp?mapid=6"><img src="<?php echo URL::base(); ?>images/OL_feedback_wee.gif" alt="feedback" align="absmiddle" border="0">&nbsp;&nbsp;<strong><?php echo __('feedback'); ?></strong></a></p>
                                        <p><a href="<?php echo URL::base().'questionManager/index/'.$templateData['map']->id; ?>"><img src="<?php echo URL::base(); ?>images/OL_question_wee.gif" alt="questions" align="absmiddle" border="0">&nbsp;&nbsp;<strong><?php echo __('questions'); ?></strong></a></p>
                                        <p><a href="<?php echo URL::base().'avatarManager/index/'.$templateData['map']->id; ?>"><img src="<?php echo URL::base(); ?>images/OL_avatar_wee.gif" alt="avatars" align="absmiddle" border="0">&nbsp;&nbsp;<strong><?php echo __('avatars'); ?></strong></a></p>
                                    </td>
                                    <td width="25%" align="left" nowrap="">
                                        <p><a href="<?php echo URL::base().'fileManager/index/'.$templateData['map']->id; ?>"><img src="<?php echo URL::base(); ?>images/OL_files_wee.gif" alt="files" align="absmiddle" border="0">&nbsp;&nbsp;<strong><?php echo __('files'); ?></strong></a></p>
                                        <p><a href="<?php echo URL::base().'counterManager/index/'.$templateData['map']->id; ?>"><img src="<?php echo URL::base(); ?>images/OL_counter_wee.gif" alt="counters" align="absmiddle" border="0">&nbsp;&nbsp;<strong><?php echo __('counters'); ?></strong></a></p>
                                        <p><a href="<?php echo URL::base().'counterManager/grid/'.$templateData['map']->id; ?>"><img src="<?php echo URL::base(); ?>images/OL_countergrid_wee.gif" alt="countergrid" align="absmiddle" border="0">&nbsp;&nbsp;<strong><?php echo __('countergrid'); ?></strong></a></p>
                                        <p><a href="vpdlist.asp?mapid=6"><img src="<?php echo URL::base(); ?>images/OL_element_wee.gif" alt="elements" align="absmiddle" border="0">&nbsp;&nbsp;<strong><?php echo __('elements'); ?></strong></a></p>
                                        <p><a href="damlist.asp?mapid=6"><img src="<?php echo URL::base(); ?>images/OL_cluster_wee.gif" alt="clusters" align="absmiddle" border="0">&nbsp;&nbsp;<strong><?php echo __('clusters'); ?></strong></a></p>
                                    </td>
                                    <td width="25%" align="left" nowrap="">
                                        <p><a href="labyrinth_report.asp?mapid=6"><img src="<?php echo URL::base(); ?>images/OL_report_wee.gif" alt="session reports" align="absmiddle" border="0">&nbsp;&nbsp;<strong><?php echo __('session reports'); ?></strong></a></p>
                                        <p><a href="<?php echo URL::base().'mapUserManager/index/'.$templateData['map']->id; ?>"><img src="<?php echo URL::base(); ?>images/OL_users_wee.gif" alt="users" align="absmiddle" border="0">&nbsp;&nbsp;<strong><?php echo __('users'); ?></strong></a></p>
                                        <p><a href="export.asp?mapid=6"><img src="<?php echo URL::base(); ?>images/OL_export_wee.gif" alt="export" align="absmiddle" border="0">&nbsp;&nbsp;<strong><?php echo __('export'); ?></strong></a></p>
                                        <p><a href="duplicatemap.asp?mapid=6"><img src="<?php echo URL::base(); ?>images/OL_duplicate_wee.gif" alt="duplicate" align="absmiddle" border="0">&nbsp;&nbsp;<strong><?php echo __('duplicate'); ?></strong></a></p>
                                        <p><a href=<?php echo URL::base().'labyrinthManager/disableMap/'.$templateData['map']->id; ?>><img src="<?php echo URL::base(); ?>images/OL_delete_wee.gif" alt="delete" align="absmiddle" border="0">&nbsp;&nbsp;<strong><?php echo __('delete'); ?></strong></a></p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>
