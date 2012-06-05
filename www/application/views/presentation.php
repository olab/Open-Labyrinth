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
?>
<table width="100%" height="100%" cellpadding='6'>
    <tr>
        <td valign="top" bgcolor="#bbbbcb">
            <h4><?php echo __('Presentations'); ?></h4>
            <table width="100%" cellpadding="6">
                <tr bgcolor="#ffffff"><td align="left">
                        <table border="0" width="100%" cellpadding="1">
                            <tr><td valign="top">
                                    <h4>my presentations</h4>
                                    <?php if(isset($templateData['presentations']) and count($templateData['presentations']) > 0) { ?>
                                    <?php foreach($templateData['presentations'] as $presentation) { ?>
                                        <p><?php echo $presentation->title; ?> (<?php echo $presentation->id; ?>) [<a href="<?php echo URL::base(); ?>presentationManager/render/<?php echo $presentation->id; ?>"><?php echo __('preview'); ?></a>] [<a href="<?php echo URL::base(); ?>presentationManager/editPresentation/<?php echo $presentation->id; ?>"><?php echo __('edit'); ?></a>]</p>
                                    <?php } ?>
                                    <?php } ?>
                                    <hr>
                                    <p><strong>add presentation</strong></p>
                                    <table cellpadding="2">
                                        <form method="POST" action="<?php echo URL::base(); ?>presentationManager/addPresentation">
                                        <tr><td><p><?php echo __('title'); ?></p></td><td><input type="text" name="title" value=""></td></tr>
                                        <tr><td><p><?php echo __('header text'); ?></p></td><td><textarea name="header" cols="40" rows="3"></textarea></td></tr>
                                        <tr><td><p><?php echo __('footer text'); ?></p></td><td><textarea name="footer" cols="40" rows="3"></textarea></td></tr>
                                        <tr><td><p><?php echo __('permit learner access'); ?></p></td><td><p>on <input type="radio" name="access" value="1"> : <input type="radio" name="access" value="0"> off</p></td></tr>
                                        <tr><td><p><?php echo __('number of attempts'); ?></p></td><td><p><input type="radio" name="tries" value="0"> unlimited attempts - <input type="radio" name="tries" value="1"> only one attempt</p></td></tr>

                                        <tr><td><p>start date</p></td><td>
                                                <select name="startday"><option value="01">select day ...&nbsp;&nbsp;</option><option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option></select>
                                                <select name="startmonth"><option value="01">select month ...</option><option value="01">JAN</option><option value="02">FEB</option><option value="03">MAR</option><option value="04">APR</option><option value="05">MAY</option><option value="06">JUN</option><option value="07">JUL</option><option value="08">AUG</option><option value="09">SEP</option><option value="10">OCT</option><option value="11">NOV</option><option value="12">DEC</option></select>
                                                <select name="startyear"><option value="1990">select year ...&nbsp;</option><option value="2012">2012</option><option value="2013">2013</option><option value="2014">2014</option></select>
                                            </td></tr>

                                        <tr><td><p>end date</p></td><td>
                                                <select name="endday"><option value="01">select day ...&nbsp;&nbsp;</option><option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option></select>
                                                <select name="endmonth"><option value="01">select month ...</option><option value="01">JAN</option><option value="02">FEB</option><option value="03">MAR</option><option value="04">APR</option><option value="05">MAY</option><option value="06">JUN</option><option value="07">JUL</option><option value="08">AUG</option><option value="09">SEP</option><option value="10">OCT</option><option value="11">NOV</option><option value="12">DEC</option></select>
                                                <select name="endyear"><option value="1990">select year ...&nbsp;</option><option value="2012">2012</option><option value="2013">2013</option><option value="2014">2014</option></select>
                                            </td></tr>

                                        <tr><td><p>skin</p></td><td><p><select name="skin"><option value="" selected="0">select ...</option><option value="">Basic</option><option value="">Basic Exam</option><option value="">NOSM</option><option value="">PINE</option></select></p></td></tr>

                                        <tr><td><p>&nbsp;</p></td><td><p><input type="submit" name="Submit" value="<?php echo __('update'); ?>"></p></td></tr>
                                        </form>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <p><a href="<?php echo URL::base(); ?>presentationManager"><?php echo __('new - list'); ?></a></p>
                    </td></tr>
            </table>
        </td>
    </tr>
</table>
