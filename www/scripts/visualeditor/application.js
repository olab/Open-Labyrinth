$(function() {
    var params = {
        'canvasContainer': '#canvasContainer',
        'canvasId': '#canvas'
    };
    
    tinyMCE.init({
        // General options
        mode: "textareas",
        relative_urls : false,
        entity_encoding: "raw",
        theme: "advanced",
        skin:"bootstrap",
        plugins: "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,imgmap",
        // Theme options
        theme_advanced_buttons1: "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull",
        theme_advanced_buttons2: "styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons3: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo",
        theme_advanced_buttons4: "link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons5:"tablecontrols,|,hr,removeformat,visualaid",
        theme_advanced_buttons6:"sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen,|,insertlayer,moveforward,movebackward,absolute",
        theme_advanced_buttons7:"styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,|,imgmap",
        theme_advanced_toolbar_location: "top",
        theme_advanced_toolbar_align: "left",
        theme_advanced_statusbar_location: "bottom",
        theme_advanced_resizing: true,
        editor_selector: "mceEditor"
    });

    var visualEditor = new VisualEditor();
    visualEditor.Init(params);
    visualEditor.copyFunction = copy;
    visualEditor.pasteFunction = paste;
    visualEditor.zoomIn = zoomIn;
    visualEditor.zoomOut = zoomOut;
    visualEditor.update = update;
    visualEditor.turnOnPanMode = turnOnPanMode;
    visualEditor.turnOnSelectMode = turnOnSelectMode;

    function copy() {
        var data = visualEditor.SerializeSelected();

        if(data != null && data.length > 0) {
            utils.ShowMessage($veMessageContainer, $veMessage, 'info', 'Copying...', null, $veActionButton, true);

            $.post(bufferCopy, {
                data: data.substring(0, data.length - 1)
                }, function(dataResponse) {
                utils.ShowMessage($veMessageContainer, $veMessage, 'success', 'Copy has been successful', 3000, $veActionButton, false);
            });
        }
    }
    
    function paste() {
        utils.ShowMessage($veMessageContainer, $veMessage, 'info', 'Pasting...', null, $veActionButton, true);
        $.post(bufferPaste, {}, 
        function(data) {
            if(data) {
                visualEditor.DeserializeFromPaste(data);
                visualEditor.Render();
                utils.ShowMessage($veMessageContainer, $veMessage, 'success', 'Pasting has been successful', 3000, $veActionButton, false);
            }
        });
    }

    if(mapJSON != null && mapJSON.length > 0) {
         if(mapType != null && mapType == 6) {
            visualEditor.DeserializeLinear(mapJSON);
        } else if(mapType != null && mapType == 9) {
            visualEditor.DeserializeBranched(mapJSON);
        } else {
            visualEditor.Deserialize(mapJSON);
        }
        
        visualEditor.Render();
    }
    
    function zoomIn() {
        if(!visualEditor.ZoomIn()) {
            $('#zoomIn').addClass('disabled');
        } else {
            $('#zoomIn').removeClass('disabled');
        }
    
        $('#zoomOut').removeClass('disabled');
        
        visualEditor.Render();
    }
    
    function zoomOut() {
        if(!visualEditor.ZoomOut()) {
            $('#zoomOut').addClass('disabled');
        } else {
            $('#zoomOut').removeClass('disabled');
        }
    
        $('#zoomIn').removeClass('disabled');
        
        visualEditor.Render();
    }
    
    $('#zoomIn').click(function() {
        zoomIn();
    });
    
    $('#zoomOut').click(function() {
        zoomOut();
    });
    
    $('#addNode').click(function() {
        visualEditor.AddNewNode();
        visualEditor.Render();
    });
    
    $('#setAsRootNodeBtn').click(function() {
        $('#veNodeRootBtn').addClass('active');
        $('#visual_editor_set_root').modal('hide');
        
        return false;
    });
    
    $('#veNodeRootBtn').click(function() {
        if($(this).hasClass('active')) return false;
        
        $('#visual_editor_set_root').modal();
        
        return false;
    })
    
    var $veMessageContainer = $('#ve_message');
    var $veMessage = $('#ve_message_text');
    var $veActionButton = $('#ve_actionButton');
    
    function update() {
        var data = visualEditor.Serialize();
        utils.ShowMessage($veMessageContainer, $veMessage, 'info', 'Updating...', null, $veActionButton, true);
        visualEditor.isChanged = false;
        
        $.post(sendURL, {
            data: data.substring(0, data.length - 1),
            id: mapId
        }, function(data) {
            if(data && data.length > 0) {
                data = data.substring(1, data.length - 1);
                data = data.substring(0, data.length - 1);
                
                utils.ShowMessage($veMessageContainer, $veMessage, 'success', 'Update has been successful', 3000, $veActionButton, false);
                
                visualEditor.Deserialize(data);
                visualEditor.Render();
            }
        });
    }
    
    $('#update').click(function() {
        update();
    });
    
    $('#veDandelion').click(function() {
       $('#visual_editor_dandelion').modal();
    });
    
    $('#veDandelionSaveBtn').click(function() {
        var value = $('#veDandelionCountContainer').children().filter('.active').attr('value');
        var count = 0;
        if(value == 'Custom') {
            count = $('#veDandelionCount').val();
            if(count < 3)
            count = 3;
        
            if(count > 30)
                count = 30;
            
            $('#veDandelionCount').val(count);
        } else {
            count = parseInt(value);
            if(isNaN(count)) count = 0;
        }
        
        if(count > 0) {
            visualEditor.AddDandelion(count);
            visualEditor.Render();
        }
        
        $('#visual_editor_dandelion').modal('hide');
        
        return false;
    });
    
    $('#veCountContainer button').click(function() {
        $('#veCount').attr('disabled', 'disabled');
    })
    
    $('#veCustom').click(function() {
        $('#veCount').removeAttr('disabled');
    })
    
    $('#backgroundColor').click(function() {
        $('#visual_editor_background_color').modal();
    });
    
    $('#veTemplate').click(function() {
       $('#visual_editor_template').modal(); 
    });
    
    $('#update').tooltip();
    $('#addNode').tooltip({html: true});
    $('#vePan').tooltip({html: true});
    $('#veSelect').tooltip({html: true});
    $('#veTemplate').tooltip({html: true});
    $('#zoomIn').tooltip({html: true});
    $('#zoomOut').tooltip({html: true});
    
    $('#veTemplateSaveBtn').click(function() {
        var value = $('#veCountContainer').children().filter('.active').attr('value');
        var type = $('#veTypeContainer').children().filter('.active').attr('value');
        var count = 0;
        if(value == 'Custom') {
            value = $('#veCount').val();
        }
        
        count = parseInt(value);
        if(isNaN(count)) count = 0;
        
        if(count > 0) {
            if(type == 'dandelion') {
                if(count < 3)
                    count = 3;
                visualEditor.AddDandelion(count);
            } else if(type == 'branched') {
                visualEditor.AddBranched(count);
            } else {
                visualEditor.AddLinear(count);
            }
            
            visualEditor.Render();
        }
        
        $('#visual_editor_template').modal('hide');
        
        return false;
    });
    
    var $definedColorContainer = $('.defined-color-picker');
    var $canvas = $('#canvas');
    $('.defined-color-picker div').click(function() {
        $definedColorContainer.children().removeClass('active');
        $(this).addClass('active');
    });
    
    var hexDigits = new Array("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f"); 

    function rgb2hex(rgb) {
        rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
        return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
    }

    function hex(x) {
        return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
    }
    
    $('#veBgColorSaveBtn').click(function() {
        $canvas.css('background-color', $definedColorContainer.children('.active').css('background-color'));
        $('#visual_editor_background_color').modal('hide');
    });
    
    var $vePan = $('#vePan');
    var $veSelect = $('#veSelect');
    
    $vePan.click(function() {
        turnOnPanMode();
    });

    function turnOnPanMode(){
        $('body').addClass('clearCursor');
        $('body').css('cursor', 'move');
        $veSelect.removeClass('active');
        $vePan.addClass('active');
        visualEditor.isSelectActive = false;
    }

    $veSelect.click(function () {
        turnOnSelectMode();
    });
    
    function turnOnSelectMode(){
        $('body').addClass('clearCursor');
        $('body').css('cursor', 'crosshair');
        $vePan.removeClass('active');
        $veSelect.addClass('active');
        visualEditor.isSelectActive = true;
    }
    
    setInterval(autoSave, 60000);
    
    function autoSave() {
        if(visualEditor.isChanged) {
            visualEditor.isChanged = false;
            var data = visualEditor.Serialize();
            utils.ShowMessage($veMessageContainer, $veMessage, 'info', 'Autosaving...', null, $veActionButton, false);

            $.post(sendURL, {
                data: data.substring(0, data.length - 1),
                id: mapId
            }, function(data) {
                utils.ShowMessage($veMessageContainer, $veMessage, 'success', 'Autosave has been completed.', 3000, $veActionButton, false);
            });
        }
    }

    var canvasWidth;
    var canvasHeight;
    $('#fullScreen').click(function () {
        if ($(this).hasClass('active')) {
            $('body').css({'padding-top':'60px', 'padding-bottom':'40px'});
            $(this).removeClass('active');
            $('#canvasContainer').css('position', 'relative');
            $('#canvasContainer').css('z-index', '0');
            $('#canvasContainer').css('height', canvasHeight);

            $('.navbar-fixed-top').css('z-index', 1030);
            $('#canvas').attr('width', canvasWidth);
            $('#canvas').attr('height', canvasHeight);

            visualEditor.Render();
        } else {
            $('body').css({'width':'100%', 'height':'100%', 'margin':'0', 'padding':'0'});
            canvasWidth = $('#canvas').attr('width');
            canvasHeight = $('#canvas').attr('height');
            $('.navbar-fixed-top').css('z-index', 0);
            $(this).addClass('active');
            $('#canvasContainer').css('position', 'absolute');
            $('#canvasContainer').css('top', '0');
            $('#canvasContainer').css('left', '0');
            $('#canvasContainer').css('z-index', '10');
            var w = window.innerWidth;
            if (w < 100) w = 100;
            $('#canvas').attr('width', w + "px");
            $('#canvas').css('display', "block");

            var h = window.innerHeight;
            if (h < 400) h = 400;
            $('#canvas').attr('height', h + "px");
            $('#canvasContainer').css('height', h + "px");
            visualEditor.Render();
        }
    });

    $('#veRightPanel').draggable({handle: '.visual-editor-right-panel-tabs', cursor: 'move', scroll: false, containment: "#canvasContainer"});
});