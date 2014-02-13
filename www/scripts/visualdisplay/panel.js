var Panel = function() {
    var self = this,
        html = '<div style="position: absolute;\
                            top: %x%;\
                            left: %y%;\
                            z-index: %zIndex%;\
                            background-color: %bgColor%;\
                            width: %width%; \
                            height: %height%; \
                            border: %size% solid %color%;\
                            border-radius: %borderRadius%;\
                            -moz-transform: rotate(%angle%);\
                            -webkit-transform: rotate(%angle%);\
                            -o-transform: rotate(%angle%);\
                            -ms-transform: rotate(%angle%);\
                            transform: rotate(%angle%);"\
                     class="draggable-panel"\
                     id="%id%">\
                </div>';
    
    var width = 100, 
        height = 100, 
        borderSize = 1, 
        borderColor = '#000000', 
        borderRadius = 0,
        visualDisplay = null,
        zIndex = 0,
        backgroundColor = 'none',
        angle = 0,
        x = 0,
        y = 0,
        panelDataId = null;
    
    this.id = null;
    this.$panel = null;
        
    this.Create = function($container, panelId, vDisplay)
    {
        if($container == null) return;
        
        this.id = panelId;
        visualDisplay = vDisplay;
        var dPanel = $('.draggable-panel');
        
        $container.append(html.replace('%id%', 'panel_' + this.id)
                              .replace('%x%', x)
                              .replace('%y%', y)
                              .replace('%zIndex%', zIndex)
                              .replace('%bgColor%', backgroundColor)
                              .replace('%width%', width + 'px')
                              .replace('%height%', height + 'px')
                              .replace('%size%', borderSize + 'px')
                              .replace('%color%', borderColor)
                              .replace(/(%angle%)/g, angle + 'deg')
                              .replace(/(%borderRadius%)/g, borderRadius + 'px'));

        dPanel.draggable({containment: $container, scroll: false, cursor: 'move'});
        dPanel.resizable();

        this.$panel = $('#panel_' + this.id);
        this.$panel.css('position', 'absolute').css('top', y).css('left', x);
        this.$panel.text(x+', '+y);
        this.$panel.live('click', Click);
        this.Coordinate();
    };
    
    this.CreateFromJSON = function(jsonObject, $container, panelId, vDisplay)
    {
        if(jsonObject == null || $container == null) return;
        
        if('id' in jsonObject)              panelDataId = jsonObject.id;
        if('width' in jsonObject)           width = jsonObject.width;
        if('height' in jsonObject)          height = jsonObject.height;
        if('border' in jsonObject)          borderSize = jsonObject.border;
        if('borderColor' in jsonObject)     borderColor = jsonObject.borderColor;
        if('borderRadius' in jsonObject)    borderRadius = jsonObject.borderRadius;
        if('zIndex' in jsonObject)          zIndex = jsonObject.zIndex;
        if('backgroundColor' in jsonObject) backgroundColor = jsonObject.backgroundColor;
        if('angle' in jsonObject)           angle = jsonObject.angle;
        if('x' in jsonObject)               x = jsonObject.x;
        if('y' in jsonObject)               y = jsonObject.y;

        this.Create($container, panelId, vDisplay);
    };
    
    this.ToJSON = function() {
        if(this.$panel == null) return null;

        return '{\
                         "id": "' + panelDataId + '",\
                      "width": "' + this.$panel.css('width').replace('px', '') + '",\
                     "height": "' + this.$panel.css('height').replace('px', '') + '",\
                     "border": "' + this.$panel.css('border-width').replace('px', '') + '",\
                "borderColor": "' + this.$panel.css('border-color') + '",\
               "borderRadius": "' + this.$panel.css('border-radius').replace('px', '') + '",\
                     "zIndex": "' + this.$panel.css('z-index') + '",\
            "backgroundColor": "' + this.$panel.css('background-color') + '",\
                      "angle": "' + utils.GetRotationAngle(this.$panel) + '",\
                          "x": "' + this.$panel.css('left').replace('px', '') + '",\
                          "y": "' + this.$panel.css('top').replace('px', '') + '"\
        }';
    };
    
    var Click = function() {
        if(visualDisplay != null) {
            visualDisplay.SelectPanel(self);
        }
    }

    this.Coordinate = function (){
        $(this.$panel).draggable({
            containment: "#visualDisplay",
            drag: function() {
                var $this = $(this);
                var thisPos = $(this).position();

                $this.text(thisPos.left + ", " + thisPos.top);
            }
        });
    }
};