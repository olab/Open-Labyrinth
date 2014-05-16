var Element = function() {
    var self = this;
    
    self.isDragging         = false;
    self.type               = 'labyrinth';
    self.width              = 230;
    self.height             = 75;
    self.headerHeight       = 30;
    self.color              = '#f0f0f0';
    self.headerColor        = '#b3b2b2';
    self.borderSize         = 1;
    self.borderColor        = '#b3b2b2';
    self.titleFontSettings  = 'bold 15px Arial';
    self.titleFontColor     = '#000000';
    self.paddingSelect      = 16;
    self.transform          = new Transform();
    
    // Data
    self.id         = 0;
    self.isNew      = false;
    self.title      = '';
    self.isSelected = false;
    self.stepId     = 0;
    self.stepName   = '';
    self.stepColor  = '';

    self.Draw = function(context, viewport) {
        if(context == null) return;

        var tr = new Transform();
        tr.Multiply(viewport);
        tr.Multiply(self.transform);

        context.save();
        context.setTransform(tr.matrix[0], tr.matrix[1], tr.matrix[2], tr.matrix[3], tr.matrix[4], tr.matrix[5]);

        if (self.isSelected) DrawSelectedArea(context);

        DrawContentArea(context);
        DrawHeaderArea(context);
        DrawTitle(context, self.title);
        if (self.stepId) DrawStep(context);

        context.restore();
    };

    // Scale current node by x, y factors
    // sx - number X-scale factor
    // sy - number Y-scale factor
    self.Scale = function(sx, sy) {
        self.transform.Scale(sx, sy);
    };
    
    // Mouse move event handler
    self.MouseMove = function(mouse, viewport, anotherNodes) {
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

            if( ! isAnotherDrag && (self.IsMainAreaCollision(mouse.x, mouse.y, viewport) || self.IsHeaderCollision(mouse.x, mouse.y, viewport) || self.isDragging)) {
                isRedraw = true;
                self.isDragging = true;
                if(self.isSelected && anotherNodes.length > 0) {
                    for(var j = 0; j < anotherNodes.length; j++) {
                        if(anotherNodes[j].isSelected) {
                            anotherNodes[j].TranslateNode(mouse.x - mouse.oldX, mouse.y - mouse.oldY, viewport);
                        }
                    }
                } else {
                    self.TranslateNode(mouse.x - mouse.oldX, mouse.y - mouse.oldY, viewport);
                }
            } else {
                self.isDragging = false;
            }
        } else self.isDragging = false;
        return isRedraw;
    };

    self.IsNodeInRect = function(x, y, width, height, viewport) {
        var tr = new Transform();
        tr.Multiply(viewport);
        tr.Multiply(self.transform);
        
        var pos = tr.GetPosition();
        var scale = tr.GetScale();
        
        var x0 = [pos[0], pos[0] + self.width * scale[0]];
        var y0 = [pos[1], pos[1] + self.height * scale[1]];
        
        var xW = x + width;
        var yH = y + height;
        
        var x1 = [Math.min(x, xW), Math.max(x, xW)];
        var y1 = [Math.min(y, yH), Math.max(y, yH)];
        
        return ((x0[0] >= x1[0] && x0[0] <= x1[1]) || (x0[1] >= x1[0] && x0[1] <= x1[1])) && 
        ((y0[0] >= y1[0] && y0[0] <= y1[1]) || (y0[1] >= y1[0] && y0[1] <= y1[1]));
    };
    
    self.IsHeaderCollision = function(x, y, viewport) {
        var tr = new Transform();
        tr.Multiply(viewport);
        tr.Multiply(self.transform);
        
        var pos = tr.GetPosition();
        var scale = tr.GetScale();
        
        return (x  >= pos[0] && x <= (pos[0] + self.headerHeight * scale[0]) && y  >= pos[1] && y <= (pos[1] + self.height * scale[1])); 
    };

    self.IsMainAreaCollision = function(x, y, viewport) {
        var tr = new Transform();
        tr.Multiply(viewport);
        tr.Multiply(self.transform);
        
        var pos = tr.GetPosition();
        var scale = tr.GetScale();
        
        return (x  >= (pos[0] + self.headerHeight * scale[0]) && x <= (pos[0] + self.width * scale[0]) && y  >= (pos[1]) && y <= (pos[1] + (self.height) * scale[1])); 
    };
    
    self.TranslateNode = function (dx, dy, viewport) {
        var scale = viewport.GetScale();
        
        self.transform.TranslateWithoutScale(dx / scale[0], dy / scale[1]);
    };

    var DrawStep = function(context) {
        context.beginPath();
        context.rect(-8, -8, self.width + 16, self.height + 16);
        context.lineWidth = 1;
        context.fillText(self.stepName, self.headerHeight + 13, -20);
        context.strokeStyle = self.stepColor;
        context.stroke();
    };
    
    var DrawContentArea = function(context) {
        context.fillStyle = self.color;
        context.lineWidth = 1;
        context.strokeStyle = self.headerColor;
        roundRect(context, 0, 0, self.width, self.height, 5, true, true);
    };
    
    var DrawHeaderArea = function(context) {
        context.fillStyle = self.headerColor;
        roundRectTwo(context, 0, 0, self.headerHeight, self.height, 5, true, true);
    };

    var DrawTitle = function (context, title)
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
        
        context.fillText(line, self.headerHeight + 13, 20);
    };
    
    var DrawSelectedArea = function(context) {
        context.beginPath();
        context.lineWidth = 2;
        
        DashedLineTo(context, -self.paddingSelect, -self.paddingSelect, self.width + self.paddingSelect, -self.paddingSelect, [5,5]);
        DashedLineTo(context, self.width + self.paddingSelect, -self.paddingSelect, self.width + self.paddingSelect, self.height + self.paddingSelect, [5,5]);
        DashedLineTo(context, self.width + self.paddingSelect, self.height + self.paddingSelect, -self.paddingSelect, self.height + self.paddingSelect, [5,5]);
        DashedLineTo(context, -self.paddingSelect, self.height + self.paddingSelect, -self.paddingSelect, -self.paddingSelect, [5,5]);
        
        context.stroke();
    };
    
    var DashedLineTo = function (context, fromX, fromY, toX, toY, pattern) {
        var lt = function (a, b) {
            return a <= b;
        };
        var gt = function (a, b) {
            return a >= b;
        };
        var capmin = function (a, b) {
            return Math.min(a, b);
        };
        var capmax = function (a, b) {
            return Math.max(a, b);
        };

        var checkX = {
            thereYet: gt, 
            cap: capmin
        };
        var checkY = {
            thereYet: gt, 
            cap: capmin
        };

        if (fromY - toY > 0) {
            checkY.thereYet = lt;
            checkY.cap = capmax;
        }
        if (fromX - toX > 0) {
            checkX.thereYet = lt;
            checkX.cap = capmax;
        }

        context.moveTo(fromX, fromY);
        var offsetX = fromX;
        var offsetY = fromY;
        var idx = 0, dash = true;
        while (!(checkX.thereYet(offsetX, toX) && checkY.thereYet(offsetY, toY))) {
            var ang = Math.atan2(toY - fromY, toX - fromX);
            var len = pattern[idx];

            offsetX = checkX.cap(toX, offsetX + (Math.cos(ang) * len));
            offsetY = checkY.cap(toY, offsetY + (Math.sin(ang) * len));

            if (dash) context.lineTo(offsetX, offsetY);
            else context.moveTo(offsetX, offsetY);

            idx = (idx + 1) % pattern.length;
            dash = !dash;
        }
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
    
    function roundRectTwo (ctx, x, y, width, height, radius, fill, stroke) {
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