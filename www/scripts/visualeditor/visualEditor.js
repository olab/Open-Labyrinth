var VisualEditor = function() {
    var self = this;
    var viewport = new Transform();
    var canvasOffsetLeft = 0;
    var canvasOffsetTop = 0;
    var generateIdNodeCounter = 1;
    var generateIdLinkCounter = 1;
    var maxZoom = 1.6;
    var minZoom = 0.1;
    var ctrlKeyPressed = false;
    var altKeyPressed = false;
    var def2PI = Math.PI * 2;
    var sectionNodeId = 0;
    
    self.$canvasContainer = null;
    self.$canvas = null;
    self.canvas = null;
    self.context = null;
    self.mouse = new Mouse();
    self.lastMouse = new Mouse();
    self.nodes = new Array();
    self.links = new Array();
    self.sections = new Array();
    self.linkConnector = null;
    self.zoomInFactor = 1.2;
    self.zommOutFactor = 0.8;
    self.colorModal = new ColorModal();
    self.linkModal = new LinkModal();
    self.deleteModal = new DeleteModal();
    self.nodeModal = new NodeModal();
    self.isChanged = false;
    self.isViewportInit = true;
    self.selectorTool = new Selector();
    self.isSelectActive = false;
    self.copyFunction = null;
    self.pasteFunction = null;
    self.zoomIn = null;
    self.zoomOut = null;
    self.update = null;
    self.turnOnPanMode = null;
    self.turnOnSelectMode = null;
    self.rightPanel = new RightPanel();
    self.unsavedData = false;
    self.save = null;
    
    self.$aButtonsContianer = $('#ve_additionalActionButton');
    
    self.selectRightPanel = null;

    self.preview = null;
    self.mode = 'node';

    self.$sectionSelect = null;

    // Initialize visual editor
    self.Init = function(params) {
        if('canvasContainer' in params) {
            self.$canvasContainer = $(params.canvasContainer);
            if(self.$canvasContainer != null) {
                $(window).resize(function() {
                    self.Resize();
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

        if('sectionSelectId' in params) {
            self.$sectionSelect = $(params.sectionSelectId);
        }
        
        if('aButtonsContianer' in params) {
            self.$aButtonsContianer = $(params.aButtonsContianer);
            
            $('#deleteSNodesBtn').click(function() {
                if(self.nodes != null && self.nodes.length > 0) {
                    var nodeId = 0;
                    for(var i = 0; i < self.nodes.length; i++) {
                        if(self.nodes[i].isSelected) {
                            nodeId = self.nodes[i].id;
                            break;
                        }
                    }

                    if(self.rightPanel != null) {
                        var node = GetNodeById(nodeId);
                        if(node != null) {
                            self.rightPanel.node = node;
                            self.rightPanel.DeleteNode();
                        }
                    }
                }
            });
            
            $('#colorSNodesBtn').click(function() {
                if(self.selectRightPanel != null)
                    self.selectRightPanel.Show();
            });
        }
        
        CreateContext();
        CreateEvents();
        
        self.rightPanel.Init({
            panelId: '#veRightPanel',
            closeBtn: '.veRightPanelCloseBtn',
            colorInputId: '#colorpickerInput',
            colorPickerId: '#colopickerContainer',
            onlySaveBtn: '#veRightPanelOnlySaveBtn',
            saveBtn: '#veRightPanelSaveBtn',
            accordion: '#veAccordionRightPanel',
            nodeRootBtn: '#veNodeRootBtn',
            nodeDeleteBtn: '#veDeleteNodeBtn',
            visualEditor: self,
            nodeIDLabel: '#nodeID_label',
            nodeIDContainer: '#nodeID_container',
            nodeTitle: '#nodetitle',
            nodeContent: '#nodecontent',
            nodeSupport: '#nodesupport',
            nodeSupportKeywords: '#nodesupportkeywords',
            nodeIsExitNodePorb: '#exitNodeOptions',
            nodeLinkStyle: '#linkStyleOptions',
            nodePriority: '#nodePriorities',
            nodeUndoLinks: '#nodeUndoLinks',
            endNode: '#nodeEndAndReport',
            nodeCounters: '#counters',
            unsavedDataForm: '#veRightPanel_unsaveddata',
            unsavedDataBtnClose: '#veRightPanel_unsaveddata_close',
            unsavedDataChange: '#veRightPanel_unsaveddataChange',
            unsavedDataBtnChangeClose: '#veRightPanel_unsaveddataChange_close'
        });
        
        self.deleteModal.Init({
            modalId: '#visual_editor_delete', 
            applyBtn: '#deleteNode', 
            visualEditor: self
        });
        
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
            linkImages: '#mimage',
            linkLabel: '#labelText',
            visualEditor: self
        });

        if(self.mode == 'node') {
            self.preview = new Preview();
            self.preview.Init({
                canvasId: '#canvasPreview',
                visualEditor: self
            });
            
            self.selectRightPanel = new SelectRightPanel();
            self.selectRightPanel.Init({
                panelID: '#veSelectRightPanel',
                colorContainer: '#veSelectColorContainer',
                inputID: '#veSelectColorInput',
                saveBtnID: '#veSelectRightPanelOnlySaveBtn',
                saveCloseBtnID: '#veSelectRightPanelSaveBtn',
                closeBtnID: '#veSelectRightPanelCloseBtn',
                visualEditor: self
            });
            
            self.history = new History();
            self.history.Init({
                visualEditor: self,
                undo: '#undo',
                redo: '#redo'
            });
            
            $('#undo').click(function() {
                $('#redo').removeClass('disabled');
                if(self.history != null) {
                    if(!self.history.Undo()) {
                        $(this).addClass('disabled');
                    }
                }
            });
            
            $('#redo').click(function() {
                $('#undo').removeClass('disabled');
                if(self.history != null) {
                    if(!self.history.Redo()) {
                        $(this).addClass('disabled');
                    }
                }
            });
        }

        self.Resize(null);
        self.ZoomOut();
        self.ZoomOut();
    }
    
    // Render current state of visual editor
    self.Render = function() {
        ClearContext();

        if(self.links.length > 0) {
            for(var i = 0; i < self.links.length; i++) {
                self.links[i].Draw(self.context, viewport);
            }
        }
        
        if(self.nodes.length > 0) {
            for(var i = 0; i < self.nodes.length; i++) {
                self.nodes[i].Draw(self.context, viewport);
            }
        }

        if(self.linkConnector != null)
            self.linkConnector.Draw(self.context, viewport);
        
        self.selectorTool.Draw(self.context, viewport);
        
        self.isChanged = true;

        if(self.preview != null) {
            self.preview.Render(self.nodes, self.links, viewport, self.canvas.width, self.canvas.height);
        }
        
        if(self.$aButtonsContianer != null) {
            if(self.IsExistSelectElements()) {
                self.$aButtonsContianer.show();
            } else {
                self.$aButtonsContianer.hide();
            }
        }
                    
    }
    
    self.IsExistSelectElements = function() {
        if(self.nodes == null || self.nodes.length <= 0) return false;
        
        for(var i = 0; i < self.nodes.length; i++)
            if(self.nodes[i].isSelected) return true;
        
        if(self.links == null || self.links.length <= 0) return false;
        
        for(var i = 0; i < self.links.length; i++)
            if(self.links[i].isSelected) return true;
        
        return false;
    }
    
    // Zoom in viewport
    self.ZoomIn = function() {
        var result    = false,
            scale     = viewport.GetScale(),
            testScale = ((scale[0] + scale[1]) * 0.5) * self.zommOutFactor,
            oldSize = [self.canvas.width / scale[0], self.canvas.height / scale[1]],
            newSize = [0, 0],
            newScale = [1, 1];

        if(testScale <= maxZoom) {
            result = true;

            viewport.Scale(self.zoomInFactor, self.zoomInFactor);
            newScale = viewport.GetScale();

            newSize = [self.canvas.width / newScale[0], self.canvas.height / newScale[1]];

            viewport.TranslateWithoutScale(-(oldSize[0] - newSize[0]) * 0.5, -(oldSize[1] - newSize[1]) * 0.5);
        }
        
        if(testScale * self.zoomInFactor > maxZoom)
            result = false;
        
        return result;
    }
    
    // Zoom out viewport
    self.ZoomOut = function() {
        var result = false,
            scale = viewport.GetScale(),
            testScale = ((scale[0] + scale[1]) * 0.5) / self.zommOutFactor,
            oldSize = [self.canvas.width / scale[0], self.canvas.height / scale[1]],
            newSize = [0, 0],
            newScale = [1, 1];

        if(testScale >= minZoom) {
            result = true;

            viewport.Scale(self.zommOutFactor, self.zommOutFactor);
            newScale = viewport.GetScale();

            newSize = [self.canvas.width / newScale[0], self.canvas.height / newScale[1]];
            viewport.TranslateWithoutScale((newSize[0] - oldSize[0]) * 0.5, (newSize[1] - oldSize[1]) * 0.5);
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

        self.DeleteNodeFromSections(nodeId);
    }

    self.DeleteNodeFromSections = function(nodeId) {
        if(self.sections.length <= 0) return;

        for(var i = self.sections.length; i--;) {
            self.sections[i].deleteNode(nodeId);
        }
    }

    // Serialize nodes info
    self.Serialize = function() {
        return SerializeElements(self.nodes, self.links);
    }
    
    self.SerializeSelected = function() {
        var selectedNodes = new Array();
        var selectedLinks = new Array();
        
        if(self.nodes.length > 0) {
            for(var i = 0; i < self.nodes.length; i++) {
                if(self.nodes[i].isSelected)
                    selectedNodes.push(self.nodes[i]);
            }
        }
        
        if(self.links.length > 0) {
            for(var i = 0; i < self.links.length; i++) {
                if(self.links[i].isSelected)
                    selectedLinks.push(self.links[i]);
            }
        }
        
        return SerializeElements(selectedNodes, selectedLinks);
    }
    
    var SerializeElements = function(nodes, links) {
        var result = '';
        
        if(nodes.length > 0) {
            var nodesStr = '';
            for(var i = 0; i < nodes.length; i++) {
                var pos = nodes[i].transform.GetPosition();

                nodesStr += '{"id": "' + nodes[i].id + '", "isRoot": "' + nodes[i].isRoot + '", "isNew": "' + nodes[i].isNew + '", "title": "' + encode64(nodes[i].title) + '", "content": "' + encode64(nodes[i].content) + '", "support": "' + encode64(nodes[i].support) + '", "supportKeywords": "' + nodes[i].supportKeywords + '", "isExit": "' + nodes[i].isExit + '", "linkStyle": "' + nodes[i].linkStyle + '", "nodePriority": "' + nodes[i].nodePriority + '", "undo": "' + nodes[i].undo + '", "isEnd": "' + nodes[i].isEnd + '", "x": "' + pos[0] + '", "y": "' + pos[1] + '", "color": "' + nodes[i].color + '"';

                if(nodes[i].counters.length > 0) {
                    var counters = '';
                    for(var j = 0; j < nodes[i].counters.length; j++) {
                        counters += '{"id": "' + nodes[i].counters[j].id + '", "func": "' + nodes[i].counters[j].func + '", "show": "' + nodes[i].counters[j].show + '"}, ';
                    }
                    
                    if(counters.length > 0) {
                        counters = counters.substring(0, counters.length - 2);
                        counters = '"counters": [' + counters + ']';
                        
                        nodesStr += ', ' + counters + '}, ';
                    } else {
                        nodesStr += '}, ';
                    }
                } else {
                    nodesStr += '}, ';
                }
            }
            
            if(nodesStr.length > 2) {
                nodesStr = nodesStr.substring(0, nodesStr.length - 2);
                nodesStr = '"nodes": [' + nodesStr + ']';
                
                result += nodesStr;
            }
        }
        
        if(links.length > 0) {
            var linksStr = '';
            for(var i = 0; i < links.length; i++) {
                linksStr += '{"id": "' + links[i].id + '", "nodeA": "' + links[i].nodeA.id + '", "nodeB": "' + links[i].nodeB.id + '", "type": "' + links[i].type + '", "isNew": "' + links[i].isNew + '", "label": "' + encode64(links[i].label) + '", "imageId": "' + links[i].imageId + '"}, ';
            }
            
            if(linksStr.length > 2) {
                linksStr = linksStr.substring(0, linksStr.length - 2);
                linksStr = '"links": [' + linksStr + ']';
                
                if(result.length > 0) {
                    result += ', ' + linksStr;
                }
            }
        }

        if(self.sections.length > 0) {
            var sectionStr   = '',
                sectionNodes = '';
            for(var i = self.sections.length; i--;) {
                sectionNodes = '';
                for(var j = self.sections[i].nodes.length; j--;) {
                    sectionNodes += '{"nodeId": "' + self.sections[i].nodes[j].node.id + '", "order": "' + self.sections[i].nodes[j].order + '"}, ';
                }
                if(sectionNodes.length > 0) {
                    sectionNodes = sectionNodes.substring(0, sectionNodes.length - 2);
                    sectionStr += '{"id": "' + self.sections[i].id + '", "name": "' + self.sections[i].name + '", "nodes": [' + sectionNodes + ']}, ';
                }
            }

            if(sectionStr.length > 2) {
                sectionStr = sectionStr.substring(0, sectionStr.length - 2);
                sectionStr = '"sections": [' + sectionStr + ']';

                if(result.length > 0) {
                    result += ', ' + sectionStr;
                }
            }
        }
        
        if(result.length > 0) {
            var pos = viewport.GetPosition();
            var scale = viewport.GetScale();
            result = '{' + result + ', "viewport": ["' + pos[0] + '", "' + pos[1] + '", "' + scale[0] + '", "' + scale[1] + '"]' + '};';
        }

        return result;
    }
    
    // Deserialize nodes info
    self.Deserialize = function(jsonString) {
        if(jsonString.length <= 0) return;
        
        var object = evalJson(jsonString);
        if(object == null) return;
        
        self.nodes = [];
        var copyNodes = null;
        if('nodes' in object && object.nodes.length > 0) {
            copyNodes = new Array();
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
                
                copyNodes.push(node);
                self.nodes.push(node);
            }
        }
        
        self.links =[];
        if('links' in object && object.links.length > 0) {
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

        if('sections' in object && object.sections.length > 0) {
            for(var i = object.sections.length; i--;) {
                var color   = self.GetRandomColor(),
                    section = new Section(object.sections[i].id, object.sections[i].name, color);
                if('nodes' in object.sections[i] && object.sections[i].nodes.length > 0) {
                    for(var j = object.sections[i].nodes.length; j--;) {
                        var n = GetNodeById(object.sections[i].nodes[j].nodeId);
                        if(n != null) {
                            var sectionNode = new SectionNode(n, object.sections[i].nodes[j].order);
                            section.nodes.push(sectionNode);
                            n.sections.push(section);
                        }
                    }
                }

                self.sections.push(section);
            }
        }
        
        if('nodeMap' in object && object.nodeMap.length > 0 && self.history != null) {
            self.history.Remap(object.nodeMap);
        }
        
        ScatterNodes(copyNodes);

        if(self.isViewportInit) {
            self.isViewportInit = false;
            var rootNode = GetRootNode();
            if(rootNode != null) {
                var pos = rootNode.transform.GetPosition();
                var scale = viewport.GetScale();
                
                viewport.SetPosition(-pos[0] + self.canvas.width / scale[0] * 0.5 - rootNode.width / scale[0] * 0.5, -pos[1] + self.canvas.height / scale[1] * 0.5 - rootNode.height / scale[1] * 0.5);
            }
        }
        
        if(generateIdNodeCounter > 1)
            generateIdNodeCounter++;
        
        if(generateIdLinkCounter > 1)
            generateIdLinkCounter++;
    }

    self.GetRandomColor = function() {
        var letters = '0123456789ABCDEF'.split(''),
            color = 'rgba(';

        function getRandomInt(min, max) {
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }

        for (var i = 0; i < 3; i++ ) {
            color += getRandomInt(10, 150) + ', ';
            console.log(color);
        }

        color += '1.0)';

        return color;
    };
    
    var ScatterNodes = function(nodes) {
        if(nodes == null || nodes.length <= 0) return;

        var i,
            j,
            max,
            max2,
            pairList,
            posA,
            posB,
            x,
            y,
            rnd,
            rnd2;

        max = nodes.length;

        for(i = 0; i < max; i += 1) {
            for(j = 0, max2 = max - 1; j < max2; j += 1) {
                posA = nodes[j].transform.GetPosition();
                posB = nodes[j+1].transform.GetPosition();

                if(posA[0] > posB[0]) {
                    var t = nodes[j];
                    nodes[j] = nodes[j+1];
                    nodes[j+1] = t;
                }
            }
        }

        pairList = new Array();
        for(i = 0; i < max - 1; i += 1) {
            posA = nodes[i].transform.GetPosition();
            for(j = i + 1; j < max; j += 1) {
                posB = nodes[j].transform.GetPosition();
                if(posB[0] >= posA[0] && posB[0] <= (posA[0] + 230)) {
                    if(posB[1] >= (posA[1] - 125) && posB[1] <= (posA[1] + 125))
                        pairList.push({nodeA: nodes[i], nodeB: nodes[j]});
                } else {
                    break;
                }
            }
        }

        if(pairList.length > 0) {
            max2 = pairList.length;

            for(i = 0; i < max2; i += 1) {
                rnd = GetRandomArbitary(0, 1);
                rnd2 = GetRandomArbitary(0, 1);
                x = (rnd > 0.5) ? 230 : -250;
                y = (rnd2 > 0.5) ? 125 : -150;

                pairList[i].nodeB.transform.Translate(x, y);
            }

            ScatterNodes(nodes);
        }
    }

    var GetRandomArbitary = function(min, max){
        return Math.random() * (max - min) + min;
    }

    self.DeserializeFromPaste = function(jsonString) {
        if(jsonString.length <= 0) return;
        
        var object = evalJson(jsonString);
        if(object == null) return;
        
        var pos = viewport.GetPosition();
        var scale = viewport.GetScale();
        var nodesMap = new Array();
        var rootNode = GetRootNode();
        var rndX = (Math.random() * (150 - 100) + 100);
        var rndY = (Math.random() * (150 - 50) + 50);
        
        var oldViewportPos = new Array();
        var oldViewportScale = new Array();
        if('viewport' in object && object.viewport.length > 0) {
            oldViewportPos[0] = parseFloat(object.viewport[0]);
            oldViewportPos[1] = parseFloat(object.viewport[1]);
            oldViewportScale[0] = parseFloat(object.viewport[2]);
            oldViewportScale[1] = parseFloat(object.viewport[3]);
            
            if(isNaN(oldViewportPos[0])) oldViewportPos[0] = 0;
            if(isNaN(oldViewportPos[1])) oldViewportPos[1] = 0;
            if(isNaN(oldViewportScale[0])) oldViewportScale[0] = 1;
            if(isNaN(oldViewportScale[1])) oldViewportScale[1] = 1;
            
            oldViewportPos[0] = self.mouse.x / scale[0];
            oldViewportPos[1] = self.mouse.y / scale[1];
        }
        
        if('nodes' in object && object.nodes.length > 0) {
            DeselectAllNodes();
            
            var tNodes = new Array();
            var minPos = null;
            for(var i = 0; i < object.nodes.length; i++) {
                var node = new Node();
                node.id = GetNewNodeId();
                nodesMap.push({oldId: object.nodes[i].id , newId: node.id});
                node.isNew = true;
                
                node.title = decode64(object.nodes[i].title);
                node.content = decode64(object.nodes[i].content);
                node.support = decode64(object.nodes[i].support);
                node.isExit = (object.nodes[i].isExit == 'true');
                node.undo = (object.nodes[i].undo == 'true');
                node.isEnd = (object.nodes[i].isEnd == 'true');
                node.isRoot = (rootNode == null) ? (object.nodes[i].isRoot == 'true') : false;
                var x = parseInt(object.nodes[i].x);
                var y = parseInt(object.nodes[i].y);

                if(isNaN(x)) x = 0;
                if(isNaN(y)) y = 0;
                
                var p = [oldViewportPos[0] - pos[0], oldViewportPos[1] - pos[1]];
                var tr = new Transform();
                tr.Translate(p[0], p[1]);
                tr.Translate(x, y);
                tr.Scale(1 / oldViewportScale[0], 1 / oldViewportScale[1]);
                tr.Scale(scale[0], scale[1]);
                
                var g = tr.GetPosition();
                
                var tx = g[0] + rndX;
                var ty = g[1] + rndY;
                
                node.transform.Translate(tx, ty);
                
                if(minPos == null) {
                    minPos = [tx, ty];
                } else {
                    if(minPos[0] > tx)
                        minPos[0] = tx;
                    
                    if(minPos[1] > ty)
                        minPos[1] = ty;
                }
                
                var linkStyle = parseInt(object.nodes[i].linkStyle);
                if(isNaN(linkStyle)) linkStyle = 1;
                node.linkStyle = linkStyle;
                
                var nodePriority = parseInt(object.nodes[i].nodePriority);
                if(isNaN(nodePriority)) nodePriority = 1;
                node.nodePriority = nodePriority;
                
                node.color = (object.nodes[i].color.length > 0) ? object.nodes[i].color : node.color;
                node.isSelected = true;
                
                tNodes.push(node);
            }
            
            if(tNodes.length > 0 && minPos != null) {
                var pasteTr = new Transform();
                pasteTr.Translate(-pos[0] + self.lastMouse.x / scale[0], -pos[1] + self.lastMouse.y / scale[1]);

                var pastePos = pasteTr.GetPosition();
                var dx = pastePos[0] - minPos[0];
                var dy = pastePos[1] - minPos[1];

                for(var i = 0; i < tNodes.length; i++) {
                    var p = tNodes[i].transform.GetPosition();

                    p = [p[0] + dx, p[1] + dy];
                    
                    tNodes[i].transform = new Transform();
                    tNodes[i].transform.Translate(p[0], p[1]);
                    
                    self.nodes.push(tNodes[i]);
                }
            }
        }
        
        if('links' in object && object.links.length > 0) {
            DeselectAllLinks();
            for(var i = 0; i < object.links.length; i++) {
                var nodeAId = GetNodeFromMap(nodesMap, object.links[i].nodeA);
                var nodeBId = GetNodeFromMap(nodesMap, object.links[i].nodeB);
                
                var nodeA = GetNodeById(nodeAId);
                if(nodeA == null) continue;
                
                var nodeB = GetNodeById(nodeBId);
                if(nodeB == null) continue;
                
                var id = GetNewLinkId();
                
                var link = new Link();
                
                link.id = id;
                link.nodeA = nodeA;
                link.nodeB = nodeB;
                link.label = decode64(object.links[i].label);
                link.type = (object.links[i].type.length > 0) ? object.links[i].type : 'direct';
                link.isSelected = true;
                
                self.links.push(link);
            }
        }
    }
    
    var DeselectAllNodes = function() {
        if(self.nodes == null || self.nodes.length <= 0) return;
        
        for(var i = 0; i < self.nodes.length; i++) {
            self.nodes[i].isSelected = false;
        }
    }
    
    var DeselectAllLinks = function() {
        if(self.links == null || self.links.length <= 0) return;
        
        for(var i = 0; i < self.links.length; i++) {
            self.links[i].isSelected = false;
        }
    }
    
    self.ChangeSelectNodesColor = function(newColor) {
        if(self.nodes == null || self.nodes.length <= 0) return;
        
        for(var i = 0; i < self.nodes.length; i++) {
            if(self.nodes[i].isSelected)
                self.nodes[i].color = newColor;
        }
    }
     
    self.DeserializeLinear = function(jsonString) {
        self.Deserialize(jsonString);
        
        if(self.nodes != null && self.nodes.length > 0) {
            for(var i = 0; i < self.nodes.length; i++) {
                for(var j = 0; j < (self.nodes.length - 1); j++) {
                    if(self.nodes[j].id > self.nodes[j+1].id) {
                        var tmp = self.nodes[j+1];
                        self.nodes[j+1] = self.nodes[j];
                        self.nodes[j] = tmp;
                    }
                }
            }
            
            BuildLinear(self.nodes);
        }
    }
    
    self.DeserializeBranched = function(jsonString) {
        self.Deserialize(jsonString);
        
        if(self.nodes != null && self.nodes.length > 0) {
            for(var i = 0; i < self.nodes.length; i++) {
                for(var j = 0; j < (self.nodes.length - 1); j++) {
                    if(self.nodes[j].id > self.nodes[j+1].id) {
                        var tmp = self.nodes[j+1];
                        self.nodes[j+1] = self.nodes[j];
                        self.nodes[j] = tmp;
                    }
                }
            }
            
            BuildBranched(self.nodes);
        }
    }
    
    self.DeserializeDandelion = function(jsonString) {
        self.Deserialize(jsonString);
        
        if(self.nodes != null && self.nodes.length > 0) {
            for(var i = 0; i < self.nodes.length; i++) {
                for(var j = 0; j < (self.nodes.length - 1); j++) {
                    if(self.nodes[j].id > self.nodes[j+1].id) {
                        var tmp = self.nodes[j+1];
                        self.nodes[j+1] = self.nodes[j];
                        self.nodes[j] = tmp;
                    }
                }
            }
            
            BuildDandelion(self.nodes);
        }
    }
    
    var BuildLinear = function(nodes) {
        if(nodes == null || nodes.length <= 0) return;
        
        var pos = viewport.GetPosition();
        var scale = viewport.GetScale();
        
        var w = self.canvas.width * 0.5 / scale[0] - pos[0] - nodes[0].width * 0.5;
        var h = 150 - pos[1] + Math.random() * (70 - 40) + 40;
        
        nodes[0].transform.SetIdentity();
        nodes[0].transform.Translate(w, h);
        for(var i = 1; i < nodes.length; i++) {
            nodes[i].transform.SetIdentity();
            nodes[i].transform.Translate(w, h + i * 2 * nodes[i].height);
        }
    }
    
    var BuildBranched = function(nodes) {
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
        
        for(var i = 1; i < nodes.length - 1; i++) {
            nodes[i].transform.SetIdentity();
            nodes[i].transform.Translate(w, h + 2 * nodes[i].height);
            w += 2 * nodes[i].width;
        }
        
        nodes[nodes.length - 1].transform.SetIdentity();
        nodes[nodes.length - 1].transform.Translate(oldW, h + 4 * nodes[nodes.length - 1].height);
    }
    
    var BuildDandelion = function(nodes) {
        if(nodes == null || nodes.length <= 0) return;
        
        var pos = viewport.GetPosition();
        var scale = viewport.GetScale();
        
        var w = self.canvas.width * 0.5 / scale[0] - pos[0] - nodes[0].width * 0.5;
        var h = 150 - pos[1] + Math.random() * (70 - 40) + 40;
        
        nodes[0].transform.SetIdentity();
        nodes[0].transform.Translate(w, h);
        
        var y0 = h + 2 * self.nodes[0].height;
        
        var step = Math.PI / (nodes.length - 3);
        var radius = 270 * Math.sin((Math.PI - step) * 0.5) / Math.sin(step);
        
        for(var i = 0, countIndex = 1; countIndex < (nodes.length - 1); i += step, countIndex++) {
            var x = w + radius * Math.cos(i);
            var y = y0 + radius * Math.sin(i);
            
            nodes[countIndex].transform.SetIdentity();
            nodes[countIndex].transform.Translate(x, y);
        }
        
        nodes[nodes.length - 1].transform.SetIdentity();
        nodes[nodes.length - 1].transform.Translate(w, y0 + radius * 2);
    }
    
    self.AddNewNode = function() {
        self.history.HistoryJSON();

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
    
    self.AddDandelion = function(count) {
        if(count <= 0) return;

        self.history.HistoryJSON();

        var pos = viewport.GetPosition();
        var scale = viewport.GetScale();
        var x0 = self.canvas.width / scale[0] * 0.5 - pos[0];
        var y0 = self.canvas.height / scale[1] * 0.5 - pos[1];
        
        var step = Math.PI / (count - 1);
        
        var radius = 270 * Math.sin((Math.PI - step) * 0.5) / Math.sin(step);
        var nodes = new Array();
        
        for(var i = 0, countIndex = 0; countIndex < count; i += step, countIndex++) {
            var x = x0 + radius * Math.cos(i);
            var y = y0 + radius * Math.sin(i);
            
            var node = new Node();
            node.id = GetNewNodeId();
            node.title = 'new node';
            node.isNew = true;
            
            node.transform.Translate(x, y);
            nodes.push(node);
            self.nodes.push(node);
        }
        
        var startNode = new Node();
        startNode.id = GetNewNodeId();
        startNode.title = 'new node';
        startNode.isNew = true;
        
        startNode.transform.Translate(x0, y0 - radius);
        self.nodes.push(startNode);
        
        var endNode = new Node();
        endNode.id = GetNewNodeId();
        endNode.title = 'new node';
        endNode.isNew = true;
        
        endNode.transform.Translate(x0, y0 + radius * 2);
        self.nodes.push(endNode);
        
        if(nodes.length > 0) {
            for(var i = 0; i < nodes.length; i++) {
                var startLink = new Link();
                startLink.nodeA = startNode;
                startLink.nodeB = nodes[i];
                startLink.type = 'direct';
                startLink.id = GetNewLinkId();
                
                self.links.push(startLink);
                
                var endLink = new Link();
                endLink.nodeA = nodes[i];
                endLink.nodeB = endNode;
                endLink.type = 'direct';
                endLink.id = GetNewLinkId();
                
                self.links.push(endLink);
                    
                for(var j = i; j < nodes.length; j++) {
                    if(nodes[i].id == nodes[j].id) continue;
                    
                    var link = new Link();
                    
                    link.nodeA = nodes[i];
                    link.nodeB = nodes[j];
                    link.type = 'dual';
                    link.id = GetNewLinkId();
                    
                    self.links.push(link);
                }
            }
        }
    }
    
    self.AddLinear = function(count) {
        if(count <= 0) return;

        self.history.HistoryJSON();

        var nodes = new Array();
        var tNode = null;
        for(var  i = 0; i < count; i++) {
            var node = new Node();
            node.id = GetNewNodeId();
            node.title = 'new node';
            node.isNew = true;
            
            nodes.push(node);
            self.nodes.push(node);
            
            if(tNode != null) {
                var link = new Link();
                link.nodeA = tNode;
                link.nodeB = node;
                link.type = 'direct';
                link.id = GetNewLinkId();
                
                self.links.push(link);
                tNode = node;
            } else {
                tNode = node;
            }
        }
        
        BuildLinear(nodes);
    }
    
    self.AddBranched = function(count) {
        if(count <= 0) return;

        self.history.HistoryJSON();

        var nodes = new Array();
        
        var sNode = new Node();
        sNode.id = GetNewNodeId();
        sNode.title = 'new node';
        sNode.isNew = true;
        
        nodes.push(sNode);
        self.nodes.push(sNode);
        
        var linkedNodes = new Array();
        for(var  i = 0; i < count; i++) {
            var node = new Node();
            node.id = GetNewNodeId();
            node.title = 'new node';
            node.isNew = true;
            
            nodes.push(node);
            linkedNodes.push(node);
            self.nodes.push(node);
            
            if(sNode != null) {
                var link = new Link();
                link.nodeA = sNode;
                link.nodeB = node;
                link.type = 'direct';
                link.id = GetNewLinkId();
                
                self.links.push(link);
            }
        }
        
        var eNode = new Node();
        eNode.id = GetNewNodeId();
        eNode.title = 'new node';
        eNode.isNew = true;
        
        nodes.push(eNode);
        self.nodes.push(eNode);
        
        for(var i = 0; i < linkedNodes.length; i++) {
            var link = new Link();
            link.nodeA = linkedNodes[i];
            link.nodeB = eNode;
            link.type = 'direct';
            link.id = GetNewLinkId();
            
            self.links.push(link);
        }
        
        BuildBranched(nodes);
    }
    
    self.AddNode = function(node) {
        if(node == null) return;

        self.history.HistoryJSON();

        node.id = GetNewNodeId();
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
    
    var GetNodeFromMap = function(nodesMap, oldId) {
        if(nodesMap == null || nodesMap.length <= 0) return null;
        
        for(var i = 0 ; i < nodesMap.length; i++) {
            if(nodesMap[i].oldId == oldId)
                return nodesMap[i].newId;
        }
    
        return null;
    }
    
    self.Resize = function() {
        if(self.$canvasContainer != null && self.$canvas != null) {
            var h = window.innerHeight;
            var w = window.innerWidth;
            if (!$("#fullScreen").hasClass('active')){
                self.$canvas.attr('width', self.$canvasContainer.width());
                h = parseInt(h) - 150;
                if (h < 400){h = 400;}
                $(self.$canvasContainer).height(h);
                self.$canvas.attr('height', self.$canvasContainer.height());
            } else {
                $(self.$canvasContainer).height(h);
                if (h > 545) h = 545;
                $('#tab-content-scrollable').css('height', (h - 115) + 'px');
                $(self.$canvasContainer).width(w);
                self.$canvas.attr('height', self.$canvasContainer.height());
                self.$canvas.attr('width', self.$canvasContainer.width());
            }
            self.Render();
        }
    }
    
    self.DeactiveLink = function(linkId) {
        if(linkId <= 0) return;
        
        var link = self.GetLinkById(linkId);
        if(link != null)
            link.isActive = false;
    }
    
    self.UpdateLinkLogic = function(linkId, newLogic) {
        if(linkId <= 0) return;
        
        var link = self.GetLinkById(linkId);
        if(link != null) {
            link.conditional = newLogic;
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
        self.canvas.addEventListener("mouseout", MouseOut, false);
        self.canvas.addEventListener("touchstart", MouseDown, false);
        self.canvas.addEventListener("touchmove", MouseMove, false);
        self.canvas.addEventListener("touchend", MouseUp, false);
        document.addEventListener("keydown", KeyDown, false);
        document.addEventListener("keyup", KeyUp, false);
    }
    
    var MouseOut = function(event) {
        $('body').css('cursor', 'default');
        $('body').removeClass('clearCursor');
    }
    
    var KeyDown = function(event) {
        ctrlKeyPressed = event.ctrlKey;
        altKeyPressed = event.altKey;

        if(ctrlKeyPressed && event.keyCode == 67) {
            if(self.copyFunction != null)
                self.copyFunction();
        } else if(ctrlKeyPressed && event.keyCode == 86) {
            if(self.pasteFunction != null)
                self.pasteFunction();
        } else if((event.keyCode == 107) || (altKeyPressed && event.keyCode == 187) || (altKeyPressed && event.keyCode == 61)) {
            if(self.zoomIn != null)
                self.zoomIn();
        } else if((event.keyCode == 109) || (altKeyPressed && event.keyCode == 189) || (altKeyPressed && event.keyCode == 173)) {
            if(self.zoomOut != null)
                self.zoomOut();
        } else if((altKeyPressed && event.keyCode == 83)) {
            if(self.save!= null)
                self.save();
        } else if((altKeyPressed && event.keyCode == 85)) {
            if(self.update!= null)
                self.update();
        } else if(event.keyCode == 46) {
            if(self.nodes != null && self.nodes.length > 0) {
                var nodeId = 0;
                for(var i = 0; i < self.nodes.length; i++) {
                    if(self.nodes[i].isSelected) {
                        nodeId = self.nodes[i].id;
                        break;
                    }
                }
                
                if(self.rightPanel != null) {
                    var node = GetNodeById(nodeId);
                    if(node != null) {
                        self.rightPanel.node = node;
                        self.rightPanel.DeleteNode();
                    }
                }
            }
        } else if (ctrlKeyPressed && event.keyCode == 32){
            if (self.isSelectActive){
                self.turnOnPanMode();
            } else {
                self.turnOnSelectMode();
            }
        }
    } 
    
    var KeyUp = function(event) {
        ctrlKeyPressed = event.ctrlKey;
        altKeyPressed = event.altKey;
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
        var positions = [];
        if(self.nodes.length > 0) {
            for(var i = self.nodes.length - 1; i >= 0; i--) {
                if(self.nodes[i].isSelected) {
                    positions.push(self.nodes[i]);
                }
                var result = self.nodes[i].MouseClick(self.mouse, viewport);
                if(result.length > 0 && !isRedraw) {
                    isRedraw = true;
                    if(result[1] == 'header') {
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
        
        if(self.nodes.length > 0) {
            for(var i = self.nodes.length - 1; i >= 0; i--) {
                if(self.nodes[i].isDragging) {
                    positions.push(self.nodes[i]);
                }
            }
        }
        
        if(self.history != null && positions.length > 0) {
            self.history.HistoryPosition(positions);
        }
        
        if(self.links.length > 0 && !isRedraw) {
            for(var i = 0; i < self.links.length; i++) {
                if(self.links[i].MouseClick(self.mouse, viewport)) {
                    for(var j = 0; j < self.links.length; j++)
                        self.links[j].isActive = false;

                    self.links[i].isActive = true;
                    isRedraw = true;
                    ShowLinkManagetDialog(self.links[i].id);
                    self.mouse.isDown = false;
                }
            }
        }
        
        if(!isRedraw && self.selectorTool != null && self.isSelectActive) {
            if(!ctrlKeyPressed) {
                for(var i = 0; i < self.links.length; i++) {
                    self.links[i].isSelected = false;
                }

                for(var i = self.nodes.length - 1; i >= 0; i--) { 
                    self.nodes[i].isSelected = false;
                }
            }
            self.selectorTool.MouseDown(self.mouse, viewport);
            isRedraw = true;
        }

        if(isRedraw)
            self.Render();
        
        return false;
    }
    
    var MouseUp = function(event) {
        //event.preventDefault();
        self.mouse.isDown = false;
        UpdateMousePosition(event);
        self.lastMouse.x = self.mouse.x;
        self.lastMouse.y = self.mouse.y;
        
        var isRedraw = false;
        if(self.linkConnector != null) {
            var r = self.linkConnector.MouseUp(self.mouse, viewport, self.nodes);
            
            if(r.length > 0) {
                var link = IsExistLink(r[1], r[2]);
                if(r[1] != r[2] && link == null) {
                    var nodeA = GetNodeById(r[1]);
                    var nodeB = GetNodeById(r[2]);
                    
                    if(self.history != null) {
                        self.history.HistoryJSON();
                    }
                    
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
        
        if(!isRedraw && self.selectorTool != null && self.isSelectActive) {
            var existSelect = false;
            if(self.nodes.length > 0) {
                for(var i = self.nodes.length - 1; i >= 0; i--) {
                    if(self.nodes[i].IsNodeInRect(self.selectorTool.x, self.selectorTool.y, self.selectorTool.width, self.selectorTool.height, viewport) || self.nodes[i].isSelected) {
                        self.nodes[i].isSelected = true;
                        
                        if(!existSelect)
                            existSelect = true;
                    }
                }
            }
            
            if(self.links.length > 0) {
                for(var i = self.links.length - 1; i >= 0; i--) {
                    if(self.links[i].IsLinkInRect(self.selectorTool.x, self.selectorTool.y, self.selectorTool.width, self.selectorTool.height, viewport) || self.links[i].isSelected) {
                        self.links[i].isSelected = true;
                        
                        if(!existSelect)
                            existSelect = true;
                    }
                }
            }
            self.selectorTool.MouseUp(self.mouse, viewport);
            if(self.$aButtonsContianer != null) {
                if(existSelect) {
                    self.$aButtonsContianer.show();
                } else {
                    self.$aButtonsContianer.hide();
                }
            }
            
            isRedraw = true;
        }

        var selectedNodes = self.GetSelectedNodes();
        if(selectedNodes.length > 0) {
            self.$sectionSelect.empty();
            $('#sectionSettings').addClass('hide');
            $('#sectionNodeContainer').empty();
            var options = '<option value="">Select section</option>';
            var existSectionsId = [];
            self.$sectionSelect.append(options);
            for(var i = selectedNodes.length; i--;) {
                if(selectedNodes[i].sections.length > 0) {
                    options = '';
                    for(var j = selectedNodes[i].sections.length; j--;) {
                        var exist = false;
                        for(var k = existSectionsId.length; k--;) {
                            if(existSectionsId[k] == selectedNodes[i].sections[j].id) {
                                exist = true;
                                break;
                            }
                        }

                        if(!exist) {
                            existSectionsId.push(selectedNodes[i].sections[j].id);
                            options += '<option value="' + selectedNodes[i].sections[j].id + '">' + selectedNodes[i].sections[j].name + '</option>';
                        }
                    }

                    self.$sectionSelect.append(options);
                }
            }
        }
        
        if(isRedraw)
            self.Render();

        return false;
    }

    self.AddNodesToSection = function(sectionId) {
        var section       = self.GetSectionById(sectionId),
            selectedNodes = self.GetSelectedNodes(),
            addedNodes    = [],
            isExist       = false;

        if(section == null || selectedNodes == null || selectedNodes.length <= 0) return;

        for(var i = selectedNodes.length; i--;) {
            isExist = false;
            for(var k = selectedNodes[i].sections.length; k--;) {
                if(selectedNodes[i].sections[k].id == sectionId) {
                    isExist = true;
                    break;
                }
            }

            if(!isExist) {
                addedNodes.push(selectedNodes[i]);
                section.nodes.push(new SectionNode(selectedNodes[i], 0));
                selectedNodes[i].sections.push(section);
            }
        }

        self.Render();

        return addedNodes;
    }

    self.GetSectionById = function(id) {
        if(self.sections == null || self.sections.length <= 0) return null;

        for(var i = self.sections.length; i--;) {
            if(self.sections[i].id == id) {
                return self.sections[i];
            }
        }

        return null;
    }

    self.RemoveNodeFromSection = function(sectionId, nodeId) {
        var section = self.GetSectionById(sectionId),
            node    = GetNodeById(nodeId),
            length1 = 0,
            length2 = 0;

        if(section == null || node == null) return;

        length1 = section.nodes.length;
        length2 = node.sections.length;

        for(var i = length1; i--;) {
            if(section.nodes[i].node.id == nodeId) {
                section.nodes.splice(i, 1);
                break;
            }
        }

        for(var i = length2; i--;) {
            if(node.sections[i].id == sectionId) {
                node.sections.splice(i, 1);
                break;
            }
        }

        self.Render();
    }

    self.RemoveSection = function(sectionId) {
        var length = self.sections.length;

        for(var i = length; i--;) {
            if(self.sections[i].id == sectionId) {
                for(var j = self.sections[i].nodes.length; j--;) {
                    for(var k = self.sections[i].nodes[j].node.sections.length; k--;) {
                        if(self.sections[i].nodes[j].node.sections[k].id == sectionId) {
                            self.sections[i].nodes[j].node.sections.splice(k, 1);
                            break;
                        }
                    }
                }

                self.sections.splice(i, 1);
                break;
            }
        }

        self.Render();
    }
    
    var MouseMove = function(event) {
        var isCursorSet = false;
        
        if(self.mouse.isDown)
            event.preventDefault();
        
        UpdateMousePosition(event);

        var isRedraw = false;
        
        event.stopPropagation();
        event.target.style.cursor = 'default';
        
        if(self.linkConnector != null) {
            if(self.linkConnector.IsConnectorCollision(self.mouse.x, self.mouse.y, viewport)) {
                event.target.style.cursor = 'move';
                isCursorSet = true;
            }
            if(self.linkConnector.MouseMove(self.mouse, viewport, self.nodes)) {
                event.target.style.cursor = 'move';
                isCursorSet = true;
                isRedraw = true;
            }
        }
        
        if(self.links.length > 0 && !isRedraw && !(self.isSelectActive && self.selectorTool != null && self.selectorTool.isDragged)) {
            for(var i = 0; i < self.links.length; i++) {
                if(self.links[i].IsLinkButtonCollision(self.mouse.x, self.mouse.y, viewport)) {
                    event.target.style.cursor = 'default';
                    isCursorSet = true;
                }
                if(self.links[i].MouseMove(self.mouse, viewport)) {
                    if(!isCursorSet) {
                        event.target.style.cursor = 'default';
                        isCursorSet = true;
                    }
                    isRedraw = true;
                }
            }
        }
        
        if(self.nodes.length > 0 && !isRedraw && !(self.isSelectActive && self.selectorTool != null && self.selectorTool.isDragged)) {
            for(var i = self.nodes.length - 1; i >= 0; i--) {
                if(!isCursorSet && 'IsLinkButtonCollision' in self.nodes[i] && self.nodes[i].IsLinkButtonCollision(self.mouse.x, self.mouse.y, viewport)) {
                    event.target.style.cursor = 'default';
                    isCursorSet = true;
                } else if(!isCursorSet && 'IsAddButtonCollision' in self.nodes[i] && self.nodes[i].IsAddButtonCollision(self.mouse.x, self.mouse.y, viewport)) {
                    event.target.style.cursor = 'default';
                    isCursorSet = true;
                } else if(!isCursorSet && self.nodes[i].IsMainAreaCollision(self.mouse.x, self.mouse.y, viewport)) {
                    event.target.style.cursor = 'default';
                    isCursorSet = true;
                } else if(!isCursorSet && self.nodes[i].IsHeaderCollision(self.mouse.x, self.mouse.y, viewport)) {
                    event.target.style.cursor = 'move';
                    isCursorSet = true;
                } 
                
                if(self.nodes[i].MouseMove(self.mouse, viewport, self.nodes)) {
                    if(!isCursorSet && self.nodes[i].IsHeaderCollision(self.mouse.x, self.mouse.y, viewport)) {
                        event.target.style.cursor = 'move';
                        isCursorSet = true;
                    }
                    isRedraw = true;
                }
            }
        }

        if(!isCursorSet && !self.isSelectActive) {
            event.target.style.cursor = 'move';
            isCursorSet = true;
        } else if(!isCursorSet) {
            event.target.style.cursor = 'crosshair';
            isCursorSet = true;
        }
        
        if(isCursorSet) {
            $('body').addClass('clearCursor');
        } else {
            $('body').removeClass('clearCursor');
        }
        
        if(!isRedraw && self.mouse.isDown && !self.isSelectActive) {
            var scale = viewport.GetScale();
            
            var tx = (self.mouse.x - self.mouse.oldX) / scale[0];
            var ty = (self.mouse.y - self.mouse.oldY) / scale[1];
            
            viewport.TranslateWithoutScale(tx, ty);
            isRedraw = true;
        } else if(!isRedraw && self.mouse.isDown && self.isSelectActive) {
            if(self.selectorTool.MouseMove(self.mouse, viewport))
                isRedraw = true;
        }

        if(isRedraw)
            self.Render();
        
        return false;
    }
    
    var AddNodeWithLink = function(nodeId) {
        var node = GetNodeById(nodeId);

        if(node == null) return;
        
        if(self.history != null) {
            self.history.HistoryJSON();
        } 
        
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
    
    self.TranslateViewport = function(x, y) {
        viewport.TranslateWithoutScale(x, y);
        self.Render();
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
        var link = self.GetLinkById(linkId);
        
        if(self.linkModal != null && link != null) {
            self.linkModal.SetLink(link);
            self.linkModal.Show();
        }
    }
    
    var ShowRightPanel = function(elementId, mode) {
        if(self.rightPanel != null) {
            if(mode == 'node') {
                var node = GetNodeById(elementId);
                if(node != null) {
                    self.rightPanel.TryChangeNode(node);
                }
            }
        }
    }
    
    var ShowNodeDialog = function(nodeId) {
        var node = GetNodeById(nodeId);
        
        if(node != null && self.nodeModal != null) {
            self.nodeModal.SetNode(node);
            self.nodeModal.Show();
        }
    }
    
    var ShowDeleteDialog = function(nodeId) {
        var node = GetNodeById(nodeId);
        var selectedNodes = new Array();
        
        if(self.nodes != null && self.nodes.length > 0) {
            for(var i = 0; i < self.nodes.length; i++) {
                if(self.nodes[i].isSelected)
                    selectedNodes.push(self.nodes[i]);
            }
        }
        
        if(selectedNodes.length > 0 && self.deleteModal != null && node != null && node.isSelected) {
            self.deleteModal.selectedNodes = selectedNodes;
            self.deleteModal.Show('multiple');
        }else if(node != null && self.deleteModal != null) {
            self.deleteModal.node = node;
            self.deleteModal.Show('single');
        }
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
    
    self.GetLinkById = function(id) {
        if(self.links.length <= 0) return null;
        
        for(var i = 0; i < self.links.length; i++) {
            if(self.links[i].id == id)
                return self.links[i];
        }
    
        return null;
    }
    
    self.GetCasesForLink = function(linkId) {
        if(linkId <= 0 || self.links.length <= 0) return null;
        
        var link = self.GetLinkById(linkId);
        if(link == null) return null;
        
        var result = new Array();

        for(var i = 0; i < self.links.length; i++) {
            if(self.links[i].nodeA.mapId == link.nodeA.mapId && link.nodeA.id == self.links[i].nodeA.id) {
                result.push(self.links[i].nodeB);
            }
        }
        
        return result;
    }
    
    self.GetLinksByNodeId = function(nodeId) {
        if(nodeId == null || nodeId < 0 || self.links == null) return null;
        
        var i = self.links.length,
            result = [];
        for(;i--;) {
            if(self.links[i].nodeA.id == nodeId || self.links[i].nodeB.id == nodeId) {
                result.push(self.links[i]);
            }
        }
        
        return result;
    }

    self.GetSelectedNodes = function() {
        var result = new Array();

        if(self.nodes.length <= 0) return result;

        for(var i = self.nodes.length; i--;) {
            if(self.nodes[i].isSelected) {
                result.push(self.nodes[i]);
            }
        }

        return result;
    }
    
    var GetRootNode = function() {
        if(self.nodes.length <= 0) return null;
        
        for(var i = 0; i < self.nodes.length; i++) {
            if(self.nodes[i].isRoot)
                return self.nodes[i];
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

    self.AddNewSection = function(name, nodes) {
        if(name == null || name.length <= 0) return;

        var section = new Section(GetSectionId(), name, self.GetRandomColor()),
            i       = nodes.length,
            node    = null;

        for(;i--;) {
            node = GetNodeById(nodes[i].nodeId);
            if(node != null) {
                section.nodes.push(new SectionNode(node, nodes[i].order));
                node.sections.push(section);
            }
        }

        self.sections.push(section);

        $('#sectionsNodesSelect').append('<option value="' + section.id + '">' + section.name + '</option>');

        self.Render();
    };

    self.UpdateSection = function(sectionId, sectionName, nodes) {
        var section = self.GetSectionById(sectionId);

        if(section == null) return;

        section.name = sectionName;

        for(var i = nodes.length; i--;) {
            for(var k = section.nodes.length; k--;) {
                if(section.nodes[k].node.id == nodes[i].nodeId) {
                    section.nodes[k].order = nodes[i].order;
                    break;
                }
            }
        }
    }

    var GetSectionId = function() {
        sectionNodeId += 1;

        return sectionNodeId + 'n';
    }
    
    var encode64 = function (input) {
        input = input.replace(/\0/g,"");
        return  B64.encode($.trim(input));
    }

    var decode64 = function(input) {
        input = input.replace(/\0/g,"");
        return  B64.decode($.trim(input));
    }
    
    var evalJson = function(jsArray) {
        eval("function parseJSON(){ return "+ jsArray +"; }");
        return parseJSON();
    }
}