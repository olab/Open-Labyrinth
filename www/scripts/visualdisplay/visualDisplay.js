var VisualDisplay = function()
{
    var self = this,
        $container = $('#visualDisplay'),
        urlBase = window.location.origin + '/';
    
    var id = null,
        
        panels = [],
        panelsIDIndex = 1,
        currentActivePanel = null,
        $panelWidthInput = $('#panelWidth'),
        $panelHeightInput = $('#panelHeight'),
        $panelBorderInput = $('#panelBorder'),
        $panelBorderColorInput = $('#panelBorderColor'),
        $panelBorderRadiusInput = $('#panelBorderRadius'),
        $panelZIndexInput = $('#panelZIndex'),
        $panelBackgroundInput = $('#panelBackgroundColor'),
        $panelAngleInput = $('#panelAngle'),
        
        images = [],
        imageIDIndex = 1,
        currentActiveImage = null,
        $imageZIndexInput = $('#panelImageZIndex'),
        $imageAngleInput = $('#panelImageAngle'),
        
        counters = [],
        counterIDIndex = 1,
        currentActiveCounter = null,
        $counterFontLabelFamilySelect = $('#counterFontLabelFamily'),
        $counterFontLabelSizeSelect = $('#counterFontLabelSize'),
        $counterLabelTextInput = $('#counterLabelText'),
        $counterFontLabelColorInput = $('#counterFontLabelColor'),
        $counterLabelZIndexInput = $('#counterLabelZIndex'),
        $counterLabelAngleInput = $('#counterLabelAngle'),
        $counterLabelBoldCheck = $('#counterLabelBold'),
        $counterLabelItalicCheck = $('#counterLabelItalic'),
        $counterLabelUnderlineCheck = $('#counterLabelUnderline'),
        $counterFontValueFamilySelect = $('#counterFontValueFamily'),
        $counterFontValueSizeSelect = $('#counterFontValueSize'),
        $counterFontValueColorInput = $('#counterFontValueColor'),
        $counterValueZIndexInput = $('#counterValueZIndex'),
        $counterValueAngleInput = $('#counterValueAngle'),
        $counterValueBoldCheck = $('#counterValueBold'),
        $counterValueItalicCheck = $('#counterValueItalic'),
        $counterValueUnderlineCheck = $('#counterValueUnderline'),

        fontInput = $('#fontManage'),

        layoutPanel = new LayoutPanel();
        
    this.Init = function(visualDisplayId) {
        id = visualDisplayId;
        
        $panelWidthInput.blur(ChangeWidth);
        $panelHeightInput.blur(ChangeHeight);
        $panelBorderInput.blur(ChangeBorder);
        $panelBorderColorInput.blur(ChangeBorderColor);
        $panelBorderRadiusInput.blur(ChangeBorderRadius);
        $panelZIndexInput.blur(ChangeZIndex);
        $panelBackgroundInput.blur(ChangeBackgroundColor);
        $panelAngleInput.blur(ChangePanelAngle);
        
        $imageZIndexInput.change(ChangeImageZIndex);
        $imageAngleInput.change(ChangeImageAngle);
        
        $counterFontLabelFamilySelect.change(ChangeLabelFontFamily);
        $counterFontLabelSizeSelect.change(ChangeLabelFontSize);
        $counterLabelTextInput.blur(ChangeLabelText);
        $counterFontLabelColorInput.blur(ChangeLabelColor);
        $counterLabelZIndexInput.blur(ChangeLabelZIndex);
        $counterLabelAngleInput.blur(ChangeLabelAngle);
        $counterLabelBoldCheck.click(ChangeLabelBold);
        $counterLabelItalicCheck.click(ChangeLabelItalic);
        $counterLabelUnderlineCheck.click(ChangeLabelUnderline);
        $counterFontValueFamilySelect.change(ChangeValueFontFamily);
        $counterFontValueSizeSelect.change(ChangeValueFontSize);
        $counterFontValueColorInput.blur(ChangeValueColor);
        $counterValueZIndexInput.blur(ChangeValueZIndex);
        $counterValueAngleInput.blur(ChangeValueAngle);
        $counterValueBoldCheck.click(ChangeValueBold);
        $counterValueItalicCheck.click(ChangeValueItalic);
        $counterValueUnderlineCheck.click(ChangeValueUnderline);
    };
    
    this.CreatePanel = function() {
        if($container == null) return;
        
        var panel = new Panel();
        panel.Create($container, panelsIDIndex, self);
        
        if(layoutPanel != null) {
            layoutPanel.AddPanel(panelsIDIndex);
        }
        
        panels.push(panel);
        panelsIDIndex += 1;
    };
    
    this.CreateImage = function($object) {
        if($container == null || $object == null) return;
        
        var path = $object.children('img').first().attr('path');
        var image = new PanelImage();
        image.Create($container, imageIDIndex, path, self);
        
        if(layoutPanel != null) {
            layoutPanel.AddImage(imageIDIndex, path);
        }
        
        images.push(image);
        imageIDIndex += 1;
    };

    this.CreateCounter = function($object) {
        if($container == null || $object == null) return;
        
        var counterId = $object.attr('counterId'),
            label = $object.attr('counterName'),
            value = $object.attr('counterValue');
            
        var counter = new Counter();
        counter.Create($container, counterIDIndex, counterId, label, value, self);
        
        if(layoutPanel != null) {
            layoutPanel.AddCounter(counterIDIndex, label);
        }
        
        counters.push(counter);
        counterIDIndex += 1;
    };
    
    this.SelectPanel = function(panel)
    {
        if(panel == null) return;

        currentActivePanel = panel;

        if(currentActivePanel.$panel != null)
        {
            $panelWidthInput.val(currentActivePanel.$panel.css('width').replace('px', ''));
            $panelHeightInput.val(currentActivePanel.$panel.css('height').replace('px', ''));
            $panelBorderInput.val(currentActivePanel.$panel.css('border-top-width').replace('px', ''));
            $panelBorderColorInput.val(currentActivePanel.$panel.css('border-top-color'));
            $panelBorderColorInput.css('background-color', currentActivePanel.$panel.css('border-top-color'));
            $panelBorderRadiusInput.val(currentActivePanel.$panel.css('border-top-right-radius').replace('px', '') === '' ? 0 : currentActivePanel.$panel.css('border-top-right-radius').replace('px', ''));//currentActivePanel.$panel.css('border-radius').replace('px', ''));
            $panelZIndexInput.val(currentActivePanel.$panel.css('z-index'));
            $panelBackgroundInput.val(currentActivePanel.$panel.css('background-color'));
            $panelBackgroundInput.css('background-color', currentActivePanel.$panel.css('background-color'));

            var angle = utils.GetRotationAngle(currentActivePanel.$panel);
            if(angle != null) $panelAngleInput.val(angle);
            else $panelAngleInput.val(0);
        }
    };
    
    this.SelectImage = function(image) {
        if(image == null) return;
        
        currentActiveImage = image;
        if(currentActiveImage.$image != null) {
            $imageZIndexInput.val(currentActiveImage.$image.css('z-index'));
            
            var angle = utils.GetRotationAngle(currentActiveImage.$image);
            if(angle != null) {
                $imageAngleInput.val(angle);
            } else {
                $imageAngleInput.val(0);
            }
        }
    };
    
    this.SelectCounter = function(counter) {
        if (counter == null) return;
        
        currentActiveCounter = counter;
        var angle = 0;

        if (currentActiveCounter.$label != null) {
            $counterFontLabelFamilySelect.children('option').removeAttr('selected');
            $counterFontLabelFamilySelect.children('option[value="' + currentActiveCounter.$label.css('font-family').replace(/\'/g, '') + '"]').attr('selected', 'selected');
            
            $counterFontLabelSizeSelect.children('option').removeAttr('selected');
            $counterFontLabelSizeSelect.children('option[value="' + currentActiveCounter.$label.css('font-size') + '"]').attr('selected', 'selected');

            $counterLabelTextInput.val(currentActiveCounter.$label.html().replace(/<.*/, ''));
            $counterFontLabelColorInput.val(currentActiveCounter.$label.css('color'));
            $counterFontLabelColorInput.css('background-color', currentActiveCounter.$label.css('color'));
            $counterLabelZIndexInput.val(currentActiveCounter.$label.css('z-index'));
            
            angle = utils.GetRotationAngle(currentActiveCounter.$label);

            if (angle != null) $counterLabelAngleInput.val(angle);
            else $counterLabelAngleInput.val(0);
            
            if (currentActiveCounter.$label.css('font-weight') == 'bold') $counterLabelBoldCheck.addClass('active');
            else $counterLabelBoldCheck.removeClass('active');

            if (currentActiveCounter.$label.css('font-style') == 'italic') $counterLabelItalicCheck.addClass('active');
            else $counterLabelItalicCheck.removeClass('active');

            if (currentActiveCounter.$label.css('text-decoration') == 'underline') $counterLabelUnderlineCheck.addClass('active');
            else $counterLabelUnderlineCheck.removeClass('active');
        }
        
        if (currentActiveCounter.$value != null) {
            $counterFontValueFamilySelect.children('option').removeAttr('selected');
            $counterFontValueFamilySelect.children('option[value="' + currentActiveCounter.$value.css('font-family').replace(/\'/g, '') + '"]').attr('selected', 'selected');
            
            $counterFontValueSizeSelect.children('option').removeAttr('selected');
            $counterFontValueSizeSelect.children('option[value="' + currentActiveCounter.$value.css('font-size') + '"]').attr('selected', 'selected');
            
            $counterFontValueColorInput.val(currentActiveCounter.$value.css('color'));
            $counterFontValueColorInput.css('background-color', currentActiveCounter.$value.css('color'));
            $counterValueZIndexInput.val(currentActiveCounter.$value.css('z-index'));
            
            angle = utils.GetRotationAngle(currentActiveCounter.$value);

            if(angle != null) $counterValueAngleInput.val(angle);
            else $counterValueAngleInput.val(0);
            
            if(currentActiveCounter.$value.css('font-weight') == 'bold') $counterValueBoldCheck.addClass('active');
            else $counterValueBoldCheck.removeClass('active');

            if(currentActiveCounter.$value.css('font-style') == 'italic') $counterValueItalicCheck.addClass('active');
            else $counterValueItalicCheck.removeClass('active');

            if(currentActiveCounter.$value.css('text-decoration') == 'underline') $counterValueUnderlineCheck.addClass('active');
            else $counterValueUnderlineCheck.removeClass('active');
        }
    };
    
    this.HidePanel = function(panelId) {
        if(panelId == null) return;
        
        var panel = GetElementById(panels, panelId);
        if(panel != null && panel.$panel != null) {
            panel.$panel.hide();
        }
    };
    
    this.ShowPanel = function(panelId) {
        if(panelId == null) return;
        
        var panel = GetElementById(panels, panelId);
        if(panel != null && panel.$panel != null) {
            panel.$panel.show();
        }
    };
    
    this.HideImage = function(imageId) {
        if(imageId == null) return;
        
        var image = GetElementById(images, imageId);
        if(image != null && image.$image != null) {
            image.$image.hide();
        }
    };
    
    this.ShowImage = function(imageId) {
        if(imageId == null) return;
        
        var image = GetElementById(images, imageId);
        if(image != null && image.$image != null) {
            image.$image.show();
        }
    };
    
    this.HideCounter = function(counterId) {
        if(counterId == null) return;
        
        var counter = GetElementById(counters, counterId);
        if(counter != null) {
            if(counter.$label != null) {
                counter.$label.hide();
            }
            
            if(counter.$value != null) {
                counter.$value.hide();
            }
        }
    };
    
    this.ShowCounter = function(counterId) {
        if(counterId == null) return;
        
        var counter = GetElementById(counters, counterId);
        if(counter != null) {
            if(counter.$label != null) {
                counter.$label.show();
            }
            
            if(counter.$value != null) {
                counter.$value.show();
            }
        }
    };
    
    this.DeletePanel = function(panelId) {
        if(panelId == null) return;
        
        var panel = GetElementById(panels, panelId);
        if(panel != null && panel.$panel != null) {
            panel.$panel.remove();
            RemoveElementById(panels, panelId);
        }
    };
    
    this.DeleteImage = function(imageId) {
        if(imageId == null) return;

        var image = GetElementById(images, imageId);
        if(image != null && image.$image != null) {
            image.$image.remove();
            RemoveElementById(images, imageId);
        }
    };
    
    this.DeleteCounter = function(counterId) {
        if(counterId == null) return;
        
        var counter = GetElementById(counters, counterId);
        if(counter != null) {
            if(counter.$label != null) {
                counter.$label.remove();
            }
            
            if(counter.$value != null) {
                counter.$value.remove();
            }
            
            RemoveElementById(counters, counterId);
        }
    };
    
    this.Serialize = function() {
        var result = '',
            panelsString,
            imagesString,
            countersString;

        panelsString   = SerializeArray(panels, 'panels');
        imagesString   = SerializeArray(images, 'images');
        countersString = SerializeArray(counters, 'counters');
        
        if(panelsString != null && panelsString.length > 0) {
            result = panelsString;
        }
        
        if(imagesString != null && imagesString.length > 0) {
            if(result.length > 0) {
                result += ', ' + imagesString;
            } else {
                result = imagesString;
            }
        }
        
        if(countersString != null && countersString.length > 0) {
            if(result.length > 0) {
                result += ', ' + countersString;
            } else {
                result = countersString;
            }
        }
        
        if(result.length > 0) {
            result = '{"id": "' + id + '", ' + result + '}';
        } else {
            result = '{"id": "' + id + '"}';
        }
        
        return result;
    };
    
    this.Deserialize = function(object) {
        if(object == null) return;
        
        panels = [];
        images = [];
        counters = [];
        
        if('id' in object) {
            id = object.id;
        }
        
        if('panels' in object) {
            DeserializePanels(object.panels);
        }
        
        if('counters' in object) {
            DeserializeCounters(object.counters);
        }
        
        if('images' in object) {
            DeserializeImages(object.images);
        }
    }
    
    var DeserializePanels = function(objPanels) {
        if(objPanels == null || objPanels.length <= 0) return;
        
        var i = objPanels.length,
            panel = null;
        for(;i--;) {
            panel = new Panel();
            panel.CreateFromJSON(objPanels[i], $container, panelsIDIndex, self);
            
            if(layoutPanel != null) {
                layoutPanel.AddPanel(panelsIDIndex);
            }

            panels.push(panel);
            panelsIDIndex += 1;
        }
    }
    
    var DeserializeCounters = function(objCounters){
        if(objCounters == null || objCounters.length <= 0) return;

        var i = objCounters.length,
            counter = null;

        for(;i--;) {
            counter = new Counter();
            counter.CreateFromJSON(objCounters[i], $container, counterIDIndex, '0', self);
            
            if (layoutPanel != null) layoutPanel.AddCounter(counterIDIndex, utils.Decode64(objCounters[i].labelText));

            counters.push(counter);
            counterIDIndex += 1;
        }
    };
    
    var DeserializeImages = function(objImages) {
        if(objImages == null || objImages.length <= 0) return;
        
        var i = objImages.length,
            image = null;
        for(;i--;) {
            image = new PanelImage();
            image.CreateFromJSON(objImages[i], $container, imageIDIndex, self);
            
            if(layoutPanel != null) {
                layoutPanel.AddImage(imageIDIndex, objImages[i].image);
            }

            images.push(image);
            imageIDIndex += 1;
        }
    }
    
    var SerializeArray = function (collection, name) {
        if (collection == null || collection.length <= 0 || name == null) return null;
        
        var i = collection.length,
            result = '';

        for(;i--;) {
            result += collection[i].ToJSON() + ', ';
        }
        if (result.length > 2) result = '"' + name + '": [' + result.substring(0, result.length - 2) + ']';

        return result;
    };
    
    var RemoveElementById = function(elements, id) {
        if(elements == null || id == null) return;

        var i = elements.length;
        for(;i--;) {
            if(elements[i].id == id) {
                
                elements.splice(i, 1);
                break;
            }
        }
    }
    
    var GetElementById = function(elements, id) {
        if(elements == null || id == null) return null;
        
        var i = elements.length,
            result = null;
        for(;i--;) {
            if(elements[i].id == id) {
                result = elements[i];
                break;
            }
        }
        
        return result;
    }
    
    var ChangeWidth = function() {
        var value = $panelWidthInput.val();
        if(currentActivePanel != null && currentActivePanel.$panel != null && value > 0) {
            currentActivePanel.$panel.css('width', value);
        }
    }
    
    var ChangeHeight = function() {
        var value = $panelHeightInput.val();
        if(currentActivePanel != null && currentActivePanel.$panel != null && value > 0) {
            currentActivePanel.$panel.css('height', value);
        }
    }
    
    var ChangeBorder = function() {
        var value = $panelBorderInput.val();
        if(currentActivePanel != null && currentActivePanel.$panel != null && value > 0) {
            currentActivePanel.$panel.css('border-width', value + 'px');
        }
    }
    
    var ChangeBorderColor = function() {
        var value = $panelBorderColorInput.val();
        if(currentActivePanel != null && currentActivePanel.$panel != null) {
            currentActivePanel.$panel.css('border-color', value);
        }
    }
    
    var ChangeBorderRadius = function() {
        var value = $panelBorderRadiusInput.val();
        if(currentActivePanel != null && currentActivePanel.$panel != null && value > 0) {
            currentActivePanel.$panel.css('border-radius', value + 'px');
        }
    }
    
    var ChangeZIndex = function() {
        var value = $panelZIndexInput.val();
        if(currentActivePanel != null && currentActivePanel.$panel != null && value > 0) {
            currentActivePanel.$panel.css('z-index', value);
        }
    }
    
    var ChangeBackgroundColor = function() {
        var value = $panelBackgroundInput.val();
        if(currentActivePanel != null && currentActivePanel.$panel != null) {
            currentActivePanel.$panel.css('background-color', value);
        }
    }
    
    var ChangePanelAngle = function() {
        var value = $panelAngleInput.val();
        if(currentActivePanel != null && currentActivePanel.$panel != null) {
            currentActivePanel.$panel.css('-moz-transform', 'rotate(' + value + 'deg)')
                                     .css('-webkit-transform', 'rotate(' + value + 'deg)')
                                     .css('-o-transform', 'rotate(' + value + 'deg)')
                                     .css('-ms-transform', 'rotate(' + value + 'deg)')
                                     .css('transform', 'rotate(' + value + 'deg)');
        }
    }
    
    var ChangeImageZIndex = function() {
        var value = $imageZIndexInput.val();
        if(currentActiveImage != null && currentActiveImage.$image != null && value > 0) {
            currentActiveImage.$image.css('z-index', value);
        }
    }
    
    var ChangeImageAngle = function() {
        var value = $imageAngleInput.val();
        if(currentActiveImage != null && currentActiveImage.$image != null) {
            currentActiveImage.$image.css('-moz-transform', 'rotate(' + value + 'deg)')
                                     .css('-webkit-transform', 'rotate(' + value + 'deg)')
                                     .css('-o-transform', 'rotate(' + value + 'deg)')
                                     .css('-ms-transform', 'rotate(' + value + 'deg)')
                                     .css('transform', 'rotate(' + value + 'deg)');
        }
    }
    
    var ChangeLabelFontFamily = function() {
        var value = $counterFontLabelFamilySelect.children('option:selected').val();

        fontInput.val(value);

        if(currentActiveCounter != null && currentActiveCounter.$label != null && value.length > 0) {
            currentActiveCounter.$label.css('font-family', value);
        }
    };
    
    var ChangeLabelFontSize = function() {
        var value = $counterFontLabelSizeSelect.children('option:selected').val();
        if(currentActiveCounter != null && currentActiveCounter.$label != null && value.length > 0) {
            currentActiveCounter.$label.css('font-size', value);
        }
    }
    
    var ChangeLabelText = function() {
        var value = $counterLabelTextInput.val();
        if(currentActiveCounter != null && currentActiveCounter.$label != null) {
            currentActiveCounter.$label.text(value);
        }
    }
    
    var ChangeLabelColor = function() {
        var value = $counterFontLabelColorInput.val();
        if(currentActiveCounter != null && currentActiveCounter.$label != null && value.length > 0) {
            currentActiveCounter.$label.css('color', value);
        }
    }
    
    var ChangeLabelZIndex = function() {
        var value = $counterLabelZIndexInput.val();
        if(currentActiveCounter != null && currentActiveCounter.$label != null) {
            currentActiveCounter.$label.css('z-index', value);
        }
    }
    
    var ChangeLabelAngle = function() {
        var value = $counterLabelAngleInput.val();
        if(currentActiveCounter != null && currentActiveCounter.$label != null) {
            currentActiveCounter.$label.css('-moz-transform', 'rotate(' + value + 'deg)')
                                       .css('-webkit-transform', 'rotate(' + value + 'deg)')
                                       .css('-o-transform', 'rotate(' + value + 'deg)')
                                       .css('-ms-transform', 'rotate(' + value + 'deg)')
                                       .css('transform', 'rotate(' + value + 'deg)');
        }
    }
    
    var ChangeLabelBold = function() {
        if(currentActiveCounter != null && currentActiveCounter.$label != null) {
            if($counterLabelBoldCheck.hasClass('active') === true) {
                currentActiveCounter.$label.css('font-weight', 'normal');
            } else {
                currentActiveCounter.$label.css('font-weight', 'bold');
            }
        }
    }
    
    var ChangeLabelItalic = function() {
        if(currentActiveCounter != null && currentActiveCounter.$label != null) {
            if($counterLabelItalicCheck.hasClass('active') === true) {
                currentActiveCounter.$label.css('font-style', 'normal');
            } else {
                currentActiveCounter.$label.css('font-style', 'italic');
            }
        }
    }
    
    var ChangeLabelUnderline = function() {
        if(currentActiveCounter != null && currentActiveCounter.$label != null) {
            if($counterLabelUnderlineCheck.hasClass('active') === true) {
                currentActiveCounter.$label.css('text-decoration', 'none');
            } else {
                currentActiveCounter.$label.css('text-decoration', 'underline');
            }
        }
    }
    
    var ChangeValueFontFamily = function() {
        var value = $counterFontValueFamilySelect.children('option:selected').val();

        fontInput.val(value);

        if(currentActiveCounter != null && currentActiveCounter.$value != null && value.length > 0) {
            currentActiveCounter.$value.css('font-family', value);
        }
    };
    
    var ChangeValueFontSize = function() {
        var value = $counterFontValueSizeSelect.children('option:selected').val();
        if(currentActiveCounter != null && currentActiveCounter.$value != null && value.length > 0) {
            currentActiveCounter.$value.css('font-size', value);
        }
    }
    
    var ChangeValueColor = function() {
        var value = $counterFontValueColorInput.val();
        if(currentActiveCounter != null && currentActiveCounter.$value != null && value.length > 0) {
            currentActiveCounter.$value.css('color', value);
        }
    }
    
    var ChangeValueZIndex = function() {
        var value = $counterValueZIndexInput.val();
        if(currentActiveCounter != null && currentActiveCounter.$value != null) {
            currentActiveCounter.$value.css('z-index', value);
        }
    }
    
    var ChangeValueAngle = function() {
        var value = $counterValueAngleInput.val();
        if(currentActiveCounter != null && currentActiveCounter.$value != null) {
            currentActiveCounter.$value.css('-moz-transform', 'rotate(' + value + 'deg)')
                                       .css('-webkit-transform', 'rotate(' + value + 'deg)')
                                       .css('-o-transform', 'rotate(' + value + 'deg)')
                                       .css('-ms-transform', 'rotate(' + value + 'deg)')
                                       .css('transform', 'rotate(' + value + 'deg)');
        }
    }
    
    var ChangeValueBold = function() {
        if(currentActiveCounter != null && currentActiveCounter.$value != null) {
            if($counterValueBoldCheck.hasClass('active') === true) {
                currentActiveCounter.$value.css('font-weight', 'normal');
            } else {
                currentActiveCounter.$value.css('font-weight', 'bold');
            }
        }
    }
    
    var ChangeValueItalic = function() {
        if(currentActiveCounter != null && currentActiveCounter.$value != null) {
            if($counterValueItalicCheck.hasClass('active') === true) {
                currentActiveCounter.$value.css('font-style', 'normal');
            } else {
                currentActiveCounter.$value.css('font-style', 'italic');
            }
        }
    };
    
    var ChangeValueUnderline = function() {
        if(currentActiveCounter != null && currentActiveCounter.$value != null) {
            if($counterValueUnderlineCheck.hasClass('active') === true) {
                currentActiveCounter.$value.css('text-decoration', 'none');
            } else {
                currentActiveCounter.$value.css('text-decoration', 'underline');
            }
        }
    };

    // ------ fonts ----- //
    $('#fontManageAdd').click(function(){
        fontManage('Add');
    });

    $('#fontManageDelete').click(function(){
        fontManage('Delete');

    });

    function fontManage(action){
        var fontName = fontInput.val();

        $.get(urlBase + 'visualdisplaymanager/ajax' + action + 'Font/' + fontName,
            function(){
                window.location.href += currentTab;
                location.reload();
            }
        );
    }
    // ------ end fonts ----- //
};