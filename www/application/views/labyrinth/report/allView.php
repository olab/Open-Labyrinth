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
if (isset($templateData['map'])) {
    ?>

    <h1><?php echo __('Labyrinth Report for "') . $templateData['map']->name . '"'; ?></h1>


    <p><?php echo __('click to view performance by session'); ?></p>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Report</th>
            <th>User</th>
            <th>Traces</th>

        </tr>
        </thead>
        <tbody>
        <?php if (isset($templateData['sessions']) and count($templateData['sessions']) > 0) { ?>
            <?php foreach ($templateData['sessions'] as $session) { ?>
                <tr>
                    <td><a href="<?php echo URL::base(); ?>reportManager/showReport/<?php echo $session->id; ?>">
                        <?php echo date('Y.m.d H:i:s', $session->start_time); ?></a></td>
                    <td><?php echo $session->user->nickname; ?></td>
                    <td><?php echo count($session->traces); ?> <?php echo __('clicks'); ?>' (0 bookmarks)</td></tr>
            <?php } ?>
        <?php } ?>

        </tbody>
    </table>
   <a class="btn btn-primary" href="<?php echo URL::base() ?>reportManager/summaryReport/<?php echo $templateData['map']->id; ?>">aggregate report</a>
<?php } ?>

