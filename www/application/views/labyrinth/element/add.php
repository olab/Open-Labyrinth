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
    <script type="text/javascript">
        function jumpMenuGo(targ, selObj, restore) {
            eval(targ + ".location='" + selObj.options[selObj.selectedIndex].value + "'");
            if (restore) selObj.selectedIndex = 0;
        }
        $('[name="demtype"]').live('click', function () {
            $('.patdem').attr('disabled', 'disabled');
            $(this).next(".patdem").removeAttr('disabled');
            $('[name="radio1"]').each(function () {
                $(this).attr('disabled', 'disabled').checkboxradio('refresh');
            });
        });

    </script>
<div class="page-header">
    <h1><?php echo __('Add data element to labyrinth "') . $templateData['map']->name . '"'; ?></h1>
    </div>
    <form class="form-horizontal">
        <fieldset class="fieldset">
            <div class="control-group">
                <label for="jumpMenu" class="control-label"><?php echo __('VPD Element Type'); ?></label>

                <div class="controls">
                    <select name="jumpMenu" id="jumpMenu" onchange="jumpMenuGo('parent',this,0);">
                        <option
                            value="<?php echo URL::base(); ?>elementManager/index/<?php echo $templateData['map']->id; ?>">
                            select ...
                        </option>
                        <?php if (isset($templateData['types']) and count($templateData['types']) > 0) { ?>
                            <?php foreach ($templateData['types'] as $type) { ?>
                                <option
                                    value="<?php echo URL::base(); ?>elementManager/addNewElement/<?php echo $templateData['map']->id; ?>/<?php echo $type->name; ?>" <?php if (isset($templateData['add_type']) and $templateData['add_type'] == $type->name) echo 'selected=""'; ?>><?php echo $type->label; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </fieldset>
    </form>
  <?php if (isset($templateData['add_type'])) { ?>
    <form class="form-horizontal"
          action="<?php echo URL::base(); ?>elementManager/saveElement/<?php echo $templateData['map']->id; ?><?php if (isset($templateData['add_type'])) echo '/' . $templateData['add_type']; ?>"
          method="post">
    <h2>Type: <?php if (isset($templateData['add_type'])) echo $templateData['add_type']; ?></h2>

        <?php
        $filesString = '<option value="">Select ...</option>';
        if (isset($templateData['files']) and count($templateData['files']) > 0) {
            foreach ($templateData['files'] as $file) {
                $filesString .= '<option value="' . $file->id . '">' . $file->name . '</option>';
            }
        }
        switch ($templateData['add_type']) {
            case 'VPDText':
                echo '
                         <fieldset class="fieldset">

                            <div class="control-group">
                                <label for="VPDTextType" class="control-label">VPD Text type</label>

                                <div class="controls">


                                                <select name="VPDTextType" id="VPDTextType">
                                                    <option value="narrative">narrative</option>
                                                    <option value="chief complaint">chief complaint</option>
                                                    <option value="history">history</option>
                                                    <option value="problem">problem</option>
                                                    <option value="allergy">allergy</option>
                                                </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="VPDText" class="control-label">VPD Text</label>

                                <div class="controls">


                                               <textarea name="VPDText" id="VPDText" cols="50" rows="8"></textarea>
                                </div>
                            </div>
                             </fieldset>';
                break;
            case 'PatientDiagnoses':
                echo '

   <input name="demtype" checked type="radio">

   <fieldset class="fieldset patdem">


                            <legend>Structured demographic</legend>
                            <div class="control-group">
                                <label for="CoreDemogType" class="control-label">Demographics Type</label>

                                <div class="controls">
                                              <select name="CoreDemogType" id="CoreDemogType">
                                          <option value="">Select ...</option>
                                          <option value="PatientID">Patient ID</option>
                                          <option value="Name">Name</option>
                                          <option value="Age">Age</option>
                                          <option value="Sex">Sex</option>
                                          <option value="Race">Race</option>
                                          <option value="Species">Species</option>
                                          <option value="Breed">Breed</option>
                                          </select>

                                </div>
                            </div>


                                <div class="control-group">
                                <label for="DemogText" class="control-label">Demographic Text</label>

                                <div class="controls">
                                           <input type="text" name="DemogText" id="DemogText" value="" >

                                </div>
                            </div></fieldset>
</input>



<input name="demtype" type="radio">
<fieldset disabled class="fieldset patdem">
                                     <legend>
                                          Unstructured demographic</legend>
                                                                    <div class="control-group">
                                <label for="DemogTitle" class="control-label">Title</label>

                                <div class="controls">
                                            <input type="text" name="DemogTitle" value="" id="DemogTitle">

                                </div>
                            </div>

                                                                                                        <div class="control-group">
                                <label for="DemogDesc" class="control-label">Description</label>

                                <div class="controls">
                                           <input type="text" name="DemogDesc" id="DemogDesc" value="">

                                </div>
                            </div>

</input>


                                          </fieldset>
                                          ';
                break;
            case 'AuthorDiagnoses':
                echo '
<fieldset class="fieldset">
                            <div class="control-group">
                                <label for="aDiagTitle" class="control-label">Diagnosis title</label>

                                <div class="controls">
                                           <input type="text" name="aDiagTitle" id="aDiagTitle" value="" >

                                </div>
                            </div>

                            <div class="control-group">
                                <label for="aDiagDesc" class="control-label">Diagnosis description</label>

                                <div class="controls">
                                           <input type="text" name="aDiagDesc" id="aDiagDesc" value="" >

                                </div>
                            </div>
</fieldset>';
                break;
            case 'Medication':
                echo '
<fieldset class="fieldset">

                           <div class="control-group">
                                <label for="MedicTitle" class="control-label">Medication title</label>

                                <div class="controls">
                                           <input type="text" value="" id="MedicTitle" name="MedicTitle">

                                </div>
                            </div>
                                                       <div class="control-group">
                                <label for="MedicDose" class="control-label">Dose</label>

                                <div class="controls">
                                           <input type="text" value="" id="MedicDose" name="MedicDose">
                                </div>
                            </div>
                                                       <div class="control-group">
                                <label for="MedicRoute" class="control-label">Route</label>

                                <div class="controls">
                                           <input type="text" value="" id="MedicRoute" name="MedicRoute">

                                </div>
                            </div>
                                                       <div class="control-group">
                                <label for="MedicFreq" class="control-label">Frequency</label>

                                <div class="controls">
                                           <input type="text" value="" name="MedicFreq" id="MedicFreq">
                                </div>
                            </div>
                                                       <div class="control-group">
                                <label for="MedicSource" class="control-label">Medication item source</label>

                                <div class="controls">
                                           <input type="text" value="" name="MedicSource" id="MedicSource">

                                </div>
                            </div>
                                                       <div class="control-group">
                                <label for="MedicSourceID" class="control-label">Medication item source ID</label>

                                <div class="controls">
                                           <input type="text" value="" name="MedicSourceID" id="MedicSourceID">

                                </div>
                            </div>

</fieldset> ';
                break;
            case 'InterviewItem':
                echo '<fieldset class="fieldset">
                            <div class="control-group">
                                <label for="QAQuestion" class="control-label">Question</label>

                                <div class="controls">
                                          <input type="text" name="QAQuestion" value="" id="QAQuestion"/>

                                </div>
                            </div>

                            <div class="control-group">
                                <label for="QAAnswer" class="control-label">Answer</label>

                                <div class="controls">
                                          <input type="text" name="QAAnswer" id="QAAnswer" value=""/>

                                </div>
                            </div>

                                                        <div class="control-group">
                                <label for="QAMedia" class="control-label">Media ID</label>

                                <div class="controls">
                                          <select name="QAMedia" id="QAMedia">' . $filesString . '</select>

                                </div>
                            </div>

                                                        <div class="control-group">
                                <label for="trigger" class="control-label">Trigger</label>

                                <div class="controls">
                                          <input type="checkbox" id="trigger" name="trigger" checked />

                                </div>
                            </div>

                </fieldset>';
                break;
            case 'PhysicalExam':
                echo '
                <fieldset class="fieldset">
                            <div class="control-group">
                                <label for="ExamName" class="control-label">Examination Name</label>

                                <div class="controls">
                                          <input type="text" value="" name="ExamName" id="ExamName">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="ExamDesc" class="control-label">Examination Description</label>

                                <div class="controls">
                                          <input type="text" value="" name="ExamDesc" id="ExamDesc">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="BodyPart" class="control-label">Location on body - part/area</label>

                                <div class="controls">
                                          <input type="text" value="" name="BodyPart" id="BodyPart">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="Action" class="control-label">Action</label>

                                <div class="controls">
                                         <input type="text" value="" name="Action" id="Action" />
                                </div>
                            </div>
                            <h5>Orientation</h5>
                 <div class="control-group">
                    <label class="control-label">Proximity</label>

                    <div class="controls">

                        <label class="radio">
                            <input type="radio" value="Proximal" name="ProxDist" />
                            Proximal
                        </label>
                        <label class="radio">
                            <input type="radio" value="Distal" name="ProxDist" />
                            Distal
                        </label>

                    </div>
                </div>

                                <div class="control-group">
                    <label class="control-label">Sagittal Plane</label>

                    <div class="controls">

                        <label class="radio">
                            <input type="radio" value="Right" name="RightLeft" />
                            Right
                        </label>
                        <label class="radio">
                            <input type="radio" value="Left" name="RightLeft" />
                            Left
                        </label>

                    </div>
                </div>


                                <div class="control-group">
                    <label class="control-label">Coronal Plane</label>

                    <div class="controls">

                        <label class="radio">
                            <input type="radio" value="Front" name="FrontBack" />
                            Front
                        </label>
                        <label class="radio">
                            <input type="radio" value="Back" name="FrontBack" />
                            Back
                        </label>

                    </div>
                </div>

                                <div class="control-group">
                    <label class="control-label">Transverse Plane</label>

                    <div class="controls">

                        <label class="radio">
                            <input type="radio" value="Inferior" name="InfSup" />
                            Inferior
                        </label>
                        <label class="radio">
                            <input type="radio" value="Superior" name="InfSup" />
                            Superior
                        </label>

                    </div>
                </div>


                            <div class="control-group">
                                <label for="FindName" class="control-label">Finding Name</label>

                                <div class="controls">
                                         <input type="text" value="" name="FindName" id="FindName">
                                </div>
                            </div>
                                                        <div class="control-group">
                                <label for="FindDesc" class="control-label">Finding Description</label>

                                <div class="controls">
                                        <input type="text" value="" name="FindDesc" id="FindDesc">
                                </div>
                            </div>
                                                        <div class="control-group">
                                <label for="FindMedia" class="control-label">Media ID</label>

                                <div class="controls">
                                         <select id="FindMedia" name="FindMedia">' . $filesString . '</select>
                                </div>
                            </div>


     </fieldset>';
                break;
            case 'DiagnosticTest':
                echo '
            <fieldset class="fieldset">

                <div class="control-group">
                                <label for="TestName" class="control-label">Test Name</label>

                                <div class="controls">
                                         <input type="text" value="" name="TestName" id="TestName">
                                </div>
                </div>
                <div class="control-group">
                                <label for="TestDesc" class="control-label">Test Description</label>

                                <div class="controls">
                                         <input type="text" value="" name="TestDesc" id="TestDesc">
                                </div>
                </div>
                <div class="control-group">
                                <label for="TestUnits" class="control-label">Units</label>

                                <div class="controls">
                                         <input type="text" value="" name="TestUnits" id="TestUnits">
                                </div>
                </div>

                <div class="control-group">
                                <label for="TestResult" class="control-label">Result</label>

                                <div class="controls">
                                         <input type="text" value="" id="TestResult" name="TestResult">
                                </div>
                </div>
                <div class="control-group">
                                <label for="TestNorm" class="control-label">Normal value</label>

                                <div class="controls">
                                         <input type="text" value="" name="TestNorm" id="TestNorm">
                                </div>
                </div>
                <div class="control-group">
                                <label for="TestMedia" class="control-label">Media ID</label>

                                <div class="controls">
                                         <select id="TestMedia" name="TestMedia">' . $filesString . '</select>
                                </div>
                 </div>
            </fieldset>';
                break;
            case 'DifferentialDiagnostic':
                echo '
<fieldset class="fieldset">
                            <div class="control-group">
                                <label for="DiagTitle" class="control-label">Diagnosis title</label>

                                <div class="controls">
                                          <input type="text" value="" id="DiagTitle" name="DiagTitle" >
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="DiagDesc" class="control-label">Diagnosis description</label>

                                <div class="controls">
                                        <input type="text" id="DiagDesc" value="" name="DiagDesc">
                                </div>
                            </div>
                           <div class="control-group">
                                <label for="Likelihood" class="control-label">Likelihood</label>

                                <div class="controls">
                                          <select name="Likelihood" id="Likelihood">
                                          <option value=""/>Select ...</option>
                                          <option value="high"/>high</option>
                                          <option value="medium"/>medium</option>
                                          <option value="low"/>low</option>
                                          <option value="none"/>none</option>
                                          </select>
                                </div>
                            </div>
                          </fieldset>

                                     ';
                break;
            case 'Intervention':
                echo '

                <fieldset>
                             <div class="control-group">
                                <label for="IntervTitle" class="control-label">Intervention title</label>

                                <div class="controls">
                                       <input type="text" value="" name="IntervTitle" id="IntervTitle">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="IntervDesc" class="control-label">Intervention description</label>

                                <div class="controls">
                                        <input type="text" id="IntervDesc" value="" name="IntervDesc">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="iMedicTitle" class="control-label">Medication title</label>

                                <div class="controls">
                                        <input type="text" id="iMedicTitle" value="" name="iMedicTitle">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="iMedicDose" class="control-label">Dose</label>

                                <div class="controls">
                                        <input type="text" id="iMedicDose" value="" name="iMedicDose">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="iMedicRoute" class="control-label">Route</label>

                                <div class="controls">
                                        <input type="text" id="iMedicRoute" value="" name="iMedicRoute">
                                </div>
                            </div>
                                                                                                                                                                       <div class="control-group">
                                <label for="iMedicFreq" class="control-label">Frequency</label>

                                <div class="controls">
                                        <input type="text" id="iMedicFreq" value="" name="iMedicFreq">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="iMedicSource" class="control-label">Medication item source</label>

                                <div class="controls">
                                        <input type="text" id="iMedicSource" value="" name="iMedicSource">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="iMedicSourceID" class="control-label">Medication item source ID</label>

                                <div class="controls">
                                        <input type="text" id="iMedicSourceID" value="" name="iMedicSourceID">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="Appropriateness" class="control-label">Appropriateness</label>

                                <div class="controls">
                                        <select name="Appropriateness" id="Appropriateness">
                                          <option value=""/>select ...</option>
                                          <option value="always"/>always</option>
                                          <option value="ok"/>ok</option>
                                          <option value="never"/>never</option>
                                          <option value="none"/>none</option>
                                          </select>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="ResultTitle" class="control-label">Results title</label>

                                <div class="controls">
                                        <input type="text" id="ResultTitle" value="" name="ResultTitle">
                                </div>
                            </div>
                                                                                                                                                                       <div class="control-group">
                                <label for="ResultDesc" class="control-label">Results description</label>

                                <div class="controls">
                                        <input type="text" id="ResultDesc" value="" name="ResultDesc">
                                </div>
                            </div>
                <div class="control-group">
                                <label for="iTestMedia" class="control-label">Media ID</label>

                                <div class="controls">
                                         <select id="iTestMedia" name="iTestMedia">' . $filesString . '</select>
                                </div>
                 </div>


                </fieldset>';
                break;
        }
        ?>
    <div class="control-group">
        <label class="control-label"><?php echo __('Private'); ?>
        </label>
        <div class="controls">
            <input type="checkbox" name="Private">
        </div>
    </div>
    <div class="form-actions">
        <div class="pull-right">
        <input class="btn btn-primary btn-large" type="submit" value="Add"></div></div>
    </form>
    <?php } ?>



<?php } ?>