var submitTextQ = [],
    questions = null,
    toNodeHref = '',
    savedTextQ = 0,
    getQuestionResponse = 0,
    alreadyPolled   = 0,
    urlBase = window.location.origin + '/';

$(document).ready(function(){
    questions = $('textarea[name^="qresponse_"]');
    var goLink = $('a[href^="/renderLabyrinth/go"]');

    goLink.click(function(e){

        toNodeHref = e.currentTarget.href;

        if(questions.length > 0){
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
    if (idPatients.length > 2) setInterval(ajaxPatient, (1500));

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

window.dhx_globalImgPath = urlBase + "scripts/dhtmlxSlider/codebase/imgs/";

function toggle_visibility(id) {
    var e = document.getElementById(id);
    if (e.style.display == 'none')
        e.style.display = 'block';
    else
        e.style.display = 'none';
}