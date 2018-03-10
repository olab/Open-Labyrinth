var LinkModal = function() {
    var self = this;
    var $modal = null;
    var $apply = null;
    var $linkTypes = null;
    var $linkLabel = null;
    var $linkImages = null;
    var $linkHidden = null;
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
        
        if('linkImages' in parameters)
            $linkImages = $(parameters.linkImages);

        if('linkLabel' in parameters)
            $linkLabel = $(parameters.linkLabel);

        if('linkHidden' in parameters)
            $linkHidden = $(parameters.linkHidden);

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

            if($linkLabel != null) {
                $linkLabel.val(self.link.label);
            }

            if($linkImages != null) {
                $.each($linkImages.children(), function(index, object) {
                    if($(object).attr('value') == self.link.imageId) {
                        $(object).attr('selected', 'selected');
                    } else {
                        $(object).removeAttr('selected');
                    }
                });
            }

            if($linkHidden != null) {
                $.each($linkHidden.children(), function(index, object) {
                    if($(object).attr('value') == self.link.linkHidden) {
                        $(object).attr('selected', 'selected');
                    } else {
                        $(object).removeAttr('selected');
                    }
                });
            }
        }
    }

    var ApplyEvent = function() {
        if(self.link != null && $linkTypes != null && $linkLabel != null && $linkImages != null && $linkHidden != null && visualEditor != null) {
            var value = $linkTypes.children().filter('.active').attr('value');
            if(value != 'delete') {
                self.link.type = value;
                self.link.label = $linkLabel.val();
                self.link.imageId = $linkImages.val();
                self.link.linkHidden = $linkHidden.val();
                self.link.lineColor = self.link.linkHidden == 1 ? '#969595': '#ffffff';
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