$(function () {
    var params = {
        'canvasContainer':'#canvasContainer',
        'canvasId':'#canvas',
        'aButtonsContianer': '#ve_additionalActionButton',
        'sectionSelectId': '#sectionsNodesSelect'
    };

    var tinyMCEConfigs = [{
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
        setup: function(ed) {
            ed.onClick.add(function(ed, e) {
                veUnsavedData();
            });
        }
    },{
        // General options
        mode: "textareas",
        relative_urls: false,
        theme: "advanced",
        skin: "bootstrap",
        plugins: "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,imgmap",
        // Theme options
        theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,cut,copy,paste,pastetext,pasteword",
        theme_advanced_buttons2: "styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons3: "bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,anchor,image,code,forecolor,backcolor,sub,sup",
        theme_advanced_buttons4: "charmap,iespell,media,advhr,|,fullscreen,del,ins,attribs,|,visualchars,nonbreaking,template",
        theme_advanced_toolbar_location: "top",
        theme_advanced_toolbar_align: "left",
        theme_advanced_statusbar_location: "bottom",
        theme_advanced_resizing: true,
        entity_encoding: "raw"
    }];

    function setTinyMCE(configNumber, id) {
        tinyMCE.settings = tinyMCEConfigs[configNumber];
        tinyMCE.execCommand('mceAddControl', true, id);
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

    var vZoomIn          = $('#zoomIn'),
        vZoomOut         = $('#zoomOut'),
        vFullScreen      = $('#fullScreen'),
        vCanvas          = $('#canvas'),
        vCanvasContainer = $('#canvasContainer'),
        canvasWidth,
        canvasHeight,
        visualEditor     = new VisualEditor();
        visualEditor.Init(params);
        visualEditor.zoomIn = zoomIn;
        visualEditor.zoomOut = zoomOut;


    if (mapJSON != null && mapJSON.length > 0)
    {
        if      (mapType != null && mapType == 6) visualEditor.DeserializeLinear(mapJSON);
        else if (mapType != null && mapType == 9) visualEditor.DeserializeBranched(mapJSON);
        else                                      visualEditor.Deserialize(mapJSON);

        visualEditor.Render();
    }

    function zoomIn()
    {
        if ( ! visualEditor.ZoomIn()) vZoomIn.addClass('disabled');
        else vZoomIn.removeClass('disabled');

        vZoomOut.removeClass('disabled');
        visualEditor.Render();
    }

    function zoomOut()
    {
        if ( ! visualEditor.ZoomOut()) vZoomOut.addClass('disabled');
        else vZoomOut.removeClass('disabled');

        vZoomIn.removeClass('disabled');
        visualEditor.Render();
    }

    vZoomIn.click(function () {
        zoomIn();
    });

    vZoomOut.click(function () {
        zoomOut();
    });

    vFullScreen.tooltip();
    vZoomIn.tooltip({html:true});
    vZoomOut.tooltip({html:true});

    vFullScreen.click(function ()
    {
        if ($(this).hasClass('active'))
        {
            $('body').css({'overflow':'auto', 'width':'auto', 'height':'auto', 'padding-top':'60px', 'padding-bottom':'40px'});
            $(this).removeClass('active');
            vCanvasContainer.css('position', 'relative');
            vCanvasContainer.css('z-index', '0');
            vCanvasContainer.css('height', canvasHeight);
            vCanvasContainer.css('width', canvasWidth);

            $('.navbar-fixed-top').css('z-index', 1030);
            vCanvas.attr('width', canvasWidth);
            vCanvas.attr('height', canvasHeight);
            $('#tab-content-scrollable').css('height', '430px');

            visualEditor.Render();
        }
        else
        {
            var h = window.innerHeight;
            var w = window.innerWidth;

            $(document).scrollTop(0);
            $('body').css({'width': '100%', 'height': '100%', 'margin':'0', 'padding':'0', 'overflow':'hidden'});
            canvasWidth = vCanvas.attr('width');
            canvasHeight = vCanvas.attr('height');
            $('.navbar-fixed-top').css('z-index', 0);
            $(this).addClass('active');
            vCanvasContainer.css('position', 'absolute');
            vCanvasContainer.css('top', '0');
            vCanvasContainer.css('left', '0');
            vCanvasContainer.css('z-index', '10');

            if (w < 100) w = 100;
            vCanvas.attr('width', w + "px");
            vCanvas.css('display', "block");
            vCanvasContainer.css('width', w + "px");

            if (h < 400) h = 400;
            vCanvas.attr('height', h + "px");
            vCanvasContainer.css('height', h + "px");

            if (h > 545) h = 545;
            $('#tab-content-scrollable').css('height', (h - 115) + 'px');
            visualEditor.Render();
        }
    });
});