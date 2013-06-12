var PanelImage = function() {
    var self = this,
        html = '<div id="%id%" class="draggable-panel"><img src="%path%" /></div>';
    
    var imageDataId = null,
        imagePath = '',
        width = -1,
        height = -1,
        angle = 0,
        x = 0,
        y = 0,
        zIndex = 0,
        visualDisplay = null
    
    this.id = null;
    this.$image = null;
    this.imageWidth = 0;
    this.imageHeight = 0;

    this.Create = function($container, imageId, path, vDisplay) {
        if($container == null) return;
        
        this.id = imageId;
        imagePath = path;
        visualDisplay = vDisplay;
        
        $container.append(html.replace('%path%', path)
                              .replace('%id%', 'panelImage_' + this.id));
        
        this.$image = $('#panelImage_' + this.id);
        this.$image.css('position', 'absolute')
                   .css('top', y)
                   .css('left', x)
                   .css('width', width)
                   .css('height', height)
                   .css('z-index', zIndex)
                   .css('-moz-transform', 'rotate(' + angle + 'deg)')
                   .css('-webkit-transform', 'rotate(' + angle + 'deg)')
                   .css('-o-transform', 'rotate(' + angle + 'deg)')
                   .css('-ms-transform', 'rotate(' + angle + 'deg)')
                   .css('transform', 'rotate(' + angle + 'deg)');
                   
        this.$image.live('click', Click);
        var $img = this.$image.children('img').first();
        
        var img = new Image();
        img.src = $img.attr('src');
        img.onload = function() {
            $('#panelImage_' + self.id).draggable({containment: $container, scroll: false, cursor: 'move'});
            $('#panelImage_' + self.id).resizable({
                aspectRatio: this.width / this.height,
                maxWidth: this.width,
                maxHeight: this.height
            });
        }
    }
    
    this.CreateFromJSON = function(jsonObject, $container, imageId, vDisplay) {
        if(jsonObject == null) return;
        
        if('id' in jsonObject) {
            imageDataId = jsonObject.id;
        }
        
        if('image' in jsonObject) {
            imagePath = jsonObject.image;
        }
        
        if('width' in jsonObject) {
            width = jsonObject.width;
        }
        
        if('height' in jsonObject) {
            height = jsonObject.height;
        }
        
        if('angle' in jsonObject) {
            angle = jsonObject.angle;
        }
        
        if('x' in jsonObject) {
            x = jsonObject.x;
        }
        
        if('y' in jsonObject) {
            y = jsonObject.y;
        }
        
        if('zIndex' in jsonObject) {
            zIndex = jsonObject.zIndex;
        }
        
        this.Create($container, imageId, imagePath, vDisplay);
    }
    
    this.ToJSON = function () {
        if(this.$image == null) return null;
        
        return '{\
                "id": "' + imageDataId + '",\
             "image": "' + imagePath + '",\
             "width": "' + this.$image.css('width').replace('px', '') + '",\
            "height": "' + this.$image.css('height').replace('px', '') + '",\
             "angle": "' + utils.GetRotationAngle(this.$image) + '",\
                 "x": "' + this.$image.css('left').replace('px', '') + '",\
                 "y": "' + this.$image.css('top').replace('px', '') + '",\
            "zIndex": "' + this.$image.css('z-index') + '"\
        }';
    }
    
    var Click = function() {
        if(visualDisplay != null) {
            visualDisplay.SelectImage(self);
        }
    }
}