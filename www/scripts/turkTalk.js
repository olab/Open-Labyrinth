/**
 * question_id  - TurkTalk question id, if exists
 *
 */

var previousNodesId = [];

function saveChatsOrder(context)
{
    var order = context.sortable('serialize'),
        data = order,
        webinar_id = $('#webinar_id').attr('value');

    console.log(data);
    if(!empty(webinar_id)) {
        $.ajax({
            data: data,
            type: 'post',
            async: true,
            url: urlBase + 'webinarManager/saveChatsOrder/' + webinar_id,
            success: function (response) {
                //console.log(response);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
    }
}

function saveChosenUser(context)
{
    var chat_id = context.closest('.ttalk').prop('id'),
        user_id = context.val(),
        webinar_id = $('#webinar_id').attr('value');

    if(!empty(chat_id) && !empty(user_id) && !empty(webinar_id)){
        $.ajax({
            url: urlBase + 'webinarManager/saveChosenUser/'+webinar_id+'/'+chat_id+'/'+user_id,
            async: true,
            success: function (response) {
                //console.log(response);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
    }
}

function getNodeLinks(chat_id)
{
    var chat = $('#'+chat_id),
        redirect_node_id = chat.find('.redirect_node_id'),
        node_id_obj = chat.find('.node_id'),
        node_id = node_id_obj.text(),
        question_id = chat.find('.question_id').attr('value');

    //don't send request if there is no TurkTalk question on the current node
    if(!empty(node_id) && !empty(question_id)){
        //don't send request if this is the same node
        if(previousNodesId[chat_id] != node_id) {
            $.ajax({
                url: urlBase + 'webinarManager/getNodeLinks/' + node_id,
                async: true,
                success: function (response) {
                    if (!empty(response)) {
                        redirect_node_id.html(response).prop('disabled', false);
                    }
                    previousNodesId[chat_id] = node_id;
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
        }
    }else{
        redirect_node_id.html('<option value="">- Redirect to... -</option>').prop('disabled', 'disabled');
    }
}

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

function loadMessages(chat_id, isLearner)
{
    var chat = $('#'+chat_id),
        session_id = chat.find('.session_id').attr('value'),
        question_id = chat.find('.question_id').attr('value'),
        chat_session_id = chat.find('.chat_session_id'),
        chat_window = chat.find('.chat-window'),
        isLearner = isLearner === 0 ? 0 : 1,
        nodeId = isLearner === 0 ? chat.find('.node_id').text() : 0;

    //console.log(question_id);
    if(!empty(session_id) && !empty(question_id)) {

        chat_session_id = !empty(chat_session_id) ? chat_session_id.attr('value') : 0;

        $.ajax({
            url: urlBase + 'webinarManager/getChatMessages/'+session_id+'/'+question_id+'/'+chat_session_id+'/'+isLearner+'/'+nodeId,
            async: true,
            success: function (response) {

                if(!empty(response)) {
                    //console.log(response);
                    response = JSON.parse(response);
                    var responseText = response.response_text;
                    if(isLearner == 1){
                        if(response.response_type == 'redirect'){
                            window.location.replace(responseText);
                        }
                    }else{
                        if(response.waiting_for_response){
                            chat_window.addClass('new-message');
                        }else{
                            chat_window.removeClass('new-message');
                        }
                    }
                    chat_window.html(responseText).show();
                }else{
                    chat_window.removeClass('new-message');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
    }else{
        chat_window.html('').removeClass('new-message');
    }
}

function addChatMessage(context, isLearner, isRedirect) {

    var ttalkDiv = context.closest('.ttalk'),
        textarea = ttalkDiv.find('.ttalk-textarea'),
        response = $.trim(textarea.val());

    if(!empty(isLearner)){
        var questionId = parseInt(textarea.prop('id').replace('qresponse_', '')),
            type = 'text';
    }else{
        var chat = ttalkDiv,
            questionId = chat.find('.question_id').attr('value'),
            sessionId = chat.find('.session_id').attr('value'),
            isRedirect = isRedirect || 0,
            type = isRedirect ? 'redirect' : 'text',
            response = isRedirect ? $('.redirect_node_id').val() : response;
        idNode = chat.find('.node_id').text();
    }

    if(response != '' && !empty(questionId) && !empty(idNode)) {
        var data = {response: response, questionId: questionId, nodeId: idNode, isLearner: isLearner, sessionId: sessionId, type: type};
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
                //$('body').append(jqXHR.responseText);
            }
        });
    }
}