<?php if (isset($templateData['map'])) { ?>
    <script type="text/javascript">
        function jumpMenu(targ,selObj,restore) { 
            eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
            if (restore) selObj.selectedIndex=0;
        }
    </script>
    <table width="100%" height="100%" cellpadding='6'>
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('add data element to labyrinth "') . $templateData['map']->name . '"'; ?></h4>
                <table width="100%" border="0" cellspacing="6">
                    <tr>
                        <td align="right"><p>VPD Element Type</p></td>
                        <td align="left">
                            <select name="jumpMenu" id="jumpMenu" onchange="jumpMenu('parent',this,0)">
                                <option value="<?php echo URL::base(); ?>elementManager/index/<?php echo $templateData['map']->id; ?>">select ...</option>
                                <?php if (isset($templateData['types']) and count($templateData['types']) > 0) { ?>
                                    <?php foreach ($templateData['types'] as $type) { ?>
                                        <option value="<?php echo URL::base(); ?>elementManager/addNewElement/<?php echo $templateData['map']->id; ?>/<?php echo $type->name; ?>" <?php if (isset($templateData['add_type']) and $templateData['add_type'] == $type->name) echo 'selected=""'; ?>><?php echo $type->label; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <form action="<?php echo URL::base(); ?>elementManager/saveElement/<?php echo $templateData['map']->id; ?><?php if(isset($templateData['add_type'])) echo '/'.$templateData['add_type']; ?>" method="post">
                    <table width="100%" border="0" cellspacing="6" bgcolor="#ffffff">
                        <tr><td align="left"><p>Type: <?php if(isset($templateData['add_type'])) echo $templateData['add_type']; ?></p></td><td align="left"><p>ID: </p></td></tr>
                        <?php if (isset($templateData['add_type'])) { ?>
                            <?php
                            $filesString = '<option value="">Select ...</option>';
                            if (isset($templateData['files']) and count($templateData['files']) > 0) {
                                foreach ($templateData['files'] as $file) {
                                    $filesString .= '<option value="' . $file->id . '">' . $file->name . '</option>';
                                }
                            }
                            switch ($templateData['add_type']) {
                                case 'VPDText':
                                    echo '<tr>
                                            <td align="left"><p>VPDText type:</p></td>
                                            <td align="left">
                                                <select name="VPDTextType">
                                                    <option value="narrative">narrative</option>
                                                    <option value="chief complaint">chief complaint</option>
                                                    <option value="history">history</option>
                                                    <option value="problem">problem</option>
                                                    <option value="allergy">allergy</option>
                                                </select>
                                            </td>
                                            </tr>
                                            <tr>
                                                <td align="left"><p>VPDText:</p></td>
                                                <td align="left">
                                                    <textarea name="VPDText" cols="50" rows="8"></textarea>
                                                </td>
                                            </tr>
                                            <tr><td colspan="2" align="left"><input type="submit" value="Submit"></td></tr></table></form>';
                                    break;
                                case 'PatientDiagnoses':
                                    echo '<tr><td align="left" colspan="2"><p>Structured demographic ...</p></td></tr>
                                          <tr><td align="left"><p>Demographics Type:</p></td>
                                          <td align="left"><select name="CoreDemogType">
                                          <option value="">Select ...</option>  
                                          <option value="PatientID">Patient ID</option>
                                          <option value="Name>Name</option>
                                          <option value="Age">Age</option>
                                          <option value="Sex">Sex</option>
                                          <option value="Race">Race</option>
                                          <option value="Species">Species</option>
                                          <option value="Breed">Breed</option>
                                          </select></td></tr>
                                          <tr><td align="left"><p>Demographic Text:</p></td>
                                          <td align="left"><input type="text" name="DemogText" value="" size="20"></td></tr>
                                          <tr><td colspan="2" align="left"><input type="submit" value="Submit" /></td></tr>
                                          </table></form><form method="post" action="'.URL::base().'elementManager/saveElement/'.$templateData['map']->id.'/'.$templateData['add_type'].'"><table width="100%" border="0" cellspacing="6" bgcolor="#ffffff">
                                          <tr><td align="left" colspan="2"><p>Or unstructured demographic ...</p></td></tr>
                                          <tr><td align="left"><p>Title:</p></td><td align="left"><input type="text" name="DemogTitle" value="" size="40"></p></td></tr>
                                          <tr><td align="left"><p>Description:</p></td><td align="left"><input type="text" name="DemogDesc" value="" size="60"></p></td></tr>
                                          <tr><td colspan="2" align="left"><input type="submit" value="Submit"/></td></tr></table></form>';
                                    break;
                                case 'AuthorDiagnoses':
                                    echo '<tr><td align="left"><p>Diagnosis title:</p></td><td align="left"><input type="text" name="aDiagTitle" value="" size="40"></td></tr>
                                          <tr><td align="left"><p>Diagnosis description:</p></td><td align="left"><input type="text" name="aDiagDesc" value="" size="60"></td></tr>
                                          <tr><td align="left" colspan="2"><input type="submit" value="Submit"/></td></tr></table></form>';
                                    break;
                                case 'Medication':
                                    echo '<tr><td align="left"><p>Medication title:</p></td><td align="left"><input type="text" value="" name="MedicTitle" size="40"></td></tr>
                                          <tr><td align="left"><p>Dose:</p></td><td align="left"><input type="text" value="" name="MedicDose" size="40"></td></tr> 
                                          <tr><td align="left"><p>Route:</p></td><td align="left"><input type="text" value="" name="MedicRoute" size="40"></td></tr>
                                          <tr><td align="left"><p>Frequency:</p></td><td align="left"><input type="text" value="" name="MedicFreq" size="40"></td></tr>
                                          <tr><td align="left"><p>Medication item source:</p></td><td align="left"><input type="text" value="" name="MedicSource" size="40"></td></tr>
                                          <tr><td align="left"><p>Medication item source ID:</p></td><td align="left"><input type="text" value="" name="MedicSourceID" size="40"></td></tr>
                                          <tr><td align="left" colspan="2"><input type="submit" value="Submit" /></td></tr></table></form>';
                                    break;
                                case 'InterviewItem':
                                    echo '<tr><td align="left"><p>Question:</p></td><td align="left"><input type="text" name="QAQuestion" value="" size="60"></td></tr>
                                          <tr><td align="left"><p>Answer:</p></td><td align="left"><input type="text" name="QAAnswer" value="" size="40"></td></tr>  
                                          <tr><td align="left"><p>Media ID:</p></td><td align="left"><select name="QAMedia">' . $filesString . '</select></td></tr>
                                          <tr><td align="left"><p>Trigger:</p></td><td align="left"><input type="checkbox" name="trigger" checked /></td></tr>
                                          <tr><td align="left" colspan="2"><input type="submit" value="Submit" /></td></tr></table></form>';
                                    break;
                                case 'PhysicalExam':
                                    echo '<tr><td align="left"><p>Examination Name:</p></td><td align="left"><input type="text" value="" name="ExamName" size="60"></td></tr>
                                          <tr><td align="left"><p>Examination Description:</p></td><td align="left"><input type="text" value="" name="ExamDesc" size="60"></td></tr>
                                          <tr><td align="left"><p>Location on body - part/area:</p></td><td align="left"><input type="text" value="" name="BodyPart" size="60"></td></tr>
                                          <tr><td align="left"><p>Action:</p></td><td align="left"><input type="text" value="" name="Action" size="60" /></td></tr>

                                          <tr><td align="left"><p>Orientation:</p></td><td align="left">
                                          <table width="100%"><tr><td align="left"><p><input type="radio" value="Proximal" name="ProxDist" /> Proximal</p></td><td align="left"><p><input type="radio" value="Distal" name="ProxDist" /> Distal</p></td><td align="left"></td></tr>
                                          <tr><td align="left"><p><input type="radio" value="Right" name="RightLeft" /> Right</p></td><td align="left"><p><input type="radio" value="Left" name="RightLeft" /> Left</p></td><td align="left"></td></tr>
                                          <tr><td align="left"><p><input type="radio" value="Front" name="FrontBack" /> Front</p></td><td align="left"><p><input type="radio" value="Back" name="FrontBack" /> Back</p></td><td align="left"></td></tr>
                                          <tr><td align="left"><p><input type="radio" value="Inferior" name="InfSup" /> Inferior</p></td><td align="left"><p><input type="radio" value="Superior" name="InfSup" /> Superior</p></td><td align="left"></td></tr></table>
                                          </td></tr>

                                          <tr><td align="left"><p>Finding Name:</p></td><td align="left"><input type="text" value="" name="FindName" size="60"></td></tr>
                                          <tr><td align="left"><p>Finding Description:</p></td><td align="left"><input type="text" value="" name="FindDesc" size="60"></td></tr>

                                          <tr><td align="left"><p>Media ID:</p></td><td align="left"><select name="FindMedia">' . $filesString . '</select></td></tr>"
                                          <tr><td align="left" colspan="2"><input type="submit" value="Submit" /></td></tr></table></form>';
                                    break;
                                case 'DiagnosticTest':
                                    echo '<tr><td align="left"><p>Test Name:</p></td><td align="left"><input type="text" value="" name="TestName" size="60"></td></tr>
                                          <tr><td align="left"><p>Test Description:</p></td><td align="left"><input type="text" value="" name="TestDesc" size="60"></td></tr>

                                          <tr><td align="left"><p>Units:</p></td><td align="left"><input type="text" value="" name="TestUnits" size="60"></td></tr>
                                          <tr><td align="left"><p>Result:</p></td><td align="left"><input type="text" value="" name="TestResult" size="60"></td></tr>
                                          <tr><td align="left"><p>Normal value:</p></td><td align="left"><input type="text" value="" name="TestNorm" size="60"></td></tr>

                                          <tr><td align="left"><p>Media ID:</p></td><td align="left"><select name="TestMedia">'.$filesString.'</select></td></tr>
                                          <tr><td align="left" colspan="2"><input type="submit" value="Submit" /></td></tr></table></form>';
                                    break;
                                case 'DifferentialDiagnostic':
                                    echo '<tr><td align="left"><p>Diagnosis title:</p></td><td align="left"><input type="text" value="" name="DiagTitle" size="40"></td></tr>
                                          <tr><td align="left"><p>Diagnosis description:</p></td><td align="left"><input type="text" value="" name="DiagDesc" size="60"></td></tr>
                                          <tr><td align="left"><p>Likelihood</p></td><td align="left"><select name="Likelihood">
                                          <option value=""/>Select ...</option>
                                          <option value="high"/>high</option>
                                          <option value="medium"/>medium</option>
                                          <option value="low"/>low</option>
                                          <option value="none"/>none</option>
                                          </select></td></tr>
                                          <tr><td align="left" colspan="2"><input type="submit" value="Submit" /></td></tr></table></form>';
                                    break;
                                case 'Intervention':
                                    echo '<tr><td align="left"><p>Intervention title:</p></td><td align="left"><input type="text" value="" name="IntervTitle" size="40"></td></tr>
                                          <tr><td align="left"><p>Intervention description:</p></td><td align="left"><input type="text" value="" name="IntervDesc" size="60"></td></tr>

                                          <tr><td align="left"><p>Medication title:</p></td><td align="left"><input type="text" value="" name="iMedicTitle" size="40"></td></tr>
                                          <tr><td align="left"><p>Dose:</p></td><td align="left"><input type="text" value="" name="iMedicDose" size="40"></td></tr>
                                          <tr><td align="left"><p>Route:</p></td><td align="left"><input type="text" value="" name="iMedicRoute" size="40"></td></tr>
                                          <tr><td align="left"><p>Frequency:</p></td><td align="left"><input type="text" value="" name="iMedicFreq" size="40"></td></tr>
                                          <tr><td align="left"><p>Medication item source:</p></td><td align="left"><input type="text" value="" name="iMedicSource" size="40"></td></tr>
                                          <tr><td align="left"><p>Medication item source ID:</p></td><td align="left"><input type="text" value="" name="iMedicSourceID" size="40"></td></tr>

                                          <tr><td align="left"><p>Appropriateness</p></td><td align="left"><select name="Appropriateness">
                                          <option value=""/>select ...</option>
                                          <option value="always"/>always</option>
                                          <option value="ok"/>ok</option>
                                          <option value="never"/>never</option>
                                          <option value="none"/>none</option>
                                          </select></td></tr>

                                          <tr><td align="left"><p>Results title:</p></td><td align="left"><input type="text" value="" name="ResultTitle" size="40"></td></tr>
                                          <tr><td align="left"><p>Results description:</p></td><td align="left"><input type="text" name="" name="ResultDesc" size="60"></td></tr>

                                          <tr><td align="left"><p>Media ID:</p></td><td align="left"><select name="iTestMedia">'.$filesString.'</select></td></tr>
                                          <tr><td align="left" colspan="2"><input type="submit" name="Submit" value="Submit" /></td></tr></table></form>';
                                    break;
                            }
                            ?>
                        <?php } ?>

                        </td>
                        </tr>
                    </table>
                <?php } ?>