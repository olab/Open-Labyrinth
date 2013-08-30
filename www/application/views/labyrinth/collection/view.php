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
    <script language="JavaScript">
        function toggle_visibility(id) {
            var e = document.getElementById(id);
            if(e.style.display == 'none')
                e.style.display = 'block';
            else
                e.style.display = 'none';
        }
    </script>
<div class="page-header">
    <div class="pull-right"><a class="btn btn-primary" href="<?php echo URL::base(); ?>collectionManager/addCollection"><i class="icon-plus-sign"></i>Add Collection</a></div>
<h1><?php echo __('Collections'); ?></h1></div>
    <table class="table table-striped table-bordered">
<thead><tr>
    <th>Collection</th>
    <th>Actions</th>
</tr></thead>
<tbody>

<?php if (isset($templateData['collections']) and count($templateData['collections']) > 0) { ?>


    <?php foreach($templateData['collections'] as $collection) { ?>
        <tr><td>
               <?php echo $collection->name; ?>

                </td>

            <td>
                <a class="btn btn-info" href="<?php echo URL::base() ?>collectionManager/editCollection/<?php echo $collection->id; ?>"><i class="icon-edit"></i> <?php echo __('Edit'); ?></a>
                <a class="btn btn-info" href="<?php echo URL::base() ?>collectionManager/viewAll/<?php echo $collection->id; ?>"><i class="icon-list"></i> <?php echo __('View'); ?></a>
            </td>

        </tr>


    <?php } ?>


<?php } ?>



</tbody>


    </table>

