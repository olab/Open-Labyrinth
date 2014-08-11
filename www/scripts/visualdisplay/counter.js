var Counter = function() {
    var self        = this,
        currentId   = null,
        counterId   = null,
        label,
        value,
        labelX      = 0,
        labelY      = 0,
        labelAngle  = 0,
        labelFont   = '',
        labelZIndex = 10,
        valueX      = 0,
        valueY      = 14,
        valueAngle  = 0,
        valueFont   = '',
        valueZIndex = 10,
        visualDisplay;

    self.id     = null;
    self.$label = null;
    self.$value = null;

    self.Create = function($container, elementId, cId, cLabel, cValue, vDisplay) {
        if($container == null) return;
        
        this.id         = elementId;
        counterId       = cId;
        label           = cLabel;
        value           = cValue;
        visualDisplay   = vDisplay;

        $container.append(
            '<div id="' + 'counterLabel_' + this.id + '" class="only-draggable-panel">' + label + '<sup>' + labelX + ', ' + labelY + '</sup></div>'+
            '<div id="' + 'counterValue_' + this.id + '" class="only-draggable-panel">' + value + '<sup>' + valueX + ', ' + valueY + '</sup></div>');
                              
        $('.only-draggable-panel').draggable({
            containment: $container,
            scroll: false,
            cursor: 'move',
            drag: function() {
                var top = parseInt($(this).css('top')),
                    left = parseInt($(this).css('left'));

                $(this.firstElementChild).text(left + ", " + top);
            }
        });

        this.$label = $('#counterLabel_' + this.id);
        this.$value = $('#counterValue_' + this.id);
        
        var color           = '#000000',
            font            = 'Helvetica Neue',
            fontWeight      = 'normal',
            fontStyle       = 'normal',
            textDecoration  = 'none',
            fontSize        = 14,
            fontSettings    = null;
        
        if (labelFont.length > 0) {
            fontSettings = labelFont.split('%#%');
            if (fontSettings.length == 6) {
                font            = fontSettings[0];
                fontSize        = fontSettings[1];
                fontWeight      = fontSettings[2];
                color           = fontSettings[3];
                fontStyle       = fontSettings[4];
                textDecoration  = fontSettings[5];
            }
        }
        
        this.$label.css('position', 'absolute').css('top', labelY)
                                               .css('left', labelX)
                                               .css('z-index', labelZIndex)
                                               .css('-moz-transform', 'rotate(' + labelAngle + 'deg)')
                                               .css('-webkit-transform', 'rotate(' + labelAngle + 'deg)')
                                               .css('-o-transform', 'rotate(' + labelAngle + 'deg)')
                                               .css('-ms-transform', 'rotate(' + labelAngle + 'deg)')
                                               .css('transform', 'rotate(' + labelAngle + 'deg)')
                                               .css('font-family', font)
                                               .css('font-size', fontSize + 'px')
                                               .css('font-weight', fontWeight)
                                               .css('color', color)
                                               .css('font-style', fontStyle)
                                               .css('text-decoration', textDecoration);
                                               
        if(valueFont.length > 0) {
            fontSettings = valueFont.split('%#%');
            if(fontSettings.length == 6) {
                font            = fontSettings[0];
                fontSize        = fontSettings[1];
                fontWeight      = fontSettings[2];
                color           = fontSettings[3];
                fontStyle       = fontSettings[4];
                textDecoration  = fontSettings[5];
            }
        }   
        
        this.$value.css('position', 'absolute').css('top', valueY)
                                               .css('left', valueX)
                                               .css('z-index', valueZIndex)
                                               .css('-moz-transform', 'rotate(' + valueAngle + 'deg)')
                                               .css('-webkit-transform', 'rotate(' + valueAngle + 'deg)')
                                               .css('-o-transform', 'rotate(' + valueAngle + 'deg)')
                                               .css('-ms-transform', 'rotate(' + valueAngle + 'deg)')
                                               .css('transform', 'rotate(' + valueAngle + 'deg)')
                                               .css('font-family', font)
                                               .css('font-size', fontSize + 'px')
                                               .css('font-weight', fontWeight)
                                               .css('color', color)
                                               .css('font-style', fontStyle)
                                               .css('text-decoration', textDecoration);
        
        this.$label.live('click', CounterClick);
        this.$value.live('click', CounterClick);
    };

    self.CreateFromJSON = function(jsonObject, $container, elementId, cValue, vDisplay) {

        if (jsonObject == null) return;
        
        if ('id' in jsonObject) currentId = jsonObject.id;
        if ('counterId' in jsonObject) counterId = jsonObject.counterId;
        if ('labelX' in jsonObject) labelX = jsonObject.labelX;
        if ('labelY' in jsonObject) labelY = jsonObject.labelY;
        if ('labelAngle' in jsonObject) labelAngle = jsonObject.labelAngle;
        if ('labelFont' in jsonObject) labelFont = jsonObject.labelFont;
        if ('labelZIndex' in jsonObject) labelZIndex = jsonObject.labelZIndex;
        if ('valueStart' in jsonObject) cValue = jsonObject.valueStart;
        if ('valueX' in jsonObject) valueX = jsonObject.valueX;
        if ('valueY' in jsonObject) valueY = jsonObject.valueY;
        if ('valueAngle' in jsonObject) valueAngle = jsonObject.valueAngle;
        if ('valueFont' in jsonObject) valueFont = jsonObject.valueFont;
        if ('valueZIndex' in jsonObject) valueZIndex = jsonObject.valueZIndex;
        if ('labelTextOriginal' in jsonObject) label = utils.Decode64(jsonObject.labelTextOriginal);
        self.Create($container, elementId, counterId, label, cValue, vDisplay);
    };

    self.ToJSON = function() {
        if (this.$label == null || this.$value == null) return null;

        return '{\
                     "id": "' + currentId + '",\
              "counterId": "' + counterId + '",\
                 "labelX": "' + this.$label.css('left').replace('px', '') + '",\
                 "labelY": "' + this.$label.css('top').replace('px', '') + '",\
             "labelAngle": "' + utils.GetRotationAngle(this.$label) + '",\
              "labelFont": "' + this.$label.css('font-family').replace(/\'/g, '') 
                              + '%#%' + this.$label.css('font-size').replace('px', '')
                              + '%#%' + this.$label.css('font-weight') 
                              + '%#%' + this.$label.css('color')
                              + '%#%' + this.$label.css('font-style') 
                              + '%#%' + this.$label.css('text-decoration') + '",\
              "labelText": "' + utils.Encode64(label) + '",\
            "labelZIndex": "' + this.$label.css('z-index') + '",\
                 "valueX": "' + this.$value.css('left').replace('px', '') + '",\
                 "valueY": "' + this.$value.css('top').replace('px', '') + '",\
             "valueAngle": "' + utils.GetRotationAngle(this.$value) + '",\
              "valueFont": "' + this.$value.css('font-family').replace(/\'/g, '') 
                              + '%#%' + this.$value.css('font-size').replace('px', '')
                              + '%#%' + this.$value.css('font-weight') 
                              + '%#%' + this.$value.css('color')
                              + '%#%' + this.$value.css('font-style') 
                              + '%#%' + this.$value.css('text-decoration') + '",\
            "valueZIndex": "' + this.$value.css('z-index') + '"\
        }';
    };
    
    var CounterClick = function() {
        if(visualDisplay != null) {
            visualDisplay.SelectCounter(self);
        }
    };
};