var RightPanel = function() {
    var self = this;
    
    self.$panel = null;
    self.$closeBtn = null;
    self.$saveBtn = null;
    self.$accordion = null;
    
    self.$colorInput = null;
    self.colorInputId = '';
    self.colorPickerId = '';
    
    self.$nodeRootBtn = null;
    self.$nodeDeleteBtn = null;
    self.deleteModal = new DeleteModal();
    
    // Data objects
    self.$nodeTitle = null;
    self.$nodeContent = null;
    self.$nodeSupport = null;
    self.$nodeSupportKeywords = null;
    self.$nodeIsExitNodePorb = null;
    self.$nodeLinkStyle = null;
    self.$nodePriority = null;
    self.$nodeUndoLinks = null;
    self.$endNode = null;
    self.$nodeCounters = null;
    self.nodeContentId = '';
    self.nodeSupportId = '';
    
    self.visualEditor = null;
    self.node = null;
    self.mode = 'node'; // mode node or link
    
    self.Init = function(parameters) {
        if('visualEditor' in parameters)
            self.visualEditor = parameters.visualEditor;
        
        if('panelId' in parameters) {
            self.$panel = $(parameters.panelId);
        }
        
        if('closeBtn' in parameters) {
            self.$closeBtn = $(parameters.closeBtn);
            if(self.$closeBtn != null)
                self.$closeBtn.click(self.Close);
        }
        
        if('colorInputId' in parameters) {
            self.colorInputId = parameters.colorInputId;
            self.$colorInput = $(parameters.colorInputId);
        }
        
        if('colorPickerId' in parameters) {
            self.colorPickerId = parameters.colorPickerId;
        }
        
        if('saveBtn' in parameters) {
            self.$saveBtn = $(parameters.saveBtn);
            if(self.$saveBtn != null)
                self.$saveBtn.click(self.Save);
        }
        
        if('accordion' in parameters) {
            self.$accordion = $(parameters.accordion);
        }
        
        if('nodeRootBtn' in parameters) {
            self.$nodeRootBtn = $(parameters.nodeRootBtn);
            if(self.$nodeRootBtn != null)
                self.$nodeRootBtn.click(self.SetRooNode);
        }
        
        if('nodeDeleteBtn' in parameters) {
            self.$nodeDeleteBtn = $(parameters.nodeDeleteBtn);
            if(self.$nodeDeleteBtn != null)
                self.$nodeDeleteBtn.click(self.DeleteNode);
        }
        
        self.deleteModal.Init({
            modalId: '#visual_editor_delete', 
            applyBtn: '#deleteNode', 
            visualEditor: self.visualEditor,
            rightPanel: self
        });
        
        if('nodeTitle' in parameters)
            self.$nodeTitle = $(parameters.nodeTitle);
        
        if('nodeContent' in parameters) {
            self.$nodeContent = $(parameters.nodeContent);
            self.nodeContentId = parameters.nodeContent;
            if(self.nodeContentId.length > 2) {
                self.nodeContentId = self.nodeContentId.substr(1, self.nodeContentId.length - 1);
            }
        }
        
        if('nodeSupport' in parameters) {
            self.$nodeSupport = $(parameters.nodeSupport);
            self.nodeSupportId = parameters.nodeSupport;
            if(self.nodeSupportId.length > 2) {
                self.nodeSupportId = self.nodeSupportId.substr(1, self.nodeSupportId.length - 1);
            }
        }
        
        if('nodeSupportKeywords' in parameters)
            self.$nodeSupportKeywords = $(parameters.nodeSupportKeywords);
        
        if('nodeIsExitNodePorb' in parameters)
            self.$nodeIsExitNodePorb = $(parameters.nodeIsExitNodePorb);
        
        if('nodeLinkStyle' in parameters)
            self.$nodeLinkStyle = $(parameters.nodeLinkStyle);
        
        if('nodePriority' in parameters)
            self.$nodePriority = $(parameters.nodePriority);
        
        if('nodeUndoLinks' in parameters)
            self.$nodeUndoLinks = $(parameters.nodeUndoLinks);
        
        if('endNode' in parameters)
            self.$endNode = $(parameters.endNode);
        
        if('nodeCounters' in parameters)
            self.$nodeCounters = $(parameters.nodeCounters);
    }
    
    self.Close = function() {
        self.Hide();
    }
    
    self.Save = function() {
        if(self.visualEditor == null) return;
        
        if(self.mode == 'node' && self.node != null) {
            if(self.$colorInput != null) {
                var color = self.$colorInput.val();
                var isOk  = /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(color);
                if(!isOk)
                    color = '#FFFFFF';
                self.node.color = color;
                
            }
            
            if(self.$nodeRootBtn != null && self.$nodeRootBtn.hasClass('active')) {
                var rootNode = GetRootNode();
                if(rootNode != null)
                    rootNode.isRoot = false;
                
                self.node.isRoot = true;
            }

            self.node.title = GetValueFromValField(self.$nodeTitle);
            self.node.content = tinymce.get(self.nodeContentId).getContent({format : 'raw', no_events : 1});//GetValueFromValField($content);
            self.node.support = tinymce.get(self.nodeSupportId).getContent({format : 'raw', no_events : 1});//GetValueFromValField($support);
            self.node.supportKeywords = GetValueFromValField(self.$nodeSupportKeywords);
            self.node.isExit = GetBooleanValueFromField(self.$nodeIsExitNodePorb);
            self.node.linkStyle = GetIntegerValueFromField(self.$nodeLinkStyle);
            self.node.nodePriority = GetIntegerValueFromField(self.$nodePriority);
            self.node.undo = GetBooleanValueFromField(self.$nodeUndoLinks);
            self.node.isEnd = GetBooleanValueFromField(self.$endNode);
            
            var counters = GetCountersData();
            if(counters != null && counters.length > 0) {
                for(var i = 0; i < counters.length; i++) {
                    var functionValue = $(counters[i].func).val();
                    var showValue = $(counters[i].show).is(':checked');
                    
                    var c = self.node.GetCounterById(counters[i].id);
                    if(c == null) {
                        self.node.counters.push({
                            id: counters[i].id, 
                            func: functionValue, 
                            show: showValue
                        });
                    } else {
                        c.func = functionValue;
                        c.show = showValue;
                    }
                }
            }
        }
        
        self.visualEditor.Render();
        self.Hide();
    }
    
    self.Show = function() {
        if(self.$panel != null) {
            self.$panel.removeClass('hide');
            if(self.mode == 'node' && self.$accordion != null && self.node != null) {
                self.node.isActive = true;
                if(self.node.isRoot && self.$nodeRootBtn != null) {
                    self.$nodeRootBtn.addClass('active');
                } else {
                    self.$nodeRootBtn.removeClass('active');
                }
                
                self.$accordion.addClass('node-panel');
                self.$colorInput.val(self.node.color);
                $(self.colorPickerId).farbtastic(self.colorInputId);
                $.farbtastic(self.colorPickerId).setColor(self.node.color);
                
                if(self.$nodeTitle != null)
                    self.$nodeTitle.val(self.node.title);

                if(self.$nodeContent != null) {
                    self.$nodeContent.val(self.node.content);
                    tinymce.get(self.nodeContentId).setContent(self.node.content);
                }

                if(self.$nodeSupport != null) {
                    self.$nodeSupport.val(self.node.support);
                    tinymce.get(self.nodeSupportId).setContent(self.node.support);
                }

                if(self.$nodeSupportKeywords != null)
                    self.$nodeSupportKeywords.val(self.node.supportKeywords);

                if(self.$nodeIsExitNodePorb != null) {
                    var val = self.node.isExit ? 1 : 0;
                    self.$nodeIsExitNodePorb.find('input[value="' + val + '"]').attr('checked', 'checked');
                }

                if(self.$nodeUndoLinks != null) {
                    var val = self.node.undo ? 1 : 0;
                    self.$nodeUndoLinks.find('input[value="' + val + '"]').attr('checked', 'checked');
                }

                if(self.$endNode != null) {
                    var val = self.node.isEnd ? 1 : 0;
                    self.$endNode.find('input[value="' + val + '"]').attr('checked', 'checked');
                }

                if(self.$nodeLinkStyle != null)
                    self.$nodeLinkStyle.find('input[value="' + self.node.linkStyle + '"]').attr('checked', 'checked');

                if(self.$nodePriority != null)
                    self.$nodePriority.find('input[value="' + self.node.nodePriority + '"]').attr('checked', 'checked');

                if(self.node.counters.length > 0 && self.$nodeCounters != null) {
                    var counters = GetCountersData();
                    if(counters != null && counters.length > 0) {
                        for(var i = 0; i < counters.length; i++) {
                            var c = self.node.GetCounterById(counters[i].id);
                            if(c != null) {
                                $(counters[i].func).val(c.func);
                                if(c.show) {
                                    $(counters[i].show).attr('checked', 'checked');
                                } else {
                                    $(counters[i].show).removeAttr('checked');
                                }
                            }
                        }
                    }
                } else if(self.$nodeCounters != null) {
                    var counters = GetCountersData();
                    if(counters != null && counters.length > 0) {
                        for(var i = 0; i < counters.length; i++) {
                            $(counters[i].func).val('');
                            $(counters[i].show).removeAttr('checked');
                        }
                    }
                }
            }
        }
    }
    
    self.Hide = function() {
        if(self.node != null)
            self.node.isActive = false;
        
        if(self.$panel != null) {
            if(self.$accordion != null) {
                self.$accordion.removeClass('node-panel');
            }
            
            self.$panel.addClass('hide');
        }
    }
    
    self.DeleteNode = function() {
        if(self.deleteModal == null) return;
        
        var selectedNodes = new Array();
        
        var selectedRoot = false;
        if(self.visualEditor != null && self.visualEditor.nodes != null && self.visualEditor.nodes.length > 0) {
            for(var i = 0; i < self.visualEditor.nodes.length; i++) {
                if(self.visualEditor.nodes[i].isSelected) {
                    selectedNodes.push(self.visualEditor.nodes[i]);
                    selectedRoot = selectedRoot || self.visualEditor.nodes[i].isRoot;
                }
            }
        }

        if(selectedNodes.length > 0 && self.node != null && self.node.isSelected) {
            self.deleteModal.selectedNodes = selectedNodes;
            self.deleteModal.selectRoot = selectedRoot;
            self.deleteModal.Show('multiple');
        }else if(self.node != null) {
            self.deleteModal.node = self.node;
            if(self.node.isRoot) {
                utils.ShowMessage($('#ve_message'), $('#ve_message_text'), 'error', 'You cannot delete the root node.', 3000, $('#ve_actionButton'), false);
            } else {
                self.deleteModal.Show('single');
            }
        }
    }
    
    var GetRootNode = function() {
        if(self.visualEditor == null || self.visualEditor.nodes.length <= 0) return null;
        
        for(var i = 0; i < self.visualEditor.nodes.length; i++) {
            if(self.visualEditor.nodes[i].isRoot)
                return self.visualEditor.nodes[i];
        }
    
        return null;
    }
    
    var GetValueFromValField = function($obj) {
        if($obj == null) return '';
        
        return $obj.val();
    }
    
    var GetBooleanValueFromField = function($objContainer) {
        if($objContainer == null) return false;
        
        var val = $objContainer.find('input[type="radio"]:checked').val();
        var result = false;
        if(val != '0')
            result = true;
        
        return result;
    }
    
    var GetIntegerValueFromField = function($objContainer) {
        if($objContainer == null) return 0;
        
        var val = $objContainer.find('input[type="radio"]:checked').val();
        
        if(isNaN(val))
            val = 0;
        
        return val;
    }
    
    var GetCountersData = function() {
        if(self.$nodeCounters == null) return null;
        
        var dataStr = self.$nodeCounters.attr('data');
        
        var result = null;
        result = eval(dataStr);
        
        return result;
    }
}