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
if (isset($templateData['map']) and isset($templateData['vpd'])) { ?>
    <script type="text/javascript">
        function jumpMenu(targ,selObj,restore) { 
            eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
            if (restore) selObj.selectedIndex=0;
        }

        $('[name="demtype"]').live('click', function () {
            $('.patdem').attr('disabled', 'disabled');
            $(this).next(".patdem").removeAttr('disabled');
            $('[name="radio1"]').each(function () {
                $(this).attr('disabled', 'disabled').checkboxradio('refresh');
            });
        });
    </script>
<div class="page-header"><h1><?php echo __('edit data element to labyrinth "') . $templateData['map']->name . '"'; ?></h1></div>


                <form class="form-horizontal" action="<?php echo URL::base(); ?>elementManager/updateElement/<?php echo $templateData['map']->id; ?><?php echo '/'.$templateData['vpd']->id; ?>" method="post">

                        <h4>Type: <?php echo $templateData['vpd']->type->label; ?></h4>
                        <h4>ID: <?php echo $templateData['vpd']->id; ?></h4>
                            <?php
                            $values = array();
                            foreach($templateData['vpd']->elements as $element) {
                                $values[$element->key] = $element->value;
                            }
                            
                            $filesString = '<option value="">Select ...</option>';
                            if (isset($templateData['files']) and count($templateData['files']) > 0) {
                                foreach ($templateData['files'] as $file) {
                                    if($file->id == Arr::get($values, 'QAMedia', NULL)) {
                                        $filesString .= '<option value="' . $file->id . '" selected="">' . $file->name . '</option>';
                                    } else if($file->id == Arr::get($values, 'FindMedia', NULL)) {
                                        $filesString .= '<option value="' . $file->id . '" selected="">' . $file->name . '</option>';
                                    } else if($file->id == Arr::get($values, 'TestMedia', NULL)) {
                                        $filesString .= '<option value="' . $file->id . '" selected="">' . $file->name . '</option>';
                                    } else if($file->id == Arr::get($values, 'iTestMedia', NULL)) {
                                        $filesString .= '<option value="' . $file->id . '" selected="">' . $file->name . '</option>';
                                    } else {
                                        $filesString .= '<option value="' . $file->id . '">' . $file->name . '</option>';
                                    }
                                }
                            }
                            
                            switch ($templateData['vpd']->type->name) {
                                case 'VPDText':        
                                    $textType = '';
                                    $textTypes = array('narrative', 'chief complaint', 'history', 'problem', 'allergy');
                                    foreach($textTypes as $t) {
                                        if($t == Arr::get($values, 'VPDTextType', '')) {
                                            $textType .= '<option value="'.$t.'" selected="">'.$t.'</option>';
                                        } else {
                                            $textType .= '<option value="'.$t.'">'.$t.'</option>';
                                        }
                                    }
                                    echo '
                           <fieldset class="fieldset">

                            <div class="control-group">
                                <label for="VPDTextType" class="control-label">VPD Text type</label>

                                <div class="controls">


                                                <select name="VPDTextType" id="VPDTextType">
                                                    '.$textType.'
                                                </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="VPDText" class="control-label">VPD Text</label>

                                <div class="controls">


                                               <textarea name="VPDText" id="VPDText" cols="50" rows="8">'.Arr::get($values, 'VPDText', '').'</textarea>
                                </div>
                            </div>
                             </fieldset>';
                                    break;
                                case 'PatientDiagnoses':
                                    $textType = '';
                                    $textTypes = array('PatientID', 'Name', 'Age', 'Sex', 'Race', 'Species', 'Breed');
                                    foreach($textTypes as $t) {
                                        if($t == Arr::get($values, 'CoreDemogType', '')) {
                                            $textType .= '<option value="'.$t.'" selected="">'.$t.'</option>';
                                        } else {
                                            $textType .= '<option value="'.$t.'">'.$t.'</option>';
                                        }
                                    }
                                    echo '

  <input name="demtype" checked type="radio">

   <fieldset class="fieldset patdem">


                            <legend>Structured demographic</legend>
                            <div class="control-group">
                                <label for="CoreDemogType" class="control-label">Demographics Type</label>

                                <div class="controls">
                                              <select name="CoreDemogType" id="CoreDemogType">
                                         <option value="">Select ...</option>
                                          '.$textType.'
                                          </select>

                                </div>
                            </div>


                                <div class="control-group">
                                <label for="DemogText" class="control-label">Demographic Text</label>

                                <div class="controls">
                                           <input type="text" name="DemogText" id="DemogText" value="'.Arr::get($values, 'DemogText', '').'" >

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
                                            <input type="text" name="DemogTitle" value="'.Arr::get($values, 'DemogTitle', '').'" id="DemogTitle">

                                </div>
                            </div>                                                                                                <div class="control-group">
                                <label for="DemogDesc" class="control-label">Description</label>

                                <div class="controls">
                                           <input type="text" name="DemogDesc" id="DemogDesc" value="'.Arr::get($values, 'DemogDesc', '').'">
                                </div>
                            </div>
</input>
                                          </fieldset>';
                                    break;
                                case 'AuthorDiagnoses':
                                    echo '

                                    <fieldset class="fieldset">
                            <div class="control-group">
                                <label for="aDiagTitle" class="control-label">Diagnosis title</label>

                                <div class="controls">
                                           <input type="text" name="aDiagTitle" id="aDiagTitle" value="'.Arr::get($values, 'aDiagTitle', '').'" >

                                </div>
                            </div>

                            <div class="control-group">
                                <label for="aDiagDesc" class="control-label">Diagnosis description</label>

                                <div class="controls">
                                           <input type="text" name="aDiagDesc" id="aDiagDesc" value="'.Arr::get($values, 'aDiagDesc', '').'" >

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
                                           <input type="text" value="'.Arr::get($values, 'MedicTitle', '').'" id="MedicTitle" name="MedicTitle">

                                </div>
                            </div>
                                                       <div class="control-group">
                                <label for="MedicDose" class="control-label">Dose</label>

                                <div class="controls">
                                           <input type="text" value="'.Arr::get($values, 'MedicDose', '').'" id="MedicDose" name="MedicDose">
                                </div>
                            </div>
                                                       <div class="control-group">
                                <label for="MedicRoute" class="control-label">Route</label>

                                <div class="controls">
                                           <input type="text" value="'.Arr::get($values, 'MedicRoute', '').'" id="MedicRoute" name="MedicRoute">

                                </div>
                            </div>
                                                       <div class="control-group">
                                <label for="MedicFreq" class="control-label">Frequency</label>

                                <div class="controls">
                                           <input type="text" value="'.Arr::get($values, 'MedicFreq', '').'" name="MedicFreq" id="MedicFreq">
                                </div>
                            </div>
                                                       <div class="control-group">
                                <label for="MedicSource" class="control-label">Medication item source</label>

                                <div class="controls">
                                           <input type="text" value="'.Arr::get($values, 'MedicSource', '').'" name="MedicSource" id="MedicSource">

                                </div>
                            </div>
                                                       <div class="control-group">
                                <label for="MedicSourceID" class="control-label">Medication item source ID</label>

                                <div class="controls">
                                           <input type="text" value="'.Arr::get($values, 'MedicSourceID', '').'" name="MedicSourceID" id="MedicSourceID">

                                </div>
                            </div>

</fieldset>

                      ';
                                    break;
                                case 'InterviewItem':
                                    $checked = '';
                                    if(Arr::get($values, 'trigger', FALSE)) {
                                        $checked = 'checked';
                                    }
                                    echo '
                                    <fieldset class="fieldset">
                            <div class="control-group">
                                <label for="QAQuestion" class="control-label">Question</label>

                                <div class="controls">
                                          <input type="text" name="QAQuestion" value="'.Arr::get($values, 'QAQuestion', '').'" id="QAQuestion"/>

                                </div>
                            </div>

                            <div class="control-group">
                                <label for="QAAnswer" class="control-label">Answer</label>

                                <div class="controls">
                                          <input type="text" name="QAAnswer" id="QAAnswer" value="'.Arr::get($values, 'QAAnswer', '').'"/>

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
                                          <input type="checkbox" id="trigger" '.$checked.' name="trigger" checked />

                                </div>
                            </div>

                </fieldset>';
                                    break;
                                case 'PhysicalExam':
                                    $checked = array();
                                    
                                    if(Arr::get($values, 'ProxDist', '') == 'Proximal') {
                                        $checked['Proximal'] = 'checked';
                                        $checked['Distal'] = '';
                                    } else if(Arr::get($values, 'ProxDist', '') == 'Distal') {
                                        $checked['Proximal'] = '';
                                        $checked['Distal'] = 'checked';
                                    } else {
                                        $checked['Proximal'] = '';
                                        $checked['Distal'] = '';
                                    }
                                    
                                    if(Arr::get($values, 'RightLeft', '') == 'Right') {
                                        $checked['Right'] = 'checked';
                                        $checked['Left'] = '';
                                    } else if(Arr::get($values, 'RightLeft', '') == 'Left') {
                                        $checked['Right'] = '';
                                        $checked['Left'] = 'checked';
                                    } else {
                                        $checked['Right'] = '';
                                        $checked['Left'] = '';
                                    }
                                    
                                    if(Arr::get($values, 'FrontBack', '') == 'Front') {
                                        $checked['Front'] = 'checked';
                                        $checked['Back'] = '';
                                    } else if(Arr::get($values, 'FrontBack', '') == 'Back') {
                                        $checked['Front'] = '';
                                        $checked['Back'] = 'checked';
                                    } else {
                                        $checked['Front'] = '';
                                        $checked['Back'] = '';
                                    }
                                    
                                    if(Arr::get($values, 'InfSup', '') == 'Inferior') {
                                        $checked['Inferior'] = 'checked';
                                        $checked['Superior'] = '';
                                    } else if(Arr::get($values, 'InfSup', '') == 'Superior') {
                                        $checked['Inferior'] = '';
                                        $checked['Superior'] = 'checked';
                                    } else {
                                        $checked['Inferior'] = '';
                                        $checked['Superior'] = '';
                                    }

                                    echo '

                                    <fieldset class="fieldset">
                            <div class="control-group">
                                <label for="ExamName" class="control-label">Examination Name</label>

                                <div class="controls">
                                          <input type="text" value="'.Arr::get($values, 'ExamName', '').'" name="ExamName" id="ExamName">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="ExamDesc" class="control-label">Examination Description</label>

                                <div class="controls">
                                          <input type="text" value="'.Arr::get($values, 'ExamDesc', '').'" name="ExamDesc" id="ExamDesc">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="BodyPart" class="control-label">Location on body - part/area</label>

                                <div class="controls">
                                          <input type="text" value="'.Arr::get($values, 'BodyPart', '').'" name="BodyPart" id="BodyPart">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="Action" class="control-label">Action</label>

                                <div class="controls">
                                         <input type="text" value="'.Arr::get($values, 'Action', '').'" name="Action" id="Action" />
                                </div>
                            </div>
                            <h5>Orientation</h5>
                 <div class="control-group">
                    <label class="control-label">Proximity</label>

                    <div class="controls">

                        <label class="radio">
                            <input type="radio" value="Proximal" '.$checked['Proximal'].' name="ProxDist" />
                            Proximal
                        </label>
                        <label class="radio">
                            <input type="radio" value="Distal" '.$checked['Distal'].' name="ProxDist" />
                            Distal
                        </label>

                    </div>
                </div>

                                <div class="control-group">
                    <label class="control-label">Sagittal Plane</label>

                    <div class="controls">

                        <label class="radio">
                            <input type="radio" '.$checked['Right'].' value="Right" name="RightLeft" />
                            Right
                        </label>
                        <label class="radio">
                            <input type="radio" '.$checked['Left'].' value="Left" name="RightLeft" />
                            Left
                        </label>

                    </div>
                </div>


                                <div class="control-group">
                    <label class="control-label">Coronal Plane</label>

                    <div class="controls">

                        <label class="radio">
                            <input type="radio" '.$checked['Front'].' value="Front" name="FrontBack" />
                            Front
                        </label>
                        <label class="radio">
                            <input type="radio" '.$checked['Back'].' value="Back" name="FrontBack" />
                            Back
                        </label>

                    </div>
                </div>

                                <div class="control-group">
                    <label class="control-label">Transverse Plane</label>

                    <div class="controls">

                        <label class="radio">
                            <input type="radio" '.$checked['Inferior'].'  value="Inferior" name="InfSup" />
                            Inferior
                        </label>
                        <label class="radio">
                            <input type="radio" '.$checked['Superior'].' value="Superior" name="InfSup" />
                            Superior
                        </label>

                    </div>
                </div>


                            <div class="control-group">
                                <label for="FindName" class="control-label">Finding Name</label>

                                <div class="controls">
                                         <input type="text" value="'.Arr::get($values, 'FindName', '').'" name="FindName" id="FindName">
                                </div>
                            </div>
                                                        <div class="control-group">
                                <label for="FindDesc" class="control-label">Finding Description</label>

                                <div class="controls">
                                        <input type="text" value="'.Arr::get($values, 'FindDesc', '').'" name="FindDesc" id="FindDesc">
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
                                         <input type="text" value="'.Arr::get($values, 'TestName', '').'" name="TestName" id="TestName">
                                </div>
                </div>
                <div class="control-group">
                                <label for="TestDesc" class="control-label">Test Description</label>

                                <div class="controls">
                                         <input type="text" value="'.Arr::get($values, 'TestDesc', '').'" name="TestDesc" id="TestDesc">
                                </div>
                </div>
                <div class="control-group">
                                <label for="TestUnits" class="control-label">Units</label>

                                <div class="controls">
                                         <input type="text" value="'.Arr::get($values, 'TestUnits', '').'" name="TestUnits" id="TestUnits">
                                </div>
                </div>

                <div class="control-group">
                                <label for="TestResult" class="control-label">Result</label>

                                <div class="controls">
                                         <input type="text" value="'.Arr::get($values, 'TestResult', '').'" id="TestResult" name="TestResult">
                                </div>
                </div>
                <div class="control-group">
                                <label for="TestNorm" class="control-label">Normal value</label>

                                <div class="controls">
                                         <input type="text" value="'.Arr::get($values, 'TestNorm', '').'" name="TestNorm" id="TestNorm">
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
                                    $textType = '';
                                    $textTypes = array('high', 'medium', 'low', 'none');
                                    foreach($textTypes as $t) {
                                        if($t == Arr::get($values, 'Likelihood', '')) {
                                            $textType .= '<option value="'.$t.'" selected="">'.$t.'</option>';
                                        } else {
                                            $textType .= '<option value="'.$t.'">'.$t.'</option>';
                                        }
                                    }
                                    echo '
                                    <fieldset class="fieldset">
                            <div class="control-group">
                                <label for="DiagTitle" class="control-label">Diagnosis title</label>

                                <div class="controls">
                                          <input type="text" value="'.Arr::get($values, 'DiagTitle', '').'" id="DiagTitle" name="DiagTitle" >
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="DiagDesc" class="control-label">Diagnosis description</label>

                                <div class="controls">
                                        <input type="text" id="DiagDesc" value="'.Arr::get($values, 'DiagDesc', '').'" name="DiagDesc">
                                </div>
                            </div>
                           <div class="control-group">
                                <label for="Likelihood" class="control-label">Likelihood</label>

                                <div class="controls">
                                          <select name="Likelihood" id="Likelihood">
                                         '.$textType.'
                                          </select>
                                </div>
                            </div>
                          </fieldset>';
                                    break;
                                case 'Intervention':
                                    $textType = '';
                                    $textTypes = array('always', 'ok', 'never', 'none');
                                    foreach($textTypes as $t) {
                                        if($t == Arr::get($values, 'Appropriateness', '')) {
                                            $textType .= '<option value="'.$t.'" selected="">'.$t.'</option>';
                                        } else {
                                            $textType .= '<option value="'.$t.'">'.$t.'</option>';
                                        }
                                    }
                                    echo '
            <fieldset>
                             <div class="control-group">
                                <label for="IntervTitle" class="control-label">Intervention title</label>

                                <div class="controls">
                                       <input type="text" value="'.Arr::get($values, 'IntervTitle', '').'" name="IntervTitle" id="IntervTitle">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="IntervDesc" class="control-label">Intervention description</label>

                                <div class="controls">
                                        <input type="text" id="IntervDesc" value="'.Arr::get($values, 'IntervDesc', '').'" name="IntervDesc">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="iMedicTitle" class="control-label">Medication title</label>

                                <div class="controls">
                                        <input type="text" id="iMedicTitle" value="'.Arr::get($values, 'iMedicTitle', '').'" name="iMedicTitle">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="iMedicDose" class="control-label">Dose</label>

                                <div class="controls">
                                        <input type="text" id="iMedicDose" value="'.Arr::get($values, 'iMedicDose', '').'" name="iMedicDose">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="iMedicRoute" class="control-label">Route</label>

                                <div class="controls">
                                        <input type="text" id="iMedicRoute" value="'.Arr::get($values, 'iMedicRoute', '').'" name="iMedicRoute">
                                </div>
                            </div>
                                                                                                                                                                       <div class="control-group">
                                <label for="iMedicFreq" class="control-label">Frequency</label>

                                <div class="controls">
                                        <input type="text" id="iMedicFreq" value="'.Arr::get($values, 'iMedicFreq', '').'" name="iMedicFreq">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="iMedicSource" class="control-label">Medication item source</label>

                                <div class="controls">
                                        <input type="text" id="iMedicSource" value="'.Arr::get($values, 'iMedicSource', '').'" name="iMedicSource">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="iMedicSourceID" class="control-label">Medication item source ID</label>

                                <div class="controls">
                                        <input type="text" id="iMedicSourceID" value="'.Arr::get($values, 'iMedicSourceID', '').'" name="iMedicSourceID">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="Appropriateness" class="control-label">Appropriateness</label>

                                <div class="controls">
                                        <select name="Appropriateness" id="Appropriateness">
                                          <option value=""/>select ...</option>
                                          '.$textType.'
                                          </select>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="ResultTitle" class="control-label">Results title</label>

                                <div class="controls">
                                        <input type="text" id="ResultTitle" value="'.Arr::get($values, 'ResultTitle', '').'" name="ResultTitle">
                                </div>
                            </div>
                                                                                                                                                                       <div class="control-group">
                                <label for="ResultDesc" class="control-label">Results description</label>

                                <div class="controls">
                                        <input type="text" id="ResultDesc" value="'.Arr::get($values, 'ResultDesc', '').'" name="ResultDesc">
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

                        <div class="form-actions">
                            <div class="pull-right">
                                <input class="btn btn-primary btn-large" type="submit" value="Submit">
                            </div>
                        </div>


    </form>

                <?php } ?>