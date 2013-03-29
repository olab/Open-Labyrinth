var Preview = function() {
    var self = this;
    var canvasOffsetLeft = 0;
    var canvasOffsetTop = 0;
    var padding = 10;
    var diagonalSize = 0;
    
    var currentMinCoords = null;
    var currentMaxCoords = null;
    var currentScaleFactor = 1;
    var currentViewport = null;
    var currentViewportSize = null;
    
    var isMouseClick = false;
    var isDrag = false;
    
    self.$canvas = null;
    self.canvas = null;
    self.context = null;
    self.nodeWidth = 230;
    self.nodeHeight = 125;
    self.mouse = new Mouse();
    self.visualEditor = null;
    
    self.Init = function(params) {       
        if('canvasId' in params) {
            self.$canvas = $(params.canvasId);

            if(self.$canvas != null) {
                self.$canvas.mousedown(MouseDown);
                self.$canvas.mouseup(MouseUp);
                self.$canvas.mousemove(MouseMove);
                self.$canvas.mouseout(MouseOut);
                
                self.canvas = self.$canvas[0];
                canvasOffsetLeft = self.canvas.offsetLeft;
                canvasOffsetTop = self.canvas.offsetTop;
                
                var dWidth = self.canvas.width - padding * 2;
                var dHeight = self.canvas.height - padding * 2;
                diagonalSize = Math.sqrt(dWidth * dWidth + dHeight * dHeight);
            }
        }
        
        if('visualEditor' in params) {
            self.visualEditor = params.visualEditor;
        }
        
        CreateContext();
    }
    
    self.Render = function(nodes, links, viewport, width, height) {
        if(self.context == null) return;
        ClearContext();
        
        var minCoords = GetMinCoords(nodes);
        var maxCoords = GetMaxCoords(nodes);

        var vptPos = viewport.GetPosition();
        var vptSca = viewport.GetScale();
        
        width /= vptSca[0];
        height /= vptSca[1];
        
        if(minCoords != null) {
            if(minCoords[0] > -vptPos[0]) minCoords[0] = -vptPos[0];
            if(minCoords[1] > -vptPos[1]) minCoords[1] = -vptPos[1];
        }
    
        if(maxCoords != null) {
            if(maxCoords[0] < -vptPos[0] + width) maxCoords[0] = -vptPos[0] + width;
            if(maxCoords[1] < -vptPos[1] + height) maxCoords[1] = -vptPos[1] + height;
        }

        var currentDiagonalSize = 0;
        if(minCoords != null && maxCoords != null) {
            var d = Math.max(maxCoords[0] - minCoords[0], maxCoords[1] - minCoords[1]);
            currentDiagonalSize = Math.sqrt(d*d*2);
        }
        
        var scaleFactor = Math.min(diagonalSize, currentDiagonalSize) / Math.max(diagonalSize, currentDiagonalSize);
        
        var offset = [0, 0];
        if(minCoords != null && maxCoords != null) {
            offset = [-(minCoords[0] + (maxCoords[0] - minCoords[0]) * 0.5), -minCoords[1]];
        }
        
        var tNodes = new Array();
        
        for(var i = 0; i < nodes.length; i++) {
            var pos = nodes[i].transform.GetPosition();
            tNodes.push([pos[0] + offset[0], pos[1] + offset[1], nodes[i].id, nodes[i].isRoot]);
        }

        if(tNodes.length > 0) {
            if(links != null && links.length > 0) {
                for(var i = 0; i < tNodes.length; i++) {
                    var nodeLinks = new Array();
                    
                    for(var j = 0; j < links.length; j++) {
                        if(links[j].nodeA.id == tNodes[i][2]) nodeLinks.push(links[j].nodeB);
                    }
                    
                    if(nodeLinks.length > 0) {
                        self.context.save();
                        
                        self.context.beginPath();
                        self.context.lineWidth = 1;
                        self.context.strokeStyle = '#C7C7C7';
                        for(var j = 0; j < nodeLinks.length; j++) {
                            var tNodeB = null;
                            for(var k = 0; k < tNodes.length; k++) {
                                if(tNodes[k][2] == nodeLinks[j].id) {
                                    tNodeB = tNodes[k];
                                    break;
                                }
                            }
                            
                            if(tNodeB != null) {
                                self.context.moveTo(tNodes[i][0] * scaleFactor + self.canvas.width * 0.5 + self.nodeWidth * scaleFactor * 0.5, tNodes[i][1] * scaleFactor + padding + self.nodeHeight * scaleFactor * 0.5);
                                self.context.lineTo(tNodeB[0] * scaleFactor + self.canvas.width * 0.5 + self.nodeWidth * scaleFactor * 0.5, tNodeB[1] * scaleFactor + padding + self.nodeHeight * scaleFactor * 0.5);
                            }
                        }
                        self.context.stroke();
                        
                        self.context.restore();
                    }
                }
            }
            
            for(var i = 0; i < tNodes.length; i++) {
                self.context.save();
                
                self.context.beginPath();
                self.context.fillStyle = (tNodes[i][3]) ? '#e9b23c' : '#000000';
                self.context.lineWidth = 1;
                self.context.strokeStyle = '#ffffff';
                self.context.rect(tNodes[i][0] * scaleFactor + self.canvas.width * 0.5, tNodes[i][1] * scaleFactor + padding, self.nodeWidth * scaleFactor, self.nodeHeight * scaleFactor);
                self.context.fill();
                self.context.stroke();
                
                self.context.restore();
            }
            
            self.context.save();
            
            self.context.beginPath();
            self.context.lineWidth = 1;
            self.context.strokeStyle = '#0A3EFC';
            self.context.rect((-vptPos[0] + offset[0]) * scaleFactor + self.canvas.width * 0.5, (-vptPos[1] + offset[1]) * scaleFactor + padding, width * scaleFactor, height * scaleFactor);
            self.context.stroke();
                
            self.context.restore();
        }
        
        currentMinCoords = minCoords;
        currentMaxCoords = maxCoords;
        currentScaleFactor = scaleFactor;
        currentViewport = [(-vptPos[0] + offset[0]) * scaleFactor + self.canvas.width * 0.5, (-vptPos[1] + offset[1]) * scaleFactor + padding];
        currentViewportSize = [width * scaleFactor, height * scaleFactor];
    }
    
    var MouseDown = function(e) {
        isMouseClick = true;
        UpdateMousePosition(e);
    } 
    
    var MouseUp = function(e) {
        isMouseClick = false;
        UpdateMousePosition(e);
    }
    
    var MouseMove = function(e) {
        if(isMouseClick) {
            UpdateMousePosition(e);

            if((IsInViewport(self.mouse.x, self.mouse.y) || isDrag) && currentViewportSize != null && self.visualEditor != null) {
                isDrag = true;
                var dx = (self.mouse.x - self.mouse.oldX) / currentScaleFactor;
                var dy = (self.mouse.y - self.mouse.oldY) / currentScaleFactor;

                self.visualEditor.TranslateViewport(-dx, -dy);
            } else {
                isDrag = false;
            }
        }
    }
    
    var MouseOut = function(e) {
        isDrag = false;
        isMouseClick = false;
    }
    
    var UpdateMousePosition = function(event) {
        self.mouse.oldX = self.mouse.x;
        self.mouse.oldY = self.mouse.y;
        
        if(event.offsetX) {
            self.mouse.x = event.offsetX;
            self.mouse.y = event.offsetY;
        } else if(event.layerX) {
            self.mouse.x = event.layerX - canvasOffsetLeft;
            self.mouse.y = event.layerY - canvasOffsetTop;
        } else {
            self.mouse.x = event.pageX - self.$canvas.offset().left;
            self.mouse.y = event.pageY - self.$canvas.offset().top;
            
            //$('#coords').text(self.$canvas.offset().left + " " + self.$canvas.offset().top);
        }
        
        if(isNaN(self.mouse.x))
            self.mouse.x = 0;
        
        if(isNaN(self.mouse.y))
            self.mouse.y = 0;
    }
    
    var IsInViewport = function(x, y) {
        if(currentViewport == null) return false;
        
        return (x >= currentViewport[0] && x <= (currentViewport[0] + currentViewportSize[0]) && y >= currentViewport[1] && y <= (currentViewport[1] + currentViewportSize[1])); 
    }
    
    var GetMinCoords = function(nodes) {
        if(nodes == null || nodes.length <= 0) return null;
        
        var coords = nodes[0].transform.GetPosition();
        for(var i = 0; i < nodes.length; i++) {
            var p = nodes[i].transform.GetPosition();
            if(coords[0] > p[0])
                coords[0] = p[0];
            
            if(coords[1] > p[1])
                coords[1] = p[1];
        }
        
        return coords;
    }
    
    var GetMaxCoords = function(nodes) {
        if(nodes == null || nodes.length <= 0) return null;
        
        var coords = nodes[0].transform.GetPosition();
        for(var i = 0; i < nodes.length; i++) {
            var p = nodes[i].transform.GetPosition();
            
            p = [p[0] + nodes[i].width, p[1] + nodes[i].height];
            
            if(coords[0] < p[0])
                coords[0] = p[0];
            
            if(coords[1] < p[1])
                coords[1] = p[1];
        }
        
        return coords;
    }
    
    var ClearContext = function() {
        self.context.save();
        self.context.setTransform(1, 0, 0, 1, 0, 0);
        self.context.clearRect(0, 0, self.canvas.width, self.canvas.height);
        self.context.restore();
    }
    
    var CreateContext = function() {
        if(self.canvas == null) return;
        
        self.context = self.canvas.getContext('2d');
    }
}