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
<div class="page-header">
    <div class="pull-right"> <a class="btn btn-primary" href="<?php echo URL::base() ?>reportManager/summaryReport/<?php echo $templateData['map']->id; ?>"><i class="icon-book"></i> Aggregate report</a></div>
    <h1><?php echo __('Labyrinth Report for "') . $templateData['map']->name . '"'; ?></h1>
</div>
<p><?php echo __('Click to view performance by session'); ?></p>
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Report</th>
            <th>User</th>
            <th>Traces</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach (Arr::get($templateData, 'sessions', array()) as $session) { ?>
        <tr>
            <td>
                <a href="<?php echo URL::base().'reportManager/showReport/'.$session->id; ?>">
                    <?php echo date('Y.m.d H:i:s', $session->start_time); ?>
                </a>
            </td>
            <td><?php echo $session->user->nickname; ?></td>
            <td><?php echo count($session->traces).' '.__('clicks').' (0 bookmarks)'; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<?php } ?>

