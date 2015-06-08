function getLastNode(chat_id)
{
    var chat = $('#'+chat_id),
        user_id = chat.find('.user_id').val(),
        webinar_id = $('#webinar_id').attr('value'),
        session_id = chat.find('.session_id'),
        node_title = chat.find('.node_title'),
        node_id = chat.find('.node_id'),
        question_id = chat.find('.question_id');

    if(!empty(user_id) && !empty(webinar_id)){
        $.ajax({
            url: urlBase + 'webinarManager/getCurrentNode/'+user_id+'/'+webinar_id,
            async: true,
            success: function (response) {
                if(!empty(response)) {
                    response = JSON.parse(response);
                    session_id.attr('value', response.session_id);
                    question_id.attr('value', response.question_id);
                    node_title.text(response.node_title);
                    node_id.text(response.node_id);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
    }
}

function loadMessages(chat_id)
{
    var chat = $('#'+chat_id),
        session_id = chat.find('.session_id').attr('value'),
        question_id = chat.find('.question_id').attr('value'),
        chat_session_id = chat.find('.chat_session_id'),
        chat_window = chat.find('.chat-window');

    if(!empty(session_id) && !empty(question_id)) {

        chat_session_id = !empty(chat_session_id) ? chat_session_id.attr('value') : 0;

        $.ajax({
            url: urlBase + 'webinarManager/getChatMessages/'+session_id+'/'+question_id+'/'+chat_session_id,
            async: true,
            success: function (response) {
                if(!empty(response)) {
                    chat_window.html(response).show();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
    }
}

function addChatMessage(context, isLearner) {

    var ttalkDiv = context.closest('.ttalk'),
        textarea = ttalkDiv.find('.ttalk-textarea'),
        response = $.trim(textarea.val());

    if(!empty(isLearner)){
        var questionId = parseInt(textarea.prop('id').replace('qresponse_', ''));
    }else{
        var chat = ttalkDiv,
            questionId = chat.find('.question_id').attr('value'),
            idNode = chat.find('.node_id').text(),
            sessionId = chat.find('.session_id').attr('value');
        //TODO: send chat_session_id
    }


    console.log(response);
    console.log(questionId);
    console.log(idNode);
    if(response != '' && !empty(questionId) && !empty(idNode)) {
        var data = {response: response, questionId: questionId, nodeId: idNode, isLearner: isLearner, sessionId: sessionId};
        $.ajax({
            url: urlBase + 'renderLabyrinth/saveTurkTalkResponse',
            type: 'post',
            data: data,
            async: true,
            success: function (response) {
                textarea.val('');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                $('body').append(jqXHR.responseText);
            }
        });
    }
}