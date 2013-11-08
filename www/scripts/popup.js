$(function() {
    var $fontColorContainer             = $('.fontColorContainer'),
        $borderColorContainer           = $('.borderColorContainer'),
        $customBackgroundColorContainer = $('.customBackgroundColorContainer'),
        $defaultBackgroundColors        = $('.defaultBackgroundColors'),
        $customBackgroundColors         = $('.customBackgroundColors'),
        $nodeAssignContainer            = $('.popup-assign-' + nodeAssignTypeId + '-container'),
        $sectionAssignContainer         = $('.popup-assign-' + sectionAssignTypeId + '-container'),
        $redirectNodesContainer         = $('.redirect-nodes-container');

    $('#fontColor').click(function() {
        $fontColorContainer.show();
        $fontColorContainer.farbtastic('#fontColor');
    }).blur(function() {
        $fontColorContainer.hide();
    });

    $('#borderColor').click(function() {
        $borderColorContainer.show();
        $borderColorContainer.farbtastic('#borderColor');
    }).blur(function() {
        $borderColorContainer.hide();
    });

    $('#customBackgroundColor').click(function() {
        $customBackgroundColorContainer.show();
        $customBackgroundColorContainer.farbtastic('#customBackgroundColor');
    }).blur(function() {
        $customBackgroundColorContainer.hide();
    });

    $('#backgroundColorDefault').click(function() {
        if($(this).is(':checked')) {
            $defaultBackgroundColors.show();
            $customBackgroundColors.hide();
        }
    });

    $('#backgroundColorCustom').click(function() {
        if($(this).is(':checked')) {
            $customBackgroundColors.show();
            $defaultBackgroundColors.hide();
        }
    });

    $('input:radio#timing-on').change(function(){
        $('#timeBefore').prop('disabled', false);
        $('#timeLength').prop('disabled', false);
        $('#redirectNodeId').prop('disabled', false);
        $('.redirect-options-container label').removeClass('disabled');
    });

    $('input:radio#timing-off').change(function(){
        $('#timeBefore').prop('disabled', true);
        $('#timeLength').prop('disabled', true);
        $('#redirectNodeId').prop('disabled', true);
        $('.redirect-options-container label').addClass('disabled');
    });

    $('#timeBefore').click(function() {return false;});
    $('#timeLength').click(function() {return false;});

    $('#assignType_' + labyrinthAssignTypeId).click(function() {
        hideAllAssignContainers();
    });

    $('#assignType_' + nodeAssignTypeId).click(function() {
        hideAllAssignContainers();
        $nodeAssignContainer.removeClass('hide');
    });

    $('#assignType_' + sectionAssignTypeId).click(function() {
        hideAllAssignContainers();
        $sectionAssignContainer.removeClass('hide');
    });

    $('.redirect-options-container label').click(function() {
        if($(this).attr('show-nodes')) {
            $redirectNodesContainer.removeClass('hide');
        } else {
            $redirectNodesContainer.addClass('hide');
        }
    });

    $('#redirectNodeId').click(function() {return false;});

    function hideAllAssignContainers() {
        $nodeAssignContainer.addClass('hide');
        $sectionAssignContainer.addClass('hide');
    }
});