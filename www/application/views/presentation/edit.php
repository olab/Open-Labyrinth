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
if (isset($templateData['presentation'])) { ?>

                <h1><?php echo __('edit presentation "') . $templateData['presentation']->title . '"'; ?></h1>


    <a class="btn btn-primary" href="<?php echo URL::base(); ?>presentationManager/render/<?php echo $templateData['presentation']->id; ?>"><?php echo __('preview'); ?></a>
    <a class="btn btn-warning" href="#"><?php echo __('reset'); ?></a>
    <a class="btn btn-primary" href="#"><?php echo __('report'); ?></a>
    <a class="btn btn-danger" href="<?php echo URL::base(); ?>presentationManager/deletePresentation/<?php echo $templateData['presentation']->id; ?>"><?php echo __('delete'); ?></a>

                                        <h3><?php echo __('presentation Labyrinths'); ?></h3>
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <td>Labyrinth</td>
                                                <td>Security</td>
                                                <td>Order</td>
                                                <td>Operations</td>
                                            </tr>
                                            </thead>
                                            <tbody>


                                            <?php if(count($templateData['presentation']->maps) > 0) { ?>
                                            <?php foreach($templateData['presentation']->maps as $mp) { ?>
                                            <tr>
                                                <td><?php echo $mp->map->name; ?></td>
                                                <td><?php echo $mp->map->security->name; ?></td>

                                                <td>
                                                    <select name="ord_18">
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                        <option value="6">6</option>
                                                        <option value="7">7</option>
                                                        <option value="8">8</option>
                                                        <option value="9">9</option>
                                                        <option value="10">10</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <a class="btn btn-primary" href="<?php echo URL::base(); ?>labyrinthManager/editMap/<?php echo $mp->map->id; ?>"><?php echo __('edit'); ?></a>
                                                    <a class="btn btn-primary" href="#"><?php echo __('report'); ?></a>
                                                    <a class="btn btn-danger" href="<?php echo URL::base(); ?>presentationManager/deleteMap/<?php echo $templateData['presentation']->id; ?>/<?php echo $mp->id; ?>">remove</a></td>
                                            <?php } ?>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                        <p>note that Labyrinths set as 'private' need to be changed to 'closed','key' or 'open' access to be used in a presentation.</p>
                                        <a class="btn btn-warning" href="<?php echo URL::base(); ?>presentationManager/resetSecurity/<?php echo $templateData['presentation']->id; ?>/4">reset all to 'key' now</a>
    <a class="btn btn-warning" href="<?php echo URL::base(); ?>presentationManager/resetSecurity/<?php echo $templateData['presentation']->id; ?>/2">reset all to 'closed' now</a>
    <a class="btn btn-warning" href="<?php echo URL::base(); ?>presentationManager/resetSecurity/<?php echo $templateData['presentation']->id; ?>/1">reset all to 'open' now</a>


                                        <form class="form-horizontal" action="<?php echo URL::base(); ?>presentationManager/addMap/<?php echo $templateData['presentation']->id; ?>" method="post">
                                            <fieldset class="fieldset">
                                                <legend></legend>
                                                <div class="control-group">
                                                    <label class="control-label"></label>
                                                    <div class="controls">

                                                    </div>
                                                </div>
                                            </fieldset>
                                            <select name="labid">
                                                <?php if(isset($templateData['maps']) and count($templateData['maps']) > 0) { ?>
                                                <?php foreach($templateData['maps'] as $map) { ?>
                                                    <option value="<?php echo $map->id; ?>"><?php echo $map->name; ?> - <?php echo $map->id; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                            <input type="submit" name="Submit" value="<?php echo __('update'); ?>">
                                        </form>
                                        <hr>

                                        <p><strong><?php echo __('Users'); ?> (<?php echo count($templateData['presentation']->users) + 1; ?>)</strong></p>
                                        <p><?php echo $templateData['presentation']->author->nickname; ?> (<?php echo $templateData['presentation']->author->nickname; ?> - author)</p>
                                        <?php if(count($templateData['presentation']->users) > 0) { ?>
                                        <?php foreach($templateData['presentation']->users as $user) { ?>
                                        <p> (<?php echo $user->user->nickname; ?> - <?php echo $user->user->type->name; ?>)&nbsp;[<a href="<?php echo URL::base(); ?>presentationManager/deleteUser/<?php echo $templateData['presentation']->id; ?>/<?php echo $user->id; ?>"><?php echo __('delete'); ?></a>]</p>
                                        <?php } ?>
                                        <?php } ?>
                                        <form method="POST" action="<?php echo URL::base(); ?>presentationManager/addUser/<?php echo $templateData['presentation']->id; ?>">
                                            <p><?php echo __('add users'); ?></p>
                                            <select name="presUserID">
                                                <option value=""><?php echo __('select'); ?> ...</option>
                                                <?php if(isset($templateData['notUsers']) and count($templateData['notUsers']) > 0) { ?>
                                                <?php foreach($templateData['notUsers'] as $user) { ?>
                                                <option value="u:<?php echo $user->id; ?>"><?php echo $user->nickname; ?> - <?php echo $user->type->name; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                                
                                                <?php if(isset($templateData['groups']) and count($templateData['groups']) > 0) { ?>
                                                <?php foreach($templateData['groups'] as $group) { ?>
                                                <option value="g:<?php echo $group->id; ?>">all users from group: '<?php echo $group->name; ?>'</option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                            <select name="presUserType"><option value="">select ...</option><option value="author">author</option><option value="learner">learner</option></select>
                                            <input type="submit" name="Submit" value="<?php echo __('submit'); ?>">
                                        </form>
                                        <hr>

                                        <p><strong>edit presentation</strong></p>
                                        <table cellpadding="2">
                                            <form method="POST" action="<?php echo URL::base(); ?>presentationManager/updatePresentation/<?php echo $templateData['presentation']->id; ?>">
                                            <tr><td><p><?php echo __('title'); ?></p></td><td><input type="text" name="title" value="<?php echo $templateData['presentation']->title; ?>"></td></tr>
                                            <tr><td><p><?php echo __('header text'); ?></p></td><td><textarea name="header" cols="40" rows="3"><?php echo $templateData['presentation']->header; ?></textarea></td></tr>
                                            <tr><td><p><?php echo __('footer text'); ?></p></td><td><textarea name="footer" cols="40" rows="3"><?php echo $templateData['presentation']->footer; ?></textarea></td></tr>
                                            <tr><td><p>permit learner access</p></td><td><p>on <input type="radio" name="access" value="1" <?php if($templateData['presentation']->access == 1) echo 'checked=""'; ?>> : <input type="radio" name="access" value="0" <?php if($templateData['presentation']->access == 0) echo 'checked=""'; ?>> off</p></td></tr>
                                            <tr><td><p>number of attempts</p></td><td><p><input type="radio" name="tries" value="0" <?php if($templateData['presentation']->tries == 0) echo 'checked=""'; ?>> unlimited attempts - <input type="radio" name="tries" value="1" <?php if($templateData['presentation']->tries == 1) echo 'checked=""'; ?>> only one attempt</p></td></tr>

                                            <tr><td><p>start date</p></td><td>
                                                    <select name="startday"><option value="">select day ...&nbsp;&nbsp;</option><option value="">select day ...&nbsp;&nbsp;</option><option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option></select>
                                                    <select name="startmonth"><option value="">select month ...</option><option value="01">JAN</option><option value="02">FEB</option><option value="03">MAR</option><option value="04">APR</option><option value="05">MAY</option><option value="06">JUN</option><option value="07">JUL</option><option value="08">AUG</option><option value="09">SEP</option><option value="10">OCT</option><option value="11">NOV</option><option value="12">DEC</option></select>
                                                    <select name="startyear"><option value="">select year ...&nbsp;</option><option value="2012">2012</option><option value="2013">2013</option><option value="2014">2014</option></select>
                                                </td></tr>

                                            <tr><td><p>end date</p></td><td>
                                                    <select name="endday"><option value="">select day ...&nbsp;&nbsp;</option><option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option></select>
                                                    <select name="endmonth"><option value="">select month ...</option><option value="01">JAN</option><option value="02">FEB</option><option value="03">MAR</option><option value="04">APR</option><option value="05">MAY</option><option value="06">JUN</option><option value="07">JUL</option><option value="08">AUG</option><option value="09">SEP</option><option value="10">OCT</option><option value="11">NOV</option><option value="12">DEC</option></select>
                                                    <select name="endyear"><option value="">select year ...&nbsp;</option><option value="2012">2012</option><option value="2013">2013</option><option value="2014">2014</option></select><
                                                </td></tr>

                                            <tr><td><p>skin</p></td><td><p><select name="skin"><option value="">select ...</option><option value="">Basic</option><option value="" selected="">Basic Exam</option><option value="">NOSM</option><option value="">PINE</option></select></p></td></tr>

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
<?php } ?>

