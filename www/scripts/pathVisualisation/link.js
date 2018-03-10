var Link = function()
{
    var self = this;
    var defColor = '#b3b2b2';
    var defColorPath = '#0088cc';
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
    self.arrowButtonRaduis = (self.lineWidth * 5 + 14) * 0.5 + 2;
    self.id = 0;
    self.pathStart = false;
    
    self.Draw = function(context, viewport)
    {
        DrawByType(context, viewport);
    };

    var DrawByType = function(context, viewport)
    {
        var stateParams = {
            lineWidth: self.lineWidth,
            color: self.color,
            lineColor: self.lineColor
        };

        if (self.type == 'direct')
        {
            DrawLine(context, stateParams, viewport);
            DrawOneArrow(context, stateParams, viewport, 'direct');
        }
        else if (self.type == 'back')
        {
            DrawLine(context, stateParams, viewport);
            DrawOneArrow(context, stateParams, viewport, 'back');
        }
        else if(self.type == 'dual')
        {
            if (self.pathStart == 'dual') stateParams.color = defColorPath;
            DrawLine(context, stateParams, viewport);
            DrawOneArrow(context, stateParams, viewport, 'direct');
            DrawOneArrow(context, stateParams, viewport, 'back');
        }
        else
        {
            DrawLine(context, stateParams, viewport);
            DrawNoneTypeLine(context, stateParams, viewport);
        }
    };

    var DrawNoneTypeLine = function(context, stateParams, viewport)
    {
        if (self.nodeA != null && self.nodeB != null)
        {
            var trs = GetNodesTransformations(viewport);
            var trA = trs[0];
            var trB = trs[1];
            var arrowPos = GetArrowPosition(trA.GetPosition(), trB.GetPosition());
            
            context.beginPath();
            context.arc(arrowPos[0], arrowPos[1], self.arrowButtonRaduis, def2PI, false);
            context.fillStyle = self.arrowButtonBackgroundColor;
            context.fill();
        }
    };
    
    var DrawOneArrow = function(context, stateParams, viewport, direction)
    {
        if(self.nodeA != null && self.nodeB != null)
        {
            var trs      = GetNodesTransformations(viewport),
                posA     = trs[0].GetPosition(),
                posB     = trs[1].GetPosition(),
                scale    = viewport.GetScale(),
                angle    = 0,
                arrowPos = GetArrowPosition(posA, posB),
                arrow    = [
                    [0, 0],
                    [-(stateParams.lineWidth * 5 + 10) * scale[0], -(stateParams.lineWidth * 1.4 + 5) * scale[1]],
                    [-(stateParams.lineWidth * 5 + 7) * scale[0], 0],
                    [-(stateParams.lineWidth * 5 + 10) * scale[0], (stateParams.lineWidth * 1.4 + 5) * scale[1]]
                ];

            if (direction == 'direct')
            {
                if (self.pathStart == self.nodeA.id) stateParams.color = defColorPath;
                angle = Math.atan2(posB[1] - posA[1], posB[0] - posA[0]);
            }
            else if(direction == 'back')
            {
                if (self.pathStart == self.nodeB.id) stateParams.color = defColorPath;
                angle = Math.atan2(posA[1] - posB[1], posA[0] - posB[0]);
            }

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
    };
    
    var DrawLine = function(context, stateParams, viewport)
    {
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
    };
    
    var DrawArrow = function(context, arrowShape, stateParams)
    {
        context.fillStyle = stateParams.color;
        context.beginPath();
        context.moveTo(arrowShape[0][0], arrowShape[0][1]);
        
        for (c in arrowShape)
        {
            if(c > 0)  context.lineTo(arrowShape[c][0], arrowShape[c][1]);
        }
        
        context.lineTo(arrowShape[0][0], arrowShape[0][1]);
        context.closePath();
        context.fill();
        if (self.pathStart != 'dual') stateParams.color = defColor;
    };
    
    var TranslateArrow = function(arrowShape, x, y) {
        var result = [];
        for(c in arrowShape) {
            result.push([arrowShape[c][0] + x, arrowShape[c][1] + y]);
        }

        return result;
    };
    
    var RotateArrow = function(arrowShape, angle) {
        var result = [];
        for(c in arrowShape) {
            result.push(RotatePoint(angle, arrowShape[c][0], arrowShape[c][1]));
        }

        return result;
    };
    
    var RotatePoint = function(angle, x, y) {
        return [(x * Math.cos(angle)) - (y * Math.sin(angle)), (x * Math.sin(angle)) + (y * Math.cos(angle))];
    };
    
    var GetNodesTransformations = function(viewport)
    {
        var trA = new Transform();
        var trB = new Transform();

        trA.Multiply(viewport);
        trA.Multiply(self.nodeA.transform);
        trA.Translate(self.nodeA.width * 0.5, self.nodeA.height * 0.5);

        trB.Multiply(viewport);
        trB.Multiply(self.nodeB.transform);
        trB.Translate(self.nodeB.width * 0.5, self.nodeB.height * 0.5);

        return [trA, trB];
    };

    var GetArrowPosition = function(posA, posB)
    {
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
    };
};