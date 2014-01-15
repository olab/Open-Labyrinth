// Basic node object
var Node = function() {
    var self = this;
    var defWidth = 230;
    var defHeight = 60;
    var defHeaderHeight = 30;
    var defColor = '#f0f0f0';
    var defHeaderColor = '#b3b2b2';
    var defRootHeaderColor = '#e9b23c';
    var defBorderSize = 1;
    var defBorderColor = '#b3b2b2';
    
    self.isDragging = false;
    self.transform = new Transform();
    self.width = defWidth;
    self.height = defHeight;
    self.headerHeight = defHeaderHeight;
    self.color = defColor;
    self.headerColor = defHeaderColor;
    self.displayBorder = true;
    self.borderSize = defBorderSize;
    self.borderColor = defBorderColor;

    self.titleFontSettings = 'bold 18px Arial';
    self.titleFontColor = '#000000';
    
    // Data
    self.id = 0;
    self.isRoot = false;
    self.title = '';
    self.counters = [];

    // Draw current node
    // context - canvas context
    // viewport - Transform view port transformation
    self.Draw = function(context, viewport)
    {
        if(context == null) return;

        var tr = new Transform();
        tr.Multiply(viewport);
        tr.Multiply(self.transform);

        context.save();
        context.setTransform(tr.matrix[0], tr.matrix[1], tr.matrix[2], tr.matrix[3], tr.matrix[4], tr.matrix[5]);

        DrawContentArea(context);
        DrawHeaderArea(context);
        if(self.title.length > 0) DrawTitle(context, self.title.replace(/<(?:.|\n)*?>/gm, ''));

        context.restore();
    };
    
    // Scale current node by x, y factors
    // sx - number X-scale factor
    // sy - number Y-scale factor
    self.Scale = function(sx, sy)
    {
        self.transform.Scale(sx, sy);
    };
    
    // Mouse move event handler
    self.MouseMove = function(mouse, viewport, anotherNodes)
    {
        var isRedraw = false;
        if(mouse.isDown) {
            var isAnotherDrag = false;
            if(anotherNodes.length > 0) {
                for(var i = 0; i < anotherNodes.length; i++) {
                    if(anotherNodes[i].isDragging && self.id != anotherNodes[i].id) {
                        isAnotherDrag = true;
                        break;
                    }
                }
            }
            
            if( ! isAnotherDrag && (self.IsHeaderCollision(mouse.x, mouse.y, viewport) || self.isDragging)) {
                isRedraw = true;
                self.isDragging = true;
                if(self.isSelected && anotherNodes.length > 0) {
                    for(var i = 0; i < anotherNodes.length; i++) {
                        if(anotherNodes[i].isSelected) {
                            anotherNodes[i].TranslateNode(mouse.x - mouse.oldX, mouse.y - mouse.oldY, viewport);
                        }
                    }
                } else {
                    self.TranslateNode(mouse.x - mouse.oldX, mouse.y - mouse.oldY, viewport);
                }
            } else {
                self.isDragging = false;
            }
        } else {
            self.isDragging = false;
            if(self.IsLinkButtonCollision(mouse.x, mouse.y, viewport)) {
                isRedraw = true;
                self.isLinkButtonIsHover = true;
            } else {
                isRedraw = true;
                self.isLinkButtonIsHover = false;
            }
        }

        return isRedraw;
    };
    
    self.MouseClick = function(mouse, viewport) {

        var result = [];

        if(self.IsHeaderCollision(mouse.x, mouse.y, viewport)) {
            result[0] = self.id;
            result[1] = 'header';
        }
        
        return result;
    };


    self.IsHeaderCollision = function(x, y, viewport)
    {
        var tr = new Transform();
        tr.Multiply(viewport);
        tr.Multiply(self.transform);

        var pos = tr.GetPosition();
        var scale = tr.GetScale();

        return (x  >= pos[0] && x <= (pos[0] + self.headerHeight * scale[0]) && y  >= pos[1] && y <= (pos[1] + self.height * scale[1]));
    };

    self.IsLinkButtonCollision = function(x, y, viewport)
    {
        var tr = new Transform();
        tr.Multiply(viewport);
        tr.Multiply(self.transform);
        tr.Translate(self.headerHeight * 0.5, self.linkButtonRaius + 5);

        var pos = tr.GetPosition();
        var scale = tr.GetScale();

        return (self.linkButtonRaius * self.linkButtonRaius * (scale[0] + scale[1]) * 0.5) >= ((pos[0] - x) * (pos[0] - x) + (pos[1] - y) * (pos[1] - y));
    };

    self.IsMainAreaCollision = function(x, y, viewport)
    {
        var tr = new Transform();
        tr.Multiply(viewport);
        tr.Multiply(self.transform);

        var pos = tr.GetPosition();
        var scale = tr.GetScale();

        return (x  >= (pos[0] + self.headerHeight * scale[0]) && x <= (pos[0] + self.width * scale[0]) && y  >= (pos[1]) && y <= (pos[1] + (self.height) * scale[1]));
    };

    self.TranslateNode = function(dx, dy, viewport)
    {
        var scale = viewport.GetScale();
        self.transform.TranslateWithoutScale(dx / scale[0], dy / scale[1]);
    };

    var DrawContentArea = function(context)
    {
        context.fillStyle = self.color;
        context.lineWidth = 1;
        context.strokeStyle = (self.isRoot) ? defRootHeaderColor : self.headerColor;
        var w =(self.title.length*12 > self.width) ? self.title.length*12 : self.width;
        roundRect(context, 0, 0, w, self.height, 5, true, true);
    };
    
    var DrawHeaderArea = function(context)
    {
        context.fillStyle = (self.isRoot) ? defRootHeaderColor : self.headerColor;
        roundRectTwo(context, 0, 0, self.headerHeight, self.height, 5, true, true);
    };

    var DrawTitle = function(context, title)
    {
        context.beginPath();
        context.font = self.titleFontSettings;
        context.fillStyle = self.titleFontColor;
        var line = '';
        for (var i = 0; i < title.length; i++)
        {
            var t = line + title[i];
            var m = context.measureText(t);
            if (m.width > self.contentMaxLineWidth) break;
            else line = t;
        }

        context.fillText(line, self.headerHeight + 15, 35);
    };

    function roundRect (ctx, x, y, width, height, radius, fill, stroke)
    {
        if (typeof stroke == "undefined" ) stroke = true;
        if (typeof radius === "undefined") radius = 5;

        ctx.beginPath();
        ctx.moveTo(x + radius, y);
        ctx.lineTo(x + width - radius, y);
        ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
        ctx.lineTo(x + width, y + height - radius);
        ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
        ctx.lineTo(x + radius, y + height);
        ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
        ctx.lineTo(x, y + radius);
        ctx.quadraticCurveTo(x, y, x + radius, y);
        ctx.closePath();
        if (fill) ctx.fill();
        if (stroke) ctx.stroke();
    }
    
    function roundRectTwo (ctx, x, y, width, height, radius, fill, stroke)
    {
        if (typeof stroke == "undefined" ) stroke = true;
        if (typeof radius === "undefined") radius = 5;
        ctx.beginPath();
        ctx.moveTo(x + radius, y);
        ctx.lineTo(x + width, y);
        ctx.lineTo(x + width, y + height);
        ctx.lineTo(x + radius, y + height);
        ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
        ctx.lineTo(x, y + radius);
        ctx.quadraticCurveTo(x, y, x + radius, y);
        ctx.closePath();
        
        if (fill) ctx.fill();
        if (stroke) ctx.stroke();
    }
};