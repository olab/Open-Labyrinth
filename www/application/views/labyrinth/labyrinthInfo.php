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

   <h1><?php echo __('Labyrinth information for') . ' "' . $templateData['map']->name . '"'; ?></h1>
   <table class="table table-bordered table-striped">
               <tbody>
               <tr>
                       <td><?php echo __('title'); ?></td>
                       <td><?php echo $templateData['map']->name; ?></td>
                   </tr>
               <tr>
                       <td><?php echo __('authors'); ?></td>
                       <td>
                               <?php if (count($templateData['map']->authors) > 0) { ?>
                                       <?php foreach ($templateData['map']->authors as $author) { ?>
                                               <?php echo $author->user->nickname; ?> (<?php echo $author->user->username; ?>),
                                           <?php } ?>
                                   <?php } ?>
                           </td>
                   </tr>
               <tr>
                       <td><?php echo __('keywords'); ?></td>
                       <td><?php echo $templateData['map']->keywords; ?></td>
                   </tr>
               <tr>
                       <td><?php echo __('Labyrinth type'); ?></td>
                       <td><?php echo $templateData['map']->type->name; ?></td>
                   </tr>
               <tr>
                       <td><?php echo __('security'); ?></td>
                       <td><?php echo $templateData['map']->security->name; ?></td>
                   </tr>
               <tr>
                       <td><?php echo __('number of nodes'); ?></td>
                       <td>
                               <?php
                               if (count($templateData['map']->nodes) > 0) {
                                       echo count($templateData['map']->nodes);
               } else {
                                       echo '0';
               }
               ?>
                           </td>
                   </tr>
               <tr>
                       <td><?php echo __('number of links'); ?></td>
                       <td><?php echo $templateData['map']->countLinks();?></td>
                   </tr>
           <?php


           $vars = $templateData["map"]->as_array();

           foreach ($vars as $property):?>    <?php if (Helper_Controller_Metadata::isMetadataRecord($property)): ?>
                   <tr>
                           <?php $view =  Helper_Controller_Metadata::getView($property); ?>
                <td><?php echo $view["label"]?></td>
                           <td>

                                   <?php echo $view["body"]?>
                               </td>
                       </tr>  <?php endif; ?>
                   <?php endforeach;?>
               </tbody>
           </table>

<?php } ?>
