var Link = function() {
    var self = this;
    var defColor = '#ffffff';
    var def2PI = Math.PI * 2;
    var defArrowButtonBackgroundColor = '#999999';
    
    self.nodeA = null;
    self.nodeB = null;
    self.color = defColor;
    self.lineWidth = 2;
    self.type = 'none';
    self.arrowButtonBackgroundColor = defArrowButtonBackgroundColor;
    self.arrowButtonHoverColor = '#ff9900';
    self.arrowButtonRaduis = (self.lineWidth * 5 + 10) * 0.5 + 2;
    
    self.id = 0;
    self.isNew = false;
    
    self.Draw = function(context, viewport) {
        DrawByType(context, viewport);
    }
    
    self.MouseMove = function(mouse, viewport) {
        var isRedraw = false;
        
        if(IsLinkButtonCollision(mouse.x, mouse.y, viewport)) {
            isRedraw = true;
            self.arrowButtonBackgroundColor = self.arrowButtonHoverColor;
        } else if(self.arrowButtonBackgroundColor != defArrowButtonBackgroundColor) {
            isRedraw = true;
            self.arrowButtonBackgroundColor = defArrowButtonBackgroundColor;
        }
        
        return isRedraw;
    }
    
    self.MouseClick = function(mouse, viewport) {       
        return IsLinkButtonCollision(mouse.x, mouse.y, viewport);
    }
    
    var IsLinkButtonCollision = function(x, y, viewport) {
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
            color: self.color
        };

        if(self.type == 'direct') {
            DrawOneArrow(context, stateParams, viewport, 'direct');
        } else if(self.type == 'back') {
            DrawOneArrow(context, stateParams, viewport, 'back');
        } else if(self.type == 'dual') {
            DrawOneArrow(context, stateParams, viewport, 'direct');
            DrawOneArrow(context, stateParams, viewport, 'back');
        } else {
            DrawNoneTypeLine(context, stateParams, viewport);
        }
    }
    
    var DrawNoneTypeLine = function(context, stateParams, viewport) {
        if(self.nodeA != null && self.nodeB != null) {
            var trs = GetNodesTransformations(viewport);
            var trA = trs[0];
            var trB = trs[1];
            
            context.save();
            context.beginPath();
            context.setTransform(trA.matrix[0], trA.matrix[1], trA.matrix[2], trA.matrix[3], trA.matrix[4], trA.matrix[5]);
            context.moveTo(0, 0);
            context.setTransform(trB.matrix[0], trB.matrix[1], trB.matrix[2], trB.matrix[3], trB.matrix[4], trB.matrix[5]);
            context.lineTo(0, 0);
            context.lineWidth = stateParams.lineWidth;
            context.strokeStyle = stateParams.color;
            context.stroke();
            context.restore();

            var arrowPos = GetArrowPosition(trA.GetPosition(), trB.GetPosition());
            
            context.beginPath();
            context.arc(arrowPos[0], arrowPos[1], self.arrowButtonRaduis, def2PI, false);
            context.fillStyle = self.arrowButtonBackgroundColor;
            context.fill();
        }
    }
    
    var DrawOneArrow = function(context, stateParams, viewport, direction) {
        if(self.nodeA != null && self.nodeB != null) {
            var trs = GetNodesTransformations(viewport);
            var trA = trs[0];
            var trB = trs[1];
            
            context.save();
            context.beginPath();
            context.setTransform(trA.matrix[0], trA.matrix[1], trA.matrix[2], trA.matrix[3], trA.matrix[4], trA.matrix[5]);
            context.moveTo(0, 0);
            context.setTransform(trB.matrix[0], trB.matrix[1], trB.matrix[2], trB.matrix[3], trB.matrix[4], trB.matrix[5]);
            context.lineTo(0, 0);
            context.lineWidth = stateParams.lineWidth;
            context.strokeStyle = stateParams.color;
            context.stroke();
            context.restore();
            
            var scale = viewport.GetScale();
            
            var arrow = [
            [0, 0],
            [-(stateParams.lineWidth * 5 + 10) * scale[0], -(stateParams.lineWidth * 1.4 + 3) * scale[1]],
            [-(stateParams.lineWidth * 5 + 10) * scale[0], (stateParams.lineWidth * 1.4 + 3) * scale[1]]
            ];
            
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

            DrawArrow(context, shape, stateParams);
        }
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
        trA.Translate(self.nodeA.width * 0.5, 0);
            
        trB.Multiply(viewport);
        trB.Multiply(self.nodeB.transform);
        trB.Translate(self.nodeB.width * 0.5, 0);
        
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
}