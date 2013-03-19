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

<h1><?php echo __('Step 2. What kind of VP do you what?'); ?></h1>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="wizard_body span9">

            <div class="row-fluid">
                <div class="span4">
                    <a href="javascript:void(0);" id="6" class="wizard_button li <?php if (isset($templateData['map']) && $templateData['map']->type_id == 6) echo 'selected'; ?>"><h2>Linear</h2>
                        <img src="<?php echo URL::base(); ?>images/labyrinth_preview/leniar.png" />
                    </a>

                </div><!--/span-->
                <div class="span4">
                    <a href="javascript:void(0);" id="7" class="wizard_button li <?php if (isset($templateData['map']) && $templateData['map']->type_id == 7) echo 'selected'; ?>"><h2>HEIDR</h2>
                        <img  src="<?php echo URL::base(); ?>images/labyrinth_preview/heidr.png" />
                    </a>

                </div><!--/span-->
                <div class="span4">
                    <a href="javascript:void(0);" id="8" class="wizard_button li <?php if (isset($templateData['map']) && $templateData['map']->type_id == 8) echo 'selected'; ?>"><h2>Semi-linear</h2>
                        <img  src="<?php echo URL::base(); ?>images/labyrinth_preview/semi-leniar.png" />
                    </a>

                </div><!--/span-->
            </div><!--/row-->
            <div class="row-fluid">
                <div class="span4">
                    <a href="javascript:void(0);" id="9" class="wizard_button li <?php if (isset($templateData['map']) && $templateData['map']->type_id == 9) echo 'selected'; ?>"><h2>Branched</h2>
                        <img src="<?php echo URL::base(); ?>images/labyrinth_preview/branch.png" />
                    </a>
                </div><!--/span-->
                <div class="span4">
                    <a href="javascript:void(0);" id="10" class="wizard_button li <?php if (isset($templateData['map']) && $templateData['map']->type_id == 10) echo 'selected'; ?>"><h2>Make My Own</h2>
                        <img src="<?php echo URL::base(); ?>images/labyrinth_preview/own.png" />
                    </a>

                </div><!--/span-->
            </div><!--/row-->
        </div><!--/span-->
    </div><!--/row-->

    <footer>
        <div class="pull-right">
            <form class="form-horizontal" id="step1_form" action="<?php echo URL::base() . 'labyrinthManager/caseWizard/2/labyrinthType/' . $templateData['map']->id ?>" method="post">
                <input autocomplete="off" type="hidden" name="labyrinthType" id="labyrinthType" value="" />
                <a href="javascript:void(0)"  id="step1_w_button" class="btn btn-primary wizard_button">Step 3(4) - Add Story</a>
            </form>
        </div>
        <a href="<?php echo URL::base() . 'labyrinthManager/caseWizard/1/' . $templateData['map']->id; ?>" style="float:left;" class="btn btn-primary wizard_button">Return to step 1</a>
    </footer>

</div><!--/.fluid-container-->
