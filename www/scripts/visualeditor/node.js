// Basic node object
var Node = function() {
    var self = this;
    var defWidth = 230;
    var defHeight = 125;
    var defHeaderHeight = 30;
    var defColor = '#f0f0f0';
    var defHeaderColor = '#b3b2b2';
    var defRootHeaderColor = '#e9b23c';
    var defBorderSize = 1;
    var defBorderColor = '#b3b2b2';
    var def2PI = Math.PI * 2;
    var defLinkButtonBackgroundColor = '#9191cf';
    
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
    
    self.linkButtonRaius = 10;
    self.linkButtonBackgroundColor = defLinkButtonBackgroundColor;
    self.linkButtonStrokeSize = 1;
    self.linkButtonStrokeColor = '#ffffff';
    self.linkButtonHoverBackgroundColor = '#ff9900';
    self.linkButtonRootBgColor = '#e9b23c';
    self.linkButtonBgColor = '#b3b2b2';
    self.linkButtonRootBgShadowColor = '#b68c30';
    self.linkButtonBgShadowColor = '#6E6D6D';
    self.linkButtonRadius2 = 8;
    self.linkButtonStrokeSize2 = 2;
    self.linkButtonStrokeColor2 = '#ffffff';
    self.linkButtonLineWidth = 2;
    self.isLinkButtonEnabled = false;
    self.isLinkButtonIsHover = false;
    
    self.addButtonRadius = 10;
    self.addButtonRootBgColor = '#e9b23c';
    self.addButtonRootBgShadowColor = '#b68c30';
    self.addButtonBgColor = '#b3b2b2';
    self.addButtonBgShadowColor = '#6E6D6D';
    self.addButtonLineWidth = 2;
    self.addButtonLineColor = '#ffffff';
    self.addButtonIsHover = false;
    
    self.titleFontSettings = 'bold 15px Arial';
    self.titleFontColor = '#000000';
    self.contentFontSettings = '12px Arial';
    self.contentFontColor = '#000000';
    self.contentLineHeight = 14;
    self.contentMaxLineWidth = defWidth - self.headerHeight - 24;
    
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
    self.isActive = false;
    self.sections = new Array();
    self.showInfo = false;
    self.annotation = '';

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

        DrawSections(context);

        if(self.isSelected && !self.isActive)
            DrawSelectedArea(context);
        
        if(self.isActive)
            DrawActiveSelectedArea(context);
        
        DrawContentArea(context);
        DrawHeaderArea(context);
        DrawLinkButton(context);
        DrawAddButton(context);
        
        if(self.title.length > 0) {
            var title = self.title.replace(/<(?:.|\n)*?>/gm, '');
            DrawTitle(context, title);
        }
        
        if(self.content.length > 0) {
            var content = self.content.replace(/<(?:.|\n)*?>/gm, '');
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
                self.isLinkButtonIsHover = true;
            } else {
                isRedraw = true;
                self.isLinkButtonIsHover = false;
            }
            
            if(self.IsAddButtonCollision(mouse.x, mouse.y, viewport)) {
                isRedraw = true;
                self.addButtonIsHover = true;
            } else {
                isRedraw = true;
                self.addButtonIsHover = false;
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
        
        return (x  >= pos[0] && x <= (pos[0] + self.headerHeight * scale[0]) && y  >= pos[1] && y <= (pos[1] + self.height * scale[1])); 
    }
    
    self.IsLinkButtonCollision = function(x, y, viewport) {
        var tr = new Transform();
        tr.Multiply(viewport);
        tr.Multiply(self.transform);
        tr.Translate(self.headerHeight * 0.5, self.linkButtonRaius + 5);
        
        var pos = tr.GetPosition();
        var scale = tr.GetScale();
        
        return (self.linkButtonRaius * self.linkButtonRaius * (scale[0] + scale[1]) * 0.5) >= ((pos[0] - x) * (pos[0] - x) + (pos[1] - y) * (pos[1] - y));
    }
    
    self.IsAddButtonCollision = function(x, y, viewport) {
        var tr = new Transform();
        tr.Multiply(viewport);
        tr.Multiply(self.transform);
        tr.Translate(self.headerHeight * 0.5, self.height - self.addButtonRadius - 5);
        
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
        
        return (x  >= (pos[0] + self.headerHeight * scale[0]) && x <= (pos[0] + self.width * scale[0]) && y  >= (pos[1]) && y <= (pos[1] + (self.height) * scale[1])); 
    }
    
    self.TranslateNode = function(dx, dy, viewport) {
        var scale = viewport.GetScale();
        
        self.transform.TranslateWithoutScale(dx / scale[0], dy / scale[1]);
    }

    var DrawSections = function(context) {
        for(var i = self.sections.length; i--;) {
            context.beginPath();
            context.rect(-(i + 10), -(i + 10), self.width + i*2 + 20, self.height + i*2 + 20);
            context.lineWidth = 1;
            context.strokeStyle = self.sections[i].color;
            context.stroke();
        }
    };
    
    var DrawContentArea = function(context) {
        context.fillStyle = self.color;
        context.lineWidth = 1;
        context.strokeStyle = (self.isRoot) ? defRootHeaderColor : self.headerColor;
        roundRect(context, 0, 0, self.width, self.height, 5, true, true);
    }
    
    var DrawHeaderArea = function(context) {
        context.fillStyle = (self.isRoot) ? defRootHeaderColor : self.headerColor;
        roundRectTwo(context, 0, 0, self.headerHeight, self.height, 5, true, true);
    }
    
    var DrawLinkButton = function(context) {
        var grd = context.createLinearGradient(self.headerHeight * 0.5, 5, self.headerHeight * 0.5, self.linkButtonRaius*2 + 5);
        if(self.isRoot) {
            grd.addColorStop(0, '#eec46a');
            grd.addColorStop(1, '#dcaa41');
        } else {
            grd.addColorStop(0, '#c5c4c4');
            grd.addColorStop(1, '#a6a6a6');
        }
        context.save();
            context.beginPath();
            context.arc(self.headerHeight * 0.5, self.linkButtonRaius + 5, self.linkButtonRaius, def2PI, false);

            context.clip();

            context.beginPath();
            context.fillStyle = grd;
            context.arc(self.headerHeight * 0.5, self.linkButtonRaius + 5, self.linkButtonRaius, def2PI, false);
            context.fill();

            context.beginPath();
            context.lineWidth = 4;
            context.shadowColor   = (self.isRoot) ? self.linkButtonRootBgShadowColor : self.linkButtonBgShadowColor;
            context.shadowBlur    = 2;
            context.shadowOffsetX = (self.isLinkButtonIsHover || self.isLinkButtonEnabled) ? -2 : 2;
            context.shadowOffsetY = (self.isLinkButtonIsHover || self.isLinkButtonEnabled) ? 1 : -1;
            context.arc(self.headerHeight * 0.5, self.linkButtonRaius + 5, self.linkButtonRaius + 2, 0, 2 * Math.PI, false);
            context.stroke();
        context.restore();
        
        context.beginPath();
        context.moveTo(self.headerHeight * 0.5, 5);
        context.lineTo(self.headerHeight * 0.5, 10);
        context.moveTo(self.headerHeight * 0.5 + self.linkButtonRaius - 1, self.linkButtonRaius + 5);
        context.lineTo(self.headerHeight * 0.5 + self.linkButtonRaius - 6, self.linkButtonRaius + 5);
        context.moveTo(self.headerHeight * 0.5, self.linkButtonRaius * 2 + 4);
        context.lineTo(self.headerHeight * 0.5, self.linkButtonRaius * 2 - 1);
        context.moveTo(self.headerHeight * 0.5 - self.linkButtonRaius + 1, self.linkButtonRaius + 5);
        context.lineTo(self.headerHeight * 0.5 - self.linkButtonRaius + 6, self.linkButtonRaius + 5);
        context.lineWidth = self.linkButtonLineWidth;
        context.strokeStyle = self.linkButtonStrokeColor;
        context.stroke();
    }
    
    var DrawAddButton = function(context) {
        var grd = context.createLinearGradient(self.headerHeight * 0.5, self.height - self.addButtonRadius*2 - 5, self.headerHeight * 0.5, self.height - self.addButtonRadius - 5);
        if(self.isRoot) {
            grd.addColorStop(0, '#eec46a');
            grd.addColorStop(1, '#dcaa41');
        } else {
            grd.addColorStop(0, '#c5c4c4');
            grd.addColorStop(1, '#a6a6a6');
        }
        context.save();
            context.beginPath();
            context.arc(self.headerHeight * 0.5, self.height - self.addButtonRadius - 5, self.addButtonRadius, def2PI, false);

            context.clip();
            
            context.beginPath();
            context.fillStyle = grd;
            context.arc(self.headerHeight * 0.5, self.height - self.addButtonRadius - 5, self.addButtonRadius, def2PI, false);
            context.fill();

            context.beginPath();
            context.lineWidth = 4;
            context.shadowColor   = (self.isRoot) ? self.addButtonRootBgShadowColor : self.addButtonBgShadowColor;;
            context.shadowBlur    = 2;
            context.shadowOffsetX = (self.addButtonIsHover) ? -2 : 2;
            context.shadowOffsetY = (self.addButtonIsHover) ? 1 : -1;
            context.arc(self.headerHeight * 0.5, self.height - self.addButtonRadius - 5, self.addButtonRadius + 2, 0, 2 * Math.PI, false);
            context.stroke();
        context.restore();
        
        context.beginPath();
        context.moveTo(self.headerHeight * 0.5, self.height - self.addButtonRadius - 10);
        context.lineTo(self.headerHeight * 0.5, self.height - self.addButtonRadius);
        context.moveTo(self.headerHeight * 0.5 - 5, self.height - self.addButtonRadius - 5);
        context.lineTo(self.headerHeight * 0.5 + 5, self.height - self.addButtonRadius - 5);
        context.lineWidth = self.addButtonLineWidth;
        context.strokeStyle = self.addButtonLineColor;
        context.stroke();
    }
    
    var DrawTitle = function(context, title) {
        context.beginPath();
        context.font = self.titleFontSettings;
        context.fillStyle = self.titleFontColor;
        var line = '';
        for(var i = 0; i < title.length; i++) {
            var t = line + title[i];
            var m = context.measureText(t);
            if(m.width > self.contentMaxLineWidth) {
                break;
            } else {
                line = t;
            }
        }
        
        context.fillText(line, self.headerHeight + 13, 20);
    }
    
    var DrawContent = function(context, content) {
        context.beginPath();
        context.font = self.contentFontSettings;
        context.fillStyle = self.contentFontColor;
        
        var line = '';
        var y = 0;
        for(var i = 0, j = 1; i < content.length && j <= 7; i++) {
             var t = line + content[i];
             var m = context.measureText(t);
             if(m.width > self.contentMaxLineWidth) {
                 line = t;
                 context.fillText(line, self.headerHeight + 13, 36 + y);
                 y += self.contentLineHeight;
                 line = '';
                 j++;
             } else {
                 line = t;
             }
        }
        
        context.fillText(line, self.headerHeight + 13, 36 + y);
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
    
    var DrawActiveSelectedArea = function(context) {
        context.beginPath();
        context.lineWidth = 2;
        context.strokeStyle = '#1E2AAC';
        DashedLineTo(context, -self.paddingSelect, -self.paddingSelect, self.width + self.paddingSelect, -self.paddingSelect, [5,5]);
        DashedLineTo(context, self.width + self.paddingSelect, -self.paddingSelect, self.width + self.paddingSelect, self.height + self.paddingSelect, [5,5]);
        DashedLineTo(context, self.width + self.paddingSelect, self.height + self.paddingSelect, -self.paddingSelect, self.height + self.paddingSelect, [5,5]);
        DashedLineTo(context, -self.paddingSelect, self.height + self.paddingSelect, -self.paddingSelect, -self.paddingSelect, [5,5]);
        
        context.stroke();
    }
    
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
    
    self.GetCounterById = function(id) {
        if(self.counters == null && self.counters.length <= 0) return null;
        
        for(var i = 0; i < self.counters.length; i++) {
            if(self.counters[i].id == id)
                return self.counters[i];
        }
    
        return null;
    }
    
    function roundRect(ctx, x, y, width, height, radius, fill, stroke) {
        if (typeof stroke == "undefined" ) {
            stroke = true;
        }
        if (typeof radius === "undefined") {
            radius = 5;
        }
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
        if (fill) {
            ctx.fill();
        }    
        if (stroke) {
            ctx.stroke();
        }  
    }
    
    function roundRectTwo(ctx, x, y, width, height, radius, fill, stroke) {
        if (typeof stroke == "undefined" ) {
            stroke = true;
        }
        if (typeof radius === "undefined") {
            radius = 5;
        }
        ctx.beginPath();
        ctx.moveTo(x + radius, y);
        ctx.lineTo(x + width, y);
        ctx.lineTo(x + width, y + height);
        ctx.lineTo(x + radius, y + height);
        ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
        ctx.lineTo(x, y + radius);
        ctx.quadraticCurveTo(x, y, x + radius, y);
        ctx.closePath();
        
        if (fill) {
            ctx.fill();
        } 
        if (stroke) {
            ctx.stroke();
        }  
    }
}