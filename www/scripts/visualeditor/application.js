$(function() {
    var params = {
        'canvasContainer': '#canvasContainer',
        'canvasId': '#canvas'
    };
    
    /*tinyMCE.init({
        // General options
        mode: "textareas",
        relative_urls : false,
        entity_encoding: "raw",
        theme: "advanced",
        plugins: "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,imgmap",
        // Theme options
        theme_advanced_buttons1: "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull",
        theme_advanced_buttons2: "styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons3: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo",
        theme_advanced_buttons4: "link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons5: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup",
        theme_advanced_buttons6: "charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
        theme_advanced_buttons7: "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,|,imgmap",
        theme_advanced_toolbar_location: "top",
        theme_advanced_toolbar_align: "left",
        theme_advanced_statusbar_location: "bottom",
        theme_advanced_resizing: true,
        editor_selector: "mceEditor"
    });*/

    var visualEditor = new VisualEditor();
    visualEditor.Init(params);
    
    if(mapJSON != null && mapJSON.length > 0 && saveMapJSON != null && saveMapJSON.length > 0) {
        $('#visual_editor_restore').modal();
    } else if(mapJSON != null && mapJSON.length > 0) {
        visualEditor.Deserialize(mapJSON);
        visualEditor.Render();
    }
    
    $('#veLastSave').click(function() {
        if(saveMapJSON == 'empty')
            saveMapJSON = '';
        visualEditor.Deserialize(saveMapJSON);
        visualEditor.Render();
        $('#visual_editor_restore').modal('hide');
    });
    
    $('#veCurrentSave').click(function() {
        visualEditor.Deserialize(mapJSON);
        visualEditor.Render();
        $('#visual_editor_restore').modal('hide');
    });
    
    $('#zoomIn').click(function() {
        if(!visualEditor.ZoomIn()) {
            $(this).addClass('disabled');
        } else {
            $(this).removeClass('disabled');
        }
    
        $('#zoomOut').removeClass('disabled');
        
        visualEditor.Render();
    });
    
    $('#zoomOut').click(function() {
        if(!visualEditor.ZoomOut()) {
            $(this).addClass('disabled');
        } else {
            $(this).removeClass('disabled');
        }
    
        $('#zoomIn').removeClass('disabled');
        
        visualEditor.Render();
    });
    
    $('#serialize').click(function() {
        visualEditor.Serialize();
    });
    
    $('#deserialize').click(function() {
        var str = '{nodes: [{id: "1", isRoot: "true", isNew: "false", title: "Sales & Operations Planning", content: "Leadership is in your sights! This position is a key liaison between Manufacturing, Marketing, Sales, Logistics, and Planning Forecasting. This role also oversees the planning", support: "", supportKeywords: "", isExit: "false", linkStyle: "1", nodePriority: "1", undo: "false", isEnd: "false", x: "180", y: "20", color: "#ffffff", counters: [{id: "1", func: "=200", show: "true"}]}, {id: "2", isRoot: "false", isNew: "false", title: "", content: "", support: "", supportKeywords: "", isExit: "false", linkStyle: "1", nodePriority: "1", undo: "false", isEnd: "false", x: "10", y: "100", color: "#ffffff", counters: [{id: "1", func: "=300", show: "false"}]}], links: [{id: "1", nodeA: "1", nodeB: "2", type: "back", isNew: "false"}]};';
        visualEditor.Deserialize(str);
        visualEditor.Render();
    });
    
    $('#addNode').click(function() {
        visualEditor.AddNewNode();
        visualEditor.Render();
    });

    var $veMessageContainer = $('#ve_message');
    var $veMessage = $('#ve_message_text');
    var $veActionButton = $('#ve_actionButton');

    $('#update').click(function() {
        var data = visualEditor.Serialize();
        showMessage($veMessageContainer, $veMessage, 'info', 'Updating...', null, $veActionButton, true);
        $.post(sendURL, {
            data: data.substring(0, data.length - 1),
            id: mapId
        }, function(data) {
            if(data && data.length > 0) {
                data = data.substring(1, data.length - 1);
                data = data.substring(0, data.length - 1);

                showMessage($veMessageContainer, $veMessage, 'success', 'Update has been successful', 3000, $veActionButton, false);

                visualEditor.Deserialize(data);
                visualEditor.Render();
            }
        });
    });
    
    setInterval(autoSave, 60000);
    
    function autoSave() {
        if(visualEditor.isChanged) {
            visualEditor.isChanged = false;
            var data = visualEditor.Serialize();
            $.post(autoSaveURL, {
                data: data.substring(0, data.length - 1),
                id: mapId
            }, function(data) {
                if(data != 'fail') {
                    showMessage($veMessageContainer, $veMessage, 'success', data, 1500, null, null);
                }
            });
        }
    }

    function showMessage(messageForm, messageObj, messageType, message, timeOut, hideObj, hide){
        messageForm.removeClass('alert-success');
        messageForm.removeClass('alert-error');
        messageForm.removeClass('alert-info');

        messageForm.addClass('alert-' + messageType);
        messageObj.text(message);

        var width = parseInt(messageForm.width());
        messageForm.css('margin-left', '-' + (width / 2) + 'px');
        messageForm.removeClass('hide');

        if (hideObj != null){
            if (hide == true){
                hideObj.addClass('hide');
            } else {
                hideObj.removeClass('hide');
            }
        }

        if (timeOut != null){
            setTimeout(function() {
                messageForm.addClass('hide');
                messageObj.text('');
            }, timeOut);
        }
    }
});