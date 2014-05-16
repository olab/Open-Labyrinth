$(function () {
    var params = {
        'canvasContainer':'#canvasContainer',
        'canvasId':'#canvas',
        'aButtonsContainer': '#ve_additionalActionButton',
        'stepSelect': '#stepSelect'
    };

    var urlBase = window.location.origin + '/',
        body    = $('body');

    var visualEditor = new VisualEditor();
        visualEditor.Init(params);
        visualEditor.zoomIn = zoomIn;
        visualEditor.zoomOut = zoomOut;
        visualEditor.turnOnPanMode = turnOnPanMode;
        visualEditor.turnOnSelectMode = turnOnSelectMode;

    if (scenarioJSON)
    {
        visualEditor.deserialize(scenarioJSON);
        visualEditor.Render();
    }

    // ----- zoom ----- //

    var $zoomIn = $('#zoomIn'),
        $zoomOut = $('#zoomOut');

    function zoomIn() {
        if ( ! visualEditor.ZoomIn()) $zoomIn.addClass('disabled');
        else $zoomIn.removeClass('disabled');

        $zoomOut.removeClass('disabled');

        visualEditor.Render();
    }

    function zoomOut() {
        if ( ! visualEditor.ZoomOut()) $zoomOut.addClass('disabled');
        else $zoomOut.removeClass('disabled');

        $zoomIn.removeClass('disabled');

        visualEditor.Render();
    }

    $zoomIn.click(function () {
        zoomIn();
    });

    $zoomOut.click(function () {
        zoomOut();
    });

    // ----- zoom end ----- //

    // ----- element (map or section) ----- //
    var messageContainer = $('#ve_message'),
        veActionButton   = $('#ve_actionButton');

    function createElement(type){
        var created = visualEditor.addNewElement(type);

        if (created) visualEditor.Render();
        else utils.ShowMessage (messageContainer, messageContainer, 'info', 'Choose ' + type + '...', 2000);
    }

    $('#addMap').click(function() {
        createElement('labyrinth');
    });

    $('#addSection').click(function() {
        createElement('section');
    });

    // ----- end element (map or section) ----- //

    // ----- update ----- //
    function update(){
        var data = visualEditor.serialize();

        utils.ShowMessage(messageContainer, messageContainer, 'info', 'Updating...', null, veActionButton, true);

        $.post(
            urlBase + 'WebinarManager/ajaxStepUpdate', {data: data, scenarioId: scenarioId},
            function (data){
                visualEditor.deserialize(data);
                visualEditor.Render();
                utils.ShowMessage(messageContainer, messageContainer, 'success', 'Update has been successful', 3000, veActionButton, false);
        });
    }

    $('#update').click(function(){
        update();
    });
    // ----- end update ----- //

    // ----- grab and select mode ----- //
    var $vePan      = $('#vePan');
    var $veSelect   = $('#veSelect');

    function turnOnPanMode(){
        body.addClass('clearCursor');
        body.css('cursor', 'move');
        $veSelect.removeClass('active');
        $vePan.addClass('active');
        visualEditor.isSelectActive = false;
    }

    function turnOnSelectMode()
    {
        body.addClass('clearCursor');
        body.css('cursor', 'crosshair');
        $vePan.removeClass('active');
        $veSelect.addClass('active');
        visualEditor.isSelectActive = true;
    }

    $vePan.click(function () {
        turnOnPanMode();
    });

    $veSelect.click(function () {
        turnOnSelectMode();
    });
    // ----- end grab and select mode ----- //

    // ----- full screen ----- //
    var $canvas = $('#canvas'),
        $canvasContainer = $('#canvasContainer'),
        canvasWidth,
        canvasHeight;

    $('#fullScreen').click(function () {
        if ($(this).hasClass('active')) {
            body.css({'overflow':'auto', 'width':'auto', 'height':'auto', 'padding-top':'60px', 'padding-bottom':'40px'});
            $(this).removeClass('active');
            $canvasContainer.css({'position':'relative', 'z-index':'0', 'height':canvasHeight, 'width':canvasWidth});

            $('.navbar-fixed-top').css('z-index', 1030);
            $canvas.attr('width', canvasWidth);
            $canvas.attr('height', canvasHeight);

            visualEditor.Render();
        } else {
            var h = window.innerHeight,
                w = window.innerWidth;

            $(document).scrollTop(0);

            body.css({'width': '100%', 'height': '100%', 'margin':'0', 'padding':'0', 'overflow':'hidden'});
            $(this).addClass('active');
            $canvasContainer.css({'position':'absolute', 'top':'0', 'left':'0', 'z-index':'10'});

            $('.navbar-fixed-top').css('z-index', 0);
            canvasWidth = $canvas.attr('width');
            canvasHeight = $canvas.attr('height');

            if (w < 100) w = 100;
            $canvas.attr('width', w + "px");
            $canvas.css('display', "block");
            $canvasContainer.css('width', w + "px");

            if (h < 400) h = 400;
            $canvas.attr('height', h + "px");
            $canvasContainer.css('height', h + "px");

            visualEditor.Render();
        }
    });
    // ----- end full screen ----- //

    // ----- step ----- //
    var currentStepName = $('#stepName'),
        stepSelect      = $('#stepSelect'),
        stepIdForUpdate = 0,
        newStepId       = 'n';

    if (currentStepName.length) stepChange();

    stepSelect.change(function() { stepChange(); });

    function stepChange(){
        stepIdForUpdate = stepSelect.val();
        currentStepName.val(stepSelect.find(":selected").text());
    }

    $('#stepUpdate').click(function(){
        var newName = currentStepName.val();

        stepSelect.children().each(function(){
           if ($(this).val() == stepIdForUpdate) $(this).text(newName);
        });

        visualEditor.updateStep(stepIdForUpdate, newName);
    });

    $('#removeStep').click(function(){
        var stepIdForDelete = stepIdForUpdate;

        stepSelect.children().each(function(){
            if ($(this).val() == stepIdForDelete) {
                $(this).remove();
                stepChange();
            }
        });

        visualEditor.deleteStep(stepIdForDelete);
    });

    $('#addStep').click(function(){
        newStepId += 1;
        stepIdForUpdate = newStepId;
        stepSelect.append('<option value="' + newStepId + '">' + currentStepName.val() + '</option>');
        stepSelect.val(newStepId);
        visualEditor.addNewStep(newStepId, currentStepName.val());
    });

    $('#addToStep').click(function(){
        visualEditor.addToStep(stepIdForUpdate);
    });
    // ----- end step ----- //
});