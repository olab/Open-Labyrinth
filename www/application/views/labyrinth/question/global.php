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
        <h1><?php echo __('Global questions for "') . $templateData['map']->name . '"'; ?></h1>
    </div>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Stem</th>
            <th>Type</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (isset($templateData['questions']) and count($templateData['questions']) > 0) { ?>
            <?php foreach ($templateData['questions'] as $question) { ?>
                <tr>
                    <td>
                        <?php echo $question->stem; ?>
                    </td>
                    <td>
                        <?php foreach($templateData['question_types'] as $type){
                            if($type['id'] == $question->entry_type_id){
                                echo $type['title'];
                            }
                        }
                        ?>
                    </td>
                    <td>
                        <a class="btn btn-info" href="<?php echo URL::base().'questionManager/importQuestion/'.$templateData['map']->id . '/' . base64_encode($question->name_file); ?>"><i class="icon-edit"></i>Add to labyrinth</a>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr class="info"><td colspan="3">There are no available questions right now. You may add a question using the menu below.</td> </tr>
        <?php }?>
        </tbody>
    </table>
<?php } ?>