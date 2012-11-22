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
<form method="post" action=<?php echo URL::base().'home/login' ?>>
    <table width="78%" border="0" cellspacing="0" cellpadding="2">
        <tr><td align="left"><font face="Arial, Helvetica, sans-serif" size="2">User name</font></td></tr>
        <tr><td align="left"><input class="not-autocomplete" type="text" name="username" size="10"></td></tr>
        <tr><td align="left"><font face="Arial, Helvetica, sans-serif" size="2">Password</font></td></tr>
        <tr><td align="left"><input type="password" name="password" size="10"></td></tr>
        <tr><td align="left"><font style="text-decoration: underline;" face="Arial, Helvetica, sans-serif" size="1"><a href="<?php echo URL::base(); ?>home/resetPassword">Forgot password</a></font></td></tr>
        <tr><td align="left"><input style="margin-top:10px;" type="submit" name="LoginSubmit" value="Login"></td></tr>
        </tr></table>
</form>
