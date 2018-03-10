var History = function() {
    var self = this,
        visualEditor = null,
        history = [],
        currentIndex = -1,
        currentStateIndex = -1,
        currentState = [],
        $undo = null,
        $redo = null;
        
    self.Init = function(options) {
        visualEditor = options.visualEditor;
        $undo = $(options.undo);
        $redo = $(options.redo);
    }
    
    self.HistoryJSON = function() {
        self.SetButtons();

        currentIndex += 1;
        var hJSON = new HistoryJSON(visualEditor);
        hJSON.Save();

        history.splice(currentIndex, 0, [hJSON]);
        currentState = [];
        currentStateIndex = -1;
    }
    
    self.HistoryNode = function(node) {
        if(node == null) return;

        self.SetButtons();
        
        currentIndex += 1;
        var hNode = new HistoryNode(visualEditor);
        hNode.Save(node);

        history.splice(currentIndex, 0, [hNode]);
        currentState = [];
        currentStateIndex = -1;
    }
    
    self.HistoryPosition = function(nodes) {
        if(nodes == null || nodes.length <= 0) return;

        self.SetButtons();
        
        currentIndex += 1;
        var i = nodes.length,
            hNodes = [],
            hNode = null;
        for(;i--;) {
            hNode = new HistoryNode(visualEditor);
            hNode.Save(nodes[i]);
            hNodes.push(hNode);
        }

        history.splice(currentIndex, 0, hNodes);
        currentState = [];
        currentStateIndex = -1;
    }
    
    self.HistoryDeleteNodes = function(nodes) {
        if(nodes == null || nodes.length <= 0) return;

        self.SetButtons();
        
        currentIndex += 1;
        var i = nodes.length,
            j = 0,
            hNodes = [],
            hNodesMap = [],
            hNode = null;
        for(;i--;) {
            hNode = new HistoryNode(visualEditor);
            hNode.Save(nodes[i].node, nodes[i].links);
            hNodesMap[hNode.GetNodeId()] = hNode;
            hNodes.push(hNode);
        }
        
        i = hNodes.length;
        for(;i--;) {
            j = hNodes[i].links.length;
            for(;j--;) {
                if(hNodes[i].links[j].nodeB.id in hNodesMap) {
                    hNodes[i].links[j].nodeB = hNodesMap[hNodes[i].links[j].nodeB.id].node;
                }
                
                if(hNodes[i].links[j].nodeA.id in hNodesMap) {
                    hNodes[i].links[j].nodeA = hNodesMap[hNodes[i].links[j].nodeA.id].node;
                }
            }
        }

        history.splice(currentIndex, 0, hNodes);
        currentState = [];
        currentStateIndex = -1;
    }
    
    self.Undo = function() {
        var result = false;
        if(currentIndex >= 0 && history.length > 0 && currentIndex < history.length) {
            currentStateIndex += 1;
            currentState.splice(currentStateIndex, 0, visualEditor.Serialize());

            var i = history[currentIndex].length;
            for(;i--;) {
                history[currentIndex][i].Restore();
            }
            currentIndex -= 1;
        } else if(currentIndex > 0) {
            currentIndex--;
        }

        if(currentIndex >= 0) {
            result = true;
        }
        
        visualEditor.Render();

        return result;
    }
    
    self.Redo = function() {
        var result = false;
        if(currentIndex < history.length && currentStateIndex >= 0) {
            visualEditor.Deserialize(currentState[currentStateIndex]);
            currentStateIndex -= 1;
            currentIndex += 1;
            if(currentStateIndex >= 0) {
                result = true;
            }
        }
        
        visualEditor.Render();

        return result;
    }
    
    self.Remap = function(nodeMap) {
        if(nodeMap == null || nodeMap.length <= 0 || history == null) return;
        
        var i = nodeMap.length,
            historyLength = history.length,
            j = 0,
            k = 0;
        for(;i--;) {
            for(j = historyLength; j--;) {
                k = history[j].length;
                for(;k--;) {
                    if(history[j][k].GetId() == nodeMap[i].oldId) {
                        history[j][k].SetId(nodeMap[i].newId);
                    }
                }
            }
        }
    }

    self.SetButtons = function() {
        if($undo != null) {
            $undo.removeClass('disabled');
        }

        if($redo != null) {
            $redo.addClass('disabled');
        }
    }
}

var HistoryLink = function(ve) {
    var self = this,
        visualEditor = ve;
        
    self.link = null;
    
    self.GetId = function() {
        return self.link.id;
    }
    
    self.SetId = function(newId) {
        if(newId == null || newId <= 0 || self.link == null) return;
        
        self.link.id = newId;
    }
    
    self.Save = function(l) {
        self.link = cloneLink(l);
    }
    
    self.Restore = function() {
        if(self.link == null || visualEditor == null) return;
        
        var i = visualEditor.links.length;
        for(;i--;) {
            if(visualEditor.links[i].id == self.link.id) {
                visualEditor.links[i].label = self.link.label;
                visualEditor.links[i].imageId = self.link.imageId;
                visualEditor.links[i].linkHidden = self.link.linkHidden;
                visualEditor.links[i].type = self.link.type;
                
                return;
            }
        }
        
        visualEditor.links.push(self.link);
    }
}

var HistoryNode = function(ve) {
    var self = this,
        visualEditor = ve;
        
        
    self.node = null;
    self.links = [];
    
    self.GetId = function() {
        return self.node.id;
    }
    
    self.SetId = function(newId) {
        if(newId == null || newId <= 0 || self.node == null) return;
        
        self.node.id = newId;
    }
     
    self.GetNodeId = function() {
        return self.node.id;
    }
        
    self.Save = function(n, l) {
        self.node = cloneNode(n);
        
        if(typeof l !== 'undefined') {
            var i = l.length;
            for(;i--;) {
                var link = cloneLink(l[i]);
                if(self.node.id == link.nodeA.id) {
                    link.nodeA = self.node;
                } else {
                    link.nodeB = self.node;
                }

                self.links.push(link);
            }
        }
    }
    
    self.Restore = function() {
        if(self.node == null || visualEditor == null || visualEditor.nodes == null) return;
        
        var i = visualEditor.nodes.length, 
            j = self.links.length,
            k = 0,
            isLinkRestored = false;
        for(;i--;) {
            if(visualEditor.nodes[i].id == self.node.id) {
                visualEditor.nodes[i].isRoot = self.node.isRoot;
                visualEditor.nodes[i].title = self.node.title;
                visualEditor.nodes[i].content = self.node.content;
                visualEditor.nodes[i].support = self.node.support;
                visualEditor.nodes[i].supportKeywords = self.node.supportKeywords;
                visualEditor.nodes[i].isExit = self.node.isExit;
                visualEditor.nodes[i].linkStyle = self.node.linkStyle;
                visualEditor.nodes[i].nodePriority = self.node.nodePriority;
                visualEditor.nodes[i].undo = self.node.undo;
                visualEditor.nodes[i].isEnd = self.node.isEnd;
                visualEditor.nodes[i].counters = self.node.counters;
                visualEditor.nodes[i].transform = new Transform();
                visualEditor.nodes[i].transform.Multiply(self.node.transform);
                
                for(;j--;) {
                    if(visualEditor.links != null)
                        k = visualEditor.links.length;
                    for(;k--;) {
                        if(self.links[j].id == visualEditor.links[k].id) {
                            isLinkRestored = true;
                            visualEditor.links[k].nodeA = self.links[j].nodeA;
                            visualEditor.links[k].nodeB = self.links[j].nodeB;
                            visualEditor.links[k].id = self.links[j].id;
                            visualEditor.links[k].label = self.links[j].label;
                            visualEditor.links[k].imageId = self.links[j].imageId;
                            visualEditor.links[k].linkHidden = self.links[j].linkHidden;
                            visualEditor.links[k].type = self.links[j].type;
                            
                            break;
                        }
                    }
                    
                    if(!isLinkRestored) {
                        visualEditor.links.push(self.links[j]);
                    }
                }
                
                return;
            }
        }
        
        visualEditor.nodes.push(cloneNode(self.node));
        
        for(;j--;) {
            if(visualEditor.links != null)
                k = visualEditor.links.length;
            for(;k--;) {
                if(self.links[j].id == visualEditor.links[k].id) {
                    isLinkRestored = true;
                    visualEditor.links[k].nodeA = self.links[j].nodeA;
                    visualEditor.links[k].nodeB = self.links[j].nodeB;
                    visualEditor.links[k].id = self.links[j].id;
                    visualEditor.links[k].label = self.links[j].label;
                    visualEditor.links[k].imageId = self.links[j].imageId;
                    visualEditor.links[k].linkHidden = self.links[j].linkHidden;
                    visualEditor.links[k].type = self.links[j].type;
                            
                    break;
                }
            }
                    
            if(!isLinkRestored) {
                visualEditor.links.push(self.links[j]);
            }
        }
    }
    
    self.SaveCurrentState = function() {
        if(visualEditor == null || visualEditor.nodes == null || self.node == null) return null;
        
        var i = visualEditor.nodes.length;
        for(;i--;) {
            if(visualEditor.nodes[i].id == self.node.id) {
                return cloneNode(visualEditor.nodes[i]);
            }
        }
    }
}

var HistoryJSON = function(ve) {
    var self = this,
        visualEditor = ve;

    self.json = '';
    
    self.GetId = function() {
        return null;
    }
    
    self.SetId = function(newId) {
        
    }
        
    self.Save = function() {
        if(visualEditor == null) return;
        
        self.json = visualEditor.Serialize();
    }
    
    self.Restore = function() {
        if(visualEditor == null || self.json == null || self.json.length <= 0) return;
        
        visualEditor.Deserialize(self.json);
    }
}

function cloneNode(obj) {
    if(obj == null || typeof(obj) != 'object') return obj;    
    
    var temp = new Node(); 
    temp.id = obj.id;
    temp.isRoot = obj.isRoot;
    temp.title = obj.title;
    temp.content = obj.content;
    temp.support = obj.support;
    temp.supportKeywords = obj.supportKeywords;
    temp.isExit = obj.isExit;
    temp.linkStyle = obj.linkStyle;
    temp.nodePriority = obj.nodePriority;
    temp.undo = obj.undo;
    temp.isEnd = obj.isEnd;
    temp.counters = obj.counters;
    temp.transform.Multiply(obj.transform);
    
    return temp;
}

function cloneLink(obj) {
    if(obj == null || typeof(obj) != 'object') return obj;    
    
    var temp = new Link(); 
    temp.nodeA = obj.nodeA;
    temp.nodeB = obj.nodeB;
    temp.id = obj.id;
    temp.label = obj.label;
    temp.imageId = obj.imageId;
    temp.linkHidden = obj.linkHidden;
    temp.type = obj.type;
    
    return temp;
}