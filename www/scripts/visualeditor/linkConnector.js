var LinkConnector = function() {
    var self = this;
    var def2PI = Math.PI * 2;
    
    self.transform = new Transform();
    self.connectorRadius = 16;
    self.connectorBackgroundColor = 'rgba(0, 0, 0, 0.5)';
    self.connectorStrokeSize = 3;
    self.connectorStrokeColor = '#ffffff';
    self.linkLineWidth = 2;
    self.linkLineColor = '#ffffff';
    self.node = null;
    self.isDragging = false;
    self.isMoved = false;
    
    self.Draw = function(context, viewport) {
        if(context == null) return;
        
        var tr = new Transform();
        tr.Multiply(viewport);
        tr.Multiply(self.transform);
        
        context.save();
        context.setTransform(tr.matrix[0], tr.matrix[1], tr.matrix[2], tr.matrix[3], tr.matrix[4], tr.matrix[5]);
        
        context.beginPath();
        context.arc(0, 0, self.connectorRadius, def2PI, false);
        context.lineWidth = self.connectorStrokeSize;
        context.strokeStyle = self.connectorStrokeColor;
        context.stroke();
        
        DrawLine(context, 0, -self.connectorRadius - 7, 0, -self.connectorRadius + 10);
        DrawLine(context, 0, self.connectorRadius + 7, 0, self.connectorRadius - 10);
        DrawLine(context, -self.connectorRadius - 7, 0, -self.connectorRadius + 10, 0);
        DrawLine(context, self.connectorRadius + 7, 0, self.connectorRadius - 10, 0);

        context.restore();
        
        DrawNodeLink(context, viewport);
    }
    
    self.MouseMove = function(mouse, viewport) {
        var isRedraw = false;
        if(mouse.isDown) {
            if(self.IsConnectorCollision(mouse.x, mouse.y, viewport) || self.isDragging) {
                isRedraw = true;
                self.isMoved = true;
                self.isDragging = true;
                TranslateConnector(mouse.x - mouse.oldX, mouse.y - mouse.oldY, viewport);
            }
        } else {
            self.isDragging = false;
            isRedraw = false;
        }
        
        return isRedraw;
    }
    
    self.MouseUp = function(mouse, viewport, nodes) {
        var result = new Array();
        
        var r = IsConnectorNodeCollision(nodes, viewport);
        
        if(r.length > 0 && r[0]) {
            result[0] = 'addLink';
            result[1] = self.node.id;
            result[2] = r[1];
        }
        
        return result;
    }
    
    self.Reset = function() {
        self.transform.SetIdentity();
        self.transform.Multiply(self.node.transform);
        self.transform.Translate(self.node.width * 0.5, -60);
    }
    
    self.IsConnectorCollision = function(x, y, viewport) {
        var tr = new Transform();
        tr.Multiply(viewport);
        tr.Multiply(self.transform);
        
        var pos = tr.GetPosition();
        var scale = viewport.GetScale();
        
        return (self.connectorRadius * self.connectorRadius * (scale[0] + scale[1]) * 0.5) >= ((pos[0] - x) * (pos[0] - x) + (pos[1] - y) * (pos[1] - y));
    }
    
    var IsConnectorNodeCollision = function(nodes, viewport) {
        if(nodes.length <= 0) return new Array();
        
        var result = new Array();
        var nodeTr = new Transform();
        var connectorTr = new Transform();
        connectorTr.Multiply(viewport);
        connectorTr.Multiply(self.transform);
        
        var connectorPos = connectorTr.GetPosition();
        var connectorRadius = connectorTr.GetScale()[0] * self.connectorRadius;
        
        for(var i = 0; i < nodes.length; i++) {
            nodeTr.SetIdentity();
            nodeTr.Multiply(viewport);
            nodeTr.Multiply(nodes[i].transform);
            
            var nodePos = nodeTr.GetPosition();
            var nodeScale = nodeTr.GetScale();
            
            var nodeXWidth = nodePos[0] + nodes[i].width * nodeScale[0];
            var nodeYHeight = nodePos[1] + nodes[i].height * nodeScale[1];
            
            var xS = connectorPos[0] + connectorRadius;
            var xD = connectorPos[0] - connectorRadius;
            var yS = connectorPos[1] + connectorRadius;
            var yD = connectorPos[1] - connectorRadius;
            if(((xS >= nodePos[0] && xS <= nodeXWidth) || (xD >= nodePos[0] && xD <= nodeXWidth)) && 
               ((yS >= nodePos[1] && yS <= nodeYHeight) || (yD >= nodePos[1] && yD <= nodeYHeight))) {
               result[0] = true;
               result[1] = nodes[i].id;
               break;
            }
        }
    
        return result;
    }
    
    var DrawLine = function(context, x, y, ex, ey) {
        context.beginPath();
        context.moveTo(x, y);
        context.lineTo(ex, ey);
        context.lineWidth = self.connectorStrokeSize;
        context.strokeStyle = self.connectorStrokeColor;
        context.stroke();
    }
    
    var DrawNodeLink = function(context, viewport) {
        if(self.node == null) return;
        
        var tr = new Transform();
        tr.Multiply(viewport);
        tr.Multiply(self.transform);

        var tr2 = new Transform();
        tr2.Multiply(viewport);
        
        if(self.node != null) {
            tr2.Multiply(self.node.transform);
            tr2.Translate(self.node.width * 0.5, 0);
        }
        
        context.save();
        
        var posA = tr.GetPosition();
        var posB = tr2.GetPosition();
        
        var vec = [posB[0] - posA[0], posB[1] - posA[1]];
        var mVec = Math.sqrt(vec[0] * vec[0] + vec[1] * vec[1]);
        vec = [vec[0] / mVec * self.connectorRadius, vec[1] / mVec * self.connectorRadius];

        var s = viewport.GetScale();

        //context.setTransform(tr.matrix[0], tr.matrix[1], tr.matrix[2], tr.matrix[3], tr.matrix[4], tr.matrix[5]);
        context.beginPath();
        context.moveTo(tr.matrix[4] + vec[0], tr.matrix[5] + vec[1]);
        //context.setTransform(tr2.matrix[0], tr2.matrix[1], tr2.matrix[2], tr2.matrix[3], tr2.matrix[4], tr2.matrix[5]);
        context.lineTo(tr2.matrix[4], tr2.matrix[5]);
        //context.lineWidth = self.linkLineWidth;
        context.lineWidth = self.linkLineWidth * (s[0] + s[1]) * 0.5;
        context.strokeStyle = self.linkLineColor;
        context.stroke();
        
        context.restore();
    }
    
    var TranslateConnector = function(dx, dy, viewport) {
        var scale = viewport.GetScale();
        
        self.transform.TranslateWithoutScale(dx / scale[0], dy / scale[1]);
    }
}