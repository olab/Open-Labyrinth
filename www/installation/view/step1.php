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
<div class="modal hide fade in" id="skipInstallationPopUp">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="alert-heading">Caution! Are you sure?</h4>
    </div>
    <div class="modal-body">
        <p>You have just clicked the skip installation button, are you certain that you wish to skip installation?</p>
        <p>
            <a class="btn btn-primary" id="skipInstallation" href="javascript:void(0);">Skip</a>
            <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        </p>
    </div>
</div>
<form class="hide" id="skipInstallationForm" method="post" action="<?php echo URL::base(); ?>installation/index.php">
    <input type="hidden" name="token" value="<?php echo $templateData['token']; ?>" />
    <input type="hidden" name="skipInstallation" value="1" />
</form>
<form class="form-validate form-horizontal" id="adminForm" method="post" action="<?php echo URL::base(); ?>installation/index.php">
    <input type="hidden" name="token" value="<?php echo $templateData['token']; ?>" />
    <div class="btn-toolbar">
        <div class="btn-group pull-right">
            <a id="skipInstallationButton" title="Skip" class="btn" href="javascript:void(0)">Skip installation, I have already setup instance of OpenLabyrinth</a>
            <a title="Start installation" id="next-step" rel="next" href="javascript:void(0);" class="btn btn-primary"><i class="icon-arrow-right icon-white"></i> Start installation</a>
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
</form>