var SelectRightPanel = function() {
    var self = this;
    
    var $panel = null;
    var $colorContainer = null;
    var $input = null;
    var inputID = null;
    var colorContainerID = null;
    var visualEditor = null;
    var $saveBtn = null;
    var $saveCloseBtn = null;
    var $closeBtn = null;
    
    self.Init = function(params) {
        if('panelID' in params) {
            $panel = $(params.panelID);
        }
        
        if('colorContainer' in params) {
            colorContainerID = params.colorContainer;
            $colorContainer = $(params.colorContainer);
        }
    
        if('inputID' in params) {
            inputID = params.inputID;
            $input = $(params.inputID);
        }
        
        if('visualEditor' in params) {
            visualEditor = params.visualEditor;
        }
        
        if('saveBtnID' in params) {
            $saveBtn = $(params.saveBtnID);
            if($saveBtn != null)
                $saveBtn.click(Save);
        }
        
        if('saveCloseBtnID' in params) {
            $saveCloseBtn = $(params.saveCloseBtnID);
            if($saveCloseBtn != null)
                $saveCloseBtn.click(SaveAndClose);
        }
        
        if('closeBtnID' in params) {
            $closeBtn = $(params.closeBtnID);
            if($closeBtn != null)
                $closeBtn.click(Close);
        }
    }
    
    self.Show = function() {
        if($panel == null) return;
        
        $input.val('#000000');
        $(colorContainerID).farbtastic(inputID);
        $panel.show();
    }
    
    var Save = function() {
        if(visualEditor == null || $input == null) return;
        
        var color = $input.val();
        if(!(/(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(color))) return;
        
        visualEditor.ChangeSelectNodesColor(color);
        visualEditor.Render();
    }
    
    var SaveAndClose = function() {
        Save();
        Close();
    }
    
    var Close = function() {
        if($panel != null)
            $panel.hide();
    }
}