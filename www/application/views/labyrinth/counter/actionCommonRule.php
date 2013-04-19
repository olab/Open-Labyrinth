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
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/rules.js"></script>

<?php if (isset($templateData['commonRule'])){ ?>
    <div class="page-header">    <h1><?php echo __('Edit Rule'); ?></h1></div>
    <form class="form-horizontal" id="form1" name="form1" method="post" action="<?php echo URL::base().'counterManager/saveCommonRule/'.$templateData['map']->id.'/'.$templateData['commonRule']->id; ?>">
<?php } else { ?>
    <h1><?php echo __('Add Rule'); ?></h1>
    <form class="form-horizontal" id="form1" name="form1" method="post" action="<?php echo URL::base().'counterManager/createCommonRule/'.$templateData['map']->id; ?>">
<?php } ?>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-text"><?php echo __('Text of rule'); ?></a></li>
            <li><a href="#tabs-code"><?php echo __('Code of rule'); ?></a></li>
        </ul>
        <div id="tabs-text">
            <textarea class="not-autocomplete" id="text" style="width:100%; height:200px;"></textarea>
        </div>

        <div id="tabs-code">
            <textarea name="commonRule" class="not-autocomplete" id="code" style="width:100%; height:200px;"><?php
                if (isset($templateData['commonRule'])){
                    echo $templateData['commonRule']->rule;
                }
                ?></textarea>
        </div>

        <div id="processed-rule">
        </div>
    </div>
    <a id="availableNodesText" style="display:none;"><?php echo $templateData['nodes']['text']; ?></a>
    <a id="availableNodesId" style="display:none;"><?php echo $templateData['nodes']['id']; ?></a>
    <a id="availableCountersText" style="display:none;"><?php echo $templateData['counters']['text']; ?></a>
    <a id="availableCountersId" style="display:none;"><?php echo $templateData['counters']['id']; ?></a>
    <div class="pull-right" style="margin-top:10px;">
        <input type="submit" class="btn btn-primary btn-large" name="Submit" value="<?php echo __('Submit'); ?>">
    </div>
</form>
<?php } ?>