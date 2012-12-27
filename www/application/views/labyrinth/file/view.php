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
if (isset($templateData['map'])) {
    ?>

    <h1><?php echo __('Edit files for Labyrinth "') . $templateData['map']->name . '"'; ?></h1>
    <p>Once uploaded copy and paste the file tag (looks like [[MR:1234567]]) into a node's content box or info box to
        display or link a file there</p>

    Labyrinth '<?php echo $templateData['map']->id; ?>' - <?php echo $templateData['files_count']; ?> files, <?php echo $templateData['files_size']; ?></p>


    <table class="table table-striped table-bordered">
        <colgroup>
            <col/>
            <col/>
            <col/>
            <col/>
        </colgroup>
        <thead>
        <tr>
            <th>Title</th>
            <th>Embeddable</th>
            <th>Details</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>

        <?php if (isset($templateData['files']) and count($templateData['files']) > 0) { ?>
            <?php foreach ($templateData['files'] as $file) { ?>
                <tr>
                         <?php
                    $preview = '';
                    $isInput = false;
                    $isImage = false;

                    if ($file->mime == 'image/gif') {
                        $preview = '<img src="' . URL::base() . $file->path . '?' . time() . '" />';
                        $isInput = true;
                        $isImage = true;
                    } else if ($file->mime == 'image/jpg') {
                        $preview = '<img src="' . URL::base() . $file->path . '?' . time() . '" />';
                        $isInput = true;
                        $isImage = true;
                    } else if ($file->mime == 'image/png') {
                        $preview = '<img src="' . URL::base() . $file->path . '?' . time() . '" />';
                        $isInput = true;
                        $isImage = true;
                    } else if ($file->mime == 'image/jpeg') {
                        $preview = '<img src="' . URL::base() . $file->path . '?' . time() . '" />';
                        $isInput = true;
                        $isImage = true;
                    } else if ($file->mime == 'application/vnd.ms-powerpoint') {
                        $preview = '<img src="' . URL::base() . 'images/PPIcon.gif">';
                    } else if ($file->mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                        $preview = '<img src="' . URL::base() . 'images/wordicon.gif">';
                    } else if ($file->mime == 'application/msword') {
                        $preview = '<img src="' . URL::base() . 'images/wordicon.gif">';
                    } else if ($file->mime == 'application/x-shockwave-flash') {
                        if (strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
                            $preview = "<p>Shockwave Flash: " . $file->name . " <object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0' width='200' height='300'>" .
                                "<param name='movie' value='" . $file->path . "' />" .
                                "<param name='allowScriptAccess' value='sameDomain' />" .
                                "<param name='quality' value='high' />" .
                                "</object></p>";
                        } else {
                            $preview = "<p>Shockwave Flash: " . $file->name . " <object type='application/x-shockwave-flash' data='" . URL::base() . $file->path . "' width='300' height='200'>" .
                                "<param name='allowScriptAccess' value='sameDomain' />" .
                                "<param name='quality' value='high' />" .
                                "</object></p>";
                        }
                        $isInput = true;
                    } else if ($file->mime == 'application/vnd.ms-excel') {
                        $preview = '<img src="' . URL::base() . 'images/wordicon.gif">';
                    } else if (strstr($file->mime, 'audio')) {
                        $preview = '<audio src="' . URL::base() . $file->path . '" controls preload="auto" autobuffer></audio>';
                        $isInput = true;
                    } else {
                        $preview = '<p>no preview</p>';
                    }
                    ?>
                    <td> <?php echo $preview; ?><a href="<?php echo URL::base() . $file->path; ?>"><?php echo $file->name; ?></a></td>

                    <td>
                        <?php if ($isInput) { ?>
                            <input type="text" size="20" value="[[MR:<?php echo $file->id; ?>]]">
                        <?php } else { ?>
                            <p><textarea cols="30"><a href="<?php echo URL::base() . $file->path; ?>"
                                                      border="0"><?php echo $file->name; ?></a></textarea></p>
                        <?php } ?>
                    </td>
                    <td>
                        <?php echo filesize(DOCROOT . '/' . $file->path) / 1000; ?> KB<br>last
                        modified <?php echo date('d.m.Y H:i:s.', filemtime(DOCROOT . '/' . $file->path)); ?>

                    </td>
                    <td>


                            <a class="btn btn-primary" href="<?php echo URL::base() . 'fileManager/editFile/' . $templateData['map']->id . '/' . $file->id; ?>">edit</a>

                        <a class="btn btn-primary" href="<?php echo URL::base() . 'fileManager/deleteFile/' . $templateData['map']->id . '/' . $file->id; ?>">delete</a>

                        <?php if ($isImage) { ?>

                                <a href="<?php echo URL::base() . 'fileManager/imageEditor/' . $templateData['map']->id . '/' . $file->id; ?>">image
                                    editor</a>
                        <?php } ?>
                    </td>

                </tr>
            <?php } ?>
        <?php } ?>

        </tbody>
    </table>

     <form method="POST" id="upload-form" enctype="multipart/form-data" class="form-horizontal"
          action="<?php echo URL::base() . 'fileManager/uploadFile/' . $templateData['map']->id; ?>">
        <fieldset class="fieldset">
            <legend><?php echo __('Upload a file to Labyrinth'); ?> "<?php echo $templateData['map']->name; ?>"</legend>

            <div class="control-group">
                <label class="control-label"><?php echo __('Select file to upload'); ?></label>
                <div class="controls">
                    <input type="FILE"  name="filename">
                </div>
            </div>

        </fieldset>
         <p>JPEG (.jpg + .jpeg), GIF (.gif), PNG (.png), Acrobat PDF (.pdf), Shockwave Flash (.swf), Microsoft Word, (.doc),
             Microsoft Excel (.xls), Microsoft PowerPoint (.ppt), Rich Text Format (.rtf), Quicktime Video (.mov), MPEG-4
             Video (.mp4), Windows Media (.wmv), Real Stream (.ram), Real Stream (.rpm), Flash video, (.flv), MP3 audio
             (.mp3), WAV audio (.wav), AAC (m4a) audio (.m4a)</p>
         <div class="pull-right">
             <input class="btn btn-large btn-primary" id="opener" type="button" name="Submit" value="<?php echo __('Upload'); ?>">
         </div>

    </form>
    <div id="dialog-confirm" title="<?php echo $templateData['media_copyright']['title']; ?>">
        <div class="dialog-box"><span class="ui-icon ui-icon-alert"
                                      style="float: left; margin: 0 7px 20px 0;"></span><?php echo $templateData['media_copyright']['copyright_message']; ?>
        </div>
    </div>



<?php } ?>