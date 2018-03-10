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
<table width="100%" bgcolor="#ffffff">
    <tr>
        <td>
            <h4><?php echo __('remote services'); ?></h4>
            <p><?php echo __('these are XML connectors to allow you to run Labyrinths remotely in other systems or contexts. Each service is mapped to a single server IP address and can have Labyrinths mapped to it. There are two service calls'); ?>:</p>
            <p>- <?php echo __('renderLabyrinth/remote - this will list the available Labyrinths registered to this service'); ?></p>
            <hr>
            <?php if(isset($templateData['services']) and count($templateData['services']) > 0) { ?>
            <?php foreach($templateData['services'] as $service) { ?>
            <p>'<?php echo $service->name; ?>' : <a href="<?php echo URL::base(); ?>remoteServiceManager/editService/<?php echo $service->id; ?>">edit service</a> - <a href="<?php echo URL::base(); ?>remoteServiceManager/editServiceMap/<?php echo $service->id; ?>">add/edit Labyrinths</a></p>
            <?php } ?>
            <?php } ?>
            <hr>
            <p><a href="<?php echo URL::base(); ?>remoteServiceManager/addService">add a service</a></p>
        </td>
    </tr>
</table>