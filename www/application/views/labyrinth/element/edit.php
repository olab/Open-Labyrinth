<?php if (isset($templateData['map']) and isset($templateData['vpd'])) { ?>
    <script type="text/javascript">
        function jumpMenu(targ,selObj,restore) { 
            eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
            if (restore) selObj.selectedIndex=0;
        }
    </script>
    <table width="100%" height="100%" cellpadding='6'>
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('edit data element to labyrinth "') . $templateData['map']->name . '"'; ?></h4>
                <form action="<?php echo URL::base(); ?>elementManager/updateElement/<?php echo $templateData['map']->id; ?><?php echo '/'.$templateData['vpd']->id; ?>" method="post">
                    <table width="100%" border="0" cellspacing="6" bgcolor="#ffffff">
                        <tr><td align="left"><p>Type: <?php echo $templateData['vpd']->type->label; ?></p></td><td align="left"><p>ID: <?php echo $templateData['vpd']->id; ?></p></td></tr>
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
                                    echo '<tr>
                                            <td align="left"><p>VPDText type:</p></td>
                                            <td align="left">
                                                <select name="VPDTextType">
                                                    '.$textType.'
                                                </select>
                                            </td>
                                            </tr>
                                            <tr>
                                                <td align="left"><p>VPDText:</p></td>
                                                <td align="left">
                                                    <textarea name="VPDText" cols="50" rows="8">'.Arr::get($values, 'VPDText', '').'</textarea>
                                                </td>
                                            </tr>
                                            <tr><td colspan="2" align="left"><input type="submit" value="Submit"></td></tr></table></form>';
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
                                    echo '<tr><td align="left" colspan="2"><p>Structured demographic ...</p></td></tr>
                                          <tr><td align="left"><p>Demographics Type:</p></td>
                                          <td align="left"><select name="CoreDemogType">
                                          <option value="">Select ...</option>  
                                          '.$textType.'
                                          </select></td></tr>
                                          <tr><td align="left"><p>Demographic Text:</p></td>
                                          <td align="left"><input type="text" name="DemogText" value="'.Arr::get($values, 'DemogText', '').'" size="20"></td></tr>
                                          <tr><td colspan="2" align="left"><input type="submit" value="Submit" /></td></tr>
                                          </table></form><form method="post" action="'.URL::base().'elementManager/updateElement/'.$templateData['map']->id.'/'.$templateData['vpd']->id.'"><table width="100%" border="0" cellspacing="6" bgcolor="#ffffff">
                                          <tr><td align="left" colspan="2"><p>Or unstructured demographic ...</p></td></tr>
                                          <tr><td align="left"><p>Title:</p></td><td align="left"><input type="text" name="DemogTitle" value="'.Arr::get($values, 'DemogTitle', '').'" size="40"></p></td></tr>
                                          <tr><td align="left"><p>Description:</p></td><td align="left"><input type="text" name="DemogDesc" value="'.Arr::get($values, 'DemogDesc', '').'" size="60"></p></td></tr>
                                          <tr><td colspan="2" align="left"><input type="submit" value="Submit"/></td></tr></table></form>';
                                    break;
                                case 'AuthorDiagnoses':
                                    echo '<tr><td align="left"><p>Diagnosis title:</p></td><td align="left"><input type="text" name="aDiagTitle" value="'.Arr::get($values, 'aDiagTitle', '').'" size="40"></td></tr>
                                          <tr><td align="left"><p>Diagnosis description:</p></td><td align="left"><input type="text" name="aDiagDesc" value="'.Arr::get($values, 'aDiagDesc', '').'" size="60"></td></tr>
                                          <tr><td align="left" colspan="2"><input type="submit" value="Submit"/></td></tr></table></form>';
                                    break;
                                case 'Medication':
                                    echo '<tr><td align="left"><p>Medication title:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'MedicTitle', '').'" name="MedicTitle" size="40"></td></tr>
                                          <tr><td align="left"><p>Dose:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'MedicDose', '').'" name="MedicDose" size="40"></td></tr> 
                                          <tr><td align="left"><p>Route:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'MedicRoute', '').'" name="MedicRoute" size="40"></td></tr>
                                          <tr><td align="left"><p>Frequency:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'MedicFreq', '').'" name="MedicFreq" size="40"></td></tr>
                                          <tr><td align="left"><p>Medication item source:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'MedicSource', '').'" name="MedicSource" size="40"></td></tr>
                                          <tr><td align="left"><p>Medication item source ID:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'MedicSourceID', '').'" name="MedicSourceID" size="40"></td></tr>
                                          <tr><td align="left" colspan="2"><input type="submit" value="Submit" /></td></tr></table></form>';
                                    break;
                                case 'InterviewItem':
                                    $checked = '';
                                    if(Arr::get($values, 'trigger', FALSE)) {
                                        $checked = 'checked';
                                    }
                                    echo '<tr><td align="left"><p>Question:</p></td><td align="left"><input type="text" name="QAQuestion" value="'.Arr::get($values, 'QAQuestion', '').'" size="60"></td></tr>
                                          <tr><td align="left"><p>Answer:</p></td><td align="left"><input type="text" name="QAAnswer" value="'.Arr::get($values, 'QAAnswer', '').'" size="40"></td></tr>  
                                          <tr><td align="left"><p>Media ID:</p></td><td align="left"><select name="QAMedia">' . $filesString . '</select></td></tr>
                                          <tr><td align="left"><p>Trigger:</p></td><td align="left"><input type="checkbox" name="trigger" '.$checked.' /></td></tr>
                                          <tr><td align="left" colspan="2"><input type="submit" value="Submit" /></td></tr></table></form>';
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

                                    echo '<tr><td align="left"><p>Examination Name:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'ExamName', '').'" name="ExamName" size="60"></td></tr>
                                          <tr><td align="left"><p>Examination Description:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'ExamDesc', '').'" name="ExamDesc" size="60"></td></tr>
                                          <tr><td align="left"><p>Location on body - part/area:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'BodyPart', '').'" name="BodyPart" size="60"></td></tr>
                                          <tr><td align="left"><p>Action:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'Action', '').'" name="Action" size="60" /></td></tr>

                                          <tr><td align="left"><p>Orientation:</p></td><td align="left">
                                          <table width="100%"><tr><td align="left"><p><input type="radio" value="Proximal" name="ProxDist" '.$checked['Proximal'].' /> Proximal</p></td><td align="left"><p><input type="radio" value="Distal" name="ProxDist" '.$checked['Distal'].' /> Distal</p></td><td align="left"></td></tr>
                                          <tr><td align="left"><p><input type="radio" value="Right" name="RightLeft" '.$checked['Right'].' /> Right</p></td><td align="left"><p><input type="radio" value="Left" name="RightLeft" '.$checked['Left'].' /> Left</p></td><td align="left"></td></tr>
                                          <tr><td align="left"><p><input type="radio" value="Front" name="FrontBack" '.$checked['Front'].' /> Front</p></td><td align="left"><p><input type="radio" value="Back" name="FrontBack" '.$checked['Back'].' /> Back</p></td><td align="left"></td></tr>
                                          <tr><td align="left"><p><input type="radio" value="Inferior" name="InfSup" '.$checked['Inferior'].' /> Inferior</p></td><td align="left"><p><input type="radio" value="Superior" name="InfSup" '.$checked['Superior'].' /> Superior</p></td><td align="left"></td></tr></table>
                                          </td></tr>

                                          <tr><td align="left"><p>Finding Name:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'FindName', '').'" name="FindName" size="60"></td></tr>
                                          <tr><td align="left"><p>Finding Description:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'FindDesc', '').'" name="FindDesc" size="60"></td></tr>

                                          <tr><td align="left"><p>Media ID:</p></td><td align="left"><select name="FindMedia">' . $filesString . '</select></td></tr>"
                                          <tr><td align="left" colspan="2"><input type="submit" value="Submit" /></td></tr></table></form>';
                                    break;
                                case 'DiagnosticTest':
                                    
                                    echo '<tr><td align="left"><p>Test Name:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'TestName', '').'" name="TestName" size="60"></td></tr>
                                          <tr><td align="left"><p>Test Description:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'TestDesc', '').'" name="TestDesc" size="60"></td></tr>

                                          <tr><td align="left"><p>Units:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'TestUnits', '').'" name="TestUnits" size="60"></td></tr>
                                          <tr><td align="left"><p>Result:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'TestResult', '').'" name="TestResult" size="60"></td></tr>
                                          <tr><td align="left"><p>Normal value:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'TestNorm', '').'" name="TestNorm" size="60"></td></tr>

                                          <tr><td align="left"><p>Media ID:</p></td><td align="left"><select name="TestMedia">'.$filesString.'</select></td></tr>
                                          <tr><td align="left" colspan="2"><input type="submit" value="Submit" /></td></tr></table></form>';
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
                                    echo '<tr><td align="left"><p>Diagnosis title:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'DiagTitle', '').'" name="DiagTitle" size="40"></td></tr>
                                          <tr><td align="left"><p>Diagnosis description:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'DiagDesc', '').'" name="DiagDesc" size="60"></td></tr>
                                          <tr><td align="left"><p>Likelihood</p></td><td align="left"><select name="Likelihood">
                                          '.$textType.'
                                          </select></td></tr>
                                          <tr><td align="left" colspan="2"><input type="submit" value="Submit" /></td></tr></table></form>';
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
                                    echo '<tr><td align="left"><p>Intervention title:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'IntervTitle', '').'" name="IntervTitle" size="40"></td></tr>
                                          <tr><td align="left"><p>Intervention description:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'IntervDesc', '').'" name="IntervDesc" size="60"></td></tr>

                                          <tr><td align="left"><p>Medication title:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'iMedicTitle', '').'" name="iMedicTitle" size="40"></td></tr>
                                          <tr><td align="left"><p>Dose:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'iMedicDose', '').'" name="iMedicDose" size="40"></td></tr>
                                          <tr><td align="left"><p>Route:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'iMedicRoute', '').'" name="iMedicRoute" size="40"></td></tr>
                                          <tr><td align="left"><p>Frequency:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'iMedicFreq', '').'" name="iMedicFreq" size="40"></td></tr>
                                          <tr><td align="left"><p>Medication item source:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'iMedicSource', '').'" name="iMedicSource" size="40"></td></tr>
                                          <tr><td align="left"><p>Medication item source ID:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'iMedicSourceID', '').'" name="iMedicSourceID" size="40"></td></tr>

                                          <tr><td align="left"><p>Appropriateness</p></td><td align="left"><select name="Appropriateness">
                                          <option value=""/>select ...</option>
                                          '.$textType.'
                                          </select></td></tr>

                                          <tr><td align="left"><p>Results title:</p></td><td align="left"><input type="text" value="'.Arr::get($values, 'ResultTitle', '').'" name="ResultTitle" size="40"></td></tr>
                                          <tr><td align="left"><p>Results description:</p></td><td align="left"><input type="text" name="'.Arr::get($values, 'ResultDesc', '').'" name="ResultDesc" size="60"></td></tr>

                                          <tr><td align="left"><p>Media ID:</p></td><td align="left"><select name="iTestMedia">'.$filesString.'</select></td></tr>
                                          <tr><td align="left" colspan="2"><input type="submit" name="Submit" value="Submit" /></td></tr></table></form>';
                                    break;
                            }
                            ?>
                        </td>
                        </tr>
                    </table>
                <?php } ?>