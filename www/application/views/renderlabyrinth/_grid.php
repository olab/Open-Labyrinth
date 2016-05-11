<?php
/** @var Model_Leap_Map_Question $question */
/** @var array $userResponses */
/** @var string $q_type */

$subQuestions = $question->subQuestions;
$responses = $question->responses;
$isPCQ = ($q_type === 'pcq-grid');
?>

<form data-questionID="<?php echo $question->id ?>">
<table class="table table-condensed">
    <thead>
    <tr>
        <th></th>
        <?php foreach ($responses as $response) { ?>
            <th>
                <?php echo $response->response ?>
            </th>
        <?php } ?>
    </tr>
    </thead>
    <tbody>

    <?php foreach ($subQuestions as $subQuestion) { ?>
        <tr>
            <td>
                <?php echo $subQuestion->stem ?>
            </td>

            <?php foreach ($responses as $response) { ?>
                <td>
                    <div class="control-group">
                        <input
                            class="mcqResponse"
                            type="<?php echo ($isPCQ ? 'radio' : 'checkbox') ?>"
                            name="userResponses[<?php echo $subQuestion->id ?>][<?php echo $response->id ?>]"
                            value="<?php echo $response->response ?>"
                            <?php if(isset($userResponses[$subQuestion->id][$response->id])) echo 'checked' ?>
                        >
                    </div>
                </td>
            <?php } ?>
        </tr>
    <?php } ?>
    </tbody>
</table>
</form>

<script>
    $(document).ready(function() {
        $('.mcqResponse').on('click', function(){
            var form = $(this).closest('form');

            <?php if($isPCQ) { ?>
            $(this).closest('tr').find('.mcqResponse').removeAttr('checked');
            $(this).attr('checked', 'checked');
            <?php } ?>

            var data = form.serialize();
            
            onMCQGridChange(form.attr('data-questionID'), data);
        });
    });
</script>