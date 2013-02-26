var LinkModal = function() {
    var self = this;
    var $modal = null;
    var $apply = null;
    var $linkTypes = null;
    var visualEditor = null;
    
    self.link = null;
    
    self.Init = function(parameters) {
        if('modalId' in parameters)
            $modal = $(parameters.modalId);
        
        if('applyBtn' in parameters) {
            $apply = $(parameters.applyBtn);
            
            if($apply != null)
                $apply.click(ApplyEvent);
        }
        
        if('linkTypes' in parameters)
            $linkTypes = $(parameters.linkTypes);
        
        if('visualEditor' in parameters)
            visualEditor = parameters.visualEditor;
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
    
    self.SetLink = function(link) {
        if(link != null) {
            self.link = link;
            if($linkTypes != null) {
                $.each($linkTypes.children(), function(index, object) {
                    if($(object).attr('value') == self.link.type) {
                        $(object).addClass('active');
                    } else {
                        $(object).removeClass('active');
                    }
                });
            }
        }
    }
     
    var ApplyEvent = function() {
        if(self.link != null && $linkTypes != null && visualEditor != null) {
            var value = $linkTypes.children().filter('.active').attr('value');
            if(value != 'delete') {
                self.link.type = value;
            } else {
                visualEditor.DeleteLinkById(self.link.id);
                self.link = null;
            }
            
            visualEditor.Render();
        }
        
        self.Hide();
        
        return false;
    }
}