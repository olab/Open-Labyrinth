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

            <h1><?php echo __('User account'); ?></h1>
            <table>
                <tr><td>
                        <?php echo __('username'); ?></td><td><?php if(isset($templateData['newUser']['uid'])) echo $templateData['newUser']['uid']; ?></td>
                    </tr>

                <tr><td><?php echo __('password'); ?></td><td><?php if(isset($templateData['newUser']['upw'])) echo $templateData['newUser']['upw']; ?></td></tr>
                <tr><td><?php echo __('name'); ?></td><td><?php if(isset($templateData['newUser']['uname'])) echo $templateData['newUser']['uname']; ?></td></tr>
                <tr><td> <?php echo __('e-mail'); ?></td><td><?php if(isset($templateData['newUser']['uemail'])) echo $templateData['newUser']['uemail']; ?></td></tr>
                <tr><td> <?php echo __('user type'); ?></td><td><?php if(isset($templateData['newUser']['usertype'])) echo $templateData['newUser']['usertype']; ?></td></tr>
                <tr><td><?php echo __('language'); ?></td><td><?php if(isset($templateData['newUser']['langID'])) echo $templateData['newUser']['langID']; ?></td></tr>



            </table>
<?php echo __('New user added successfully.'); ?>
<a class="btn btn-primary" href=<?php echo URL::base().'usermanager'; ?>><?php echo __('users'); ?></a>
