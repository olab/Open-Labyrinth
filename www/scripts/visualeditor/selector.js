var Selector = function() {
    var self = this;
    var defStrokeColor = '#0A3EFC';
    var defFillColor = 'rgba(10,62,252, 0.3)';
    
    self.x = 0;
    self.y = 0;
    self.width = 0;
    self.height = 0;
    self.isDragged = false;
    self.strokeColor = defStrokeColor;
    self.strokeSize = 2;
    self.fillColor = defFillColor;
    self.isVisible = false;
    
    self.Draw = function(context, viewport) {
        if(context == null || !self.isVisible) return;
        
        context.save();
        
        context.beginPath();
        context.rect(self.x, self.y, self.width, self.height);
        context.fillStyle = self.fillColor;
        context.fill();
        context.lineWidth = self.strokeSize;
        context.strokeStyle = self.strokeColor;
        context.stroke();
        
        context.restore();
    }
    
    self.MouseDown = function(mouse, viewport) {
        self.isVisible = true;
        self.isDragged = true;
        self.x = mouse.x; 
        self.y = mouse.y;
        self.width = 0;
        self.height = 0;
    }
    
    self.MouseUp = function(mouse, viewport) {
        self.isVisible = false;
        self.isDragged = false;
        self.x = mouse.x; 
        self.y = mouse.y;
        self.width = 0;
        self.height = 0;
    }
    
    self.MouseMove = function(mouse, viewport) {
        if(self.isDragged) {
            self.width += (mouse.x - mouse.oldX);
            self.height += (mouse.y - mouse.oldY);
            
            return true;
        }

        return false;
    }
}