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
<div class="page-header">
    <h1><?php echo __('Edit files for Labyrinth "') . $templateData['map']->name . '"'; ?></h1></div>


    <div class="alert alert-info"> Labyrinth '<?php echo $templateData['map']->id; ?>' - <?php echo $templateData['files_count']; ?> files, <?php echo $templateData['files_size']; ?></div>


    <table class="table table-striped table-bordered">
        <colgroup>
            <col style="width:25%"/>
            <col/>
            <col/>
            <col/>
        </colgroup>
        <thead>
        <tr>
            <th>Title &amp; preview</th>
            <th>Embeddable</th>
            <th>Details</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>

        <?php if (isset($templateData['files']) and count($templateData['files']) > 0) { ?>
            <?php foreach ($templateData['files'] as $file) { ?>
                <tr>
                    <td>
                        <div class="span-12">
                           <a href="<?php echo URL::base() . $file->path; ?>"><?php echo $file->name; ?></a>
                        </div>
                         <?php
                    $preview = '';
                    $isInput = false;
                    $isImage = false;

                    if ($file->mime == 'image/gif') {
                        $preview = '<img style="max-width:600px" src="' . URL::base() . $file->path . '?' . time() . '" />';
                        $isInput = true;
                        $isImage = true;
                    } else if ($file->mime == 'image/jpg') {
                        $preview = '<img style="max-width:600px" src="' . URL::base() . $file->path . '?' . time() . '" />';
                        $isInput = true;
                        $isImage = true;
                    } else if ($file->mime == 'image/png') {
                        $preview = '<img style="max-width:600px" src="' . URL::base() . $file->path . '?' . time() . '" />';
                        $isInput = true;
                        $isImage = true;
                    } else if ($file->mime == 'image/jpeg') {
                        $preview = '<img style="max-width:600px" src="' . URL::base() . $file->path . '?' . time() . '" />';
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
                        <?php echo $preview; ?>
                   </td>

                    <td>
                        <?php if ($isInput) { ?>
                         <label> <input class="code" readonly="readonly" type="text"  value="[[MR:<?php echo $file->id; ?>]]"></label>
                        <?php } else { ?>
                        <label>   <textarea readonly="readonly" class="code">&lt;a href="<?php echo URL::base() . $file->path; ?>"&gt;<?php echo $file->name; ?>&lt;/a&gt;</textarea></label>
                        <?php } ?>
                    </td>
                    <td>
                        <?php
                        $currentFile = DOCROOT . '/' . $file->path;

                        if (file_exists($currentFile)){
                            echo (filesize($currentFile) / 1000).'KB<br/>last modified '.date('d.m.Y H:i:s.', filemtime($currentFile));
                        } else {
                            echo '<i>File not exist.</i>';
                        }
                        ?>
                    </td>
                    <td>

                    <div class="btn-group-vertical">
                            <a class="btn btn-info" href="<?php echo URL::base() . 'fileManager/editFile/' . $templateData['map']->id . '/' . $file->id; ?>"><i class="icon-edit"></i> Edit</a>

                        <a class="btn btn-danger" href="<?php echo URL::base() . 'fileManager/deleteFile/' . $templateData['map']->id . '/' . $file->id; ?>"><i class="icon-trash"></i> Delete</a>

                        <?php if ($isImage) { ?>

                                <a class="btn btn-inverse" href="<?php echo URL::base() . 'fileManager/imageEditor/' . $templateData['map']->id . '/' . $file->id; ?>"><i class="icon-picture"></i>Image
                                    editor</a>
                        <?php } ?></div>
                    </td>

                </tr>
            <?php } ?>
        <?php }else{ ?>
        <tr class="info"><td colspan="4">There are no files available for this labyrinth, yet. You may add a file, using the form below.</td></tr>
        <?php } ?>
        </tbody>
    </table>

     <form method="POST" id="upload-form" enctype="multipart/form-data" class="form-horizontal"
          action="<?php echo URL::base() . 'fileManager/uploadFile/' . $templateData['map']->id; ?>">
        <fieldset class="fieldset">
            <legend><?php echo __('Upload a file to Labyrinth'); ?> "<?php echo $templateData['map']->name; ?>"</legend>
<p>
    Once uploaded copy and paste the file tag (looks like [[MR:1234567]]) into a node's content box or info box to
    display or link a file there
</p>
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

        <div class="form-actions">
         <div class="pull-right">
             <input class="btn btn-large btn-primary" id="opener" type="button" name="Submit" value="<?php echo __('Upload'); ?>">
         </div></div>

    </form>
    <div id="dialog-confirm" title="<?php echo $templateData['media_copyright']['title']; ?>">
        <div class="dialog-box"><span class="ui-icon ui-icon-alert"
                                      style="float: left; margin: 0 7px 20px 0;"></span><?php echo $templateData['media_copyright']['copyright_message']; ?>
        </div>
    </div>



<?php } ?>