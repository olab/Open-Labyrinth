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
if (isset($templateData['map']) && isset($templateData['node'])) { ?>

                <h1><?php echo __('Conditional rules for "') . $templateData['node']->title . '"'; ?></h1>

                            <p>previously: <?php echo $templateData['node']->conditional; ?></p>
                            <form action="<?php echo URL::base().'nodeManager/saveConditional/'.$templateData['node']->id.'/'.$templateData['countOfCondidtionFiled']; ?>" class="form-horizontal" method="post">
                                <fieldset>
                                    <div class="control-group">
                                        <label  class="control-label"><?php echo __('First add enough nodes'); ?><br/>Currently: <?php echo $templateData['countOfCondidtionFiled']; ?> nodes</label>

                                        <div class="controls">
                                            <a class="btn btn-primary" href="<?php echo URL::base().'nodeManager/addConditionalCount/'.$templateData['node']->id.'/'.$templateData['countOfCondidtionFiled']; ?>"><?php echo __('add one'); ?></a> <?php if($templateData['countOfCondidtionFiled'] > 0) { ?><a class="btn btn-primary" href="<?php echo URL::base().'nodeManager/deleteConditionalCount/'.$templateData['node']->id.'/'.$templateData['countOfCondidtionFiled']; ?>"><?php echo __('remove one'); ?></a><?php } ?>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label  class="control-label"><?php echo __('then select which nodes are required'); ?></label>

                                        <?php if(count($templateData['nodes']) > 0) { ?>
                                            <?php for($i = 0; $i < $templateData['countOfCondidtionFiled']; $i++) { ?>
                                        <div class="controls"><label><select name="el_<?php echo $i; ?>">
                                                    <?php foreach($templateData['nodes'] as $node) { ?>
                                                        <option value="<?php echo $node->id; ?>"><?php echo $node->title; ?> [<?php echo $node->id; ?>]</option>
                                                    <?php } ?>
                                                </select></label></div>

                                            <?php } } ?>
                                    </div>
                                    <div class="control-group">
                                        <label for="operator" class="control-label"><?php echo __('then select the Boolean operator \'and\' or \'or\' - note that \'and\' means all of these nodes must already have been visited and \'or\' means that at least one of these nodes should have been visited'); ?></label>

                                        <div class="controls">
                                            <select id="operator" name="operator"><option value="and">and</option><option value="or">or</option></select>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label for="abs" class="control-label"><?php echo __('and finally add the message given to the user if they haven\'t met these conditions'); ?></label>

                                        <div class="controls">
                                            <textarea name="abs" id="abs"><?php echo $templateData['node']->conditional_message; ?></textarea>
                                        </div>
                                    </div>



                                </fieldset>
<input class="btn btn-primary" type="submit" name="Submit" value="<?php echo __('submit'); ?>">
                            </form>
<?php } ?>