<?php if (isset($templateData['map'])) { ?>
    <script language="javascript" type="text/javascript">
        function jumpMenu(targ,selObj,restore){ 
            eval(targ+".location='<?php echo URL::base(); ?>questionManager/addQuestion/<?php echo $templateData['map']->id; ?>/"+selObj.options[selObj.selectedIndex].value+"'");
            if (restore) selObj.selectedIndex=0;
        }
    </script>
    <table width="100%" height="100%" cellpadding="6">
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('questions "') . $templateData['map']->name . '"'; ?></h4>

                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff"><td>
                            <table border="0" width="100%" cellpadding="1">
                                <?php if(isset($templateData['questions']) and count($templateData['questions']) > 0) { ?>
                                    <?php foreach($templateData['questions'] as $question) { ?>
                                        <tr>
                                            <td>
                                                <p>
                                                    <input type="text" value="[[QU:<?php echo $question->id; ?>]]"><?php echo $question->stem; ?> (<?php echo $question->type->value; ?>, <?php echo $question->width; ?>, <?php echo $question->height; ?>) 
                                                    [<a href="<?php echo URL::base().'questionManager/editQuestion/'.$templateData['map']->id.'/'.$question->entry_type_id.'/'.$question->id; ?>">edit</a> 
                                                    - <a href="<?php echo URL::base().'questionManager/deleteQuestion/'.$templateData['map']->id.'/'.$question->id; ?>">delete</a>]
                                                </p>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                                <tr>
                                    <td>
                                        <p>add question 
                                            <?php if(isset($templateData['question_types']) and count($templateData['question_types']) > 0) { ?>
                                            <select onchange="jumpMenu('parent',this,0)" name="qt">
                                                <option value="">select ...</option>
                                                <?php foreach($templateData['question_types'] as $type) { ?>
                                                    <option value="<?php echo $type->id ?>"><?php echo $type->title; ?></option>
                                                <?php } ?>
                                            </select>
                                            <?php } ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td></tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>


