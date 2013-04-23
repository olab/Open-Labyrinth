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
            <a title="Next" id="next-step" rel="next" href="javascript:void(0);" class="btn btn-primary"><i class="icon-arrow-right icon-white"></i> Install and go to the login page</a>
        </div>
    </div>
<!--    <h3>Overview</h3>-->
<!--    <hr class="hr-condensed">-->
<!---->
<!--    <div id="summary_email" class="control-group">-->
<!--        <div class="control-label">-->
<!--            <label class="" for="summary_email" id="summary_email-lbl">Email Configuration</label>-->
<!--        </div>-->
<!--        <div class="controls">-->
<!--            <div class="radio_extended btn-group">-->
<!--                <input type="radio" class="summary_email" checked="checked" value="0" name="olab[summary_email]" id="summary_email0" />-->
<!--                <label data-class="btn-danger" for="summary_email0" class="btn">No</label>-->
<!--                <input type="radio" class="summary_email" value="1" name="olab[summary_email]" id="summary_email1" />-->
<!--                <label data-class="btn-success" for="summary_email1" class="btn">Yes</label>-->
<!--            </div>-->
<!--            <p class="help-block">Send configuration settings to <span class="label">--><?php //echo $templateData['configuration']->admin_email; ?><!--</span> by email after installation.</p>-->
<!--        </div>-->
<!--    </div>-->
<!---->
<!--    <div class="hide" id="div_email_passwords" class="control-group">-->
<!--        <div class="control-label">-->
<!--            <label class="" id="email_passwords-lbl">Include Passwords in Email</label>-->
<!--        </div>-->
<!--        <div class="controls">-->
<!--            <div class="radio_extended btn-group">-->
<!--                <input type="radio" checked="checked" value="0" name="olab[summary_email_passwords]" id="email_passwords0">-->
<!--                <label data-class="btn-danger" for="email_passwords0" class="btn">No</label>-->
<!--                <input type="radio" value="1" name="olab[summary_email_passwords]" id="email_passwords1">-->
<!--                <label data-class="btn-success" for="email_passwords1" class="btn">Yes</label>-->
<!--            </div>-->
<!--            <p class="help-block">Warning! It is recommended to not send and store your passwords in emails.</p>-->
<!--        </div>-->
<!--    </div>-->

    <div class="row-fluid">
        <div class="span6">
            <h3>Main Configuration</h3>
            <hr class="hr-condensed">
            <table class="table table-striped table-condensed">
                <tbody>
                <tr>
                    <td class="item">
                        Admin Email
                    </td>
                    <td>
                        <span class="label"><?php echo $templateData['configuration']->admin_email; ?></span>
                    </td>
                </tr>
                <tr>
                    <td class="item">
                        Admin Username
                    </td>
                    <td>
                        <span class="label"><?php echo $templateData['configuration']->admin_user; ?></span>
                    </td>
                </tr>
                <tr>
                    <td class="item">
                        Admin Password
                    </td>
                    <td>
                        ***
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="span6">
            <h3>Database Configuration</h3>
            <hr class="hr-condensed">
            <table class="table table-striped table-condensed">
                <tbody>
                <tr>
                    <td class="item">
                        Database Type
                    </td>
                    <td>
                        mysql
                    </td>
                </tr>
                <tr>
                    <td class="item">
                        Host Name
                    </td>
                    <td>
                        <?php echo $templateData['database']->db_host; ?>
                    </td>
                </tr>
                <tr>
                    <td class="item">
                        Port
                    </td>
                    <td>
                        <?php echo $templateData['database']->db_port; ?>
                    </td>
                </tr>
                <tr>
                    <td class="item">
                        Username
                    </td>
                    <td>
                        <?php echo $templateData['database']->db_user; ?>
                    </td>
                </tr>
                <tr>
                    <td class="item">
                        Password
                    </td>
                    <td>
                        <?php echo ($templateData['database']->db_pass != '') ? '***' : ''; ?>
                    </td>
                </tr>
                <tr>
                    <td class="item">
                        Database Name
                    </td>
                    <td>
                        <?php echo $templateData['database']->db_name; ?>
                    </td>
                </tr>
                <tr>
                    <td class="item">
                        Old Database Process
                    </td>
                    <td>
                        <?php
                        if ($templateData['database']->db_old == 'backup'){
                            echo '<span class="label label-success">Backup</span>';
                        } else {
                            echo '<span class="label label-important">Remove</span>';
                        }
                        ?>

                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span6">
            <h3>Pre-Installation Check</h3>
            <hr class="hr-condensed">
            <table class="table table-striped table-condensed">
                <tbody>
                <?php
                if (isset($templateData['pre-check']) && (count($templateData['pre-check']) > 0)){
                    foreach($templateData['pre-check'] as $preCheck){
                        echo '<tr>
                        <td class="item">'.$preCheck['item'].'</td>
                        <td><span class="label label-'.$preCheck['label'].'">'.$preCheck['status'].'</span></td>
                        </tr>';
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
        <div class="span6">
            <h3>Access to file system objects</h3>
            <hr class="hr-condensed">
            <p class="install-text">All of the next files and directories should be writable:</p>
            <table class="table table-striped table-condensed">
                <tbody>
                <?php
                if (isset($templateData['file_objects']) && (count($templateData['file_objects']) > 0)){
                    foreach($templateData['file_objects'] as $file_objects){
                        echo '<tr>
                        <td class="item">'.$file_objects['item'].'</td>
                        <td><span class="label label-'.$file_objects['label'].'">'.$file_objects['status'].'</span></td>
                        </tr>';
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span6">
            <h3>Recommended settings:</h3>
            <hr class="hr-condensed">
            <p class="install-text">
                These settings are recommended for PHP in order to ensure full compatibility with OpenLabyrinth.<br>However, OpenLabyrinth will still operate if your settings do not quite match the recommended configuration.</p>
            <table class="table table-striped table-condensed">
                <thead>
                <tr>
                    <th>Directive</th>
                    <th>Recommended</th>
                    <th>Actual</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if (isset($templateData['recommended']) && (count($templateData['recommended']) > 0)){
                    foreach($templateData['recommended'] as $recommended){
                        echo '<tr>
                        <td class="item">'.$recommended['item'].'</td>
                        <td><span class="label label-'.$recommended['label'].'">'.$recommended['status'].'</span></td>
                        <td><span class="label label-'.$recommended['ac-label'].'">'.$recommended['ac-status'].'</span></td>
                        </tr>';
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
        <div class="span6"></div>
    </div>
</form>