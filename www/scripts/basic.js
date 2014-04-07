var submitTextQ = [],
    questions = null,
    toNodeHref = '',
    savedTextQ = 0,
    urlBase = window.location.origin + '/';

$(document).ready(function(){
    questions = $('textarea[name^="qresponse_"]');

    if(questions.length > 0){
        $('a[href^="/renderLabyrinth/go"]').click(function(e){
            e.preventDefault();
            toNodeHref = e.currentTarget.href;

            questions.each(function(){
                var idTextQ = parseInt($(this).prop('name').replace('qresponse_', ''));
                if ($.inArray(idTextQ, submitTextQ) === -1){
                    ajaxFunction(idTextQ);
                }
            });

            return false;
        });
    }
});

function ajaxFunction(qid) {
    submitTextQ.push(qid);
    var qresp = $("#qresponse_" + qid).val();

    if (qresp != ''){
        qresp = B64.encode(qresp);
        var URL = urlBase + "renderLabyrinth/questionResponse/" + qresp + "/" + qid + "/" + idNode;

        var $response = $('#AJAXresponse' + qid);
        $.get(
            URL,
            function(data) {
                if(data != '') $response.html(data);
                savedTextQ += 1;
                if (savedTextQ == questions.length) window.location.href = toNodeHref;
            }
        )
    }
}

window.dhx_globalImgPath = urlBase + "scripts/dhtmlxSlider/codebase/imgs/";

function toggle_visibility(id) {
    var e = document.getElementById(id);
    if (e.style.display == 'none')
        e.style.display = 'block';
    else
        e.style.display = 'none';
}