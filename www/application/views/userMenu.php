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
<table><tr>
        <td bgcolor="#ffffff" align="left" width="60%" valign="top">
            <?php if(isset($presentations) and count($presentations) > 0) { ?>
            <p><img src="<?php echo URL::base(); ?>images/presentl.jpg" border="0" alt="OLPresentations">Presentations</p>
            <?php foreach($presentations as $presentation) { ?>
            <p><a href="<?php echo URL::base(); ?>presentationManager/render/<?php echo $presentation->id; ?>"><?php echo $presentation->title; ?></a></p>
            <?php } ?>
            <hr>
            <?php } ?>
            <p><img src="<?php echo URL::base(); ?>images/olsphere.jpg" border="0" alt="OLMaps">Open Labyrinths</p>
            <?php if (isset($openLabyrinths) and count($openLabyrinths) > 0) { ?>
                <?php foreach ($openLabyrinths as $labyrinth) { ?>
                    <p><a href="<?php echo URL::base(); ?>renderLabyrinth/index/<?php echo $labyrinth->id; ?>"><?php echo $labyrinth->name; ?></a></p>
                <?php } ?>
            <?php } ?>
        </td>
    </tr>
</table>
