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
defined('SYSPATH') or die('No direct access allowed.');

return array ("mailfrom" => "admin@localhost.com", "fromname" => "Open-Labyrinth", "email_password_reset_subject" => "Password reset request", "email_password_reset_body" => "Hello <%name%>,

A request has been made to reset your \"<%username%>\" account password.
To reset your password, you will need to click on the URL below and proceed with resetting your password.

<%link%>

Thank you.", "email_password_complete_subject" => "Password is changed successfully", "email_password_complete_body" => "Hello <%name%>,

Password for your \"<%username%>\" account is changed successfully.

Thank you.", );