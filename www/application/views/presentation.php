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

            <h1><?php echo __('Presentations'); ?></h1>

                                    <h3>My presentations</h3>
<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th>Title</th>
        <th>Operations</th>
    </tr>
    </thead>
    <tbody>
    <?php if(isset($templateData['presentations']) and count($templateData['presentations']) > 0) { ?>
        <?php foreach($templateData['presentations'] as $presentation) { ?>

            <tr>

                <td><?php echo $presentation->title; ?> (<?php echo $presentation->id; ?>)</td>
                <td><a class="btn btn-primary" href="<?php echo URL::base(); ?>presentationManager/render/<?php echo $presentation->id; ?>"><?php echo __('preview'); ?>

                    </a>
                    <a class="btn btn-primary" href="<?php echo URL::base(); ?>presentationManager/editPresentation/<?php echo $presentation->id; ?>"><?php echo __('edit'); ?></a></td>
            </tr>
        <?php } ?>
    <?php } ?>

    </tbody>
</table>




                                        <form class="form-horizontal" method="POST" action="<?php echo URL::base(); ?>presentationManager/addPresentation">
                                        <fieldset class="fieldset">
                                        <legend>Add presentation</legend>
                                        <div class="control-group">
                                            <label for="title" class="control-label"><?php echo __('Title'); ?></label>

                                            <div class="controls">
                                                <input type="text" id="title" name="title">
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label for="header" class="control-label"><?php echo __('Header text'); ?></label>

                                            <div class="controls">
                                                <textarea name="header" id="header"></textarea>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label for="footer" class="control-label"><?php echo __('Footer text'); ?></label>

                                            <div class="controls">
                                                <textarea name="footer" id="footer"></textarea>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label">Permit learner access</label>

                                            <div class="controls">
                                                <label class="radio">
                                                    <?php echo __('On'); ?>
                                                    <input type="radio" name="access"
                                                           value="1" >

                                                </label>
                                                <label class="radio">
                                                    <?php echo __('Off'); ?>
                                                    <input type="radio" name="access"
                                                           value="0" >

                                                </label>

                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label">Number of attempts</label>

                                            <div class="controls">
                                                <label class="radio">
                                                    <?php echo __('Unlimited attempts'); ?>
                                                    <input type="radio" name="tries"
                                                           value="0" >

                                                </label>
                                                <label class="radio">
                                                    <?php echo __('Only one attempt'); ?>
                                                    <input type="radio" name="tries"
                                                           value="1" >

                                                </label>


                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label">Start date</label>

                                            <div class="controls">
                                                <select name="startday">
                                                    <option value="">select day ...&nbsp;&nbsp;</option>
                                                    <option value="01">01</option>
                                                    <option value="02">02</option>
                                                    <option value="03">03</option>
                                                    <option value="04">04</option>
                                                    <option value="05">05</option>
                                                    <option value="06">06</option>
                                                    <option value="07">07</option>
                                                    <option value="08">08</option>
                                                    <option value="09">09</option>
                                                    <option value="10">10</option>
                                                    <option value="11">11</option>
                                                    <option value="12">12</option>
                                                    <option value="13">13</option>
                                                    <option value="14">14</option>
                                                    <option value="15">15</option>
                                                    <option value="16">16</option>
                                                    <option value="17">17</option>
                                                    <option value="18">18</option>
                                                    <option value="19">19</option>
                                                    <option value="20">20</option>
                                                    <option value="21">21</option>
                                                    <option value="22">22</option>
                                                    <option value="23">23</option>
                                                    <option value="24">24</option>
                                                    <option value="25">25</option>
                                                    <option value="26">26</option>
                                                    <option value="27">27</option>
                                                    <option value="28">28</option>
                                                    <option value="29">29</option>
                                                    <option value="30">30</option>
                                                    <option value="31">31</option>
                                                </select>
                                                <select name="startmonth">
                                                    <option value="">select month ...</option>
                                                    <option value="01">JAN</option>
                                                    <option value="02">FEB</option>
                                                    <option value="03">MAR</option>
                                                    <option value="04">APR</option>
                                                    <option value="05">MAY</option>
                                                    <option value="06">JUN</option>
                                                    <option value="07">JUL</option>
                                                    <option value="08">AUG</option>
                                                    <option value="09">SEP</option>
                                                    <option value="10">OCT</option>
                                                    <option value="11">NOV</option>
                                                    <option value="12">DEC</option>
                                                </select>
                                                <select name="startyear">
                                                    <option value="">select year ...&nbsp;</option>
                                                    <option value="2012">2012</option>
                                                    <option value="2013">2013</option>
                                                    <option value="2014">2014</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label">end date</label>

                                            <div class="controls">
                                                <select name="endday">
                                                    <option value="">select day ...&nbsp;&nbsp;</option>
                                                    <option value="01">01</option>
                                                    <option value="02">02</option>
                                                    <option value="03">03</option>
                                                    <option value="04">04</option>
                                                    <option value="05">05</option>
                                                    <option value="06">06</option>
                                                    <option value="07">07</option>
                                                    <option value="08">08</option>
                                                    <option value="09">09</option>
                                                    <option value="10">10</option>
                                                    <option value="11">11</option>
                                                    <option value="12">12</option>
                                                    <option value="13">13</option>
                                                    <option value="14">14</option>
                                                    <option value="15">15</option>
                                                    <option value="16">16</option>
                                                    <option value="17">17</option>
                                                    <option value="18">18</option>
                                                    <option value="19">19</option>
                                                    <option value="20">20</option>
                                                    <option value="21">21</option>
                                                    <option value="22">22</option>
                                                    <option value="23">23</option>
                                                    <option value="24">24</option>
                                                    <option value="25">25</option>
                                                    <option value="26">26</option>
                                                    <option value="27">27</option>
                                                    <option value="28">28</option>
                                                    <option value="29">29</option>
                                                    <option value="30">30</option>
                                                    <option value="31">31</option>
                                                </select>
                                                <select name="endmonth">
                                                    <option value="">select month ...</option>
                                                    <option value="01">JAN</option>
                                                    <option value="02">FEB</option>
                                                    <option value="03">MAR</option>
                                                    <option value="04">APR</option>
                                                    <option value="05">MAY</option>
                                                    <option value="06">JUN</option>
                                                    <option value="07">JUL</option>
                                                    <option value="08">AUG</option>
                                                    <option value="09">SEP</option>
                                                    <option value="10">OCT</option>
                                                    <option value="11">NOV</option>
                                                    <option value="12">DEC</option>
                                                </select>
                                                <select name="endyear">
                                                    <option value="">select year ...&nbsp;</option>
                                                    <option value="2012">2012</option>
                                                    <option value="2013">2013</option>
                                                    <option value="2014">2014</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label for="skin" class="control-label">skin</label>

                                            <div class="controls">
                                                <select name="skin" id="skin">
                                                    <option value="">select ...</option>
                                                    <option value="">Basic</option>
                                                    <option value="" selected="">Basic Exam</option>
                                                    <option value="">NOSM</option>
                                                    <option value="">PINE</option>
                                                </select>
                                            </div>
                                        </div>

                                        </fieldset>

                                        <input class="btn btn-primary" type="submit" name="Submit" value="<?php echo __('create'); ?>">

                                        </form>
