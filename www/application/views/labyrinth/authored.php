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
$user           = Auth::instance()->get_user();
$userId         = 0;
$userType       = 0;
$authorRight    = Arr::get($templateData, 'authorRight', array());
if ($user)
{
    $userId     = $user->id;
    $userType   = $user->type_id;
}
?>
<div class="page-header">
    <div class="pull-right">
        <div class="btn-group">
            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                <i class="icon-plus-sign icon-white"></i>Create Labyrinth<span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="<?php echo URL::base().'labyrinthManager/caseWizard'; ?>"><?php echo __('Create Step-by-Step'); ?></a></li>
                <li><a href="<?php echo URL::base().'labyrinthManager/addManual'; ?>"><?php echo __('Create Manually'); ?></a></li>
                <li><a href="<?php echo URL::base().'#'; ?>"><?php echo __('Duplicate Existing'); ?></a></li>
            </ul>
        </div>
    </div><?php
    if (isset($templateData['maps'])) { ?>

    <h1><?php echo __('My Labyrinths'); ?></h1>
    </div>
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables.css"/>
    <script type="text/javascript" charset="utf8" src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="<?php echo URL::base(); ?>scripts/olab/dataTablesTB.js"></script>

    <table class="table table-striped table-bordered dataTable" id="my-labyrinths">
        <colgroup>
            <col style="width: 5%"/>
            <col style="width: 50%"/>
            <col style="width: 20%"/>
            <col style="width: 25%"/>
        </colgroup>
        <thead>
        <tr>
            <th><?php echo __('ID'); ?></th>
            <th><?php echo __('Labyrinth Title'); ?></th>
            <th><?php echo __('Contributors'); ?></th>
            <th><?php echo __('Actions'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($templateData['maps'] as $map) { ?>
            <tr>
                <td><?php echo $map->id; ?></td>
                <td><a <?php if ($map->author_id == $userId OR $userType == 4)  echo 'href='.URL::base().'labyrinthManager/info/'.$map->id; ?>><?php echo $map->name; ?></a>
                </td>
                <td><?php
                    if (count($map->contributors) > 0) {
                        $contributors = array();
                        foreach ($map->contributors as $contributor) {
                            $contributors[] = '<a href="#" rel="tooltip" title="'.ucwords($contributor->role->name).'">'.$contributor->name . '</a>';
                        }
                        echo implode(', ', $contributors);
                    } ?>
                </td>
                <td class="center">
                    <div class="btn-group"><?php
                        if (true) { ?>
                        <a class="btn btn-success" href="<?php echo URL::base(); ?>renderLabyrinth/index/<?php echo $map->id; ?>">
                            <i class="icon-play icon-white"></i>
                            <span class="visible-desktop">Play</span>
                        </a><?php
                        } else { ?>
                        <a class="btn btn-success show-root-error" href="javascript:void(0)">
                            <i class="icon-play icon-white"></i>
                            <span class="visible-desktop">Play</span>
                        </a><?php
                        }
                        if ($map->author_id == $userId OR $userType == 4 OR Arr::get($authorRight, $map->id, false)) { ?>
                        <a class="btn btn-info" href="<?php echo URL::base().'labyrinthManager/global/'.$map->id; ?>">
                            <i class="icon-edit icon-white"></i> Edit
                        </a><?php
                        } ?>
                        <a class="btn" data-toggle="modal" data-target="#duplicate_labyrinth<?php echo $map->id; ?>"
                           href="#">
                            <i class="icon-th icon-white"></i>
                            Duplicate
                        </a></div>
                    <div class="modal hide fade in" id="duplicate_labyrinth<?php echo $map->id; ?>">
                        <div class="modal-header block">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4><?php echo __('Are you sure want to duplicate Case?'); ?></h4>
                        </div>
                        <div class="modal-body">
                            <p><?php echo __('You have just clicked the duplicate button, are you certain that you wish to proceed with duplicate "' . $map->name . '" labyrinth?'); ?></p>

                            <p>
                                <a class="btn confirm-link"
                                   href="<?php echo URL::base(); ?>authoredLabyrinth/duplicate/<?php echo $map->id; ?>"><?php echo __('Duplicate Case'); ?></a>
                                <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                            </p>
                        </div>
                    </div>
                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>
    </div>
<?php
} else {
    ?>
    <div class="alert alert-info">
        <p class="lead"><?php echo __('You do not appear to have any labyrinths authored at this time.'); ?></p>

        <p><?php echo __('Now is as good-a-time as any to click the Create Labyrinth button above.'); ?></p>
    </div>
<?php
}
?>
