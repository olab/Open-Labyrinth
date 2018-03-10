var ColorModal = function() {
    var self = this;
    var $modal = null;
    var $input = null;
    var $apply = null;
    var visualEditor = null;
    
    var inputId = '';
    var colorPickerId = '';
    
    self.node = null;
    
    self.Init = function(parameters) {
        if('modalId' in parameters)
            $modal = $(parameters.modalId);
                
        if('inputId' in parameters) {
            inputId = parameters.inputId;
            $input = $(parameters.inputId);
        }
        
        if('applyBtn' in parameters) {
            $apply = $(parameters.applyBtn);
            
            if($apply != null)
                $apply.click(ApplyEvent);
        }
        
        if('visualEditor' in parameters)
            visualEditor = parameters.visualEditor;
        
        if('colorPickerContainer' in parameters)
            colorPickerId = parameters.colorPickerContainer;
    }
    
    self.Show = function() {
        if($modal != null) {
            if(self.node != null) {
                $input.val(self.node.color);
                $(colorPickerId).farbtastic(inputId);
                $.farbtastic(colorPickerId).setColor(self.node.color);
            }
            $modal.modal();
        }
    }
    
    self.Hide = function() {
        if($modal != null) {
            $modal.modal('hide');
        }
    }
    
    self.SetNode = function(node) {
        if(node != null && $input != null) {
            $input.val(node.headerColor);
            self.node = node;
        }
    }
    
    var ApplyEvent = function() {
        if($input != null && self.node != null && visualEditor != null) {
            var color = $input.val();
            var isOk  = /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(color);
            if(!isOk)
                color = '#FFFFFF';
            self.node.color = color;
            visualEditor.Render();
        }
        
        self.Hide();
        
        return false;
    }
}