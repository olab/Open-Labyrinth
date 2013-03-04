var NodeModal = function() {
    var self = this;
    var $modal = null;
    var $apply = null;
    var visualEditor = null;
    
    // Data objects
    var $title = null;
    var $content = null;
    var $support = null;
    var $supportKeywords = null;
    var $isExitNodePorb = null;
    var $linkStyle = null;
    var $nodePriority = null;
    var $undoLinks = null;
    var $endNode = null;
    var $counters = null;
    
    var contentId = '';
    var supportId = '';
    
    self.node = null;
    
    self.Init = function(parameters) {
        if('modalId' in parameters)
            $modal = $(parameters.modalId);
        
        if('applyBtn' in parameters) {
            $apply = $(parameters.applyBtn);
            
            if($apply != null)
                $apply.click(ApplyEvent);
        }
        
        if('visualEditor' in parameters)
            visualEditor = parameters.visualEditor;
        
        if('title' in parameters)
            $title = $(parameters.title);
        
        if('content' in parameters) {
            $content = $(parameters.content);
            contentId = parameters.content;
            if(contentId.length > 2) {
                contentId = contentId.substr(1, contentId.length - 1);
            }
        }
        
        if('support' in parameters) {
            $support = $(parameters.support);
            supportId = parameters.support;
            if(supportId.length > 2) {
                supportId = supportId.substr(1, supportId.length - 1);
            }
        }
        
        if('supportKeywords' in parameters)
            $supportKeywords = $(parameters.supportKeywords);
        
        if('isExitNodePorb' in parameters)
            $isExitNodePorb = $(parameters.isExitNodePorb);
        
        if('linkStyle' in parameters)
            $linkStyle = $(parameters.linkStyle);
        
        if('nodePriority' in parameters)
            $nodePriority = $(parameters.nodePriority);
        
        if('undoLinks' in parameters)
            $undoLinks = $(parameters.undoLinks);
        
        if('endNode' in parameters)
            $endNode = $(parameters.endNode);
        
        if('counters' in parameters)
            $counters = $(parameters.counters);
    }
    
    self.Show = function() {
        if($modal != null) {
            $modal.modal();
        }
    }
    
    self.Hide = function() {
        if($modal != null) {
            $modal.modal('hide');
        }
    }

    self.SetNode = function(node) {
        if(node == null) return;
        
        self.node = node;
        
        if($title != null)
            $title.val(self.node.title);
        
        if($content != null) {
            $content.val(self.node.content);
            tinymce.get(contentId).setContent(self.node.content);
        }
    
        if($support != null) {
            $support.val(self.node.support);
            tinymce.get(supportId).setContent(self.node.support);
        }
        
        if($supportKeywords != null)
            $supportKeywords.val(self.node.supportKeywords);
        
        if($isExitNodePorb != null) {
            var val = self.node.isExit ? 1 : 0;
            $isExitNodePorb.find('input[value="' + val + '"]').attr('checked', 'checked');
        }
        
        if($undoLinks != null) {
            var val = self.node.undo ? 1 : 0;
            $undoLinks.find('input[value="' + val + '"]').attr('checked', 'checked');
        }
        
        if($endNode != null) {
            var val = self.node.isEnd ? 1 : 0;
            $endNode.find('input[value="' + val + '"]').attr('checked', 'checked');
        }
        
        if($linkStyle != null)
            $linkStyle.find('input[value="' + self.node.linkStyle + '"]').attr('checked', 'checked');
        
        if($nodePriority != null)
            $nodePriority.find('input[value="' + self.node.nodePriority + '"]').attr('checked', 'checked');
        
        if(self.node.counters.length > 0 && $counters != null) {
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
        } else if($counters != null) {
            var counters = GetCountersData();
            if(counters != null && counters.length > 0) {
                for(var i = 0; i < counters.length; i++) {
                    $(counters[i].func).val('');
                    $(counters[i].show).removeAttr('checked');
                }
            }
        }
    }

    var ApplyEvent = function() {
        if(self.node != null && visualEditor != null) {
            self.node.title = GetValueFromValField($title);
            self.node.content = tinymce.get(contentId).getContent({format : 'raw', no_events : 1});//GetValueFromValField($content);
            self.node.support = tinymce.get(supportId).getContent({format : 'raw', no_events : 1});//GetValueFromValField($support);
            self.node.supportKeywords = GetValueFromValField($supportKeywords);
            self.node.isExit = GetBooleanValueFromField($isExitNodePorb);
            self.node.linkStyle = GetIntegerValueFromField($linkStyle);
            self.node.nodePriority = GetIntegerValueFromField($nodePriority);
            self.node.undo = GetBooleanValueFromField($undoLinks);
            self.node.isEnd = GetBooleanValueFromField($endNode);
            
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

            visualEditor.Render();
        }
        
        self.Hide();
        
        return false;
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
        if($counters == null) return null;
        
        var dataStr = $counters.attr('data');
        
        var result = null;
        result = eval(dataStr);
        
        return result;
    }
}