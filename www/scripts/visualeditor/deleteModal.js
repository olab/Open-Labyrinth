var DeleteModal = function() {
    var self = this;
    var $modal = null;
    var $apply = null;
    var visualEditor = null;
    
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
     
    var ApplyEvent = function() {
        if(self.node != null && visualEditor != null) {
            visualEditor.DeleteNodeById(self.node.id);
            self.node = null;
            
            visualEditor.Render();
        }
        
        self.Hide();
        
        return false;
    }
}