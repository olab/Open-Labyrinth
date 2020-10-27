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
    <div class="page-header"><h1><?php echo __('Global files for Labyrinth "').$templateData['map']->name.'"'; ?></h1></div>
    <div class="alert alert-info"><?php echo 'Labyrinth '.$templateData['map']->id.'\' - '.$templateData['files_count'].' files, '.$templateData['files_size']; ?></div>
    <table class="table table-striped table-bordered">
        <colgroup>
            <col style="width:2%;"/>
            <col style="width:25%"/>
            <col/>
        </colgroup>
        <thead>
        <tr>
            <th>Title &amp; preview</th>
            <th>Details</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody><?php
        if (isset($templateData['files']) and count($templateData['files'])) {
            foreach ($templateData['files'] as $file) { ?>
                <tr>
                    <td>
                        <div class="span-12">
                            <a href="<?php echo URL::base() . 'global/files/'; ?>"><?php echo $file['name']; ?></a>
                        </div><?php
                        $preview = '';
                        $isInput = false;
                        $isImage = false;
                        $isArchive = false;
                        $mime = mime_content_type('global/files/'.$file['name']);
                        if ($mime == 'image/gif') {
                            $preview = '<img style="max-width:600px" src="' . URL::base() . 'global/files/' . $file['name'] . '?' . time() . '" />';
                            $isInput = true;
                            $isImage = true;
                        } else if ($mime == 'image/jpg') {
                            $preview = '<img style="max-width:600px" src="' . URL::base() . 'global/files/' . $file['name'] . '?' . time() . '" />';
                            $isInput = true;
                            $isImage = true;
                        } else if ($mime == 'image/png') {
                            $preview = '<img style="max-width:600px" src="' . URL::base() . 'global/files/' . $file['name'] . '?' . time() . '" />';
                            $isInput = true;
                            $isImage = true;
                        } else if ($mime == 'application/zip') {
                            $preview = '<img src="' . URL::base() . 'images/zipicon.gif">';
                            $isArchive = true;
                        } else if ($mime == 'image/jpeg') {
                            $preview = '<img style="max-width:600px" src="' . URL::base() . 'global/files/' . $file['name'] . '?' . time() . '" />';
                            $isInput = true;
                            $isImage = true;
                        } else if ($mime == 'application/vnd.ms-powerpoint') {
                            $preview = '<img src="' . URL::base() . 'images/PPIcon.gif">';
                        } else if ($mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                            $preview = '<img src="' . URL::base() . 'images/wordicon.gif">';
                        } else if ($mime == 'application/msword') {
                            $preview = '<img src="' . URL::base() . 'images/wordicon.gif">';
                        } else if ($mime == 'application/x-shockwave-flash') {
                            if (strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
                                $preview = "<p>Shockwave Flash: " . $file['name'] . " <object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0' width='200' height='300'>" .
                                    "<param name='movie' value='" .'global/files/' . $file['name'] . "' />" .
                                    "<param name='allowScriptAccess' value='sameDomain' />" .
                                    "<param name='quality' value='high' />" .
                                    "</object></p>";
                            } else {
                                $preview = "<p>Shockwave Flash: " . $file['name'] . " <object type='application/x-shockwave-flash' data='" . URL::base() . 'global/files/' . $file['name'] . "' width='300' height='200'>" .
                                    "<param name='allowScriptAccess' value='sameDomain' />" .
                                    "<param name='quality' value='high' />" .
                                    "</object></p>";
                            }
                            $isInput = true;
                        } else if ($mime == 'application/vnd.ms-excel') {
                            $preview = '<img src="' . URL::base() . 'images/wordicon.gif">';
                        } else if (strstr($mime, 'audio')) {
                            $preview = '<audio src="' . URL::base() . 'global/files/' . $file['name'] . '" controls preload="auto" autobuffer></audio>';
                            $isInput = true;
                        } else {
                            $preview = '<p>no preview</p>';
                        }
                        echo $preview; ?>
                    </td>
                    <td><?php
                        $currentFile = DOCROOT.'/'.'global/files/'.$file['name'];
                        if (file_exists($currentFile)){
                            echo (filesize($currentFile) / 1000).'KB<br/>last modified '.date('d.m.Y H:i:s.', filemtime($currentFile));
                        } else {
                            echo '<i>File not exist.</i>';
                        } ?>
                    </td>
                    <td>
                        <div class="btn-group-vertical"><?php
                            if( ! $file['there']){?>
                                <a class="btn btn-info" href="<?php echo URL::base().'fileManager/addToLabyrinth/'.$templateData['map']->id.'/'.base64_encode($file['name']); ?>">
                                    <i class="icon-edit"></i><?php echo __('Add to labyrinth'); ?>
                                </a><?php
                            } else {
                                echo 'A file with such name already exist in this labyrinth';
                            } ?>
                        </div>
                    </td>
                </tr><?php
            }
        }else{ ?>
            <tr class="info"><td colspan="3">There are no files available for this labyrinth, yet. You may add a file, using the form below.</td></tr><?php
        } ?>
        </tbody>
    </table>
    <script src="//blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script>
    <script src="//blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script><?php
} ?>