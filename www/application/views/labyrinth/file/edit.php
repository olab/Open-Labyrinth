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
if (isset($templateData['map']) and isset($templateData['file'])) {
    $preview = '';
    $isInput = false;

    if ($templateData['file']->mime == 'image/gif') {
        $preview = '<img src="' . URL::base() . $templateData['file']->path . '">';
        $isInput = true;
    } else if ($templateData['file']->mime == 'image/jpg') {
        $preview = '<img src="' . URL::base() . $templateData['file']->path . '">';
        $isInput = true;
    } else if ($templateData['file']->mime == 'image/png') {
        $preview = '<img src="' . URL::base() . $templateData['file']->path . '">';
        $isInput = true;
    } else if ($templateData['file']->mime == 'image/jpeg') {
        $preview = '<img src="' . URL::base() . $templateData['file']->path . '">';
        $isInput = true;
    } else if ($templateData['file']->mime == 'application/vnd.ms-powerpoint') {
        $preview = '<img src="' . URL::base() . 'images/PPIcon.gif">';
    } else if ($templateData['file']->mime == 'application/msword') {
        $preview = '<img src="' . URL::base() . 'images/wordicon.gif">';
    } else if ($templateData['file']->mime == 'application/x-shockwave-flash') {
        if (strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
            $preview = "<p>Shockwave Flash: " . $templateData['file']->name . " <object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0' width='200' height='300'>" .
                    "<param name='movie' value='" . URL::base().$templateData['file']->path . "' />" .
                    "<param name='allowScriptAccess' value='sameDomain' />" .
                    "<param name='quality' value='high' />" .
                    "</object></p>";
        } else {
            $preview = "<p>Shockwave Flash: " . $templateData['file']->name . " <object type='application/x-shockwave-flash' data='" . URL::base() . $templateData['file']->path . "' width='300' height='200'>" .
                    "<param name='allowScriptAccess' value='sameDomain' />" .
                    "<param name='quality' value='high' />" .
                    "</object></p>";
        }
    }
    ?>
    <table width="100%" height="100%" cellpadding="6">
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('edit file: "') . $templateData['file']->name . '"'; ?></h4>
                <form method="POST" action="<?php echo URL::base() . 'fileManager/updateFile/' . $templateData['map']->id . '/' . $templateData['file']->id; ?>">
                    <table width="100%" border="0" cellspacing="6" bgcolor="#ffffff">
                        <tr align="left"><td><p><?php echo __('preview'); ?></p></td><td><?php echo $preview; ?></td></tr>
                        <tr align="left"><td><p><?php echo __('chemin'); ?>path</p></td><td><p><?php echo URL::base().$templateData['file']->path; ?></p></td></tr>
                        <tr align="left"><td><p>ID</p></td><td><p><?php echo $templateData['file']->id; ?></p></td></tr>
                        <tr align="left"><td><p>MIME</p></td><td>
                                <select name="mrelmime" size="1">
                                    <option value="not_selected">select</option>
                                    <option value="video/x-msvideo" <?php if ($templateData['file']->mime == 'video/x-msvideo') echo 'selected=""'; ?>>avi:video/x-msvideo</option>
                                    <option value="application/vnd.openxmlformats-officedocument.wordprocessingml.document" <?php if ($templateData['file']->mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') echo 'selected=""'; ?>>doc:application/vnd.openxmlformats-officedocument.wordprocessingml.document</option>
                                    <option value="application/msword" <?php if ($templateData['file']->mime == 'application/msword') echo 'selected=""'; ?>>doc:application/msword</option>
                                    <option value="application/msword" <?php if ($templateData['file']->mime == 'application/msword') echo 'selected=""'; ?>>docx:application/msword</option>
                                    <option value="application/x-director" <?php if ($templateData['file']->mime == 'application/x-director') echo 'selected=""'; ?>>dcr:application/x-director</option>
                                    <option value="application/x-director" <?php if ($templateData['file']->mime == 'application/x-director') echo 'selected=""'; ?>>dxr:application/x-director</option>
                                    <option value="image/gif" <?php if ($templateData['file']->mime == 'image/gif') echo 'selected=""'; ?>>gif:image/gif</option>
                                    <option value="text/html" <?php if ($templateData['file']->mime == 'text/html') echo 'selected=""'; ?>>htm:text/html</option>
                                    <option value="text/html" <?php if ($templateData['file']->mime == 'text/html') echo 'selected=""'; ?>>html:text/html</option>
                                    <option value="image/jpeg" <?php if ($templateData['file']->mime == 'image/jpeg') echo 'selected=""'; ?>>jpeg:image/jpeg</option>
                                    <option value="image/jpeg" <?php if ($templateData['file']->mime == 'image/jpeg') echo 'selected=""'; ?>>jpg:image/jpeg</option>
                                    <option value="application/x-msaccess" <?php if ($templateData['file']->mime == 'application/x-msaccess') echo 'selected=""'; ?>>mdb:application/x-msaccess</option>
                                    <option value="video/quicktime" <?php if ($templateData['file']->mime == 'video/quicktime') echo 'selected=""'; ?>>mov:video/quicktime</option>
                                    <option value="video/x-sgi-movie" <?php if ($templateData['file']->mime == 'video/x-sgi-movie') echo 'selected=""'; ?>>movie:video/x-sgi-movie</option>
                                    <option value="video/mpeg" <?php if ($templateData['file']->mime == 'video/mpeg') echo 'selected=""'; ?>>mp2:video/mpeg</option>
                                    <option value="audio/mpeg" <?php if ($templateData['file']->mime == 'audio/mpeg') echo 'selected=""'; ?>>mp3:audio/mpeg</option>
                                    <option value="video/mpeg" <?php if ($templateData['file']->mime == 'video/mpeg') echo 'selected=""'; ?>>mp4:video/mpeg</option>
                                    <option value="video/mpeg" <?php if ($templateData['file']->mime == 'video/mpeg') echo 'selected=""'; ?>>mpeg:video/mpeg</option>
                                    <option value="video/mpeg" <?php if ($templateData['file']->mime == 'video/mpeg') echo 'selected=""'; ?>>mpg:video/mpeg</option>
                                    <option value="application/pdf" <?php if ($templateData['file']->mime == 'application/pdf') echo 'selected=""'; ?>>pdf:application/pdf</option>
                                    <option value="application/vnd.ms-powerpoint" <?php if ($templateData['file']->mime == 'application/vnd.ms-powerpoint') echo 'selected=""'; ?>>ppt:application/vnd.ms-powerpoint</option>
                                    <option value="application/vnd.ms-powerpoint" <?php if ($templateData['file']->mime == 'application/vnd.ms-powerpoint') echo 'selected=""'; ?>>pptx:application/vnd.ms-powerpoint</option>
                                    <option value="video/quicktime" <?php if ($templateData['file']->mime == 'video/quicktime') echo 'selected=""'; ?>>qt:video/quicktime</option>
                                    <option value="audio/x-pn-realaudio" <?php if ($templateData['file']->mime == 'audio/x-pn-realaudio') echo 'selected=""'; ?>>ra:audio/x-pn-realaudio</option>
                                    <option value="audio/x-pn-realaudio" <?php if ($templateData['file']->mime == 'audio/x-pn-realaudio') echo 'selected=""'; ?>>ram:audio/x-pn-realaudio</option>
                                    <option value="application/rtf" <?php if ($templateData['file']->mime == 'application/rtf') echo 'selected=""'; ?>>rtf:application/rtf</option>
                                    <option value="application/x-shockwave-flash" <?php if ($templateData['file']->mime == 'application/x-shockwave-flash') echo 'selected=""'; ?>>swf:application/x-shockwave-flash</option>
                                    <option value="text/plain" <?php if ($templateData['file']->mime == 'text/plain') echo 'selected=""'; ?>>txt:text/plain</option>
                                    <option value="audio/x-wav" <?php if ($templateData['file']->mime == 'audio/x-wav') echo 'selected=""'; ?>>wav:audio/x-wav</option>
                                    <option value="application/vnd.ms-excel" <?php if ($templateData['file']->mime == 'application/vnd.ms-excel') echo 'selected=""'; ?>>xls:application/vnd.ms-excel</option>
                                    <option value="application/vnd.ms-excel" <?php if ($templateData['file']->mime == 'application/vnd.ms-excel') echo 'selected=""'; ?>>xlsx:application/vnd.ms-excel</option>
                                    <option value="application/zip" <?php if ($templateData['file']->mime == 'application/zip') echo 'selected=""'; ?>>zip:application/zip</option></select></td></tr>
                        <tr align="left"><td><p><?php echo __('title'); ?></p></td><td><input type="text" name="mrelname" size="40" value="<?php echo $templateData['file']->name; ?>"></td></tr>
                        <tr align="left"><td><p><?php echo __('properties'); ?></p></td><td>
                                <table>
                                    <tr>
                                        <td><p><?php echo __('height'); ?>: <input type="text" name="h" size="4" value="<?php if ($templateData['file']->height > 0) echo $templateData['file']->height; ?>">
                                                <select name="hv">
                                                    <option <?php if ($templateData['file']->height_type == 'px') echo 'selected=""'; ?> value="px">pixels</option>
                                                    <option <?php if ($templateData['file']->height_type == '%') echo 'selected=""'; ?> value="%">percent</option></select></p></td>
                                        <td><p><?php echo __('width'); ?>: <input type="text" name="w" size="4" value="<?php if ($templateData['file']->width > 0) echo $templateData['file']->width; ?>">
                                                <select name="wv">
                                                    <option <?php if ($templateData['file']->width_type == 'px') echo 'selected=""'; ?> value="px">pixels</option>
                                                    <option value="%" <?php if ($templateData['file']->width_type == '%') echo 'selected=""'; ?>>percent</option>
                                                </select></p></td>
                                    </tr>
                                    <tr>
                                        <td><p><?php echo __('horizontal align'); ?>: <select name="a">
                                                    <option <?php if ($templateData['file']->h_align == '') echo 'selected=""'; ?> value="">none</option>
                                                    <option <?php if ($templateData['file']->h_align == 'left') echo 'selected=""'; ?> value="left">left</option>
                                                    <option <?php if ($templateData['file']->h_align == 'middle') echo 'selected=""'; ?> value="middle">middle</option>
                                                    <option <?php if ($templateData['file']->h_align == 'right') echo 'selected=""'; ?> value="right">right</option>
                                                </select></p></td>
                                        <td><p><?php echo __('vertical align'); ?>: <select name="v">
                                                    <option <?php if ($templateData['file']->v_align == '') echo 'selected=""'; ?> value="">none</option>
                                                    <option <?php if ($templateData['file']->v_align == 'top') echo 'selected=""'; ?> value="top">top</option>
                                                    <option <?php if ($templateData['file']->v_align == 'middle') echo 'selected=""'; ?> value="middle">middle</option>
                                                    <option <?php if ($templateData['file']->v_align == 'bottom') echo 'selected=""'; ?> value="bottom">bottom</option>
                                                </select></p></td>
                                    </tr>
                                </table>
                            </td></tr>
                        <tr align="left"><td><p>&nbsp;</p></td><td>
                                <input type="submit" name="Submit" value="<?php echo __('submit'); ?>">
                            </td></tr>
                    </table>
                </form>  
            </td>
        </tr>
    </table>
<?php } ?>