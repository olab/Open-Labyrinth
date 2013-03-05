var VisualEditor = function() {
    var self = this;
    var viewport = new Transform();
    var canvasOffsetLeft = 0;
    var canvasOffsetTop = 0;
    var generateIdNodeCounter = 1;
    var generateIdLinkCounter = 1;
    var maxZoom = 1.6;
    var minZoom = 0.5;
    
    var keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    
    self.$canvasContainer = null;
    self.$canvas = null;
    self.canvas = null;
    self.context = null;
    self.mouse = new Mouse();
    self.nodes = new Array();
    self.links = new Array();
    self.linkConnector = null;
    self.zoomInFactor = 1.2;
    self.zommOutFactor = 0.8;
    self.colorModal = new ColorModal();
    self.linkModal = new LinkModal();
    self.deleteModal = new DeleteModal();
    self.nodeModal = new NodeModal();
    self.isChanged = false;
    self.isViewportInit = true;

    // Initialize visual editor
    self.Init = function(params) {
        if('canvasContainer' in params) {
            self.$canvasContainer = $(params.canvasContainer);
            if(self.$canvasContainer != null) {
                $(window).resize(function() {
                    Resize();
                });
            }
        }
        
        if('canvasId' in params) {
            self.$canvas = $(params.canvasId);
            
            if(self.$canvas != null) {
                self.canvas = self.$canvas[0];
                canvasOffsetLeft = self.canvas.offsetLeft;
                canvasOffsetTop = self.canvas.offsetTop;
            }
        }
        
        CreateContext();
        CreateEvents();
        
        self.colorModal.Init({
            modalId: '#visual_editor_colorpicker', 
            inputId: '#colorpicker_input', 
            applyBtn: '#colorpickerApply', 
            visualEditor: self, 
            colorPickerContainer: '#colopicker_container'
        });
        self.linkModal.Init({
            modalId: '#visual_editor_link', 
            applyBtn: '#linkApply', 
            linkTypes: '#linkTypes', 
            visualEditor: self
        });
        self.deleteModal.Init({
            modalId: '#visual_editor_delete', 
            applyBtn: '#deleteNode', 
            visualEditor: self
        });
        self.nodeModal.Init({
            modalId: '#visual_editor_node', 
            applyBtn: '#nodeApply', 
            visualEditor: self, 
            title: '#nodetitle', 
            content: '#nodecontent', 
            support: '#nodesupport', 
            supportKeywords: '#nodesupportkeywords',
            isExitNodePorb: '#exitNodeOptions', 
            linkStyle: '#linkStyleOptions', 
            nodePriority: '#nodePriorities',
            undoLinks: '#nodeUndoLinks', 
            endNode: '#nodeEndAndReport', 
            counters: '#counters'
        });
                         
        Resize(null);
    }
    
    // Render current state of visual editor
    self.Render = function() {
        ClearContext();
        
        if(self.nodes.length > 0) {
            for(var i = 0; i < self.nodes.length; i++) {
                self.nodes[i].Draw(self.context, viewport);
            }
        }
        
        if(self.links.length > 0) {
            for(var i = 0; i < self.links.length; i++) {
                self.links[i].Draw(self.context, viewport);
            }
        }
    
        if(self.linkConnector != null)
            self.linkConnector.Draw(self.context, viewport);
        
        self.isChanged = true;
    }
    
    // Zoom in viewport
    self.ZoomIn = function() {
        var result = false;
        var scale = viewport.GetScale();
        
        var testScale = ((scale[0] + scale[1]) * 0.5) * self.zommOutFactor;
        if(testScale <= maxZoom) {
            result = true;
            viewport.Scale(self.zoomInFactor, self.zoomInFactor);
        }
        
        if(testScale * self.zoomInFactor > maxZoom)
            result = false;
        
        return result;
    }
    
    // Zoom out viewport
    self.ZoomOut = function() {
        var result = false;
        var scale = viewport.GetScale();
        var testScale = ((scale[0] + scale[1]) * 0.5) / self.zommOutFactor;
        if(testScale >= minZoom) {
            result = true;
            viewport.Scale(self.zommOutFactor, self.zommOutFactor);
        }
    
        if(testScale / self.zoomInFactor < minZoom)
            result = false;
        
        return result;
    }
    
    // Delete link by Id
    self.DeleteLinkById = function(linkId) {
        if(self.links.length <= 0) return;
        
        var l = new Array();
        for(var i = 0; i < self.links.length; i++) {
            if(self.links[i].id != linkId) {
                l.push(self.links[i]);
            }
        }
        
        self.links = l;
    }
    
    // Delete node by Id with all links for this node
    self.DeleteNodeById = function(nodeId) {
        if(self.nodes.length <= 0) return;
        
        for(var i = 0; i < self.nodes.length; i++) {
            if(self.nodes[i].id == nodeId) {
                DeleteAllLinksByNode(nodeId);
                self.nodes.splice(i, 1);
                break;
            }
        }
    }
    
    // Serialize nodes info
    self.Serialize = function() {
        var result = '';
        
        if(self.nodes.length > 0) {
            var nodes = '';
            for(var i = 0; i < self.nodes.length; i++) {
                var pos = self.nodes[i].transform.GetPosition();

                nodes += '{"id": "' + self.nodes[i].id + '", "isRoot": "' + self.nodes[i].isRoot + '", "isNew": "' + self.nodes[i].isNew + '", "title": "' + encode64(self.nodes[i].title) + '", "content": "' + encode64(self.nodes[i].content) + '", "support": "' + encode64(self.nodes[i].support) + '", "supportKeywords": "' + self.nodes[i].supportKeywords + '", "isExit": "' + self.nodes[i].isExit + '", "linkStyle": "' + self.nodes[i].linkStyle + '", "nodePriority": "' + self.nodes[i].nodePriority + '", "undo": "' + self.nodes[i].undo + '", "isEnd": "' + self.nodes[i].isEnd + '", "x": "' + pos[0] + '", "y": "' + pos[1] + '", "color": "' + self.nodes[i].color + '"';

                if(self.nodes[i].counters.length > 0) {
                    var counters = '';
                    for(var j = 0; j < self.nodes[i].counters.length; j++) {
                        counters += '{"id": "' + self.nodes[i].counters[j].id + '", "func": "' + self.nodes[i].counters[j].func + '", "show": "' + self.nodes[i].counters[j].show + '"}, ';
                    }
                    
                    if(counters.length > 0) {
                        counters = counters.substring(0, counters.length - 2);
                        counters = '"counters": [' + counters + ']';
                        
                        nodes += ', ' + counters + '}, ';
                    } else {
                        nodes += '}, ';
                    }
                } else {
                    nodes += '}, ';
                }
            }
            
            if(nodes.length > 2) {
                nodes = nodes.substring(0, nodes.length - 2);
                nodes = '"nodes": [' + nodes + ']';
                
                result += nodes;
            }
        }
        
        if(self.links.length > 0) {
            var links = '';
            for(var i = 0; i < self.links.length; i++) {
                links += '{"id": "' + self.links[i].id + '", "nodeA": "' + self.links[i].nodeA.id + '", "nodeB": "' + self.links[i].nodeB.id + '", "type": "' + self.links[i].type + '", "isNew": "' + self.links[i].isNew + '"}, ';
            }
            
            if(links.length > 2) {
                links = links.substring(0, links.length - 2);
                links = '"links": [' + links + ']';
                
                if(result.length > 0) {
                    result += ', ' + links;
                }
            }
        }
        
        if(result.length > 0) {
            result = '{' + result + '};';
        }

        return result;
    }
    
    // Deserialize nodes info
    self.Deserialize = function(jsonString) {
        if(jsonString.length <= 0) return;
        
        function evalJson(jsArray){
            eval("function parseJSON(){ return "+ jsArray +"; }");
            return parseJSON();
        }
        
        var object = evalJson(jsonString);
        if(object == null) return;
        
        if('nodes' in object && object.nodes.length > 0) {
            self.nodes = new Array();
            for(var i = 0; i < object.nodes.length; i++) {
                var node = new Node();
                node.id = object.nodes[i].id;
                
                var testId = node.id + "";
                if(testId.indexOf('g') > -1) {
                    var g = parseInt(testId.substr(0, testId.length - 1));
                    if(!isNaN(g))
                        generateIdNodeCounter = g;
                    
                    node.isNew = true;
                }
                
                node.title = decode64(object.nodes[i].title);
                node.content = decode64(object.nodes[i].content);
                node.support = decode64(object.nodes[i].support);
                node.isExit = (object.nodes[i].isExit == 'true');
                node.undo = (object.nodes[i].undo == 'true');
                node.isEnd = (object.nodes[i].isEnd == 'true');
                node.isRoot = (object.nodes[i].isRoot == 'true');
                var x = parseInt(object.nodes[i].x);
                var y = parseInt(object.nodes[i].y);
                
                if(isNaN(x)) x = 0;
                
                if(isNaN(y)) y = 0;
                
                var linkStyle = parseInt(object.nodes[i].linkStyle);
                if(isNaN(linkStyle)) linkStyle = 1;
                node.linkStyle = linkStyle;
                
                var nodePriority = parseInt(object.nodes[i].nodePriority);
                if(isNaN(nodePriority)) nodePriority = 1;
                node.nodePriority = nodePriority;
                
                node.transform.Translate(x, y);
                node.color = (object.nodes[i].color.length > 0) ? object.nodes[i].color : node.color;
                
                if('counters' in object.nodes[i] && object.nodes[i].counters.length > 0) {
                    node.counters.push.apply(node.counters, object.nodes[i].counters);
                }
                
                self.nodes.push(node);
            }
        }
        
        if('links' in object && object.links.length > 0) {
            self.links = new Array();
            for(var i = 0; i < object.links.length; i++) {
                var nodeAId = object.links[i].nodeA;
                var nodeBId = object.links[i].nodeB;
                
                var nodeA = GetNodeById(nodeAId);
                if(nodeA == null) continue;
                
                var nodeB = GetNodeById(nodeBId);
                if(nodeB == null) continue;
                
                var id = object.links[i].id;
                
                var testLinkId = object.links[i].id + "";
                if(testLinkId.indexOf('g') > -1) {
                    var l = parseInt(testLinkId.substr(0, testLinkId.length - 1));
                    if(!isNaN(l))
                        generateIdLinkCounter = l;
                }
                
                var existLink = GetLinkById(id);
                if(existLink != null) continue;
                
                var link = new Link();
                
                link.id = id;
                link.nodeA = nodeA;
                link.nodeB = nodeB;
                link.type = (object.links[i].type.length > 0) ? object.links[i].type : 'direct';
                
                self.links.push(link);
            }
        }

        if(self.isViewportInit) {
            self.isViewportInit = false;
            var rootNode = GetRootNode();
            if(rootNode != null) {
                var pos = rootNode.transform.GetPosition();
                viewport.Translate(-pos[0] + self.canvas.width * 0.5 - rootNode.width * 0.5, -pos[1] + self.canvas.height * 0.5 - rootNode.height * 0.5);
            }
        }
        
        if(generateIdNodeCounter > 1)
            generateIdNodeCounter++;
        
        if(generateIdLinkCounter > 1)
            generateIdLinkCounter++;
    }

    self.AddNewNode = function() {
        var node = new Node();
        node.id = GetNewNodeId();
        node.title = 'new node';
        node.isNew = true;
        
        if(self.$canvas != null) {
            var pos = viewport.GetPosition();
            
            var w = 100 - pos[0] + Math.random() * (70 - 40) + 40;
            var h = 150 - pos[1] + Math.random() * (70 - 40) + 40;
            
            node.transform.Translate(w, h);
        }
        
        self.nodes.push(node);
    }
    
    self.GetWidth = function() {
        if(self.$canvas == null) return 0;
        
        return self.$canvas.width();
    }
    
    var Resize = function() {
        if(self.$canvasContainer != null && self.$canvas != null) {
            self.$canvas.attr('width', self.$canvasContainer.width());
            self.Render();
        }
    }
    
    var DeleteAllLinksByNode = function(nodeId) {
        if(self.links.length <= 0) return;
        
        var ids = new Array();
        for(var i = 0; i < self.links.length; i++) {
            if(self.links[i].nodeA.id == nodeId || self.links[i].nodeB.id == nodeId) {
                ids.push(self.links[i].id);
            }
        }
        
        if(ids.length > 0) {
            for(var i = 0; i < ids.length; i++) {
                self.DeleteLinkById(ids[i]);
            }
        }
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
    
    var CreateEvents = function() {
        if(self.canvas == null) return;
        
        self.canvas.addEventListener("mousedown", MouseDown, false);
        self.canvas.addEventListener("mouseup", MouseUp, false);
        self.canvas.addEventListener("mousemove", MouseMove, false);
        self.canvas.addEventListener("touchstart", MouseDown, false);
        self.canvas.addEventListener("touchmove", MouseMove, false);
        self.canvas.addEventListener("touchend", MouseUp, false);
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
            self.mouse.x = event.pageX - canvasOffsetLeft;
            self.mouse.y = event.pageY - canvasOffsetTop;
        }
        
        if(isNaN(self.mouse.x))
            self.mouse.x = 0;
        
        if(isNaN(self.mouse.y))
            self.mouse.y = 0;
    }
    
    // Events
    var MouseDown = function(event) {
        //event.preventDefault();
        self.mouse.isDown = true;
        UpdateMousePosition(event);
        
        var isRedraw = false;
        if(self.links.length > 0) {
            for(var i = 0; i < self.links.length; i++) {
                if(self.links[i].MouseClick(self.mouse, viewport)) {
                    isRedraw = true;
                    ShowLinkManagetDialog(self.links[i].id);
                    self.mouse.isDown = false;
                }
            }
        }
        
        if(self.nodes.length > 0) {
            for(var i = self.nodes.length - 1; i >= 0; i--) {
                var result = self.nodes[i].MouseClick(self.mouse, viewport);
                if(result.length > 0 && !isRedraw) {
                    isRedraw = true;
                    if(result[1] == 'header') {
                    } else if(result[1] == 'add') {
                        AddNodeWithLink(result[0]);
                    } else if(result[1] == 'root') {
                        SetRootNode(result[0]);
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
                        ShowNodeDialog(result[0]);
                        self.mouse.isDown = false;
                    }
                }
            }
        }

        if(isRedraw)
            self.Render();
        
        return false;
    }
    
    var MouseUp = function(event) {
        //event.preventDefault();
        self.mouse.isDown = false;
        UpdateMousePosition(event);
        
        var isRedraw = false;
        if(self.linkConnector != null) {
            var r = self.linkConnector.MouseUp(self.mouse, viewport, self.nodes);
            
            if(r.length > 0) {
                var link = IsExistLink(r[1], r[2]);
                if(r[1] != r[2] && link == null) {
                    var nodeA = GetNodeById(r[1]);
                    var nodeB = GetNodeById(r[2]);
                    
                    var newLink = new Link();
                    
                    newLink.nodeA = nodeA;
                    newLink.nodeB = nodeB;
                    newLink.type = 'direct';
                    newLink.id = GetNewLinkId();
                    
                    self.links.push(newLink);
                    
                    self.linkConnector.node.isLinkButtonEnabled = false;
                    self.linkConnector = null;
                } else if(r[1] != r[2] && link != null) {
                    link.type = 'dual';
                    self.linkConnector.node.isLinkButtonEnabled = false;
                    self.linkConnector = null;
                } else {
                    self.linkConnector.node.isLinkButtonEnabled = false;
                    self.linkConnector = null;
                }
                isRedraw = true;
            }/* else if(self.linkConnector.isMoved) {
                self.linkConnector = null;
                isRedraw = true;
            }*/
        }
        
        if(isRedraw)
            self.Render();

        return false;
    }
    
    var MouseMove = function(event) {
        if(self.mouse.isDown)
            event.preventDefault();
        
        UpdateMousePosition(event);

        var isRedraw = false;
        
        if(self.linkConnector != null) {
            if(self.linkConnector.MouseMove(self.mouse, viewport, self.nodes)) {
                isRedraw = true;
            }
        }
        
        if(self.nodes.length > 0 && !isRedraw) {
            for(var i = self.nodes.length - 1; i >= 0; i--) {
                if(self.nodes[i].MouseMove(self.mouse, viewport, self.nodes))
                    isRedraw = true;
            }
        }
        
        if(self.links.length > 0 && !isRedraw) {
            for(var i = 0; i < self.links.length; i++) {
                if(self.links[i].MouseMove(self.mouse, viewport))
                    isRedraw = true;
            }
        }

        if(!isRedraw && self.mouse.isDown) {
            var scale = viewport.GetScale();

            var tx = (self.mouse.x - self.mouse.oldX) / scale[0];
            var ty = (self.mouse.y - self.mouse.oldY) / scale[1];
            
            viewport.TranslateWithoutScale(tx, ty);
            isRedraw = true;
        }

        if(isRedraw)
            self.Render();
        
        return false;
    }
    
    var AddNodeWithLink = function(nodeId) {
        var node = GetNodeById(nodeId);

        if(node == null) return;
        
        var newNode = new Node();
        
        newNode.id = GetNewNodeId();
        newNode.isNew = true;
        newNode.title = 'new node';
        
        newNode.transform.Multiply(node.transform);
        newNode.transform.Translate(newNode.width * 0.5 + Math.random() * (70 - 40) + 40, newNode.height + 20 + Math.random() * (70 - 40) + 40);
        
        var newLink = new Link();
        
        newLink.nodeA = node;
        newLink.nodeB = newNode;
        newLink.isNew = true;
        newLink.id = GetNewLinkId();
        newLink.type = 'direct';
        
        self.nodes.push(newNode);
        self.links.push(newLink);
    }
    
    var SetRootNode = function(nodeId) {
        if(self.nodes.length <= 0) return;
        
        for(var i = 0; i < self.nodes.length; i++) {
            self.nodes[i].isRoot = (self.nodes[i].id == nodeId) ? true: false;
        }
    }
    
    var ShowLinkConnector = function(nodeId) {
        var node = GetNodeById(nodeId);
        if(node == null) return;
        
        if(self.linkConnector == null)
            self.linkConnector = new LinkConnector();
        
        self.linkConnector.isMoved = false;
        self.linkConnector.node = node;
        
        self.linkConnector.transform.SetIdentity();
        self.linkConnector.transform.Multiply(node.transform);
        self.linkConnector.transform.Translate(node.width * 0.5, -60);
    }
    
    var ShowColorpickerDialog = function(nodeId) {
        var node = GetNodeById(nodeId);
        if(self.colorModal != null && node != null) {
            self.colorModal.SetNode(node);
            self.colorModal.Show();
        }
    }
    
    var ShowLinkManagetDialog = function(linkId) {
        var link = GetLinkById(linkId);
        
        if(self.linkModal != null && link != null) {
            self.linkModal.SetLink(link);
            self.linkModal.Show();
        }
    }
    
    var ShowDeleteDialog = function(nodeId) {
        var node = GetNodeById(nodeId);
        
        if(node != null && self.deleteModal != null) {
            self.deleteModal.node = node;
            self.deleteModal.Show();
        }
    }
    
    var ShowNodeDialog = function(nodeId) {
        var node = GetNodeById(nodeId);
        
        if(node != null && self.nodeModal != null) {
            self.nodeModal.SetNode(node);
            self.nodeModal.Show();
        }
    }
    
    var GetRootNode = function() {
        if(self.nodes.length <= 0) return null;

        for(var i = 0; i < self.nodes.length; i++) {
            if(self.nodes[i].isRoot)
                return self.nodes[i];
        }

        return null;
    }

    var GetNodeById = function(id) {
        if(self.nodes.length <= 0) return null;

        for(var i = 0; i < self.nodes.length; i++) {
            if(self.nodes[i].id == id) {
                return self.nodes[i];
            }
        }
    
        return null;
    }
    
    var GetLinkById = function(id) {
        if(self.links.length <= 0) return null;
        
        for(var i = 0; i < self.links.length; i++) {
            if(self.links[i].id == id)
                return self.links[i];
        }
    
        return null;
    }
    
    var GetNewNodeId = function() {
        var id = generateIdNodeCounter + 'g';
        generateIdNodeCounter++;

        return id;
    }
    
    var GetNewLinkId = function() {
        var id = generateIdLinkCounter + 'g';
        generateIdLinkCounter++;
        
        return id;
    }
    
    var IsExistLink = function(nodeA, nodeB) {
        if(self.links.length <= 0) return null;
        
        for(var i = 0; i < self.links.length; i++) {
            if(self.links[i].nodeA != null && self.links[i].nodeB != null && 
                ((self.links[i].nodeA.id == nodeA && self.links[i].nodeB.id == nodeB) ||
                    (self.links[i].nodeB.id == nodeA && self.links[i].nodeA.id == nodeB))) {
                return self.links[i];
            }
        }
        
        return null;
    }
    
    var encode64 = function (input) {
        input = escape(input);
        var output = "";
        var chr1, chr2, chr3 = "";
        var enc1, enc2, enc3, enc4 = "";
        var i = 0;

        do {
            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);

            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;

            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }

            output = output +
            keyStr.charAt(enc1) +
            keyStr.charAt(enc2) +
            keyStr.charAt(enc3) +
            keyStr.charAt(enc4);
            chr1 = chr2 = chr3 = "";
            enc1 = enc2 = enc3 = enc4 = "";
        } while (i < input.length);

        return output;
    }

    var decode64 = function(input) {
        var output = "";
        var chr1, chr2, chr3 = "";
        var enc1, enc2, enc3, enc4 = "";
        var i = 0;

        // remove all characters that are not A-Z, a-z, 0-9, +, /, or =
        var base64test = /[^A-Za-z0-9\+\/\=]/g;
        if (base64test.exec(input)) {
            alert("There were invalid base64 characters in the input text.\n" +
                "Valid base64 characters are A-Z, a-z, 0-9, '+', '/',and '='\n" +
                "Expect errors in decoding.");
        }
        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

        do {
            enc1 = keyStr.indexOf(input.charAt(i++));
            enc2 = keyStr.indexOf(input.charAt(i++));
            enc3 = keyStr.indexOf(input.charAt(i++));
            enc4 = keyStr.indexOf(input.charAt(i++));

            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;

            output = output + String.fromCharCode(chr1);

            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }

            chr1 = chr2 = chr3 = "";
            enc1 = enc2 = enc3 = enc4 = "";

        } while (i < input.length);

        return unescape(output);
    }
}