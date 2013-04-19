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

    <h3><?php echo __('Counters') . ' "' . $templateData['map']->name . '"'; ?></h3>
    <?php if (isset($templateData['counters']) and count($templateData['counters']) > 0) { ?>
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>Title</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($templateData['counters'] as $counter) { ?>
                <tr>
                    <td>
                        <?php echo $counter->name; ?>
                    </td>
                    <td>
                        <a class="btn btn-primary"
                           href="<?php echo URL::base() . 'labyrinthManager/caseWizard/4/editCounter/' . $templateData['map']->id . '/' . $counter->id; ?>"><?php echo __('Edit'); ?></a>

                        <a class="btn btn-primary"
                           href="<?php echo URL::base() . 'labyrinthManager/caseWizard/4/previewCounter/' . $templateData['map']->id . '/' . $counter->id; ?>"><?php echo __('preview'); ?></a>

                        <a class="btn btn-primary"
                           href="<?php echo URL::base() . 'labyrinthManager/caseWizard/4/grid/' . $templateData['map']->id . '/' . $counter->id; ?>">grid</a>
                        <a class="btn btn-primary"
                           href="<?php echo URL::base() . 'labyrinthManager/caseWizard/4/deleteCounter/' . $templateData['map']->id . '/' . $counter->id; ?>"><?php echo __('delete'); ?></a>
                    </td>

                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } ?>


    <a class="btn btn-primary"
       href="<?php echo URL::base() . 'labyrinthManager/caseWizard/4/addNewCounter/' . $templateData['map']->id; ?>"><?php echo __('add counter'); ?></a>

    <a class="btn btn-primary"
       href="<?php echo URL::base() . 'labyrinthManager/caseWizard/4/grid/' . $templateData['map']->id; ?>"><?php echo __('counter grid'); ?></a>

<?php } ?>