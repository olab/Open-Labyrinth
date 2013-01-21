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
if (isset($templateData['map']) and isset($templateData['dam'])) {
    ?>

    <h1 xmlns="http://www.w3.org/1999/html"><?php echo __('Edit a Labyrinth Data Cluster "') . $templateData['dam']->name . '"'; ?></h1>

    <h3>Preview</h3>
    <h3>Data Cluster: <?php echo $templateData['dam']->name; ?></h3>

    <form method='post'
          action='<?php echo URL::base(); ?>clusterManager/updateDamName/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['dam']->id; ?>'>
        <fieldset class="fieldset">
            <div class="control-group">

                <label for="damname" class="control-label"><?php echo __("Data cluster name"); ?></label>

                <div class="controls">
                    <input name='damname' id='damname' type='text' class="span6"
                           value='<?php echo $templateData['dam']->name; ?>'/>
                </div>
            </div>
        </fieldset>

        <input class="btn btn-primary"
               type='submit' value='update'/>
    </form>
    <?php if (count($templateData['dam']->elements) > 0) { ?>
        <h4><?php echo __("Elements");?></h4>
        <?php foreach ($templateData['dam']->elements as $element) { ?>
            <?php if ($element->element_type == 'vpd') { ?>
                <form class="form-horizontal" method="post"
                      action="<?php echo URL::base(); ?>clusterManager/updateDamElement/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['dam']->id; ?>/<?php echo $element->id; ?>">





                    <fieldset class="fieldset">
                        <legend>VPD (<?php echo $element->vpd->type->label; ?>) element
                            (<?php echo $element->element_id; ?>)</legend>
                        <div class="control-group">
                            <label for="vpd_order" class="control-label">Order</label>

                            <div class="controls">
                                <select id="vpd_order" name='order'>
                                    <?php for ($i = 1; $i <= count($templateData['dam']->elements); $i++) { ?>
                                        <option
                                            value="<?php echo $i; ?>" <?php if ($element->order == $i) echo 'selected=""'; ?>><?php echo $i; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="vpd_trigger" class="control-label">Trigger</label>

                            <div class="controls">
                                <select id="vpd_trigger" name='trigger'>
                                            <option
                                            value='immediately' <?php if ($element->display == 'immediately') echo 'selected=""'; ?>>
                                            immediately
                                        </option>
                                        <option
                                            value='ontrigger' <?php if ($element->display == 'ontrigger') echo 'selected=""'; ?>>
                                            ontrigger
                                        </option>
                                        <option
                                            value='delayed' <?php if ($element->display == 'delayed') echo 'selected=""'; ?>>
                                            delayed
                                        </option>
                                        <option
                                            value='ifrequested' <?php if ($element->display == 'ifrequested') echo 'selected=""'; ?>>
                                            ifrequested
                                        </option>

                                </select>
                            </div>
                        </div>


                    </fieldset>


                       <input class="btn btn-primary" type="submit" value="update">

                                <a class="btn btn-primary" href="<?php echo URL::base(); ?>clusterManager/removeElementFormDam/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['dam']->id; ?>/<?php echo $element->id; ?>">delete</a>

                </form>
            <?php }

            else if ($element->element_type == 'mr') { ?>
                <form method="post" class="form-horizontal"
                      action="<?php echo URL::base(); ?>clusterManager/updateDamElement/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['dam']->id; ?>/<?php echo $element->id; ?>">

                    <fieldset class="fieldset">
                        <legend>MR (<?php echo $element->element->name; ?>) element (<?php echo $element->element_id; ?>)</legend>
                        <div class="control-group">
                            <label for="mr_order" class="control-label">Order</label>

                            <div class="controls">
                                <select id="mr_order" name='order'>
                                    <?php for ($i = 1; $i <= count($templateData['dam']->elements); $i++) { ?>
                                        <option
                                            value="<?php echo $i; ?>" <?php if ($element->order == $i) echo 'selected=""'; ?>><?php echo $i; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </fieldset>

                        <input class="btn btn-primary" type="submit" value="update">

                                <a class="btn btn-primary" href="<?php echo URL::base(); ?>clusterManager/removeElementFormDam/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['dam']->id; ?>/<?php echo $element->id; ?>">delete</a>


                </form>




            <?php }

            else if ($element->element_type == 'dam') { ?>

                    <form class="form-horizontal" method="post"
                          action="<?php echo URL::base(); ?>clusterManager/updateDamElement/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['dam']->id; ?>/<?php echo $element->id; ?>">
                        <fieldset class="fieldset">
                            <legend><?php echo __('DAM'); ?>
                                (<?php echo $element->edam->name; ?>) element (<?php echo $element->element_id; ?>)</legend>
                            <div class="control-group">
                                <label for="order" class="control-label">Order</label>

                                <div class="controls">
                                    <select id="order" name='order'>
                                        <?php for ($i = 1; $i <= count($templateData['dam']->elements); $i++) { ?>
                                            <option
                                                value="<?php echo $i; ?>" <?php if ($element->order == $i) echo 'selected=""'; ?>><?php echo $i; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </fieldset>

                        <input class="btn btn-primary" type="submit" value="update">


                <a class="btn btn-primary"
                   href="<?php echo URL::base(); ?>clusterManager/removeElementFormDam/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['dam']->id; ?>/<?php echo $element->id; ?>">delete</a>

                </form>
            <?php } ?>
        <?php } ?>

    <?php } ?>




                <?php if ($templateData['vpds'] != NULL and count($templateData['vpds']) > 0) { ?>
                    <form method='post' class="form-horizontal"
                          action='<?php echo URL::base(); ?>clusterManager/addElementToDam/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['dam']->id; ?>'>

                        <fieldset class="fieldset">

                            <div class="control-group">
                                <label for="vpdid" class="control-label">Add a data element</label>

                                <div class="controls">
                                    <select name='vpdid' id='vpdid'>
                                        <?php foreach ($templateData['vpds'] as $vpd) { ?>
                                            <option value="<?php echo $vpd->id; ?>"><?php echo $vpd->type->label; ?>
                                                (<?php echo $vpd->id; ?>)
                                            </option>
                                        <?php } ?>
                                    </select>

                                </div>
                            </div>
                        </fieldset>

                        <input class="btn btn-primary" type='submit' value='add'/>
                    </form>
                <?php } else { ?>
                    <p>no elements to add</p>
                <?php } ?>

                <?php if ($templateData['files'] != NULL and count($templateData['files']) > 0) { ?>
                    <form method='post' class="form-horizontal"
                          action='<?php echo URL::base(); ?>clusterManager/addFileToDam/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['dam']->id; ?>'>


                        <fieldset class="fieldset">

                            <div class="control-group">
                                <label for="mrid" class="control-label">Add a media resource</label>

                                <div class="controls">
                                    <select name='mrid' id='mrid'>
                                        <?php foreach ($templateData['files'] as $file) { ?>
                                            <option value="<?php echo $file->id; ?>"><?php echo $file->name; ?></option>
                                        <?php } ?>
                                    </select>

                                </div>
                            </div>
                        </fieldset>

                        <input class="btn btn-primary" type='submit' value='add'/>

                    </form>
                <?php } else { ?>
                    <p>no media resources to add</p>
                <?php } ?>

                <?php if ($templateData['dams'] != NULL and count($templateData['dams']) > 0) { ?>
                    <form method='post' class="form-horizontal"
                          action='<?php echo URL::base(); ?>clusterManager/addDamToDam/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['dam']->id; ?>'>


                        <fieldset class="fieldset">

                            <div class="control-group">
                                <label for="adamid" class="control-label">Add a data cluster to this data cluster</label>

                                <div class="controls">
                                    <select name='adamid' id='adamid'>
                                        <?php foreach ($templateData['dams'] as $dam) { ?>
                                            <option value="<?php echo $dam->id; ?>"><?php echo $dam->name; ?></option>
                                        <?php } ?>
                                    </select>

                                </div>
                            </div>
                        </fieldset>

                        <input class="btn btn-primary" type='submit' value='add'/>

                    </form>
                <?php } else { ?>
                    <p>no clusters to add</p>
                <?php } ?>


<?php } ?>

