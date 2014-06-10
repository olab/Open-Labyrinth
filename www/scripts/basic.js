var submitTextQ = [],
    questions = null,
    toNodeHref = '',
    savedTextQ = 0,
    getQuestionResponse = 0,
    alreadyPolled   = 0,
    urlBase = window.location.origin + '/';

$(document).ready(function(){
    questions = $('[name^="qresponse_"]');
    var goLink = $('a[href^="/renderLabyrinth/go"]');

    goLink.click(function(e){

        toNodeHref = e.currentTarget.href;

        if (questions.length > 0){
            var notSubmitTextQuestion = [];

            questions.each(function(){
                var idTextQ = parseInt($(this).prop('name').replace('qresponse_', ''));
                if ($.inArray(idTextQ, submitTextQ) === -1) notSubmitTextQuestion.push(idTextQ);
            });

            if (notSubmitTextQuestion.length)
            {
                e.preventDefault();
                getQuestionResponse = 1;

                for (var i=0; i<notSubmitTextQuestion.length; i++){
                    ajaxFunction(notSubmitTextQuestion[i]);
                }
            }
        }

        // ----- poll ----- //
        if (pollTime){
            e.preventDefault();
            savedTextQ += 100; // cancel ajaxFunction()

            var split           = toNodeHref.split('/'),
                keys            = split.length,
                selectedNodeId  = split[keys-1];

            if ( ! alreadyPolled){
                alreadyPolled++;
                $.get(urlBase + 'renderLabyrinth/savePoll/' + idNode + '/' + selectedNodeId, function(data){});
            }

            setTimeout(function() {
                $.get(urlBase + 'renderLabyrinth/getNodeIdByPoll/' + idNode + '/' + pollTime, function(data){
                    window.location.href = urlBase + 'renderLabyrinth/go/' + split[keys-2] + '/' + data;
                });

            }, pollTime * 1000);
        }
        // ----- end poll ----- //
    });

    // ----- patient ----- //
    if (idPatients.length > 2)
    {
        ajaxPatient();
        setInterval(ajaxPatient, (1500));
    }

    function ajaxPatient(){
        $.get(
            urlBase + 'renderLabyrinth/dataPatientAjax/' + idPatients,
            function(data){
                data = $.parseJSON(data)
                // change condition block
                var ulPatient       = $('.patient-js'),
                    patientArray    = data.conditions,
                    deactivate      = data.deactivateNode;

                for (var i = 0; i < ulPatient.length; i++) ulPatient.eq(i).html(patientArray[i]);

                // deactivate go link
                if(deactivate){
                    goLink.each(function(){
                        var href    = $(this).prop('href'),
                            split   = href.split('/'),
                            idNode  = split[split.length -1];

                        if ($.inArray(idNode, deactivate) != -1){
                            $(this).css('opacity','0.5');
                            $(this).click(function(e){
                                e.preventDefault();
                            });
                        }
                    });
                }
            }
        );
    }
    // ----- end patient ----- //

    // ----- single line text question ----- //
    $(".lightning-single").focusout(function(){
        questionAjaxSave($(this));
    });

    $(".lightning-multi").focusout(function(){
        questionAjaxSave($(this));
    });

    function questionAjaxSave($this){
        var dbId       = $this.data('dbid'),
            questionId = $this.prop('id').replace('qresponse_', ''),
            response   = $this.val();

        if (response.length == 0) response = 'no response';

        $.post(
            urlBase + 'renderLabyrinth/ajaxTextQuestionSave',
            {response: response, questionId: questionId, nodeId: idNode, dbId:dbId },
            function(data){
                $this.data('dbid', data);
                if (rightAnswer) imitateGo();
            }
        );
    }
    // ----- end single line text question ----- //

    // ----- lightning rule ----- //
    var rightAnswer = false,
        ruleExist = jsonRule.length > 2;

    $('.lightning-single').focusout(function(){
        validationCreate($(this));
    });

    $('.lightning-multi').focusout(function(){
        validationCreate($(this));
    });

    function validationCreate($this){
        var value           = $this.val(),
            validatorName   = $this.data('validator'),
            errorMsg        = $this.data('errormsg'),
            parameter       = $this.data('parameter'),
            parameters      = '',
            validation      = false,
            $thisId         = parseInt($this.prop('id').replace('qresponse_', ''));

        submitTextQ.push($thisId);

        parameter = parameter.split(',');
        for (var i = 0; i < parameter.length; i++) {
            if (i>0) parameters += ", '" + parameter[i] + "'";
            else parameters += "'" + parameter[i] + "'";
        }

        if (parameters) validation = eval("validator." + validatorName + "('" + value + "', " + parameters + ')');
        else if (validatorName) validation = validator.isEmail(value);

        $this.parent().find('.error-validation').each(function(){
            $(this).remove();
        });

        if ( ! validation) $this.after('<span class="error-validation" style="color: red; margin-left: 5px;">' + errorMsg + '</span>');

        if (ruleExist) rightAnswer = checkAnswer($thisId, $this.val());
    }

    $('.drag-question-container').sortable({
        axis: "y",
        cursor: "move",
        create: function(event, ui) {
            dragAndDropPost($(this));
        },
        stop: function(event, ui) {
            dragAndDropPost($(this));
        }
    });

    function dragAndDropPost(obj) {
        var questionId      = obj.attr('questionId'),
            responsesObject = [];

        obj.children().each(function(index, value) {
            responsesObject.push($(value).attr('responseId'));
        });

        var responsesJSON = JSON.stringify(responsesObject);
        rightAnswer = checkAnswer(questionId, responsesJSON.replace(/"|\[|\]/g,''));

        $.post(
            urlBase + 'renderLabyrinth/ajaxDraggingQuestionResponse',
            { questionId: questionId, responsesJSON: responsesJSON },
            function(){
                if (rightAnswer) imitateGo();
            }
        );
    }

    $('.lightning-choice').change(function(){
        var dbId       = $(this).data('dbId'),
            questionId = $(this).data('question'),
            responseId = $(this).data('response'),
            tries      = $(this).data('tries'),
            response   = $(this).data('val'),
            check      = $(this).is(':checked'),
            URL        = urlBase + 'renderLabyrinth/questionResponse/' + responseId + '/' + questionId + '/' + idNode,
            $response  = $('#AJAXresponse' + responseId);

        rightAnswer = checkAnswer(questionId, response);

        URL += check ? '/1' : '/0';

        if (tries == 1) $('.questionForm_' + questionId + ' .click').remove();

        $.get(
            URL,
            function(data){
                if (data != '') $response.html(data);
                if (rightAnswer) imitateGo();
            }
        );
    });

    $('.sct-question').change(function(){
        var questionId = $(this).data('question');

        if($(this).hasClass('disposable')){
            $('.sct-question').each(function(i, v){
                var current = $('.sct-question').eq(i);
                if (current.data('question') == questionId) current.prop('disabled', true);
            });
        }

        rightAnswer = checkAnswer(questionId, $(this).data('val'));

        $.post(
            urlBase + 'renderLabyrinth/ajaxScriptConcordanceTesting',
            { idResponse: $(this).data('response'), idQuestion: questionId },
            function(){
                if (rightAnswer) imitateGo();
            }
        );
    });

    function checkAnswer(questionId, answer){
        var result = false;
        $.each(JSON.parse(jsonRule), function(response, id){
            if (answer == response && id == questionId) result = true;
        });
        return result;
    }

    function imitateGo(){
        $("[href*='/renderLabyrinth/go']").each(function(){
            window.location.href = this.href;
        });
    }
    // ----- end lightning rule ----- //


});

function ajaxFunction(qid) {
    submitTextQ.push(qid);
    var qresp = $("#qresponse_" + qid).val();

    if (qresp == '') qresp = 'no response';

    qresp = B64.encode(qresp);

    var URL = urlBase + "renderLabyrinth/questionResponse/" + qresp + "/" + qid + "/" + idNode,
        $response = $('#AJAXresponse' + qid);

    $.get(
        URL,
        function(data) {
            if(data != '') $response.html(data);
            savedTextQ += 1;
            if (savedTextQ == questions.length && getQuestionResponse) window.location.href = toNodeHref;
        }
    )
}

// ----- different type of questions ----- //
function ajaxDrag(id) {
    $('#questionSubmit'+id).show();

    var response = $('#qresponse_'+id),
        responsesObject = [];

    response.sortable( "option", "cancel", "li" );

    response.children('.sortable').each(function(index, value) {
        responsesObject.push($(value).attr('responseId'));
        $(value).css('color','gray');
    });

    $.post(
        urlBase + 'renderLabyrinth/ajaxDraggingQuestionResponse',
        {questionId: id, responsesJSON: JSON.stringify(responsesObject)},
        function(){}
    );
}


function sendSliderValue(qid, value) {
    var URL = urlBase + 'renderLabyrinth/saveSliderQuestionResponse/' + qid;
    $.post(
        URL,
        {value: value},
        function(){}
    );
}
// ----- end different type of questions ----- //

window.dhx_globalImgPath = urlBase + "scripts/dhtmlxSlider/codebase/imgs/";

function toggle_visibility(id){
    var e = document.getElementById(id);
    if (e.style.display == 'none') e.style.display = 'block';
    else e.style.display = 'none';
}