<?php
/** @var $question Model_Leap_Map_Question */

$question_id_attribute = 'dropDown-question' . $question->id;
$submitText = ($question->submit_text != null) ? $question->submit_text : 'Submit';
?>
<select style="width:200px;" id="<?php echo $question_id_attribute ?>">
    <option value=""></option>
    <?php foreach ($question->responses as $response) { ?>
        <option value="<?php echo $response->response ?>"><?php echo $response->response ?></option>
    <?php } ?>
</select>
<div id="AJAXresponse<?php echo $question->id ?>"></div>

<?php if ($question->show_submit == 1) { ?>
<span id="questionSubmit<?php echo $question->id ?>" style="display:none;font-size:12px">Answer has been sent.</span>
<button onclick="ajaxDrag(<?php echo $question->id ?>);$(this).hide();" ><?php echo $submitText ?></button>
<?php } ?>

<script>
    dropDownQuestion = dhtmlXComboFromSelect('<?php echo $question_id_attribute ?>');
    dropDownQuestion.question_id = <?php echo $question->id ?>;
    dropDownQuestion.enableFilteringMode(true);

    <?php if(!$question->isFreeTextAllowed()){ ?>
    dropDownQuestion.allowFreeText(false);
    <?php } ?>

    dropDownQuestion.attachEvent('onChange', onDropDownChange);
    //dropDownQuestion.attachEvent('onBlur', onDropDownChange);

    //for future usage
    if (typeof dropDownQuestions == 'undefined') {
        dropDownQuestions = {};
    }
    dropDownQuestions['<?php echo $question_id_attribute ?>'] = dropDownQuestion;
    //end for future usage
</script>