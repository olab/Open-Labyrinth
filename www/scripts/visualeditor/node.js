// Basic node object
var Node = function() {
    var self = this;
    var defWidth = 230;
    var defHeight = 125;
    var defHeaderHeight = 16;
    var defColor = '#FFFFFF';
    var defHeaderColor = '#8a8ab1';
    var defBorderSize = 1;
    var defBorderColor = '#eeeeee';
    var def2PI = Math.PI * 2;
    var defLinkButtonBackgroundColor = '#9191cf';
    var defAddButtonBackgroundColor = '#9191cf';
    var defRootButtonBackgroundColor = '#9191cf';
    var defDeleteButtonBackgroundColor = '#9191cf';
    
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
    
    self.linkButtonRaius = 12;
    self.linkButtonBackgroundColor = defLinkButtonBackgroundColor;
    self.linkButtonStrokeSize = 1;
    self.linkButtonStrokeColor = '#ffffff';
    self.linkButtonHoverBackgroundColor = '#ff9900';
    self.linkButtonRadius2 = 8;
    self.linkButtonStrokeSize2 = 2;
    self.linkButtonStrokeColor2 = '#ffffff';
    self.linkButtonLineWidth = 3;
    self.isLinkButtonEnabled = false;
    
    self.addButtonRadius = 12;
    self.addButtonBackgroundColor = defAddButtonBackgroundColor;
    self.addButtonStrokeSize = 1;
    self.addButtonStrokeColor = '#ffffff';
    self.addButtonHoverBackgroundColor = '#ff9900';
    self.addButtonLineWidth = 5;
    
    self.rootButtonRadius = 12;
    self.rootButtonBackgroundColor = defRootButtonBackgroundColor;
    self.rootButtonStrokeSize = 1;
    self.rootButtonStrokeColor = '#ffffff';
    self.rootButtonHoverBackgroundColor = '#ff9900';
    self.rootButtonActiveColor = '#ff9900';
    self.rootFontSettings = 'bold 16px Arial';
    self.rootFontColor = '#ffffff';
    
    self.deleteButtonRadius = 12;
    self.deleteButtonBackgroundColor = defDeleteButtonBackgroundColor;
    self.deleteButtonStrokeSize = 1;
    self.deleteButtonStrokeColor = '#ffffff';
    self.deleteButtonHoverBackgroundColor = '#ff9900';
    self.deleteButtonActiveColor = '#ff9900';
    self.deleteFontSettings = 'bold 16px Arial';
    self.deleteFontColor = '#ffffff';
    
    self.titleFontSettings = 'bold 15px Arial';
    self.titleFontColor = '#000000';
    self.contentFontSettings = '12px Arial';
    self.contentFontColor = '#000000';
    self.contentLineHeight = 14;
    self.contentMaxLineWidth = defWidth - self.linkButtonRaius * 0.5 - self.rootButtonRadius * 0.5 - 6;
    
    self.colorButtonWidth = 30;
    self.colorButtonHeight = 10;
    
    self.paddingSelect = 16;
    
    // Data
    self.id = 0;
    self.isRoot = false;
    self.isNew = false;
    self.title = '';
    self.content = '';
    self.support = '';
    self.supportKeywords = '';
    self.isExit = false;
    self.linkStyle = 1;
    self.nodePriority = 1;
    self.undo = false;
    self.isEnd = false;
    self.counters = new Array();
    self.isSelected = false;
    
    // Daraw current node
    // context - canvas context
    // viewport - Transform viewport transfomration
    self.Draw = function(context, viewport) {
        if(context == null) return;

        var tr = new Transform();
        tr.Multiply(viewport);
        tr.Multiply(self.transform);

        context.save();
        context.setTransform(tr.matrix[0], tr.matrix[1], tr.matrix[2], tr.matrix[3], tr.matrix[4], tr.matrix[5]);
        
        if(self.isSelected)
            DrawSelectedArea(context);
        
        DrawContentArea(context);
        DrawHeaderArea(context);
        DrawLinkButton(context);
        DrawAddButton(context);
        
        if(self.title.length > 0) {
            var title = self.title.replace(/<(?:.|\n)*?>/gm, '');
            title = (title.length > 27) ? (title.substring(0, 24) + '...') : title;
            DrawTitle(context, title);
        }
        
        if(self.content.length > 0) {
            var content = self.content.replace(/<(?:.|\n)*?>/gm, '');
            content = (content.length >= 160) ? (content.substring(0, 157) + '...') : content;
            DrawContent(context, content);
        }

        
        context.restore();
    }
    
    // Scale current node by x, y factors
    // sx - number X-scale factor
    // sy - number Y-scale factor
    self.Scale = function(sx, sy) {
        self.transform.Scale(sx, sy);
    }
    
    // Mouse move event hadler
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
            
            if(!isAnotherDrag && (self.IsHeaderCollision(mouse.x, mouse.y, viewport) || self.isDragging)) {
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
                self.linkButtonBackgroundColor = self.linkButtonHoverBackgroundColor;
            } else if(self.linkButtonBackgroundColor != defLinkButtonBackgroundColor) {
                isRedraw = true;
                self.linkButtonBackgroundColor = defLinkButtonBackgroundColor;
            }
            
            if(self.IsAddButtonCollision(mouse.x, mouse.y, viewport)) {
                isRedraw = true;
                self.addButtonBackgroundColor = self.addButtonHoverBackgroundColor;
            } else if(self.addButtonBackgroundColor != defAddButtonBackgroundColor) {
                isRedraw = true;
                self.addButtonBackgroundColor = defAddButtonBackgroundColor;
            }

        }

        return isRedraw;
    }
    
    self.MouseClick = function(mouse, viewport) {
        var result = new Array();

        if(self.IsAddButtonCollision(mouse.x, mouse.y, viewport)) {
            result[0] = self.id;
            result[1] = 'add';
        } else if(self.IsLinkButtonCollision(mouse.x, mouse.y, viewport)) {
            if(!self.isLinkButtonEnabled) {
                result[0] = self.id;
                result[1] = 'link';
            } else {
                result[0] = self.id;
                result[1] = 'rlink';
            }
            self.isLinkButtonEnabled = !self.isLinkButtonEnabled;
        } else if(self.IsMainAreaCollision(mouse.x, mouse.y, viewport)) {
            result[0] = self.id;
            result[1] = 'main';
        } else if(self.IsHeaderCollision(mouse.x, mouse.y, viewport)) {
            result[0] = self.id;
            result[1] = 'header';
        }
        
        return result;
    }
    
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
    }
    
    self.IsHeaderCollision = function(x, y, viewport) {
        var tr = new Transform();
        tr.Multiply(viewport);
        tr.Multiply(self.transform);
        
        var pos = tr.GetPosition();
        var scale = tr.GetScale();
        
        return (x  >= pos[0] && x <= (pos[0] + self.width * scale[0]) && y  >= pos[1] && y <= (pos[1] + self.headerHeight * scale[1])); 
    }
    
    self.IsLinkButtonCollision = function(x, y, viewport) {
        var tr = new Transform();
        tr.Multiply(viewport);
        tr.Multiply(self.transform);
        tr.Translate(self.width, self.height * 0.5 + self.headerHeight * 0.5);
        
        var pos = tr.GetPosition();
        var scale = tr.GetScale();
        
        return (self.linkButtonRaius * self.linkButtonRaius * (scale[0] + scale[1]) * 0.5) >= ((pos[0] - x) * (pos[0] - x) + (pos[1] - y) * (pos[1] - y));
    }
    
    self.IsAddButtonCollision = function(x, y, viewport) {
        var tr = new Transform();
        tr.Multiply(viewport);
        tr.Multiply(self.transform);
        tr.Translate(self.width * 0.5, self.height);
        
        var pos = tr.GetPosition();
        var scale = tr.GetScale();
        
        return (self.linkButtonRaius * self.linkButtonRaius * (scale[0] + scale[1]) * 0.5) >= ((pos[0] - x) * (pos[0] - x) + (pos[1] - y) * (pos[1] - y));
    }
    
    self.IsRootButtonCollision = function(x, y, viewport) {
        var tr = new Transform();
        tr.Multiply(viewport);
        tr.Multiply(self.transform);
        tr.Translate(0, self.height * 0.5 - self.rootButtonRadius - 3 + self.headerHeight * 0.5);
        
        var pos = tr.GetPosition();
        var scale = tr.GetScale();
        
        return (self.rootButtonRadius * self.rootButtonRadius * (scale[0] + scale[1]) * 0.5) >= ((pos[0] - x) * (pos[0] - x) + (pos[1] - y) * (pos[1] - y));
    }
    
    self.IsDeleteButtonCollision = function(x, y, viewport) {
        var tr = new Transform();
        tr.Multiply(viewport);
        tr.Multiply(self.transform);
        tr.Translate(0, self.height * 0.5 + self.deleteButtonRadius + 3 + self.headerHeight * 0.5);
        
        var pos = tr.GetPosition();
        var scale = tr.GetScale();
        
        return (self.deleteButtonRadius * self.deleteButtonRadius * (scale[0] + scale[1]) * 0.5) >= ((pos[0] - x) * (pos[0] - x) + (pos[1] - y) * (pos[1] - y));
    }
    
    self.IsColorButtonCollision = function(x, y, viewport) {
        var tr = new Transform();
        tr.Multiply(viewport);
        tr.Multiply(self.transform);
        tr.Translate(3, self.height - self.colorButtonHeight - 2);
        
        var pos = tr.GetPosition();
        var scale = tr.GetScale();
        
        return (x >= pos[0] && x <= (pos[0] + self.colorButtonWidth * scale[0]) && y >= pos[1] && y <= (pos[1] + self.colorButtonHeight * scale[1]));
    }
    
    self.IsMainAreaCollision = function(x, y, viewport) {
        var tr = new Transform();
        tr.Multiply(viewport);
        tr.Multiply(self.transform);
        
        var pos = tr.GetPosition();
        var scale = tr.GetScale();
        
        return (x  >= pos[0] && x <= (pos[0] + self.width * scale[0]) && y  >= (pos[1] + self.headerHeight * scale[1]) && y <= (pos[1] + (self.height - self.headerHeight) * scale[1])); 
    }
    
    self.TranslateNode = function(dx, dy, viewport) {
        var scale = viewport.GetScale();
        
        self.transform.TranslateWithoutScale(dx / scale[0], dy / scale[1]);
    }
    
    var DrawContentArea = function(context) {
        context.beginPath();
        context.rect(0, 0, self.width, self.height);
        context.fillStyle = self.color;
        context.fill();
        context.lineWidth = 1;
        context.strokeStyle = self.headerColor;
        context.stroke();
    }
    
    var DrawHeaderArea = function(context) {
        context.beginPath();
        context.rect(0, 0, self.width, self.headerHeight);
        context.fillStyle = (self.isRoot) ? self.rootButtonActiveColor : self.headerColor;
        context.fill();
    }
    
    var DrawLinkButton = function(context) {
        context.beginPath();
        context.arc(self.width, self.height * 0.5 + self.headerHeight * 0.5, self.linkButtonRaius, def2PI, false);
        context.fillStyle = (self.isLinkButtonEnabled) ? self.linkButtonHoverBackgroundColor : self.linkButtonBackgroundColor;
        context.fill();
        context.lineWidth = self.linkButtonStrokeSize;
        context.strokeStyle = self.linkButtonStrokeColor;
        context.stroke();
        
        context.beginPath();
        context.arc(self.width, self.height * 0.5 + self.headerHeight * 0.5, self.linkButtonRadius2, def2PI, false);
        context.lineWidth = self.linkButtonStrokeSize2;
        context.strokeStyle = self.linkButtonStrokeColor2;
        context.stroke();
        
        context.beginPath();
        context.moveTo(self.width, self.height * 0.5 + self.headerHeight * 0.5 - self.linkButtonRaius - 2);
        context.lineTo(self.width, self.height * 0.5 + self.headerHeight * 0.5 + self.linkButtonRaius + 2);
        context.lineWidth = self.linkButtonLineWidth;
        context.stroke();
        
        context.beginPath();
        context.moveTo(self.width - self.linkButtonRaius - 2, self.height * 0.5 + self.headerHeight * 0.5);
        context.lineTo(self.width + self.linkButtonRaius + 2, self.height * 0.5 + self.headerHeight * 0.5);
        context.lineWidth = self.linkButtonLineWidth;
        context.stroke();
    }
    
    var DrawAddButton = function(context) {
        context.beginPath();
        context.arc(self.width * 0.5, self.height, self.addButtonRadius, def2PI, false);
        context.fillStyle = self.addButtonBackgroundColor;
        context.fill();
        context.lineWidth = self.addButtonStrokeSize;
        context.strokeStyle = self.addButtonStrokeColor;
        context.stroke();
        
        context.beginPath();
        context.moveTo(self.width * 0.5 - self.addButtonRadius + 3, self.height);
        context.lineTo(self.width * 0.5 + self.addButtonRadius - 3, self.height);
        context.lineWidth = self.addButtonLineWidth;
        context.stroke();
        
        context.beginPath();
        context.moveTo(self.width * 0.5, self.height - self.addButtonRadius + 3);
        context.lineTo(self.width * 0.5, self.height + self.addButtonRadius - 3);
        context.lineWidth = self.addButtonLineWidth;
        context.stroke();
    }

    var DrawRootButton = function(context) {
        context.beginPath();
        context.arc(0, self.height * 0.5 - self.rootButtonRadius - 3 + self.headerHeight * 0.5, self.rootButtonRadius, def2PI, false);
        context.fillStyle = (self.isRoot) ? self.rootButtonActiveColor : self.rootButtonBackgroundColor;
        context.fill();
        context.lineWidth = self.rootButtonStrokeSize;
        context.strokeStyle = self.rootButtonStrokeColor;
        context.stroke();
        
        context.font = self.rootFontSettings;
        context.fillStyle = self.rootFontColor;
        context.fillText('R', -6, self.height * 0.5 - self.rootButtonRadius + self.headerHeight * 0.5 + 3);
    }
    
    var DrawDeleteButton = function(context) {
        context.beginPath();
        context.arc(0, self.height * 0.5 + self.deleteButtonRadius + 3 + self.headerHeight * 0.5, self.deleteButtonRadius, def2PI, false);
        context.fillStyle = self.deleteButtonBackgroundColor;
        context.fill();
        context.lineWidth = self.deleteButtonStrokeSize;
        context.strokeStyle = self.deleteButtonStrokeColor;
        context.stroke();
        
        context.font = self.deleteFontSettings;
        context.fillStyle = self.deleteFontColor;
        context.fillText('D', -6, self.height * 0.5 + self.rootButtonRadius + self.headerHeight * 0.5 + 9);
    }
    
    var DrawTitle = function(context, title) {
        context.beginPath();
        context.font = self.titleFontSettings;
        context.fillStyle = self.titleFontColor;
        context.fillText(title, 13, self.headerHeight + 20);
    }
    
    var DrawContent = function(context, content) {
        context.beginPath();
        context.font = self.contentFontSettings;
        context.fillStyle = self.contentFontColor;
        
        var words = content.split(' ');
        var line = '';
        var y = 0;
        for(var i = 0; i < words.length; i++) {
            var t = line + words[i] + ' ';
            var m = context.measureText(t);
            if(m.width > self.contentMaxLineWidth) {
                context.fillText(line, 14, self.headerHeight + 36 + y);
                line = words[i] + ' ';
                y += self.contentLineHeight;
            } else {
                line = t;
            }
        }
        
        var m = context.measureText(line);
        if(m.width > self.contentMaxLineWidth) {
            context.fillText(line.substring(0, 57) + '...', 13, self.headerHeight + 36 + y);
        } else {
            context.fillText(line, 13, self.headerHeight + 36 + y);
        }
        
    }
    
    var DrawColorButton = function(context) {
        var blockWidth = self.colorButtonWidth / 5;
        var colors = ['#ffffff', '#CC99FF', '#6699FF', '#66FF66', '#FFFF66'];
        
        for(var i = 0; i < 5; i++) {
            context.beginPath();
            context.rect(3 + blockWidth * i, self.height - self.colorButtonHeight - 2, blockWidth, self.colorButtonHeight);
            context.fillStyle = colors[i];
            context.fill();
        }
    }
    
    var DrawSelectedArea = function(context) {
        context.beginPath();
        context.lineWidth = 2;
        
        DashedLineTo(context, -self.paddingSelect, -self.paddingSelect, self.width + self.paddingSelect, -self.paddingSelect, [5,5]);
        DashedLineTo(context, self.width + self.paddingSelect, -self.paddingSelect, self.width + self.paddingSelect, self.height + self.paddingSelect, [5,5]);
        DashedLineTo(context, self.width + self.paddingSelect, self.height + self.paddingSelect, -self.paddingSelect, self.height + self.paddingSelect, [5,5]);
        DashedLineTo(context, -self.paddingSelect, self.height + self.paddingSelect, -self.paddingSelect, -self.paddingSelect, [5,5]);
        
        context.stroke();
    }
    
    var DashedLineTo = function (context, fromX, fromY, toX, toY, pattern) {
        var lt = function (a, b) {return a <= b;};
        var gt = function (a, b) {return a >= b;};
        var capmin = function (a, b) {return Math.min(a, b);};
        var capmax = function (a, b) {return Math.max(a, b);};

        var checkX = {thereYet: gt, cap: capmin};
        var checkY = {thereYet: gt, cap: capmin};

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
    
    self.GetCounterById = function(id) {
        if(self.counters == null && self.counters.length <= 0) return null;
        
        for(var i = 0; i < self.counters.length; i++) {
            if(self.counters[i].id == id)
                return self.counters[i];
        }
    
        return null;
    }
}