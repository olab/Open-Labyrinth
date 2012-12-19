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
<h3>Login</h3>
<form method="post" action="<?php echo URL::base() . 'home/login' ?>">
    <label for="username"><?php echo __('Username'); ?></label>
    <input class="not-autocomplete" type="text" id="username" name="username" placeholder="<?php echo __('Your Username'); ?>" />

    <label for="password"><?php echo __('Password'); ?></label>
    <input class="not-autocomplete" type="password" id="password" name="password" placeholder="<?php echo __('Your Password'); ?>" />
    <span class="help-block"><a data-toggle="modal" href="#forgot-password-window"><?php echo __('Forgot My Password'); ?></a></span>

    <button type="submit" class="btn"><?php echo __('Login'); ?></button>
</form>

<div id="forgot-password-window" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="forgot-password-label" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 id="forgot-password-label"><?php echo __('Forgot My Password'); ?></h3>
    </div>
    <div class="modal-body">
        <form id="forgot-password-form" action="<?php echo URL::base(); ?>home/resetPassword" method="post">
            <p><?php echo __('If you have forgotten your OpenLabyrinth password please enter your e-mail address in the form below to initiate the password reset process.'); ?></p>

            <div class="control-group">
                <label class="control-label" for="email"><?php echo __('E-Mail Address'); ?></label>
                <div class="controls">
                    <div class="input-prepend">
                        <span class="add-on"><i class="icon-envelope"></i></span>
                        <input class="not-autocomplete" type="text" id="email" name="email" value="" autocomplete="off" placeholder="<?php echo __('Your e-mail address'); ?>" />
                    </div>
                </div>
            </div>
            <input type="hidden" name="token" value="<?php echo Security::token(); ?>" />
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Close</button>
        <button id="forgot-password-submit" class="btn btn-primary">Reset Password</button>
    </div>
</div>