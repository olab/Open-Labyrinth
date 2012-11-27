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
if (isset($templateData['map'])) { ?>
    <table width="100%" height="100%" cellpadding="6">
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('edit files for Labyrinth "') . $templateData['map']->name . '"'; ?></h4>
                <p>Once uploaded copy and paste the file tag (looks like [[MR:1234567]]) into a node's content box or info box to display or link a file there</p>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff">
                        <td>
                            <p>Labyrinth '<?php echo $templateData['map']->id; ?>' - <?php echo $templateData['files_count']; ?> files, <?php echo $templateData['files_size']; ?></p><table border="0" width="100%" cellpadding="1">
                            </table>
                            <table>
                                <?php if(isset($templateData['files']) and count($templateData['files']) > 0) { ?>
                                <?php foreach($templateData['files'] as $file) { ?>
                                <tr>
                                    <?php
                                        $preview = '';
                                        $isInput = false;
                                        $isImage = false;

                                        if($file->mime == 'image/gif') {
                                            $preview = '<img src="'.URL::base().$file->path.'?'.time().'" />';
                                            $isInput = true;
                                            $isImage = true;
                                        } else if($file->mime == 'image/jpg') {
                                            $preview = '<img src="'.URL::base().$file->path.'?'.time().'" />';
                                            $isInput = true;
                                            $isImage = true;
                                        } else if($file->mime == 'image/png') {
                                            $preview = '<img src="'.URL::base().$file->path.'?'.time().'" />';
                                            $isInput = true;
                                            $isImage = true;
                                        } else if($file->mime == 'image/jpeg') {
                                            $preview = '<img src="'.URL::base().$file->path.'?'.time().'" />';
                                            $isInput = true;
                                            $isImage = true;
                                        } else if($file->mime == 'application/vnd.ms-powerpoint') {
                                            $preview = '<img src="'.URL::base().'images/PPIcon.gif">';
                                        } else if($file->mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                            $preview = '<img src="'.URL::base().'images/wordicon.gif">';
                                        } else if($file->mime == 'application/msword') {
                                            $preview = '<img src="'.URL::base().'images/wordicon.gif">';
                                        } else if($file->mime == 'application/x-shockwave-flash') {
                                            if(strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
                                                $preview = "<p>Shockwave Flash: ".$file->name." <object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0' width='200' height='300'>".
                                                            "<param name='movie' value='". $file->path ."' />".
                                                            "<param name='allowScriptAccess' value='sameDomain' />".
                                                            "<param name='quality' value='high' />".
                                                            "</object></p>";
                                            } else {
                                                $preview = "<p>Shockwave Flash: ".$file->name." <object type='application/x-shockwave-flash' data='".URL::base().$file->path ."' width='300' height='200'>".
                                                            "<param name='allowScriptAccess' value='sameDomain' />".
                                                            "<param name='quality' value='high' />".
                                                            "</object></p>";
                                            }
                                            $isInput = true;
                                        } else if($file->mime == 'application/vnd.ms-excel') {
                                            $preview = '<img src="'.URL::base().'images/wordicon.gif">';
                                        } else {
                                            $preview = '<p>no preview</p>';
                                        }
                                    ?>
                                            <td>
                                                <?php if($isInput) { ?>
                                                    <input type="text" size="20" value="[[MR:<?php echo $file->id; ?>]]">
                                                <?php } else { ?>
                                                    <p><textarea cols="30"><a href="<?php echo URL::base().$file->path; ?>" border="0"><?php echo $file->name; ?></a></textarea></p>
                                                <?php } ?> 
                                            </td>
                                            <td align="center" valign="middle">
                                                <?php echo $preview; ?>
                                            </td>
                                            <td>
                                                <p>
                                                    <a href="<?php echo URL::base().$file->path; ?>"><?php echo $file->name; ?></a>
                                                    <br><?php echo filesize(DOCROOT.'/'.$file->path) / 1000; ?> kb<br>last modified <?php echo date('d.m.Y H:i:s.', filemtime(DOCROOT.'/'.$file->path)); ?>
                                                        <br>[<a href="<?php echo URL::base().'fileManager/editFile/'.$templateData['map']->id.'/'.$file->id; ?>">edit</a>]&nbsp;&nbsp;&nbsp;[<a href="<?php echo URL::base().'fileManager/deleteFile/'.$templateData['map']->id.'/'.$file->id; ?>">delete</a>]
                                                </p>
                                                <?php if ($isImage){ ?>
                                                <p>[<a href="<?php echo URL::base().'fileManager/imageEditor/'.$templateData['map']->id.'/'.$file->id; ?>">image editor</a>]</p>
                                                <?php } ?>
                                            </td>
                                            <tr><td colspan="3"><hr></td></tr>
                                </tr>
                                <?php } ?>
                                <?php } ?>
                                
                            </table>

                            <p><?php echo __('upload a file to Labyrinth'); ?> "<?php echo $templateData['map']->name; ?>"</p>

                            <form method="POST" enctype="multipart/form-data" action="<?php echo URL::base().'fileManager/uploadFile/'.$templateData['map']->id; ?>">
                                <table width="100%" border="0" cellspacing="6">
                                    <tr>
                                        <td nowrap=""><p><?php echo __('select file to upload'); ?></p></td>
                                        <td align="center">
                                            <input type="FILE" size="50" name="filename"></td>
                                        <td>
                                            <input type="submit" name="Submit" value="<?php echo __('submit'); ?>">
                                        </td>
                                    </tr>
                                </table>
                            </form>
                            <hr>
                            <p>JPEG (.jpg + .jpeg), GIF (.gif), PNG (.png), Acrobat PDF (.pdf), Shockwave Flash (.swf), Microsoft Word, (.doc), Microsoft Excel (.xls), Microsoft PowerPoint (.ppt), Rich Text Format (.rtf), Quicktime Video (.mov), MPEG-4 Video (.mp4), Windows Media (.wmv), Real Stream (.ram), Real Stream (.rpm), Flash video, (.flv), MP3 audio (.mp3), WAV audio (.wav), AAC (m4a) audio (.m4a)</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>