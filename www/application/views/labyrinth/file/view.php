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
<script>
    var dataURL = '<?php echo URL::base(); ?>scripts/fileupload/php/';
    var replaceAction = '<?php echo URL::base(); ?>fileManager/replaceFiles';
    var displayMapId = <?php echo $templateData['map']->id; ?>;
    var fileManagerUrl = '<?php echo URL::base(); ?>fileManager/<?php echo $templateData['map']->id; ?>';
</script>

<link rel="stylesheet" type="text/css" href="<?php echo URL::base(); ?>css/jquery.fileupload-ui.css" />

<div class="page-header">
    <h1><?php echo __('Edit files for Labyrinth "') . $templateData['map']->name . '"'; ?></h1></div>
    <div class="alert alert-info"> Labyrinth '<?php echo $templateData['map']->id; ?>' - <?php echo $templateData['files_count']; ?> files, <?php echo $templateData['files_size']; ?></div>
    <div class="container" style="position: relative;">
        <p>Once uploaded copy and paste the file tag (looks like [[MR:1234567]]) into a node's content box or info box to display or link a file there</p>

        <form id="fileupload" action="" method="POST" enctype="multipart/form-data">
            <div class="fileupload-buttonbar">
                <span class="btn btn-success fileinput-button">
                    <i class="icon-plus icon-white"></i>
                    <span>Add files...</span>
                    <input type="file" name="files[]" multiple>
                </span>
                <button id="uploadBtn" type="submit" class="btn btn-primary start" style="display: none"></button>
                <a data-toggle="modal" href="#upload-by-url" class="btn btn-primary" >Add file by URL</a>
                <button id="opener" type="button" name="Submit" class="btn btn-primary">
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start upload</span>
                </button>
                <span class="fileupload-loading"></span>
            </div>
            <p>JPEG (.jpg + .jpeg), GIF (.gif), PNG (.png), Acrobat PDF (.pdf), Shockwave Flash (.swf), Microsoft Word, (.doc), Microsoft Excel (.xls), Microsoft PowerPoint (.ppt), Rich Text Format (.rtf), Quicktime Video (.mov), MPEG-4 Video (.mp4), Windows Media (.wmv), Real Stream (.ram), Real Stream (.rpm), Flash video, (.flv), MP3 audio(.mp3), WAV audio (.wav), AAC (m4a) audio (.m4a)</p>
            <div class="col-lg-5 fileupload-progress fade" style="margin-top: 22px;">
                <div class="progress progress-striped active" id="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="margin-bottom: 0px;">
                    <div class="progress-bar progress-bar-success bar" style="width:0%;"></div>
                </div>
                <div class="progress-extended">&nbsp;</div>
            </div>
            <table role="presentation" id="filesTable" class="table table-striped"><tbody class="files"></tbody></table>
        </form>
        <form action="<?php echo URL::base().'fileManager/saveByUrl/'.$templateData['map']->id ?>" method="POST">
            <div id="upload-by-url" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3>Add file by URL</h3>
                </div>
                <div class="modal-body">
                    Add URL of file below:<br>
                    <input type="url" name="url" value="" />
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary add-from-url" value="Add" />
                </div>
            </div>
        </form>
    </div>

    <div id="dialog-confirm" title="<?php echo $templateData['media_copyright']['title']; ?>">
        <div class="dialog-box"><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span><?php echo $templateData['media_copyright']['copyright_message']; ?></div>
    </div>

    <script id="template-upload" type="text/x-tmpl">
        {% for (var i=0, file; file=o.files[i]; i++) { %}
        <tr class="template-upload fade">
            <td>
                <span class="preview"></span>
            </td>
            <td>
                <p class="name">{%=file.name%}</p>
                {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                {% } %}
            </td>
            <td>
                <p class="size">{%=o.formatFileSize(file.size)%}</p>
                {% if (!o.files.error) { %}
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success bar" style="width:0%;"></div></div>
                {% } %}
            </td>
            <td>
                {% if (!o.files.error && !i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start hide">
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start</span>
                </button>
                {% } %}
                {% if (!i) { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
                {% } %}
            </td>
        </tr>
        {% } %}
    </script>

    <script id="template-download" type="text/x-tmpl">
        {% for (var i=0, file; file=o.files[i]; i++) { %}
        <tr class="template-download fade">
            <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
            </td>
            <td>
                <p class="name">
                    {% if (file.url) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                    {% } else { %}
                    <span>{%=file.name%}</span>
                    {% } %}
                </p>
                {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                {% } %}
            </td>
            <td>
                <span class="size">{%=o.formatFileSize(file.size)%}</span>
            </td>
            <td>
                {% if (file.deleteUrl) { %}
                <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                <i class="glyphicon glyphicon-trash"></i>
                <span>Delete</span>
                </button>
                <input type="checkbox" name="delete" value="1" class="toggle">
                {% } else { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
                {% } %}
            </td>
        </tr>
        {% } %}
    </script>

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
                        <label> <input class="code" readonly="readonly" type="text"  value="[[MR:<?php echo $file->id; ?>]]"></label>
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
    <input type="hidden" id="redirect_url" name="redirect_url" value="<?php echo URL::base().'fileManager/index/'.$templateData['map']->id; ?>" />

    <script src="<?php echo URL::base(); ?>scripts/fileupload/js/vendor/jquery.ui.widget.js"></script>
    <script src="http://blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script>
    <script src="http://blueimp.github.io/JavaScript-Load-Image/js/load-image.min.js"></script>

    <script src="<?php echo URL::base(); ?>scripts/fileupload/js/jquery.iframe-transport.js"></script>
    <script src="<?php echo URL::base(); ?>scripts/fileupload/js/newfile/jquery.fileupload.js"></script>
    <script src="<?php echo URL::base(); ?>scripts/fileupload/js/jquery.fileupload-process.js"></script>
    <script src="<?php echo URL::base(); ?>scripts/fileupload/js/jquery.fileupload-image.js"></script>
    <script src="<?php echo URL::base(); ?>scripts/fileupload/js/jquery.fileupload-audio.js"></script>
    <script src="<?php echo URL::base(); ?>scripts/fileupload/js/jquery.fileupload-video.js"></script>
    <script src="<?php echo URL::base(); ?>scripts/fileupload/js/jquery.fileupload-validate.js"></script>
    <script src="<?php echo URL::base(); ?>scripts/fileupload/js/newfile/jquery.fileupload-ui.js"></script>
    <script src="<?php echo URL::base(); ?>scripts/filemanager.js"></script>
<?php } ?>