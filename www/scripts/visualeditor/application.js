$(function () {
    var params = {
        'canvasContainer':'#canvasContainer',
        'canvasId':'#canvas',
        'aButtonsContianer': '#ve_additionalActionButton',
        'sectionSelectId': '#sectionsNodesSelect'
    };

    var tinyMCEConfigs = [{
        // General options

        theme: "modern",
        content_css: "<?php echo URL::base(); ?>scripts/tinymce/js/tinymce/plugins/rdface/css/rdface.css,<?php echo URL::base(); ?>scripts/tinymce/js/tinymce/plugins/rdface/schema_creator/schema_colors.css",
        entity_encoding: "raw",
        contextmenu: "link image inserttable | cell row column rdfaceMain",
        closed: /^(br|hr|input|meta|img|link|param|area|source)$/,
        valid_elements : "+*[*]",
        plugins: ["compat3x",
            "advlist autolink lists link image charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "emoticons template paste textcolor layer advtextcolor rdface imgmap"
        ],
        toolbar1: "insertfile undo redo | styleselect | bold italic | fontselect fontsizeselect | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
        toolbar2: " link image imgmap|print preview media | forecolor backcolor emoticons ltr rtl layer restoredraft | rdfaceMain",
        image_advtab: true,
        templates: [

        ],
        setup: function(ed) {

            ed.on("init", function(e){

                ed.on("click",function(e) {

                    veUnsavedData();
                });
            });


        }
    },{
        // General options

        theme: "modern",
        content_css: "<?php echo URL::base(); ?>scripts/tinymce/js/tinymce/plugins/rdface/css/rdface.css,<?php echo URL::base(); ?>scripts/tinymce/js/tinymce/plugins/rdface/schema_creator/schema_colors.css",
        entity_encoding: "raw",
        contextmenu: "link image inserttable | cell row column rdfaceMain",
        closed: /^(br|hr|input|meta|img|link|param|area|source)$/,
        valid_elements : "+*[*]",
        plugins: ["compat3x",
            "advlist autolink lists link image charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "emoticons template paste textcolor layer advtextcolor rdface imgmap"
        ],
        toolbar1: "insertfile undo redo | styleselect | bold italic | fontselect fontsizeselect | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
        toolbar2: " link image imgmap|print preview media | forecolor backcolor emoticons ltr rtl layer restoredraft | rdfaceMain",
        image_advtab: true,
        templates: [

        ]
    }];

    function setTinyMCE(configNumber, id) {
        tinyMCE.settings = tinyMCEConfigs[configNumber];
        tinyMCE.execCommand('mceAddEditor', true, id);
    }

    setTinyMCE(0, 'nodecontent');
    setTinyMCE(0, 'nodesupport');
    setTinyMCE(1, 'annotation');

    /*tinyMCE.init({
        // General options
        mode:"textareas",
        relative_urls:false,
        entity_encoding:"raw",
        theme:"advanced",
        skin:"bootstrap",
        plugins:"autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,imgmap",
        // Theme options
        theme_advanced_buttons1:"bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,cut,copy,paste,|,bullist,numlist,|,blockquote,",
        theme_advanced_buttons2:"styleselect,formatselect,fontselect,fontsizeselect,visualchars",
        theme_advanced_buttons3:"link,unlink,anchor,image,template,code,forecolor,backcolor,iespell,media,advhr,fullscreen,attribs,nonbreaking,outdent,indent",
        theme_advanced_buttons4:"tablecontrols,|,hr,removeformat,visualaid,help,",
        theme_advanced_toolbar_location:"top",
        theme_advanced_toolbar_align:"left",
        theme_advanced_statusbar_location:"bottom",
        theme_advanced_resizing:true,
        editor_selector:"mceEditor",
        setup: function(ed) {
            ed.onClick.add(function(ed, e) {
                veUnsavedData();
            });
        }
    });

    tinyMCE.init({
        // General options
        mode: "textareas",
        relative_urls: false,
        theme: "advanced",
        skin: "bootstrap",
        plugins:"autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,imgmap",
        // Theme options
        theme_advanced_buttons1:"bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,cut,copy,paste,|,bullist,numlist,|,blockquote,",
        theme_advanced_buttons2:"styleselect,formatselect,fontselect,fontsizeselect,visualchars",
        theme_advanced_buttons3: "cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,anchor,image,code,|,forecolor,backcolor",
        theme_advanced_buttons4: "sub,sup,|,charmap,iespell,media,advhr,|,fullscreen,del,ins,attribs,|,visualchars,nonbreaking,template",
        theme_advanced_toolbar_location: "top",
        theme_advanced_toolbar_align: "left",
        theme_advanced_statusbar_location: "bottom",
        theme_advanced_resizing: true,
        editor_selector: "mceEditorLite"
    });*/

    var autoSaveData = null;
    var visualEditor = new VisualEditor();
    visualEditor.Init(params);
    visualEditor.copyFunction = copy;
    visualEditor.pasteFunction = paste;
    visualEditor.zoomIn = zoomIn;
    visualEditor.zoomOut = zoomOut;
    visualEditor.update = update;
    visualEditor.turnOnPanMode = turnOnPanMode;
    visualEditor.turnOnSelectMode = turnOnSelectMode;
    visualEditor.save = save;

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
    
    $('#copySNodesBtn').click(function() {
        copy();
    });

    function paste() {
        utils.ShowMessage($veMessageContainer, $veMessage, 'info', 'Pasting...', null, $veActionButton, true);
        $.post(bufferPaste, {},
            function (data) {
                if (data) {
                    visualEditor.DeserializeFromPaste(data);
                    visualEditor.Render();
                utils.ShowMessage($veMessageContainer, $veMessage, 'success', 'Pasting has been successful', 3000, $veActionButton, false);
                }
            });
    }
    
    $('#pasteSNodesBtn').click(function() {
        paste();
    });

    if (mapJSON != null && mapJSON.length > 0) {
        if (mapType != null && mapType == 6) {
            visualEditor.DeserializeLinear(mapJSON);
        } else if (mapType != null && mapType == 9) {
            visualEditor.DeserializeBranched(mapJSON);
        } else {
            visualEditor.Deserialize(mapJSON);
        }

        visualEditor.Render();
    }

    function zoomIn() {
        if (!visualEditor.ZoomIn()) {
            $('#zoomIn').addClass('disabled');
        } else {
            $('#zoomIn').removeClass('disabled');
        }

        $('#zoomOut').removeClass('disabled');

        visualEditor.Render();
    }

    function zoomOut() {
        if (!visualEditor.ZoomOut()) {
            $('#zoomOut').addClass('disabled');
        } else {
            $('#zoomOut').removeClass('disabled');
        }

        $('#zoomIn').removeClass('disabled');

        visualEditor.Render();
    }

    $('#zoomIn').click(function () {
        zoomIn();
    });

    $('#zoomOut').click(function () {
        zoomOut();
    });

    $('#addNode').click(function () {
        visualEditor.AddNewNode();
        visualEditor.Render();
    });

    $('#setAsRootNodeBtn').click(function () {
        $('#veNodeRootBtn').addClass('active');
        $('#visual_editor_set_root').modal('hide');

        return false;
    });

    $('#veNodeRootBtn').click(function () {
        if ($(this).hasClass('active')) return false;

        $('#visual_editor_set_root').modal();

        return false;
    });

    var $veMessageContainer = $('#ve_message');
    var $veMessage = $('#ve_message_text');
    var $veActionButton = $('#ve_actionButton');

    function update() {
        if (autoSaveTimer != null) {
            clearTimeout(autoSaveTimer);
            visualEditor.isChanged = false;
            autoSaveTimerNotSet = true;
        }
        var data = visualEditor.Serialize();
        utils.ShowMessage($veMessageContainer, $veMessage, 'info', 'Updating...', null, $veActionButton, true);
        visualEditor.isChanged = false;
        autoSaveData = null;
        $('#leaveBox').modal('hide');

        $.post(sendURL, {
            data:data.substring(0, data.length - 1),
            id:mapId
        }, function (data) {
            if (data && data.length > 0) {
                data = data.substring(1, data.length - 1);
                data = data.substring(0, data.length - 1);

                utils.ShowMessage($veMessageContainer, $veMessage, 'success', 'Update has been successful', 3000, $veActionButton, false);

                if(leaveLink != null) {
                    $(location).attr('href', leaveLink);
                }

                visualEditor.Deserialize(data);
                visualEditor.Render();
            }
        });
    }

    $('.breadcrumb a').click(function() { return leaveBox($(this)); });
    $('.navbar-inner .dropdown-menu a:not(.dropdown-toggle)').click(function() { return leaveBox($(this)); });
    $('.navbar-inner .nav a:not(.dropdown-toggle)').click(function() { return leaveBox($(this)); });
    $('.nav-list a').click(function() { return leaveBox($(this)); });
    $('.wizard-next-buttons a').click(function() { return leaveBox($(this)); });

    var leaveLink = null;

    $('#closeLeaveBox').click(function() {
        leaveLink = null;
    });

    function leaveBox($object) {
        if(autoSaveData != null) {
            if($object != null) {
                leaveLink = $object.attr('href');
            }
            $('#leaveBox').modal();

            return false;
        }

        return true;
    }

    $('#uploadUnsaved').click(function() {
        update();
    });

    $('#leave').click(function() {
        if(leaveLink != null) {
            $(location).attr('href', leaveLink);
        }
    });

    $('#update').click(function () {
        update();
    });

    $('#veDandelion').click(function () {
        $('#visual_editor_dandelion').modal();
    });

    $('#veDandelionSaveBtn').click(function () {
        var value = $('#veDandelionCountContainer').children().filter('.active').attr('value');
        var count = 0;
        if (value == 'Custom') {
            count = $('#veDandelionCount').val();
            if (count < 3)
                count = 3;

            if (count > 30)
                count = 30;

            $('#veDandelionCount').val(count);
        } else {
            count = parseInt(value);
            if (isNaN(count)) count = 0;
        }

        if (count > 0) {
            visualEditor.AddDandelion(count);
            visualEditor.Render();
        }

        $('#visual_editor_dandelion').modal('hide');

        return false;
    });

    $('#veCountContainer button').click(function () {
        $('#veCount').attr('disabled', 'disabled');
    });

    $('#veCustom').click(function () {
        $('#veCount').removeAttr('disabled');
    });

    $('#backgroundColor').click(function () {
        $('#visual_editor_background_color').modal();
    });

    $('#veTemplate').click(function () {
        $('#visual_editor_template').modal();
    });

    $('#settings').click(function() {
        $('#veSettings').modal();
        $('#autosaveTime').val(autosaveInterval * 0.001);
    });

    $('#veSaveSettings').click(function() {
        var value = $('#autosaveTime').val();
        if(value != null && value > 0) {
            if(value < 10) {
                value = 10;
            }

            autosaveInterval = value * 1000;
            $.post(settingsURL, {
                time: autosaveInterval
            }, function(data) {
                $('#veSettings').modal('hide');
            });
        }
    });

    $('#fullScreen').tooltip();
    $('#update').tooltip();
    $('#addNode').tooltip({html:true});
    $('#vePan').tooltip({html:true});
    $('#veSelect').tooltip({html:true});
    $('#veTemplate').tooltip({html:true});
    $('#zoomIn').tooltip({html:true});
    $('#zoomOut').tooltip({html:true});
    $('#undo').tooltip({html: true});
    $('#redo').tooltip({html: true});

    $('#veTemplateSaveBtn').click(function () {
        var value = $('#veCountContainer').children().filter('.active').attr('value');
        var type = $('#veTypeContainer').children().filter('.active').attr('value');
        var count = 0;
        if (value == 'Custom') {
            value = $('#veCount').val();
        }

        count = parseInt(value);
        if (isNaN(count)) count = 0;

        if (count > 0) {
            if (type == 'dandelion') {
                if (count < 3)
                    count = 3;
                visualEditor.AddDandelion(count);
            } else if (type == 'branched') {
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
    $('.defined-color-picker div').click(function () {
        $definedColorContainer.children().removeClass('active');
        $(this).addClass('active');
    });

    var hexDigits = new Array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "b", "c", "d", "e", "f");

    function rgb2hex(rgb) {
        rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
        return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
    }

    function hex(x) {
        return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
    }

    $('#veBgColorSaveBtn').click(function () {
        $canvas.css('background-color', $definedColorContainer.children('.active').css('background-color'));
        $('#visual_editor_background_color').modal('hide');
    });

    var $vePan = $('#vePan');
    var $veSelect = $('#veSelect');

    $vePan.click(function () {
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

    setInterval(checkForAutoSave, 5000);

    var autoSaveTimerNotSet = true;
    var autoSaveTimer = null;
    function checkForAutoSave(){
        if (visualEditor.isChanged) {
            if (autoSaveTimerNotSet){
                autoSaveTimerNotSet = false;
                autoSaveTimer = setTimeout(autoSave, autosaveInterval);
            }
        }
    }

    function autoSave() {
        if (autoSaveTimer != null) {clearTimeout(autoSaveTimer)};

        utils.ShowMessage($veMessageContainer, $veMessage, 'info', 'Autosaving...', null, $veActionButton, false);
        visualEditor.isChanged = false;
        autoSaveTimerNotSet = true;
        autoSaveData = visualEditor.Serialize();
        utils.ShowMessage($veMessageContainer, $veMessage, 'success', 'Autosave has been completed.', 3000, $veActionButton, false);
    }

    function save() {
        utils.ShowMessage($veMessageContainer, $veMessage, 'info', 'Saving...', null, $veActionButton, false);
        visualEditor.isChanged = false;
        autoSaveTimerNotSet = true;
        autoSaveData = visualEditor.Serialize();
        utils.ShowMessage($veMessageContainer, $veMessage, 'success', 'Save has been completed.', 3000, $veActionButton, false);
    }

    var canvasWidth;
    var canvasHeight;
    $('#fullScreen').click(function () {
        if ($(this).hasClass('active')) {
            $('body').css({'overflow':'auto', 'width':'auto', 'height':'auto', 'padding-top':'60px', 'padding-bottom':'40px'});
            $(this).removeClass('active');
            $('#canvasContainer').css('position', 'relative');
            $('#canvasContainer').css('z-index', '0');
            $('#canvasContainer').css('height', canvasHeight);
            $('#canvasContainer').css('width', canvasWidth);

            $('.navbar-fixed-top').css('z-index', 1030);
            $('#canvas').attr('width', canvasWidth);
            $('#canvas').attr('height', canvasHeight);
            $('#tab-content-scrollable').css('height', '430px');

            visualEditor.Render();
        } else {
            var h = window.innerHeight;
            var w = window.innerWidth;

            $(document).scrollTop(0);
            $('body').css({'width': '100%', 'height': '100%', 'margin':'0', 'padding':'0', 'overflow':'hidden'});
            canvasWidth = $('#canvas').attr('width');
            canvasHeight = $('#canvas').attr('height');
            $('.navbar-fixed-top').css('z-index', 0);
            $(this).addClass('active');
            $('#canvasContainer').css('position', 'absolute');
            $('#canvasContainer').css('top', '0');
            $('#canvasContainer').css('left', '0');
            $('#canvasContainer').css('z-index', '10');

            if (w < 100) w = 100;
            $('#canvas').attr('width', w + "px");
            $('#canvas').css('display', "block");
            $('#canvasContainer').css('width', w + "px");

            if (h < 400) h = 400;
            $('#canvas').attr('height', h + "px");
            $('#canvasContainer').css('height', h + "px");

            if (h > 545) h = 545;
            $('#tab-content-scrollable').css('height', (h - 115) + 'px');
            visualEditor.Render();
        }
    });

    $('#veRightPanel').draggable({handle: '.visual-editor-right-panel-tabs', cursor: 'move', scroll: false, containment: "#canvasContainer"});
    $('#veSelectRightPanel').draggable({handle: '.visual-editor-right-panel-tabs', cursor: 'move', scroll: false, containment: "#canvasContainer"});
    $('#veSectionPanel').draggable({handle: '.visual-editor-right-panel-tabs', cursor: 'move', scroll: false, containment: "#canvasContainer"});

    $('#veRightPanel input[type=text]').keyup(function(){
        veUnsavedData();
    });

    $('#veRightPanel button:not("#veDeleteNodeBtn")').click(function(){
        veUnsavedData();
    });

    $('#veRightPanel input[type=radio]').click(function(){
        veUnsavedData();
    });

    $('#veRightPanel input[type=checkbox]').click(function(){
        veUnsavedData();
    });

    function veUnsavedData(){
        visualEditor.unsavedData = true;
    }

    var $makeSectionBox       = $('#veMakeSectionBox'),
        $sectionNodeContainer = $('#sectionNodesContainer');

    $('#veMakeSectionBtn').click(function() {
        var html          = '<label for="nodetitle" class="control-label" style="text-align: left;"><strong>%nodeTitle%</strong></label>' +
                            '<div class="controls"><select id="sectionNodeOrder%orderNodeId%" class="section-nodes-order">%sectionOptions%</select></div>' +
                            '<div class="section-node" nodeId="%nodeId%"></div>',
            optionsHtml   = '<option value="%value%">%title%</option>',
            selectedNodes = visualEditor.GetSelectedNodes(),
            length        = 0,
            options       = '',
            nodes         = '',
            i             = 0;

        length = selectedNodes.length;
        if(length <= 0) return false;

        for(i = 0; i < length; i++) {
            options += optionsHtml.replace('%value%', i).replace('%title%', i);
        }

        for(i = length; i--;) {
            nodes += html.replace('%nodeTitle%', selectedNodes[i].title)
                         .replace('%sectionOptions%', options)
                         .replace('%orderNodeId%', selectedNodes[i].id)
                         .replace('%nodeId%', selectedNodes[i].id);
        }

        $sectionNodeContainer.empty().append(nodes);

        $makeSectionBox.modal();
    });

    $('#veMakeNewSectionBtn').click(function() {
        var $sectionNodes = $('.section-node'),
            sectionName   = $('#sectionNameInput').val(),
            nodesIDs      = [];

        $sectionNodes.each(function(index, value) {
            var nodeId = $(this).attr('nodeId');
            if(nodeId != null) {
                nodesIDs.push({nodeId: nodeId, order: $('#sectionNodeOrder' + nodeId).val()});
            }
        });

        visualEditor.AddNewSection(sectionName, nodesIDs);

        $makeSectionBox.modal('hide');
    });

    $('#veSectionClosePanelBtn').click(function() {
        $('#veSectionPanel').addClass('hide');
    });

    $('#sectionsNodesSelect').change(function() {
        sectionData($(this).val());
    });

    $('.removeNodeFromSection').live('click', function() {
        var nodeId    = $(this).attr('nodeId'),
            sectionId = $(this).attr('sectionId');

        visualEditor.RemoveNodeFromSection(sectionId, nodeId);
        $(this).parent().parent().remove();
    });

    $('#removeSection').click(function() {
        var sectionId = $(this).attr('sectionId');

        visualEditor.RemoveSection(sectionId);
        $('#sectionSettings').addClass('hide');
        $('#sectionNodeContainer').empty();
        $('#sectionsNodesSelect option[value="' + sectionId + '"]').remove();
    });

    $('#addNodeToSection').click(function() {
        var sectionId  = $(this).attr('sectionId'),
            addedNodes = visualEditor.AddNodesToSection(sectionId);

        sectionData(sectionId);
    });

    function sectionData(sectionId) {
        var section = visualEditor.GetSectionById(sectionId),
            html    = '<div><label for="nodetitle" class="control-label" style="text-align: left;"><strong>%nodeName%</strong></label><div class="controls">'+
                      '<select id="selectSectionNodeOrder%orderNodeId%" style="margin-bottom: 0">%options%</select>' +
                      '<button class="btn btn-danger removeNodeFromSection" nodeId="%removeNodeId%" sectionId="%sectionId%"><i class="icon-trash"></i></button></div>' +
                      '<div class="sectionNode" nodeId="%nodeId%"></div></div>',
            options = '',
            length  = 0,
            append  = '';

        $('#sectionSettings').removeClass('hide');
        $('#sectionName').val(section.name);
        $('#veSectionSaveBtn').attr('sectionId', section.id);
        $('#removeSection').attr('sectionId', section.id);
        $('#addNodeToSection').attr('sectionId', section.id);
        $('#sectionNodeContainer').empty().append('<div><b>Nodes:</b></div>');
        if(section != null) {
            length = section.nodes.length;
            if(length > 0) {
                for(var i = length; i--;) {
                    options = '';
                    append  = '';
                    for(var j = 0; j < length; j++) {
                        options += '<option value="' + j + '" ' + (j == section.nodes[i].order ? 'selected="selected"' : '') + '>' + j + '</option>';
                    }

                    append = html.replace('%nodeName%', section.nodes[i].node.title)
                        .replace('%options%', options)
                        .replace('%orderNodeId%', section.nodes[i].node.id)
                        .replace('%nodeId%', section.nodes[i].node.id)
                        .replace('%sectionId%', section.id)
                        .replace('%removeNodeId%', section.nodes[i].node.id);

                    $('#sectionNodeContainer').append(append);
                }
            }
        }
    }

    $('#veSectionSaveBtn').click(function() {
        var sectionName = $('#sectionName').val(),
            $nodes      = $('.sectionNode'),
            nodes       = [],
            nodeId      = 0;

        $nodes.each(function(index, value) {
            nodeId = $(this).attr('nodeId');
            nodes.push({nodeId: nodeId, order: $('#selectSectionNodeOrder' + nodeId).val()});
        });

        visualEditor.UpdateSection($(this).attr('sectionId'), sectionName, nodes);
    });

    $('#sectionsBtn').click(function() {
        $('#veSectionPanel').removeClass('hide');
    });
});