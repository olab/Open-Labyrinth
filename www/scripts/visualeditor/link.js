var Link = function() {
    var self = this;
    var defColor = '#b3b2b2';
    var defLineColor = '#ffffff';
    var def2PI = Math.PI * 2;
    var defArrowButtonBackgroundColor = '#ffffff';
    
    self.nodeA = null;
    self.nodeB = null;
    self.color = defColor;
    self.lineWidth = 2;
    self.lineColor = defLineColor;
    self.type = 'none';
    self.arrowButtonBackgroundColor = defArrowButtonBackgroundColor;
    self.arrowButtonHoverColor = '#e9b23c';
    self.arrowButtonRaduis = (self.lineWidth * 5 + 14) * 0.5 + 2;
    self.isHover = false;
    
    self.id = 0;
    self.isNew = false;
    self.isSelected = false;
    self.selectPadding = 5;
    self.label = '';
    self.imageId = 0;
    
    self.Draw = function(context, viewport) {
        DrawByType(context, viewport);
    }
    
    self.MouseMove = function(mouse, viewport) {
        var isRedraw = false;
        
        if(self.IsLinkButtonCollision(mouse.x, mouse.y, viewport)) {
            isRedraw = true;
            self.isHover = true;
            self.arrowButtonBackgroundColor = self.arrowButtonHoverColor;
        } else if(self.arrowButtonBackgroundColor != defArrowButtonBackgroundColor) {
            isRedraw = true;
            self.isHover = false;
            self.arrowButtonBackgroundColor = defArrowButtonBackgroundColor;
        }
        
        return isRedraw;
    }
    
    self.MouseClick = function(mouse, viewport) {       
        return self.IsLinkButtonCollision(mouse.x, mouse.y, viewport);
    }
    
    self.IsLinkInRect = function(x, y, width, height, viewport) {
        var trs = GetNodesTransformations(viewport);
        var trA = trs[0];
        var trB = trs[1];
        
        var xW = x + width;
        var yH = y + height;
        
        var x1 = [Math.min(x, xW), Math.max(x, xW)];
        var y1 = [Math.min(y, yH), Math.max(y, yH)];
        
        var posA = trA.GetPosition();
        var posB = trB.GetPosition();
        
        var scale = viewport.GetScale();
        
        var cdx = Math.abs((posA[0] - posB[0]) * 0.5);
        var cdy = Math.abs((posA[1] - posB[1]) * 0.5);
        
        var cPos = [Math.min(posA[0], posB[0]) + cdx, Math.min(posA[1], posB[1]) + cdy];
        
        return (cPos[0] >= x1[0] && cPos[0] <= x1[1]) && (cPos[1] >= y1[0] && cPos[1] <= y1[1]);
    }
    
    self.IsLinkButtonCollision = function(x, y, viewport) {
        var result = false;
        
        var trs = GetNodesTransformations(viewport);
        var trA = trs[0];
        var trB = trs[1];
        
        var angle = new Array();
        var circlePos = new Array();
        var xDiff = 0;
        var yDiff = 0;
        var posA = trA.GetPosition();
        var posB = trB.GetPosition();
        var scale = viewport.GetScale();
        
        var arrowPos = GetArrowPosition(posA, posB);
        var doubleR = self.arrowButtonRaduis * self.arrowButtonRaduis;

        if(self.type == 'dual') {
            angle[0] = Math.atan2(posB[1] - posA[1], posB[0] - posA[0]);
            angle[1] = Math.atan2(posA[1] - posB[1], posA[0] - posB[0]);
            
            circlePos = RotatePoint(angle[0], (-(self.lineWidth * 5 + 10) * 0.5 - 3) * (scale[0] + scale[1]) * 0.5, 0);
            circlePos[0] += arrowPos[0];
            circlePos[1] += arrowPos[1];
            
            var xDiff = x - circlePos[0];
            var yDiff = y - circlePos[1];
            var r1 = doubleR * (scale[0] + scale[1]) * 0.5 > ((xDiff * xDiff) + (yDiff * yDiff));
            
            var circlePos = RotatePoint(angle[1], (-(self.lineWidth * 5 + 10) * 0.5 - 3) * (scale[0] + scale[1]) * 0.5, 0);
            circlePos[0] += arrowPos[0];
            circlePos[1] += arrowPos[1];
            
            xDiff = x - circlePos[0];
            yDiff = y - circlePos[1];
            var r2 = doubleR * (scale[0] + scale[1]) * 0.5 > ((xDiff * xDiff) + (yDiff * yDiff));
            
            result = r1 || r2;
        } else if(self.type == 'direct') {
            angle[0] = Math.atan2(posB[1] - posA[1], posB[0] - posA[0]);
            
            circlePos = RotatePoint(angle[0], (-(self.lineWidth * 5 + 10) * 0.5 - 3) * (scale[0] + scale[1]) * 0.5, 0);
            circlePos[0] += arrowPos[0];
            circlePos[1] += arrowPos[1];
            
            xDiff = x - circlePos[0];
            yDiff = y - circlePos[1];
            result = doubleR * (scale[0] + scale[1]) * 0.5 > ((xDiff * xDiff) + (yDiff * yDiff));
        } else if(self.type == 'back') {
            angle[0] = Math.atan2(posA[1] - posB[1], posA[0] - posB[0]);
            
            circlePos = RotatePoint(angle[0], (-(self.lineWidth * 5 + 10) * 0.5 - 3) * (scale[0] + scale[1]) * 0.5, 0);
            circlePos[0] += arrowPos[0];
            circlePos[1] += arrowPos[1];
            
            xDiff = x - circlePos[0];
            yDiff = y - circlePos[1];
            result = doubleR > ((xDiff * xDiff) + (yDiff * yDiff));
        } else {
            circlePos = [0, 0];
            circlePos[0] += arrowPos[0];
            circlePos[1] += arrowPos[1];
            
            xDiff = x - circlePos[0];
            yDiff = y - circlePos[1];
            result = doubleR * (scale[0] + scale[1]) * 0.5 > ((xDiff * xDiff) + (yDiff * yDiff));
        }
        
        return result;
    }
    
    var DrawByType = function(context, viewport) {
        var stateParams = {
            lineWidth: self.lineWidth, 
            color: self.color,
            lineColor: self.lineColor
        };

        if(self.type == 'direct') {
            DrawLine(context, stateParams, viewport);
            DrawOneArrow(context, stateParams, viewport, 'direct', false);
        } else if(self.type == 'back') {
            DrawLine(context, stateParams, viewport);
            DrawOneArrow(context, stateParams, viewport, 'back', false);
        } else if(self.type == 'dual') {
            DrawLine(context, stateParams, viewport);
            DrawOneArrow(context, stateParams, viewport, 'back', true);
            DrawOneArrow(context, stateParams, viewport, 'direct', true);
        } else {
            DrawLine(context, stateParams, viewport);
            DrawNoneTypeLine(context, stateParams, viewport);
        }
        
        if(self.isSelected)
            DrawSelected(context, viewport, stateParams);
    }

    var DrawSelected = function(context, viewport, stateParams) {
        var trs = GetNodesTransformations(viewport);
        var trA = trs[0];
        var trB = trs[1];
        
        var posA = trA.GetPosition();
        var posB = trB.GetPosition();
        
        var scale = viewport.GetScale();
        var avgScale = (scale[0] + scale[1]) * 0.5;
        
        var cdx = Math.abs((posA[0] - posB[0]) * 0.5);
        var cdy = Math.abs((posA[1] - posB[1]) * 0.5);
        
        var cPos = [Math.min(posA[0], posB[0]) + cdx, Math.min(posA[1], posB[1]) + cdy];
        var x = new Array();
        var y = new Array();
        if(self.type == 'direct' || self.type == 'back') {
            var angle = 0;
            if(self.type == 'direct') {
                angle = Math.atan2(posB[1] - posA[1], posB[0] - posA[0]);
            } else if(self.type == 'back') {
                angle = Math.atan2(posA[1] - posB[1], posA[0] - posB[0]);
            }
            
            var arrowPos = GetArrowPosition(trA.GetPosition(), trB.GetPosition());
            
            var circlePos = RotatePoint(angle, (-(stateParams.lineWidth * 5 + 10) * 0.5 - 3) * (scale[0] + scale[1]) * 0.5, 0);
            
            circlePos[0] += arrowPos[0];
            circlePos[1] += arrowPos[1];

            x = [circlePos[0] - self.arrowButtonRaduis * avgScale - self.selectPadding, circlePos[0] + self.arrowButtonRaduis * avgScale + self.selectPadding];
            y = [circlePos[1] - self.arrowButtonRaduis * avgScale - self.selectPadding, circlePos[1] + self.arrowButtonRaduis * avgScale + self.selectPadding];
        } else if(self.type == 'dual') {
            x = [cPos[0] - self.arrowButtonRaduis * 2 * avgScale - self.selectPadding * 2, cPos[0] + self.arrowButtonRaduis * 2 * avgScale + self.selectPadding * 2];
            y = [cPos[1] - self.arrowButtonRaduis * 2 * avgScale - self.selectPadding * 2, cPos[1] + self.arrowButtonRaduis * 2 * avgScale + self.selectPadding * 2];
        } else {
            x = [cPos[0] - self.arrowButtonRaduis * avgScale - self.selectPadding, cPos[0] + self.arrowButtonRaduis * avgScale + self.selectPadding];
            y = [cPos[1] - self.arrowButtonRaduis * avgScale - self.selectPadding, cPos[1] + self.arrowButtonRaduis * avgScale + self.selectPadding];
        }
        
        context.beginPath();
        context.lineWidth = 2 * avgScale;
        
        var st = [5 * avgScale, 5 * avgScale];
        DashedLineTo(context, x[0], y[0], x[1], y[0], st);
        DashedLineTo(context, x[1], y[0], x[1], y[1], st);
        DashedLineTo(context, x[1], y[1], x[0], y[1], st);
        DashedLineTo(context, x[0], y[1], x[0], y[0], st);
        
        context.stroke();
    }
    
    var DrawNoneTypeLine = function(context, stateParams, viewport) {
        if(self.nodeA != null && self.nodeB != null) {
            var trs = GetNodesTransformations(viewport);
            var trA = trs[0];
            var trB = trs[1];

            var arrowPos = GetArrowPosition(trA.GetPosition(), trB.GetPosition());
            
            context.beginPath();
            context.arc(arrowPos[0], arrowPos[1], self.arrowButtonRaduis, def2PI, false);
            context.fillStyle = self.arrowButtonBackgroundColor;
            context.fill();
        }
    }
    
    var DrawOneArrow = function(context, stateParams, viewport, direction, mirror) {
        if(self.nodeA != null && self.nodeB != null) {
            var trs = GetNodesTransformations(viewport);
            var trA = trs[0];
            var trB = trs[1];
            
            var scale = viewport.GetScale();
            
            var arrow = new Array();
            if(mirror) {
                arrow = [
                [-(stateParams.lineWidth * 5 + 14) * scale[0], 0],
                [-7 * scale[0], -(stateParams.lineWidth * 1.4 + 5) * scale[1]],
                [-10 * scale[0], 0],
                [-7 * scale[0], (stateParams.lineWidth * 1.4 + 5) * scale[1]]
                ];
            } else {
                arrow = [
                [0, 0],
                [-(stateParams.lineWidth * 5 + 10) * scale[0], -(stateParams.lineWidth * 1.4 + 5) * scale[1]],
                [-(stateParams.lineWidth * 5 + 7) * scale[0], 0],
                [-(stateParams.lineWidth * 5 + 10) * scale[0], (stateParams.lineWidth * 1.4 + 5) * scale[1]]
                ];
            }
            
            var angle = 0;
            var posA = trA.GetPosition();
            var posB = trB.GetPosition();
            if(direction == 'direct') {
                angle = Math.atan2(posB[1] - posA[1], posB[0] - posA[0]);
            } else if(direction == 'back') {
                angle = Math.atan2(posA[1] - posB[1], posA[0] - posB[0]);
            }
            
            var arrowPos = GetArrowPosition(trA.GetPosition(), trB.GetPosition());
            
            var circlePos = RotatePoint(angle, (-(stateParams.lineWidth * 5 + 10) * 0.5 - 3) * (scale[0] + scale[1]) * 0.5, 0);
            var shape = TranslateArrow(RotateArrow(arrow, angle), arrowPos[0], arrowPos[1]);
            
            circlePos[0] += arrowPos[0];
            circlePos[1] += arrowPos[1];
            
            context.beginPath();
            context.arc(circlePos[0], circlePos[1], self.arrowButtonRaduis * (scale[0] + scale[1]) * 0.5, def2PI, false);
            context.fillStyle = self.arrowButtonBackgroundColor;
            context.fill();
            
            if(self.isHover) {
                stateParams.color = '#ffffff';
            }
            DrawArrow(context, shape, stateParams);
        }
    }
    
    var DrawLine = function(context, stateParams, viewport) {
        var trs = GetNodesTransformations(viewport);
        var trA = trs[0];
        var trB = trs[1];

        var s = viewport.GetScale();

        context.save();
            context.beginPath();
            //context.setTransform(trA.matrix[0], trA.matrix[1], trA.matrix[2], trA.matrix[3], trA.matrix[4], trA.matrix[5]);
            context.moveTo(trA.matrix[4], trA.matrix[5]);
            //context.setTransform(trB.matrix[0], trB.matrix[1], trB.matrix[2], trB.matrix[3], trB.matrix[4], trB.matrix[5]);
            context.lineTo(trB.matrix[4], trB.matrix[5]);
            context.lineWidth = stateParams.lineWidth * (s[0] + s[1]) * 0.5;
            context.strokeStyle = stateParams.lineColor;
            context.stroke();
        context.restore();
    }
    
    var DrawArrow = function(context, arrowShape, stateParams) {
        context.fillStyle = stateParams.color;
        context.beginPath();
        context.moveTo(arrowShape[0][0], arrowShape[0][1]);
        
        for(c in arrowShape) {
            if(c > 0) 
                context.lineTo(arrowShape[c][0], arrowShape[c][1]);
        }
        
        context.lineTo(arrowShape[0][0], arrowShape[0][1]);
        context.closePath();
        context.fill();
    }
    
    var TranslateArrow = function(arrowShape, x, y) {
        var result = new Array();
        for(c in arrowShape) {
            result.push([arrowShape[c][0] + x, arrowShape[c][1] + y]);
        }
        
        return result;
    }
    
    var RotateArrow = function(arrowShape, angle) {
        var result = new Array();
        for(c in arrowShape) {
            result.push(RotatePoint(angle, arrowShape[c][0], arrowShape[c][1]));
        }
        
        return result;
    }
    
    var RotatePoint = function(angle, x, y) {
        return [(x * Math.cos(angle)) - (y * Math.sin(angle)), (x * Math.sin(angle)) + (y * Math.cos(angle))];
    }
    
    var GetNodesTransformations = function(viewport) {
        var trA = new Transform();
        var trB = new Transform();
            
        trA.Multiply(viewport);
        trA.Multiply(self.nodeA.transform);
        trA.Translate(self.nodeA.width * 0.5, self.nodeA.height * 0.5);
            
        trB.Multiply(viewport);
        trB.Multiply(self.nodeB.transform);
        trB.Translate(self.nodeB.width * 0.5, self.nodeB.height * 0.5);
        
        return [trA, trB];
    }
    
    var GetArrowPosition = function(posA, posB) {
        var delta = [Math.abs(posA[0] - posB[0]) * 0.5, Math.abs(posA[1] - posB[1]) * 0.5];
        var arrowPos = [0, 0];
            
        if(posA[0] > posB[0]) {
            arrowPos[0] = posA[0] - delta[0];
        } else {
            arrowPos[0] = posB[0] - delta[0];
        }
            
        if(posA[1] > posB[1]) {
            arrowPos[1] = posA[1] - delta[1];
        } else {
            arrowPos[1] = posB[1] - delta[1];
        }
        
        return arrowPos;
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
}