$(function () {
    var $responsesContainer = $('.question-response-panel-group'),
        parent,
        id,
        button,
        scoreOptions = '',
        correctnessOptions = '<option value="2">- Select -</option><option value="1" >Correct</option><option value="2" selected>Neutral</option><option value="0" >Incorrect</option>';

    $responsesContainer.sortable({
        axis: "y",
        cursor: "move",
        stop: function (event, ui) {
            recalculateOrder();
        }
    });

    $('body').on('click', '.question-response-panel-group .panel-heading input', function () {
        return false;
    });

    $('body').on('click', '.question-response-panel-group .btn-remove-response', function () {
        $(this).parent().parent().remove();
        recalculateOrder();

        return false;
    });

    $(".radio_extended input[type=radio]").each(function () {
        if ($(this).is(':checked')) {
            parent = $(this).parent('.radio_extended');
            id = $(this).attr('id');
            button = $(parent).find('label[for=' + id + ']');
            changeRadioBootstrap(button);
        }
    });

    $(".radio_extended .btn").live("click", function () {
        changeRadioBootstrap(this);
    });

    for (var i = -10; i <= 10; i += 1) {
        scoreOptions += '<option value="' + i + '"' + (i == 0 ? 'selected=""' : '') + '>' + i + '</option>';
    }

    $('#addResponse').click(function () {
        var newId = (new Date()).getTime(),
            orderOptions = generateOrderListOptions(),
            html = "<div class=\"panel sortable\">" +
                "<input type=\"hidden\" name=\"responses[]\" value=\"\"/>" +
                "<div class=\"panel-heading\" class=\"accordion-toggle\" data-toggle=\"collapse\" data-parent=\"#accordion\" href=\"#responseCollapse_" + newId + "\">" +
                "<label for=\"response_" + newId + "\">Response</label>" +
                "<input type=\"text\" class=\"response-input\" id=\"response_" + newId + "\" value=\"\"> " +
                "<button type=\"button\" class=\"btn-remove-response btn btn-danger btn-small\"><i class=\"icon-trash\"></i></button>" +
                "</div>" +
                "<div id=\"responseCollapse_" + newId + "\" class=\"panel-collapse collapse\">" +
                "<div class=\"panel-body\">" +
                "<div class=\"control-group\">" +
                "<label for=\"feedback_" + newId + "\" class=\"control-label\">Feedback</label>" +
                "<div class=\"controls\"><input autocomplete=\"off\" class=\"feedback-input\" type=\"text\" id=\"feedback_" + newId + "\" name=\"feedback_" + newId + "\" value=\"\"/></div>" +
                "</div>" +

                "<div class=\"control-group\">" +
                "<label for=\"correctness_" + newId + "\" class=\"control-label\">Correctness</label>" +
                "<div class=\"controls\">" +
                "<select class=\"correctness-select\" id=\"correctness_" + newId + "\" name=\"correctness_" + newId + "\">" + correctnessOptions + "</select>" +
                "</div>" +
                "</div>" +

                "<div class=\"control-group\">" +
                "<label for=\"score_" + newId + "\" class=\"control-label\">Score</label>" +
                "<div class=\"controls\"><select autocomplete=\"off\" class=\"score-select\" id=\"score_\" name=\"score_\">" + scoreOptions + "</select></div>" +
                "</div>" +

                "<div class=\"control-group\">" +
                "<label for=\"order_" + newId + "\" class=\"control-label\">Order</label>" +
                "<div class=\"controls\">" +
                "<select class=\"response-order-select\" id=\"order_" + newId + "\" name=\"order_" + newId + "\">" + orderOptions + "</select>" +
                "</div>" +
                "</div>" +
                "</div>" +
                "</div>" +
                "</div>";

        $responsesContainer.append(html);
    });

    $('.question-save-btn').click(function () {
        $('.question-response-panel-group .panel').each(function (index, value) {
            var $hidden = $(value).find('input[type="hidden"]'),
                jsonStr = $hidden.val(),
                jsonObject = (jsonStr.length > 0) ? JSON.parse(jsonStr) : {},
                response = $(value).find('.response-input').val(),
                feedback = $(value).find('.feedback-input').val(),
                correct = parseInt($(value).find('.correctness-select').val()),
                score = parseInt($(value).find('.score-select').val()),
                order = parseInt($(value).find('.response-order-select').val());

            if (isNaN(correct)) {
                correct = 2;
            }
            if (isNaN(score)) {
                score = 0;
            }
            if (isNaN(order)) {
                order = 1;
            }

            jsonObject.response = B64.encode($.trim(response.replace(/\0/g, "")));
            jsonObject.feedback = B64.encode($.trim(feedback.replace(/\0/g, "")));
            jsonObject.correctness = correct;
            jsonObject.score = score;
            jsonObject.order = order;

            $hidden.val(JSON.stringify(jsonObject));
        });

        $('form').submit();
    });

    function changeRadioBootstrap(obj) {
        $(obj).parent(".radio_extended").find(".btn").removeAttr('class').addClass('btn');
        $(obj).addClass('active');
        var additionClass = $(obj).attr('data-class');
        if (additionClass !== null) {
            $(obj).addClass(additionClass);
        }
    }

    function recalculateOrder() {
        $('.question-response-panel-group .response-order-select').each(function (index, value) {
            $(value).val(index + 1);
        });
    }

    function generateOrderListOptions() {
        var count = $('.question-response-panel-group .panel').length + 1,
            result = '';

        for (var i = 1; i <= count; i += 1) {
            result += "<option value=\"" + i + "\" " + ((i == count) ? "selected='selected'" : "") + ">" + i + "</option>";
        }

        return result;
    }

    $('#addResponseSct').click(function () {
        var block = $('.sct-js').last().clone().show();
        $responsesContainer.append(block);
    });

    // Grid question types
    $(document).ready(function () {
        var responses = $('#responses_list').find('tbody');

        $(document).on('click', '.addResponseGrid', function (e) {
            e.preventDefault();
            var responseCounter = $('.responseInput').length + 1,
                responseTemplate = '<tr><td></td><td><input class="responseInput" type="text" name="responses[]"></td><td><input class="orderInput" type="text" name="responsesOrder[]" value="'+responseCounter+'"></td><td><span class="btn btn-danger removeRow"><i class="icon-trash"></i></span></td></tr>';
            responses.append(responseTemplate);
        });

        var sub_questions = $('#sub_questions_list').find('tbody');
        $(document).on('click', '.addSubQuestion', function (e) {
            e.preventDefault();
            var subQuestionCounter = $('.subQuestionInput').length + 1,
                questionTemplate = '<tr><td></td><td><input class="subQuestionInput"  type="text" name="subQuestions[]"></td><td><input class="orderInput" type="text" name="subQuestionsOrder[]" value="'+subQuestionCounter+'"></td><td><span class="btn btn-danger removeRow"><i class="icon-trash"></i></span></td></tr>';
            sub_questions.append(questionTemplate);
        });

        $(document).on('click', '.removeRow', function (e) {
            e.preventDefault();
            $(this).closest('tr').remove();
        });
    });
    // end Grid question types
});