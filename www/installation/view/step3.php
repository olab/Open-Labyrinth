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
<form class="hide" id="previousStep" method="post" action="<?php echo URL::base(); ?>installation/index.php">
    <input type="hidden" name="token" value="<?php echo $templateData['token']; ?>" />
    <input type="hidden" name="previousStep" value="1" />
</form>
<form class="form-validate form-horizontal" id="adminForm" method="post" action="<?php echo URL::base(); ?>installation/index.php">
    <input type="hidden" name="token" value="<?php echo $templateData['token']; ?>" />
    <div class="btn-toolbar">
        <div class="btn-group pull-right">
            <a title="Previous" id="previous-step" rel="prev" href="javascript:void(0);" class="btn"><i class="icon-arrow-left"></i> Previous</a>
            <a title="Next" id="next-step" rel="next" href="javascript:void(0);" class="btn btn-primary"><i class="icon-arrow-right icon-white"></i> Next</a>
        </div>
    </div>
    <h3>Database Configuration</h3>
    <hr class="hr-condensed">
    <div class="control-group">
        <div class="control-label">
            <label class=" required" for="db_type" id="db_type-lbl">Database Type<span class="star">&nbsp;*</span></label>		</div>
        <div class="controls">
            <select class="inputbox" name="olab[db_type]" id="db_type">
                <option selected="selected" value="mysql">MySQL</option>
            </select>
            <p class="help-block">This is probably "MySQL"</p>
        </div>
    </div>
    <div class="control-group">
        <div class="control-label">
            <label for="db_host" id="db_host-lbl">Host Name<span class="star">&nbsp;*</span></label>
        </div>
        <div class="controls">
            <input autocomplete="off" type="text" class="inputbox" value="<?php echo (isset($templateData['data']) ? $templateData['data']->db_host : 'localhost'); ?>" id="db_host" name="olab[db_host]" />
            <p class="help-block">This is usually "localhost"</p>
        </div>
    </div>
    <div class="control-group">
        <div class="control-label">
            <label for="db_port" id="db_port-lbl">Port</label>
        </div>
        <div class="controls">
            <input autocomplete="off" type="text" class="inputbox" value="<?php echo (isset($templateData['data']) ? $templateData['data']->db_port : ''); ?>" id="db_port" name="olab[db_port]" />
        </div>
    </div>
    <div class="control-group">
        <div class="control-label">
            <label for="db_user" id="db_user-lbl">Username<span class="star">&nbsp;*</span></label>
        </div>
        <div class="controls">
            <input autocomplete="off" type="text" class="inputbox" value="<?php echo (isset($templateData['data']) ? $templateData['data']->db_user : ''); ?>" id="db_user" name="olab[db_user]" />
            <p class="help-block">Either something as "root" or a username given by the host</p>
        </div>
    </div>
    <div class="control-group">
        <div class="control-label">
            <label for="db_pass" id="db_pass-lbl">Password</label>
        </div>
        <div class="controls">
            <input autocomplete="off" type="password" class="inputbox" value="<?php echo (isset($templateData['data']) ? $templateData['data']->db_pass : ''); ?>" id="db_pass" name="olab[db_pass]">
            <p class="help-block">For site security using a password for the database account is mandatory</p>
        </div>
    </div>
    <div class="control-group">
        <div class="control-label">
            <label for="db_name" id="db_name-lbl">Database Name<span class="star">&nbsp;*</span></label>
        </div>
        <div class="controls">
            <input autocomplete="off" type="text" class="inputbox" value="<?php echo (isset($templateData['data']) ? $templateData['data']->db_name : ''); ?>" id="db_name" name="olab[db_name]" />
        </div>
    </div>
    <div class="control-group">
        <div class="control-label">
            <label id="db_old-lbl">Old Database Process<span class="star">&nbsp;*</span></label>
        </div>
        <div class="controls">
            <div class="radio_extended btn-group">
                <input autocomplete="off" type="radio"
                    <?php
                        if (isset($templateData['data'])){
                            if ($templateData['data']->db_old == 'backup') echo 'checked="checked"';
                        } else {
                            echo 'checked="checked"';
                        }
                    ?> value="backup" name="olab[db_old]" id="db_old0">
                <label data-class="btn-success" for="db_old0" class="btn">Backup</label>
                <input autocomplete="off" type="radio" <?php echo ((isset($templateData['data']) && ($templateData['data']->db_old == 'remove')) ? 'checked="checked"' : ''); ?> value="remove" name="olab[db_old]" id="db_old1">
                <label data-class="btn-danger" for="db_old1" class="btn">Remove</label>
            </div>
            <p class="help-block">Any existing backup tables from former OpenLabyrinth installations will be replaced</p>
        </div>
    </div>
</form>