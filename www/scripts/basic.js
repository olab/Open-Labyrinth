var submitTextQ = [],
    questions = null,
    toNodeHref = '',
    savedTextQ = 0,
    getQuestionResponse = 0;
    urlBase = window.location.origin + '/';

$(document).ready(function(){
    questions = $('textarea[name^="qresponse_"]');

        $('a[href^="/renderLabyrinth/go"]').click(function(e){
            if(questions.length > 0){
                var notSubmitTextQuestion = [];

                questions.each(function(){
                    var idTextQ = parseInt($(this).prop('name').replace('qresponse_', ''));
                    if ($.inArray(idTextQ, submitTextQ) === -1) notSubmitTextQuestion.push(idTextQ);
                });

                if (notSubmitTextQuestion.length)
                {
                    e.preventDefault();
                    toNodeHref = e.currentTarget.href;
                    getQuestionResponse = 1;

                    for (var i=0; i<notSubmitTextQuestion.length; i++){
                        ajaxFunction(notSubmitTextQuestion[i]);
                    }
                }
            }
        });
});

function ajaxFunction(qid) {
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