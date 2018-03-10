var DeleteModal = function() {
    var self = this;
    var $modal = null;
    var $apply = null;
    var visualEditor = null;
    var dialogMode = 'single'; // single node delete or multiply
    
    self.node = null;
    self.selectedNodes = null;
    self.rightPanel = null;
    self.selectRoot = false;

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
        
        if('rightPanel' in parameters)
            self.rightPanel = parameters.rightPanel;
    };
    
    self.Show = function(mode) {
        dialogMode = mode;
        if($modal != null) {
            self.Hide();
            if(mode == 'single') {
                $('.deleteModalHeaderNode').show();
                $('.deleteModalContentNode').show();
            } else if(mode == 'multiple') {
                $('.deleteModalHeaderNodes').show();
                $('.deleteModalContentNodes').show();
            }
            
            $modal.modal();
        }
    };
    
    self.Hide = function() {
        if($modal != null) {
            $('.deleteModalHeaderNode').hide();
            $('.deleteModalContentNode').hide();
            $('.deleteModalHeaderNodes').hide();
            $('.deleteModalContentNodes').hide();
            $modal.modal('hide');
        }
    };
     
    var ApplyEvent = function() {
        if(dialogMode == 'single') {
            if(self.node != null && visualEditor != null) {
                visualEditor.DeleteNodeById(self.node.id);
                self.node = null;
                visualEditor.Render();
            }
        } else if(dialogMode == 'multiple') {
            if(self.selectedNodes != null && self.selectedNodes.length > 0 && visualEditor != null) {
                for(var i = 0; i < self.selectedNodes.length; i++) {
                    if( ! self.selectedNodes[i].isRoot) visualEditor.DeleteNodeById(self.selectedNodes[i].id);
                }
                visualEditor.Render();
            }
        }
        
        if(self.rightPanel != null) self.rightPanel.Hide();
        
        self.Hide();
        $('#update').prop('disabled', false).css('background-color', '#777676');

        if(self.selectRoot) utils.ShowMessage($('#ve_message'), $('#ve_message_text'), 'error', 'You cannot delete the root node.', 3000, $('#ve_actionButton'), false);

        return false;
    }
};