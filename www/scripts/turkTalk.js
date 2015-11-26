/**
 * question_id  - TurkTalk question id, if exists
 *
 */

var previousNodesId = [];


function doBell()
{
    $.ajax({
        async: true,
        url: urlBase + 'webinarManager/checkBell',
        success: function (response) {
            response = JSON.parse(response);
            if(response.need_bell){
                $('body').append('<audio autoplay style="display:none;"><source src="' + urlBase + 'media/bell.mp3" type="audio/mpeg"></audio>');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        }
    });
}

function macros(context, text)
{
    var val = context.val();
    context.val(val+text);
}

function saveChatsOrder(context)
{
    var order = context.sortable('serialize'),
        webinar_id = $('#webinar_id').attr('value');

    if(!empty(webinar_id)) {
        $.ajax({
            data: order,
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

function clearChat(context, chat)
{
    var chat = chat || context.closest('.ttalk'),
        chat_window = chat.find('.chat-window'),
        session_id = chat.find('.session_id'),
        node_title = chat.find('.node_title'),
        node_id = chat.find('.node_id'),
        question_id = chat.find('.question_id');

    chat.attr('data-waiting-time', '0');
    chat_window.attr('data-responses-counter', '0');
    chat_window.removeClass('new-message');
    chat_window.html('');
    session_id.attr('value', '');
    question_id.attr('value', '');
    node_title.text('');
    node_id.text('');
}

function saveChosenUser(context)
{
    var chat_id = context.closest('.ttalk').prop('id'),
        user_id = context.val(),
        webinar_id = $('#webinar_id').attr('value');
    user_id = !empty(user_id) ? user_id : 0;

    if(!empty(chat_id) && !empty(webinar_id)){
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
        question_id = chat.find('.question_id').attr('value'),
        int_chat_id = parseInt(chat_id.replace('chat-', ''));

    //don't send request if there is no TurkTalk question on the current node
    if(!empty(node_id) && !empty(question_id)){
        //don't send request if this is the same node
        if(previousNodesId[int_chat_id] != node_id) {
            $.ajax({
                url: urlBase + 'webinarManager/getNodeLinks/' + node_id,
                async: true,
                success: function (response) {
                    if (!empty(response)) {
                        redirect_node_id.html(response).prop('disabled', false);
                        previousNodesId[int_chat_id] = node_id;
                    }else{
                        previousNodesId[int_chat_id] = '';
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    previousNodesId[int_chat_id] = '';
                }
            });
        }
    }else{
        redirect_node_id.html('<option value="">- Redirect to... -</option>').prop('disabled', 'disabled');
        previousNodesId[int_chat_id] = '';
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
                }else{
                    clearChat(null, chat);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
    }else{
        clearChat(null, chat);
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

    if(!empty(session_id) && !empty(question_id)) {

        chat_session_id = !empty(chat_session_id) ? chat_session_id.attr('value') : 0;

        $.ajax({
            url: urlBase + 'webinarManager/getChatMessages/'+session_id+'/'+question_id+'/'+chat_session_id+'/'+isLearner+'/'+nodeId,
            async: true,
            success: function (response) {

                if(!empty(response)) {

                    response = JSON.parse(response);
                    var responseText = response.response_text;
                    if(isLearner == 1){
                        if(response.response_type == 'redirect'){
                            setJSCookie('wasRedirected', 'yes');
                            window.location.replace(responseText);
                        }
                    }else{
                        if(response.waiting_for_response){
                            chat_window.addClass('new-message');
                            chat.attr('data-waiting-time', response.waiting_time);
                        }else{
                            chat_window.removeClass('new-message');
                            chat.attr('data-waiting-time', '0');
                        }
                    }
                    chat_window.html(responseText);
                    if(chat_window.attr('data-responses-counter') !== response.responses_counter) {
                        chat_window.animate({scrollTop: chat_window.prop("scrollHeight")}, 1000);
                    }
                    chat_window.attr('data-responses-counter', response.responses_counter);
                }else{
                    clearChat(null, chat);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
    }else{
        clearChat(null, chat);
    }
}

function addChatMessage(context, isLearner, messageType) {

    var ttalkDiv = context.closest('.ttalk'),
        textarea = ttalkDiv.find('.ttalk-textarea'),
        response = $.trim(textarea.val());

    if(!empty(isLearner)){
        var questionId = textarea.data('questionId'),
            type = 'text';
    }else{
        var chat = ttalkDiv,
            questionId = chat.find('.question_id').attr('value'),
            sessionId = chat.find('.session_id').attr('value'),
            messageType = messageType || 0,
            type,
            response;
            switch(messageType){
                case 1:
                    type = 'redirect';
                    response = chat.find('.redirect_node_id').val();
                    break;
                case 2:
                    type = 'bell';
                    response = '';
                    break;
                default :
                    type = 'text';
                    break;
            }

        idNode = chat.find('.node_id').text();
    }

    if((response != '' || type === 'bell') && !empty(questionId) && !empty(idNode)) {
        var data = {response: response, questionId: questionId, nodeId: idNode, isLearner: isLearner, sessionId: sessionId, type: type};
        $.ajax({
            url: urlBase + 'renderLabyrinth/saveTurkTalkResponse',
            type: 'post',
            data: data,
            async: true,
            success: function (response) {
                if(type !== 'bell') {
                    textarea.val('');
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

function showWaitingTime()
{
    var chats = $('#chats').find('.chat');

    chats.sort(function(a, b) {
        return (parseInt($(a).attr('data-waiting-time')) - parseInt($(b).attr('data-waiting-time')))*(-1);
    });

    chats.each(function(index, chat) {
        var chat = $(chat),
            progressBar = chat.find('.progress');

        progressBar.removeClass(function (ind, css) {
            return (css.match (/(^|\s)progress-\S+/g) || []).join(' ');
        });

        if(!empty(chat.attr('data-waiting-time'))){
            console.log(index);
            switch(index){
                case 0:
                    progressBar.addClass('progress-danger').find('.bar').css({width: '90%'});
                    break;
                case 1:
                    progressBar.addClass('progress-warning').find('.bar').css({width: '80%'});
                    break;
                case 2:
                    progressBar.addClass('progress-yellow').find('.bar').css({width: '70%'});
                    break;
                case 3:
                    progressBar.addClass('progress-success').find('.bar').css({width: '60%'});
                    break;
                case 4:
                    progressBar.addClass('progress-info').find('.bar').css({width: '50%'});
                    break;
                default:
                    progressBar.addClass('progress-info').find('.bar').css({width: '40%'});
                    break;
            }
            progressBar.removeClass('hide');
        }else{
            progressBar.addClass('hide');
        }
    });

}