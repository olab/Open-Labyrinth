var submitTextQ         = [],
    questions           = null,
    toNodeHref          = '',
    savedTextQ          = 0,
    getQuestionResponse = 0,
    alreadyPolled       = 0,
    lightningNotSaved   = false,
    actionGoClicked     = false;

function valToTextarea() {
    tinyMCE.activeEditor.on('keyUp', function() {
        $('#'+$(this).prop('id')).html(tinyMCE.activeEditor.getContent());
    });
}

$(document).ready(function(){
    questions = $('[name^="qresponse_"]');
    var goLink = $('a[href^="/renderLabyrinth/go"]');

    goLink.click(function(e){

        toNodeHref = e.currentTarget.href;

        actionGoClicked = true;
        if (lightningNotSaved) e.preventDefault();

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
        setInterval(ajaxPatient, (5000));
    }

    function ajaxPatient(){
        $.post(
            urlBase + 'renderLabyrinth/dataPatientAjax?patients=' + idPatients,
            function(data){
                console.log(data);
                data = $.parseJSON(data);
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

    // ----- lightning rule ----- //
    var rightAnswer     = false,
        ruleExist       = jsonRule.length > 2,
        $lightningMulti = $('.lightning-multi'),
        cumulative      = $lightningMulti.hasClass('cumulative');

    $('.lightning-single').focusout(function(){
        validationAndLightningText($(this));
    });

    $lightningMulti.focusout(function(){
        validationAndLightningText($(this));
    });

    $('.lightning-choice').change(function(){
        var type = $(this).attr('type');
        lightningChoice($(this));
        if(typeof type != 'undefined' && type == 'checkbox') {
            var inputs = $(this).closest('.navigation').find('.lightning-choice');
            inputs.each(function () {
                lightningChoice($(this), true);
            });
        }
    });

    $('.drag-question-container').sortable({
        axis: "y",
        cursor: "move",
        create: function (event, ui) { dragAndDropPost($(this)); },
        stop: function (event, ui) { dragAndDropPost($(this)); }
    });

    $('.sct-question').change(function(){
        lightningSct($(this));
    });

    $(document).ready(function(){
        var ttalkButton = $('.ttalkButton');
        ttalkButton.on('click', function () {
            addChatMessage($(this), 1);
        });

        $('textarea.ttalk-textarea').on('keyup', function(e){
            if(!e.shiftKey && e.keyCode == 13) {
                ttalkButton.trigger('click');
            }
        });
    });

    function validationAndLightningText($this){
        lightningNotSaved = true;

        var response        = $this.val(),
            dbId            = $this.data('dbid'),
            validatorName   = $this.data('validator'),
            errorMsg        = $this.data('errormsg'),
            parameter       = $this.data('parameter'),
            parameters      = '',
            validation      = false,
            $thisId         = parseInt($this.prop('id').replace('qresponse_', ''));

        submitTextQ.push($thisId);

        if (response.length == 0) response = 'no response';

        if (validatorName) {
            parameter = parameter.toString().split(',');

            for (var i = 0; i < parameter.length; i++) {
                if (i > 0) parameters += ", '" + parameter[i] + "'";
                else parameters += "'" + parameter[i] + "'";
            }

            if (parameters) validation = eval("validator." + validatorName + "('" + response + "', " + parameters + ')');
            else validation = eval("validator." + validatorName + "('" + response + "')");

            $this.parent().find('.error-validation').remove();

            if ( ! validation) $this.after('<span class="error-validation" style="color: red; margin-left: 5px;">' + errorMsg + '</span>');

        }

        if (ruleExist) rightAnswer = checkAnswer($thisId, $this.val());
        $.post(
            urlBase + 'renderLabyrinth/ajaxTextQuestionSave',
            {response: response, questionId: $thisId, nodeId: idNode, dbId:dbId },
            function(data){
                $this.data('dbid', data);
                if (rightAnswer) imitateGo();
                if (actionGoClicked) window.location.href = toNodeHref;
                lightningNotSaved = false;
            }
        );
    }

    function dragAndDropPost(obj) {
        lightningNotSaved = true;

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
                if (actionGoClicked) window.location.href = toNodeHref;
                lightningNotSaved = false;
            }
        );
    }

    function lightningChoice($this, notShowResponse){
        lightningNotSaved = true;

        var questionId = $this.data('question'),
            responseId = $this.data('response'),
            tries      = $this.data('tries'),
            response   = $this.data('val'),
            check      = $this.is(':checked'),
            URL        = urlBase + 'renderLabyrinth/questionResponse/' + responseId + '/' + questionId + '/' + idNode + (check ? '/1' : '/0');

        if (tries == 1) $('.questionForm_' + questionId + ' .click').remove();

        $.get(
            URL,
            function(data){
                if (data != '' && notShowResponse !== true) {
                    $('#AJAXresponse' + responseId).html(data);
                }
                if (checkAnswer(questionId, response)) imitateGo();
                if (actionGoClicked) window.location.href = toNodeHref;
                lightningNotSaved = false;
            }
        );
    }

    function lightningSct($this){
        lightningNotSaved = true;

        var questionId = $this.data('question');

        if($this.hasClass('disposable')){
            $('.sct-question').each(function(i, v){
                var current = $('.sct-question').eq(i);
                if (current.data('question') == questionId) current.prop('disabled', true);
            });
        }

        rightAnswer = checkAnswer(questionId, $this.data('val'));

        $.post(
            urlBase + 'renderLabyrinth/ajaxScriptConcordanceTesting',
            { idResponse: $this.data('response'), idQuestion: questionId },
            function(){
                if (rightAnswer) imitateGo();
                if (actionGoClicked) window.location.href = toNodeHref;
                lightningNotSaved = false;
            }
        );
    }

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
    var response = $("#qresponse_" + qid).val();

    if (response == '') response = 'no response';

    $.get(
        urlBase + "renderLabyrinth/questionResponse/" + B64.encode(response) + "/" + qid + "/" + idNode,
        function(data) {
            if(data != '') $('#AJAXresponse' + qid).html(data);
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
    e.style.display = (e.style.display == 'none') ? 'block' : 'none';
}

function ajaxBookmark() {
    $.get(
        urlBase + 'renderLabyrinth/addBookmark/' + idNode,
        function(){
            $("input[name='bookmark']").val('saved');
        }
    )
}