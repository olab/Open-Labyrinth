var VisualEditor = function()
{
    var self = this,
        viewport = new Transform(),
        canvasOffsetLeft = 0,
        canvasOffsetTop = 0,
        generateIdNodeCounter = 1,
        generateIdLinkCounter = 1,
        maxZoom = 1.6,
        minZoom = 0.1,
        body = $('body');
    
    self.$canvasContainer = null;
    self.$canvas = null;
    self.canvas = null;
    self.context = null;
    self.mouse = new Mouse();
    self.lastMouse = new Mouse();
    self.nodes = [];
    self.links = [];
    self.zoomInFactor = 1.2;
    self.zommOutFactor = 0.8;
    self.isChanged = false;
    self.isViewportInit = true;
    self.isSelectActive = false;
    self.copyFunction = null;
    self.pasteFunction = null;
    self.zoomIn = null;
    self.zoomOut = null;
    self.update = null;
    self.turnOnPanMode = null;
    self.turnOnSelectMode = null;
    self.unsavedData = false;
    self.save = null;
    self.path = [];

    self.$aButtonsContianer = $('#ve_additionalActionButton');

    self.selectRightPanel = null;

    self.preview = null;
    self.mode = 'node';

    self.$sectionSelect = null;

    // Initialize visual editor
    self.Init = function(params)
    {
        if ('canvasContainer' in params) self.$canvasContainer = $(params.canvasContainer);
        
        if ('canvasId' in params)
        {
            self.$canvas = $(params.canvasId);
            if (self.$canvas != null)
            {
                self.canvas = self.$canvas[0];
                canvasOffsetLeft = self.canvas.offsetLeft;
                canvasOffsetTop = self.canvas.offsetTop;
            }
        }

        CreateContext();
        CreateEvents();

        if (self.mode == 'node')
        {
            self.preview = new Preview();
            self.preview.Init({
                canvasId: '#canvasPreview',
                visualEditor: self
            });
        }

        self.Resize(null);
        self.ZoomOut();
        self.ZoomOut();
    };
    
    // Render current state of visual editor
    self.Render = function()
    {
        ClearContext();
        if (self.links.length > 0)
        {
            for (var i = 0; i < self.links.length; i++) self.links[i].Draw(self.context, viewport);
        }

        if (self.path.length > 0)
        {
            for (var i = 0; i < self.path.length; i++)
            {
                var link = self.path[i];
                if (link.length)
                {
                    link.lineColor = '#0088cc';
                    link.Draw(self.context, viewport);
                }
            }
        }

        if (self.nodes.length > 0)
        {
            for (var i = 0; i < self.nodes.length; i++) self.nodes[i].Draw(self.context, viewport);
        }

        if(self.preview != null) self.preview.Render(self.nodes, self.links, viewport, self.canvas.width, self.canvas.height);
    };

    // Zoom in viewport
    self.ZoomIn = function()
    {
        var result      = false,
            scale       = viewport.GetScale(),
            testScale   = ((scale[0] + scale[1]) * 0.5) * self.zommOutFactor,
            oldSize     = [self.canvas.width / scale[0], self.canvas.height / scale[1]],
            newSize     = [0, 0],
            newScale    = [1, 1];

        if (testScale <= maxZoom)
        {
            result = true;

            viewport.Scale(self.zoomInFactor, self.zoomInFactor);
            newScale = viewport.GetScale();

            newSize = [self.canvas.width / newScale[0], self.canvas.height / newScale[1]];

            viewport.TranslateWithoutScale(-(oldSize[0] - newSize[0]) * 0.5, -(oldSize[1] - newSize[1]) * 0.5);
        }

        if (testScale * self.zoomInFactor > maxZoom) result = false;

        return result;
    };
    
    // Zoom out viewport
    self.ZoomOut = function()
    {
        var result      = false,
            scale       = viewport.GetScale(),
            testScale   = ((scale[0] + scale[1]) * 0.5) / self.zommOutFactor,
            oldSize     = [self.canvas.width / scale[0], self.canvas.height / scale[1]],
            newSize     = [0, 0],
            newScale    = [1, 1];

        if (testScale >= minZoom)
        {
            result = true;

            viewport.Scale(self.zommOutFactor, self.zommOutFactor);
            newScale = viewport.GetScale();

            newSize = [self.canvas.width / newScale[0], self.canvas.height / newScale[1]];
            viewport.TranslateWithoutScale((newSize[0] - oldSize[0]) * 0.5, (newSize[1] - oldSize[1]) * 0.5);
        }
    
        if (testScale / self.zoomInFactor < minZoom) result = false;
        
        return result;
    };

    // Deserialize nodes info
    self.Deserialize = function(jsonString)
    {
        if(jsonString.length <= 0) return;

        var object = evalJson(jsonString);
        if (object == null) return;

        self.nodes = [];
        var copyNodes = null;

        if ('nodes' in object && object.nodes.length > 0)
        {
            copyNodes = [];
            for (var i = 0; i < object.nodes.length; i++)
            {
                var node = new Node();
                node.id = object.nodes[i].id;

                var testId = node.id + "";
                if (testId.indexOf('g') > -1)
                {
                    var g = parseInt(testId.substr(0, testId.length - 1));
                    if ( ! isNaN(g)) generateIdNodeCounter = g;

                    node.isNew = true;
                }

                node.title = decode64(object.nodes[i].title);
                node.content = decode64(object.nodes[i].content);
                node.support = decode64(object.nodes[i].support);
                node.annotation = decode64(object.nodes[i].annotation);
                node.isExit = (object.nodes[i].isExit == 'true');
                node.undo = (object.nodes[i].undo == 'true');
                node.isEnd = (object.nodes[i].isEnd == 'true');
                node.isRoot = (object.nodes[i].isRoot == 'true');
                node.showInfo = (object.nodes[i].showInfo == 1);
                var x = parseInt(object.nodes[i].x);
                var y = parseInt(object.nodes[i].y);

                if(isNaN(x)) x = 0;
                if(isNaN(y)) y = 0;

                var linkStyle = parseInt(object.nodes[i].linkStyle);
                if (isNaN(linkStyle)) linkStyle = 1;
                node.linkStyle = linkStyle;

                var nodePriority = parseInt(object.nodes[i].nodePriority);
                if (isNaN(nodePriority)) nodePriority = 1;
                node.nodePriority = nodePriority;

                node.transform.Translate(x, y);
                node.color = (object.nodes[i].color.length > 0) ? object.nodes[i].color : node.color;

                copyNodes.push(node);
                self.nodes.push(node);
            }
        }

        self.links = [];
        if ('links' in object && object.links.length > 0)
        {
            for (var i = 0; i < object.links.length; i++)
            {
                var nodeAId = object.links[i].nodeA;
                var nodeBId = object.links[i].nodeB;

                var nodeA = GetNodeById(nodeAId);
                if (nodeA == null) continue;

                var nodeB = GetNodeById(nodeBId);
                if (nodeB == null) continue;

                var id = object.links[i].id;

                var testLinkId = object.links[i].id + "";
                if (testLinkId.indexOf('g') > -1)
                {
                    var l = parseInt(testLinkId.substr(0, testLinkId.length - 1));
                    if(!isNaN(l)) generateIdLinkCounter = l;
                }

                var existLink = self.GetLinkById(id);
                if(existLink != null) continue;

                var link = new Link();

                link.id = id;
                link.nodeA = nodeA;
                link.nodeB = nodeB;
                link.label = decode64(object.links[i].label);
                link.imageId = object.links[i].imageId;
                link.type = (object.links[i].type.length > 0) ? object.links[i].type : 'direct';
                self.links.push(link);
            }
        }

        if (selectedSession)
        {
            for (i = 0; i < selectedSession.length; i++)
            {
                if (i+1 < selectedSession.length)
                {
                    self.path.push(getLinkIDbyNodes(selectedSession[i].id_node, selectedSession[i+1].id_node, object));
                }
            }
        }

        if ('nodeMap' in object && object.nodeMap.length > 0 && self.history != null)  self.history.Remap(object.nodeMap);

        if (self.isViewportInit)
        {
            self.isViewportInit = false;
            var rootNode = GetRootNode();
            if (rootNode != null)
            {
                var pos = rootNode.transform.GetPosition();
                var scale = viewport.GetScale();

                viewport.SetPosition(-pos[0] + self.canvas.width / scale[0] * 0.5 - rootNode.width / scale[0] * 0.5, -pos[1] + self.canvas.height / scale[1] * 0.5 - rootNode.height / scale[1] * 0.5);
            }
        }

        if (generateIdNodeCounter > 1) generateIdNodeCounter++;
        if (generateIdLinkCounter > 1) generateIdLinkCounter++;
    };

    var getLinkIDbyNodes = function (nodeFirst, nodeSecond, object)
    {
        var link = null;
        if ('links' in object && object.links.length > 0)
        {
            for (var i = 0; i < object.links.length; i++)
            {
                var nodeA = object.links[i].nodeA;
                var nodeB = object.links[i].nodeB;

                if ((nodeA == nodeFirst && nodeB == nodeSecond) || (nodeB == nodeFirst && nodeA == nodeSecond))
                {
                    var id_link = object.links[i].id;
                    link = self.GetLinkById(id_link);

                    for (var j=0; j < self.path.length; j++)
                    {
                       if (self.path[j].id == id_link) {
                           link.pathStart = 'dual';
                           self.path.splice(j, 1);
                           return link;
                       }
                    }

                    link.pathStart = nodeFirst;
                }
            }
        }
        return link;
    };

    self.DeserializeLinear = function(jsonString)
    {
        self.Deserialize(jsonString);
        
        if (self.nodes != null && self.nodes.length > 0)
        {
            for (var i = 0; i < self.nodes.length; i++)
            {
                for (var j = 0; j < (self.nodes.length - 1); j++)
                {
                    if (self.nodes[j].id > self.nodes[j+1].id)
                    {
                        var tmp = self.nodes[j+1];
                        self.nodes[j+1] = self.nodes[j];
                        self.nodes[j] = tmp;
                    }
                }
            }
            BuildLinear(self.nodes);
        }
    };
    
    self.DeserializeBranched = function(jsonString)
    {
        self.Deserialize(jsonString);
        
        if (self.nodes != null && self.nodes.length > 0)
        {
            for (var i = 0; i < self.nodes.length; i++)
            {
                for (var j = 0; j < (self.nodes.length - 1); j++)
                {
                    if (self.nodes[j].id > self.nodes[j+1].id)
                    {
                        var tmp = self.nodes[j+1];
                        self.nodes[j+1] = self.nodes[j];
                        self.nodes[j] = tmp;
                    }
                }
            }
            BuildBranched(self.nodes);
        }
    };

    var BuildLinear = function(nodes) {
        if(nodes == null || nodes.length <= 0) return;

        var pos = viewport.GetPosition();
        var scale = viewport.GetScale();

        var w = self.canvas.width * 0.5 / scale[0] - pos[0] - nodes[0].width * 0.5;
        var h = 150 - pos[1] + Math.random() * (70 - 40) + 40;

        nodes[0].transform.SetIdentity();
        nodes[0].transform.Translate(w, h);
        for (var i = 1; i < nodes.length; i++)
        {
            nodes[i].transform.SetIdentity();
            nodes[i].transform.Translate(w, h + i * 2 * nodes[i].height);
        }
    };
    
    var BuildBranched = function(nodes)
    {
        if(nodes == null || nodes.length <= 0) return;
        
        var pos = viewport.GetPosition();
        var scale = viewport.GetScale();
        
        var w = self.canvas.width * 0.5 / scale[0] - pos[0] - nodes[0].width * 0.5;
        var h = 150 - pos[1] + Math.random() * (70 - 40) + 40;
        
        var oldW = w;
        nodes[0].transform.SetIdentity();
        nodes[0].transform.Translate(w, h);
        w += nodes[0].width * 0.5;
        w -= ((nodes.length - 2) * nodes[0].width + (nodes.length - 3) * nodes[0].width) * 0.5;
        
        for (var i = 1; i < nodes.length - 1; i++)
        {
            nodes[i].transform.SetIdentity();
            nodes[i].transform.Translate(w, h + 2 * nodes[i].height);
            w += 2 * nodes[i].width;
        }
        
        nodes[nodes.length - 1].transform.SetIdentity();
        nodes[nodes.length - 1].transform.Translate(oldW, h + 4 * nodes[nodes.length - 1].height);
    };

    self.GetWidth = function()
    {
        if(self.$canvas == null) return 0;

        return self.$canvas.width();
    };

    self.Resize = function()
    {
        if(self.$canvasContainer != null && self.$canvas != null)
        {
            var h = window.innerHeight;
            var w = window.innerWidth;

            if ( ! $("#fullScreen").hasClass('active'))
            {
                self.$canvas.attr('width', self.$canvasContainer.width());
                h = parseInt(h) - 270;
                if (h < 400) h = 400;
                $(self.$canvasContainer).height(h);
                self.$canvas.attr('height', self.$canvasContainer.height());
            }
            else
            {
                $(self.$canvasContainer).height(h);
                if (h > 545) h = 545;
                $('#tab-content-scrollable').css('height', (h - 115) + 'px');
                $(self.$canvasContainer).width(w);
                self.$canvas.attr('height', self.$canvasContainer.height());
                self.$canvas.attr('width', self.$canvasContainer.width());
            }
            self.Render();
        }
    };

    var ClearContext = function()
    {
        self.context.save();
        self.context.setTransform(1, 0, 0, 1, 0, 0);
        self.context.clearRect(0, 0, self.canvas.width, self.canvas.height);
        self.context.restore();
    };

    var CreateContext = function()
    {
        if(self.canvas == null) return;

        self.context = self.canvas.getContext('2d');
    };

    var CreateEvents = function()
    {
        if(self.canvas == null) return;
        
        self.canvas.addEventListener("mousedown", MouseDown, false);
        self.canvas.addEventListener("mouseup", MouseUp, false);
        self.canvas.addEventListener("mousemove", MouseMove, false);
        self.canvas.addEventListener("mouseout", MouseOut, false);
        self.canvas.addEventListener("touchstart", MouseDown, false);
        self.canvas.addEventListener("touchmove", MouseMove, false);
        self.canvas.addEventListener("touchend", MouseUp, false);
    };
    
    var MouseOut = function()
    {
        body.css('cursor', 'default');
        body.removeClass('clearCursor');
    };

    var UpdateMousePosition = function(event)
    {
        self.mouse.oldX = self.mouse.x;
        self.mouse.oldY = self.mouse.y;
        
        if (event.offsetX)
        {
            self.mouse.x = event.offsetX;
            self.mouse.y = event.offsetY;
        }
        else if(event.layerX)
        {
            self.mouse.x = event.layerX - canvasOffsetLeft;
            self.mouse.y = event.layerY - canvasOffsetTop;
        }
        else
        {
            self.mouse.x = event.pageX - canvasOffsetLeft;
            self.mouse.y = event.pageY - canvasOffsetTop;
        }
        
        if(isNaN(self.mouse.x)) self.mouse.x = 0;
        if(isNaN(self.mouse.y)) self.mouse.y = 0;
    };
    
    // Events
    var MouseDown = function(event)
    {
        //event.preventDefault();
        self.mouse.isDown = true;
        UpdateMousePosition(event);

        var isRedraw = false;
        var positions = [];

        if (self.nodes.length > 0)
        {
            for (var i = self.nodes.length - 1; i >= 0; i--)
            {
                if (self.nodes[i].isSelected) positions.push(self.nodes[i]);
                var result = self.nodes[i].MouseClick(self.mouse, viewport);
                if (result.length > 0 && !isRedraw)
                {
                    isRedraw = true;
                    if (result[1] == 'header') {
                        positions.push(self.nodes[i]);
                    } else if(result[1] == 'add') {
                        AddNodeWithLink(result[0]);
                    } else if(result[1] == 'link') {
                        ShowLinkConnector(result[0]);
                    } else if(result[1] == 'rlink') {
                        self.linkConnector = null;
                    } else if(result[1] == 'color') {
                        ShowColorpickerDialog(result[0]);
                        self.mouse.isDown = false;
                    } else if(result[1] == 'delete') {
                        ShowDeleteDialog(result[0]);
                        self.mouse.isDown = false;
                    } else if(result[1] == 'main') {
                        ShowRightPanel(result[0], 'node');
                        self.mouse.isDown = false;
                    } else if(result[1] == 'deleteC') {
                        ShowDeleteDialog(result[0]);
                        self.mouse.isDown = false;
                    } else if(result[1] == 'rootC') {
                        SetRootNode(result[0]);
                    }
                }
            }
        }
        
        if (self.nodes.length > 0)
        {
            for (var i = self.nodes.length - 1; i >= 0; i--)
            {
                if (self.nodes[i].isDragging) positions.push(self.nodes[i]);
            }
        }
        
        if (self.history != null && positions.length > 0)  self.history.HistoryPosition(positions);
        
        if ( ! isRedraw && self.selectorTool != null && self.isSelectActive)
        {
            if( ! ctrlKeyPressed)
            {
                for (var i = 0; i < self.links.length; i++) self.links[i].isSelected = false;
                for (var i = self.nodes.length - 1; i >= 0; i--) self.nodes[i].isSelected = false;
            }
            self.selectorTool.MouseDown(self.mouse, viewport);
            isRedraw = true;
        }

        if (isRedraw) self.Render();
        
        return false;
    };
    
    var MouseUp = function(event)
    {
        //event.preventDefault();
        self.mouse.isDown = false;
        UpdateMousePosition(event);
        self.lastMouse.x = self.mouse.x;
        self.lastMouse.y = self.mouse.y;
        
        var isRedraw = false;

        if (self.linkConnector != null)
        {
            var r = self.linkConnector.MouseUp(self.mouse, viewport, self.nodes);
            
            if (r.length > 0)
            {
                var link = IsExistLink(r[1], r[2]);
                if (r[1] != r[2] && link == null)
                {
                    var nodeA = GetNodeById(r[1]);
                    var nodeB = GetNodeById(r[2]);
                    
                    if(self.history != null) self.history.HistoryJSON();
                    
                    var newLink = new Link();
                    
                    newLink.nodeA = nodeA;
                    newLink.nodeB = nodeB;
                    newLink.type = 'direct';
                    newLink.id = GetNewLinkId();
                    self.links.push(newLink);
                    self.linkConnector.node.isLinkButtonEnabled = false;
                    self.linkConnector = null;
                }
                else if(r[1] != r[2] && link != null)
                {
                    link.type = 'dual';
                    self.linkConnector.node.isLinkButtonEnabled = false;
                    self.linkConnector = null;
                }
                else
                {
                    self.linkConnector.node.isLinkButtonEnabled = false;
                    self.linkConnector = null;
                }
                isRedraw = true;
            }
        }
        
        if ( ! isRedraw && self.selectorTool != null && self.isSelectActive)
        {
            var existSelect = false;
            if (self.nodes.length > 0)
            {
                for (var i = self.nodes.length - 1; i >= 0; i--)
                {
                    if (self.nodes[i].IsNodeInRect(self.selectorTool.x, self.selectorTool.y, self.selectorTool.width, self.selectorTool.height, viewport) || self.nodes[i].isSelected)
                    {
                        self.nodes[i].isSelected = true;
                        
                        if( ! existSelect) existSelect = true;
                    }
                }
            }
            
            if (self.links.length > 0)
            {
                for (var i = self.links.length - 1; i >= 0; i--)
                {
                    if(self.links[i].IsLinkInRect(self.selectorTool.x, self.selectorTool.y, self.selectorTool.width, self.selectorTool.height, viewport) || self.links[i].isSelected)
                    {
                        self.links[i].isSelected = true;
                        
                        if( ! existSelect) existSelect = true;
                    }
                }
            }

            self.selectorTool.MouseUp(self.mouse, viewport);

            if (self.$aButtonsContianer != null)
            {
                if(existSelect) self.$aButtonsContianer.show();
                else self.$aButtonsContianer.hide();
            }
            
            isRedraw = true;
        }

        if(isRedraw) self.Render();

        return false;
    };

    var MouseMove = function(event)
    {
        var isCursorSet = false;
        
        if(self.mouse.isDown) event.preventDefault();
        
        UpdateMousePosition(event);

        var isRedraw = false;
        
        event.stopPropagation();
        event.target.style.cursor = 'default';
        
        if (self.linkConnector != null)
        {
            if (self.linkConnector.IsConnectorCollision(self.mouse.x, self.mouse.y, viewport))
            {
                event.target.style.cursor = 'move';
                isCursorSet = true;
            }
            if (self.linkConnector.MouseMove(self.mouse, viewport, self.nodes))
            {
                event.target.style.cursor = 'move';
                isCursorSet = true;
                isRedraw = true;
            }
        }

        if (self.nodes.length > 0 && !isRedraw && !(self.isSelectActive && self.selectorTool != null && self.selectorTool.isDragged))
        {
            for (var i = self.nodes.length - 1; i >= 0; i--)
            {
                if( ! isCursorSet && 'IsLinkButtonCollision' in self.nodes[i] && self.nodes[i].IsLinkButtonCollision(self.mouse.x, self.mouse.y, viewport))
                {
                    event.target.style.cursor = 'default';
                    isCursorSet = true;
                }
                else if ( ! isCursorSet && 'IsAddButtonCollision' in self.nodes[i] && self.nodes[i].IsAddButtonCollision(self.mouse.x, self.mouse.y, viewport))
                {
                    event.target.style.cursor = 'default';
                    isCursorSet = true;
                }
                else if ( ! isCursorSet && self.nodes[i].IsMainAreaCollision(self.mouse.x, self.mouse.y, viewport))
                {
                    event.target.style.cursor = 'default';
                    isCursorSet = true;
                }
                else if ( ! isCursorSet && self.nodes[i].IsHeaderCollision(self.mouse.x, self.mouse.y, viewport))
                {
                    event.target.style.cursor = 'move';
                    isCursorSet = true;
                } 
                
                if (self.nodes[i].MouseMove(self.mouse, viewport, self.nodes))
                {
                    if ( ! isCursorSet && self.nodes[i].IsHeaderCollision(self.mouse.x, self.mouse.y, viewport))
                    {
                        event.target.style.cursor = 'move';
                        isCursorSet = true;
                    }
                    isRedraw = true;
                }
            }
        }

        if ( ! isCursorSet && !self.isSelectActive)
        {
            event.target.style.cursor = 'move';
            isCursorSet = true;
        }
        else if ( ! isCursorSet)
        {
            event.target.style.cursor = 'crosshair';
            isCursorSet = true;
        }
        
        if (isCursorSet) body.addClass('clearCursor');
        else body.removeClass('clearCursor');
        
        if ( ! isRedraw && self.mouse.isDown && !self.isSelectActive)
        {
            var scale = viewport.GetScale(),
                tx = (self.mouse.x - self.mouse.oldX) / scale[0],
                ty = (self.mouse.y - self.mouse.oldY) / scale[1];
            
            viewport.TranslateWithoutScale(tx, ty);
            isRedraw = true;
        }
        else if ( ! isRedraw && self.mouse.isDown && self.isSelectActive)
        {
            if (self.selectorTool.MouseMove(self.mouse, viewport)) isRedraw = true;
        }

        if(isRedraw) self.Render();
        
        return false;
    };

    var GetNodeById = function(id)
    {
        for (var i = 0; i < self.nodes.length; i++)
        {
            if(self.nodes[i].id == id) return self.nodes[i];
        }
        return null;
    };

    self.GetLinkById = function(id)
    {
        for (var i = 0; i < self.links.length; i++)
        {
            if(self.links[i].id == id) return self.links[i];
        }
        return null;
    };

    self.TranslateViewport = function(x, y) {
        viewport.TranslateWithoutScale(x, y);
        self.Render();
    };

    var GetRootNode = function()
    {
        for (var i = 0; i < self.nodes.length; i++)
        {
            if(self.nodes[i].isRoot) return self.nodes[i];
        }
        return null;
    };

    var decode64 = function(input)
    {
        input = input.replace(/\0/g,"");
        return  B64.decode($.trim(input));
    };
    
    var evalJson = function(jsArray)
    {
        eval("function parseJSON(){ return "+ jsArray +"; }");
        return parseJSON();
    };
}