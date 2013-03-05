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
if (isset($templateData['node']) && isset($templateData['map'])) {
    ?>

    <h1><?php echo __('Edit links of node'); ?>&nbsp; <a href="#"><?php echo $templateData['node']->id; ?>
            : <?php echo $templateData['node']->title; ?></a></h1>

    <?php if (isset($templateData['linkTypes']) and count($templateData['linkTypes']) > 0) { ?>
        <form class="form-horizontal" id="form0" name="form00" method="post"
              action="<?php echo URL::base() . 'linkManager/updateLinkType/' . $templateData['map']->id . '/' . $templateData['node']->id; ?>">
            <fieldset class="fieldset">

                <div class="control-group">
                    <label class="control-label"><?php echo __('Order'); ?></label>

                    <div class="controls">


                        <?php foreach ($templateData['linkTypes'] as $linkType) { ?>


                            <label class="radio">

                                <input type="radio" name="linktype"
                                                        value="<?php echo $linkType->id; ?>" <?php if ($linkType->id == $templateData['node']->link_type_id) echo 'checked=""'; ?>><?php echo $linkType->name; ?>



                            </label>
                        <?php } ?>
                    </div>
                </div>

            </fieldset>

                <input class="btn btn-primary" type="submit" name="Submit" value="update">
        </form>

    <?php } ?>



    <h4><?php echo __('Linked to'); ?></h4>
    <?php if ($templateData['node']->link_type->name == 'ordered' or $templateData['node']->link_type->name == 'random select one *') { ?>
        <form class="form-horizontal" method="post" action="<?php echo URL::base() . 'linkManager/updateOrder/' . $templateData['map']->id . '/' . $templateData['node']->id; ?>">
    <?php } ?>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Target Node</th>
            <th>Actions</th>
    <?php if ($templateData['node']->link_type->name == 'ordered') { ?>
                <th>Order</th>

        <?php } ?>
        </tr>
        </thead>
        <tbody>
            <?php if (count($templateData['node']->links) > 0) { ?>
                <?php foreach ($templateData['node']->links as $link) { ?>
                    <tr>
                        <td><?php echo $link->node_2->title; ?> (<?php echo $link->node_2->id; ?>)</td>
                        <td >
                                <a class="btn btn-primary" href="<?php echo URL::base() . 'linkManager/editLink/' . $templateData['map']->id . '/' . $templateData['node']->id . '/' . $link->id ."#edit"; ?>"><?php echo __('edit'); ?>
                                    </a>

                                <a class="btn btn-primary" href="<?php echo URL::base() . 'linkManager/deleteLink/' . $templateData['map']->id . '/' . $templateData['node']->id . '/' . $link->id; ?>"><?php echo __('delete'); ?>
                                    </a>

                        <?php if ($templateData['node']->link_type->name == 'ordered') { ?>
                            <td>
                            <label>
                                <select name="order_<?php echo $link->id; ?>">
                                    <?php for ($i = 0; $i < count($templateData['node']->links); $i++) { ?>
                                        <option
                                            value="<?php echo $i + 1; ?>" <?php if ($link->order == ($i + 1)) echo 'selected=""'; ?>><?php echo $i + 1; ?></option>
                                    <?php } ?>
                                </select></label></td>
                        <?php } else if ($templateData['node']->link_type->name == 'random select one *') { ?>
                            <td>
                                <label  class="control-label" for="weight_<?php echo $link->id; ?>"><?php echo __('Probability'); ?></label>
                                <div class="controls"><input type="text" size="5" name="weight_<?php echo $link->id; ?>" id="weight_<?php echo $link->id; ?>" value="<?php echo $link->probability; ?>"/> </div>


                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            <?php } else {?>
                <tr class="info"><td colspan="3"><?php echo __('No links available yet. You may link this node to others, using the form below.'); ?></td></tr>
            <?php }?>



        </tbody>
    </table>
    <?php if ($templateData['node']->link_type->name == 'ordered' or $templateData['node']->link_type->name == 'random select one *') { ?>
        <input class="btn btn-primary" type="submit" name="Submit" value="update"></form>
    <?php } ?>
    <?php if (isset($templateData['linkStylies'])) { ?>

        <form id="form2" name="formx" method="post" class="form-horizontal"
              action="<?php echo URL::base() . 'linkManager/updateLinkStyle/' . $templateData['map']->id . '/' . $templateData['node']->id; ?>">
            <fieldset class="fieldset">

                <?php if (count($templateData['linkStylies']) > 0) { ?>
                <div class="control-group">
                    <label class="control-label"><?php echo __('Link presentation style'); ?></label>

                    <div class="controls">


                <?php foreach ($templateData['linkStylies'] as $style) { ?>
                            <label class="radio">
                                <input type="radio" name="linkstyle"
                                value="<?php echo $style->id; ?>" <?php if ($style->id == $templateData['node']->link_style_id) echo 'checked=""'; ?>/> <?php echo $style->name; ?>
                            </label>
                        <?php } ?>


                    </div>
                </div>

                <input class="btn btn-primary" type="submit" name="Submit" value="<?php echo __('update'); ?>"></p>
                <?php } ?></fieldset>


        </form>
    <?php } ?>
    <hr>
    <?php if (isset($templateData['editLink'])) { ?>
        <form class="form-horizontal" id="form3" name="form1" method="post" action="<?php echo URL::base() . 'linkManager/updateLink/' . $templateData['map']->id . '/' . $templateData['node']->id . '/' . $templateData['editLink']->id; ?>">


    <?php } else { ?>
        <form class="form-horizontal" id="form3" name="form1" method="post" action="<?php echo URL::base() . 'linkManager/addLink/' . $templateData['map']->id . '/' . $templateData['node']->id; ?>">
    <?php } ?>

    <fieldset class="fieldset">
    <legend>
        <?php if (isset($templateData['editLink'])) { ?>
            <a name="edit">Edit Link</a>
        <?php } else { ?>
            Add new Link
        <?php } ?>

    </legend>

    <div class="control-group">
        <label for="linkmnodeid" class="control-label"><?php echo __('Target node'); ?>
        </label>
        <div class="controls">
            <?php if (isset($templateData['link_nodes']) and count($templateData['link_nodes']) > 0) { ?>
                <select name="linkmnodeid" id="linkmnodeid">
                    <?php foreach ($templateData['link_nodes'] as $node) { ?>
                        <option value="<?php echo $node->id; ?>"><?php echo $node->id; ?>
                            : <?php echo $node->title; ?></option>
                    <?php } ?>
                </select>
            <?php } else if (isset($templateData['editLink'])) { ?>
                <input value="<?php echo $templateData['editLink']->node_2->title; ?> [<?php echo $templateData['editLink']->node_2->id; ?>]" type="text" readonly="readonly"/>
            <?php } ?>
        </div>
    </div>


        <div class="control-group">
            <label for="linkimage" class="control-label"><?php echo __('Link image'); ?>
            </label>
            <div class="controls">
                <?php if (isset($templateData['images']) and count($templateData['images']) > 0) { ?>
                    <select id="linkimage" name="linkImage">
                        <option
                            value="0" <?php if (isset($templateData['editLink']) and $templateData['editLink']->image_id == NULL) echo 'selected=""'; ?>>
                            no image
                        </option>
                        <?php foreach ($templateData['images'] as $image) { ?>
                            <option
                                value="<?php echo $image->id; ?>" <?php if (isset($templateData['editLink']) and $image->id == $templateData['editLink']->image_id) echo 'selected=""'; ?>><?php echo $image->name; ?>
                                (<?php echo $image->id; ?>)
                            </option>
                        <?php } ?>
                    </select>
                <?php } else { ?>
                    <select id="linkimage" name="linkImage">
                        <option value="0" selected="">no image</option>
                    </select>
                <?php } ?>
            </div>
        </div>


        <div class="control-group">
            <label for="linkLabel" class="control-label"><?php echo __('Link label'); ?>
            </label>
            <div class="controls">
                <input type="text" name="linkLabel"  id="linkLabel"
                       value="<?php if (isset($templateData['editLink'])) echo $templateData['editLink']->text; ?>">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('Direction'); ?></label>

            <div class="controls">
               <label class="radio">
                        <input type="radio" name="linkDirection" value="1" checked=""><?php echo __('one way'); ?>
                    </label>
            </div>
            <div class="controls">
                <label class="radio">
                    <input
                        type="radio" name="linkDirection" value="2"><?php echo __('both ways'); ?>
                </label>

            </div>
        </div>

    </fieldset>


    <?php if (isset($templateData['editLink']))
    echo Helper_Controller_Metadata::displayEditor($templateData["editLink"],"map_node_link");?>


<input class="btn btn-primary" type="submit" name="Submit" value="<?php if (isset($templateData['editLink'])) echo __('update');
else echo __('add');?>">

    </form>

<?php } ?>