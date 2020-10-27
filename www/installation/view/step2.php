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

<div class="btn-toolbar">
    <div class="btn-group pull-right">
        <a title="Next" rel="next" id="next-step" class="btn btn-primary" href="javascript:void(0)"><i class="icon-arrow-right icon-white"></i> Next</a>
    </div>
</div>
<form class="form-validate form-horizontal" id="adminForm" method="post" action="<?php echo URL::base(); ?>installation/index.php">
    <h3>Main Configuration</h3>
    <hr class="hr-condensed">

    <div class="row-fluid">
        <div class="span6">
            <div class="control-group">
                <div class="control-label">
                    <label class=" required" for="admin_email" id="admin_email-lbl">Admin Email<span class="star">&nbsp;*</span></label>
                </div>
                <div class="controls">
                    <input autocomplete="off" type="text" value="<?php echo (isset($templateData['data']) ? $templateData['data']->admin_email : ''); ?>" id="admin_email" class="inputbox" name="olab[admin_email]" />
                    <p class="help-block">Enter an email address.</p>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <label class=" required" for="admin_user" id="admin_user-lbl">Admin Username<span class="star">&nbsp;*</span></label>
                </div>
                <div class="controls">
                    <input autocomplete="off" type="text" class="inputbox" value="<?php echo (isset($templateData['data']) ? $templateData['data']->admin_user : 'admin'); ?>" id="admin_user" name="olab[admin_user]" />
                    <p class="help-block">You may change the default username <strong>admin</strong>.</p>
                </div>
            </div>
        </div>
        <div class="span6">
            <div class="control-group">
                <div class="control-label">
                    <label class=" required" for="admin_password" id="admin_password-lbl">Admin Password<span class="star">&nbsp;*</span></label>
                </div>
                <div class="controls">
                    <input autocomplete="off" type="password" class="inputbox" value="<?php echo (isset($templateData['data']) ? $templateData['data']->admin_password : ''); ?>" id="admin_password" name="olab[admin_password]" />
                    <p class="help-block">Set the password for your Administrator account and confirm it in the field below.</p>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <label class=" required" for="admin_password2" id="admin_password2-lbl">Confirm Admin Password<span class="star">&nbsp;*</span></label>
                </div>
                <div class="controls">
                    <input autocomplete="off" type="password" class="inputbox" value="<?php echo (isset($templateData['data']) ? $templateData['data']->admin_password2 : ''); ?>" id="admin_password2" name="olab[admin_password2]" />
                </div>
            </div>
        </div>
    </div>
    <h3>Language and time zone</h3>
    <p>These settings may be helpful in the future, right now their value is low. We will try to guess correct values but this requires an internet connection.<br>
        Feel free to correct settings below, but make sure to stick to correct formatting as no checks will be made about that and
        wrong settings may have adverse impact on future releases.
    </p>

    <div class="row-fluid">
        <div class="span6">
            <div class="control-group">
                <div class="control-label">
                    <label for="timezone" id="timezone-lbl">Time Zone</label>
                </div>
                <div class="controls">
                    <input autocomplete="off" type="text" value="<?php echo (isset($templateData['data']) ? $templateData['data']->timezone : 'America/Denver'); ?>" id="timezone" class="inputbox" name="olab[timezone]" />
                    <p class="help-block">Enter your Time Zone</p>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <label for="language" id="language-lbl">Language-Code</label>
                </div>
                <div class="controls">
                    <input autocomplete="off" type="text" class="inputbox" value="<?php echo (isset($templateData['data']) ? $templateData['data']->languagecode : 'en-us'); ?>" id="lang" name="olab[lang]" />
                    <p class="help-block">Please enter your Language Code.</p>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <label for="locale" id="locale-lbl">Locale</label>
                </div>
                <div class="controls">
                    <input autocomplete="off" type="text" class="inputbox" value="<?php echo (isset($templateData['data']) ? $templateData['data']->locale : 'en_US.utf-8'); ?>" id="locale" name="olab[locale]" />
                    <p class="help-block">Please enter your locale.</p>
                </div>
            </div>        </div>
    </div>

    <input type="hidden" name="token" value="<?php echo $templateData['token']; ?>" />
</form>

