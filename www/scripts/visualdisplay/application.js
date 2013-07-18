$(function() {
    var visualDisplay = null;
    
    visualDisplay = new VisualDisplay();
    visualDisplay.Init(displayId);
    
    if(displayJSON != null) {
        if(visualDisplay != null) {
            visualDisplay.Deserialize(displayJSON);
        }
    }
    
    $('#createPanelBtn').click(function() {
        if(visualDisplay != null)
            visualDisplay.CreatePanel();
    });
    
    $('.visual-display-images-container .images div img').live('click', function() {
       if(visualDisplay != null)
           visualDisplay.CreateImage($(this).parent());
    });
    
    $('.counter-container').live('click', function() {
       if(visualDisplay != null)
           visualDisplay.CreateCounter($(this));
    });
    
    $('.couter-layout-eye-btn').live('click', function() {
        if(visualDisplay == null) return;
       
        var $eye = $(this).children('i'),
        $parent = $(this).parent();
               
        if($eye.hasClass('icon-eye-open') === true) {
            $eye.removeClass('icon-eye-open');
            $eye.addClass('icon-eye-close');
               
            if($parent.attr('panelId') !== undefined) {
                visualDisplay.HidePanel($parent.attr('panelId'));
            } else if($parent.attr('imageId') !== undefined) {
                visualDisplay.HideImage($parent.attr('imageId'));
            } else if($parent.attr('counterId') !== undefined) {
                visualDisplay.HideCounter($parent.attr('counterId'));
            }
        } else {
            $eye.removeClass('icon-eye-close');
            $eye.addClass('icon-eye-open');
               
            if($parent.attr('panelId') !== undefined) {
                visualDisplay.ShowPanel($parent.attr('panelId'));
            } else if($parent.attr('imageId') !== undefined) {
                visualDisplay.ShowImage($parent.attr('imageId'));
            } else if($parent.attr('counterId') !== undefined) {
                visualDisplay.ShowCounter($parent.attr('counterId'));
            }
        }
    });
    
    $('.delete-panel').live('click', function() {
        if(visualDisplay == null) return;
       
        var $parent = $(this).parent();
        if($parent != null) {
            visualDisplay.DeletePanel($parent.attr('panelId'));
            $parent.remove();
        }
    });
    
    $('.delete-image').live('click', function() {
        if(visualDisplay == null) return;
       
        var $parent = $(this).parent();
        if($parent != null) {
            visualDisplay.DeleteImage($parent.attr('imageId'));
            $parent.remove();
        }
    });
    
    $('.delete-counter').live('click', function() {
        if(visualDisplay == null) return;
       
        var $parent = $(this).parent();
        if($parent != null) {
            visualDisplay.DeleteCounter($parent.attr('counterId'));
            $parent.remove();
        }
    });
    
    $('#saveVisualDisplayBtn').click(function() {
        if(visualDisplay == null) return;
        
        var mapId   = $(this).attr('mapId'),
            postURL = $(this).attr('postURL'),
            data    = visualDisplay.Serialize();
            
        if(mapId == null || postURL == null) return;

        $.post(postURL, 
               {
                   mapId: mapId,
                   allPages: $('#showOnAllPage').is(':checked'),
                   data: data
               }, 
               function(responseData) {
                   $(location).attr('href', displayBaseURL + responseData);
               });
    });
    
    var $displayImagesContainer = $('#displayImagesContainer');

    $('#fileupload').fileupload({
        url: dataURL,
        dataType: 'json',
        done: function (e, data) {
            $.each(data.files, function (index, file) {
                $.ajaxSetup({async: false});
                $.post(replaceAction, 
                    { 
                        mapId: displayMapId, 
                        fileName: file.name 
                    }, 
                    function(data) {
                        if(data != '') {
                            $displayImagesContainer.append('<div>\
                                                                <img src="' + baseDisplayImagesPath + '/thumbs/' + data + '" path="' + baseDisplayImagesPath + '/' + data + '"/>\
                                                                <div>\
                                                                    <form method="POST" action="' + displayDeleteImageURL + '">\
                                                                        <input type="hidden" name="imageName" value="' + data + '"/>\
                                                                        <input type="submit" class="btn btn-danger btn-small" value="delete"/>\
                                                                    </form>\
                                                                </div>\
                                                            </div>');
                        }
                    }
                );
                $.ajaxSetup({async: true});
            });
            
            setTimeout(function() {
                $('#progress .bar').hide();
                $('#progress .bar').css('width', '0%');
            }, 1000);
        },
        add: function (e, data) {
            $('#progress .bar').show();
            data.submit();
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .bar').css(
                'width',
                progress + '%'
            );
        }
    });
    
    var $panelBorderColor = $('#panelBorderColor'),
        $borderColorPicker = $('#borderColorFarbtastic'),
        $panelBackgroundColor = $('#panelBackgroundColor'),
        $backgroundColorFarbtastic = $('#backgroundColorFarbtastic'),
        $counterFontLabelColor = $('#counterFontLabelColor'),
        $labelFontColorFarbtastic = $('#labelFontColorFarbtastic'),
        $counterFontValueColor = $('#counterFontValueColor'),
        $valueFontColorFarbtastic = $('#valueFontColorFarbtastic');
    
    $panelBorderColor.click(function() {
        $borderColorPicker.show();
        $borderColorPicker.farbtastic('#panelBorderColor', function(color) {
            $panelBorderColor.val(color);
        });
        
        $borderColorPicker.css('left', $(this).position().left).css('top', $(this).position().top + 40);
    });
    
    $panelBorderColor.blur(function() {
        $borderColorPicker.hide();
    });
    
    $panelBackgroundColor.click(function() {
        $backgroundColorFarbtastic.show();
        $backgroundColorFarbtastic.farbtastic('#panelBackgroundColor', function(color) {
            $panelBackgroundColor.val(color);
        });
        
        $backgroundColorFarbtastic.css('left', $(this).position().left).css('top', $(this).position().top + 40);
    });
    
    $panelBackgroundColor.blur(function() {
        $backgroundColorFarbtastic.hide();
    });
    
    $counterFontLabelColor.click(function() {
        $labelFontColorFarbtastic.show();
        $labelFontColorFarbtastic.farbtastic('#counterFontLabelColor', function(color) {
            $counterFontLabelColor.val(color);
        });
        
        $labelFontColorFarbtastic.css('left', $(this).position().left).css('top', $(this).position().top + 40);
    });
    
    $counterFontLabelColor.blur(function() {
        $labelFontColorFarbtastic.hide();
    });
    
    $counterFontValueColor.click(function() {
        $valueFontColorFarbtastic.show();
        $valueFontColorFarbtastic.farbtastic('#counterFontValueColor', function(color) {
            $counterFontValueColor.val(color);
        });
        
        $valueFontColorFarbtastic.css('left', $(this).position().left).css('top', $(this).position().top + 40);
    });
    
    $counterFontValueColor.blur(function() {
        $valueFontColorFarbtastic.hide();
    });
});