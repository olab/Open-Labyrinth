/**
 * Main application class
 */
var SkinEditor = (function() {
    
    SkinEditor.UI_LEFT_PANEL_HTML        = '<div class="skin-editor-panel left">' +
                                               '<div class="panel-content"></div>' +
                                           '</div>';
    SkinEditor.UI_RIGHT_PANEL_HTML       = '<div class="skin-editor-panel right">' +
                                               '<div class="panel-content"></div>' +
                                           '</div>';
    SkinEditor.UI_CONTENT_CONTAINER_HTML = '<div class="skin-editor-content-container"></div>';
    SkinEditor.UI_BUTTONS_HTML           = '<div style="position: fixed; bottom: 5px; margin-left: 39%;display: block" class="btn-group">' +
                                                '<button class="btn btn-success btn-save">Save</button>' +
                                                '<button class="btn btn-danger btn-delete-component">Delete component</button>' +
                                                '<a href="' + getPlayURL() + '" target="_blank" class="btn btn-play-labyrinth">Test Play</a>' +
                                                '<a href="' + getCloseURL() + '" class="btn btn-warning btn-close-editor">Close</a>' +
                                           '</div>';
    SkinEditor.UI_SAVE_DIALOG_HTML       = '<div class="saving-dialog alert alert-warning" style="position:absolute;top:0;left:46%;display: none">Saving...</div>';
    SkinEditor.UI_SAVED_DIALOG_HTML      = '<div class="saved-dialog alert alert-success" style="position:absolute;top:0;left:46%; display: none">Saved</div>';
    
    /**
     * Default constructor
     * 
     * @param {*} $container - skin editor container
     * @constructor
     */
    function SkinEditor($container, data) {
        var instance = this;

        this._$uiMainContainer       = $container;
        this._$uiLeftPanelContainer  = this._CreatePanel(SkinEditor.UI_LEFT_PANEL_HTML, 
                                                         'icon-chevron-right', 
                                                         'icon-chevron-left');
        this._$uiRightPanelContainer = this._CreatePanel(SkinEditor.UI_RIGHT_PANEL_HTML, 
                                                         'icon-chevron-left', 
                                                         'icon-chevron-right');
        this._$uiContentContainer    = this._CreateContentContainer();

        this._$uiSaving              = this._CreateSavingDialog();
        this._$uiSaved               = this._CreateSavedDialog();

        this._$uiButtons             = this._CreateButtons();

        this._rootComponent          = null;
        this._componentsList         = new ComponentsListUIComponent(this);
        this._componentsTree         = new ComponentsTreeUIComponent(this);
        this._propertyWindow         = new PropertyWindowUIComponent(this);

        if(data === '') {
            this._rootComponent = ComponentsManager.GetInstance().CreateRootComponent(this._$uiContentContainer);
        } else {
            ComponentsManager.GetInstance().DeserializeAllComponents(data, this._$uiContentContainer, this);
            this._rootComponent = ComponentsManager.GetInstance().GetRootComponent();
        }

        this._componentsList.AppendTo(this._$uiLeftPanelContainer);
        this._componentsTree.AppendTo(this._$uiLeftPanelContainer, data);
        
        this._propertyWindow.AppendTo(this._$uiRightPanelContainer);
    };
    
    SkinEditor.prototype.GetRootComponent  = function() { return this._rootComponent;  };
    SkinEditor.prototype.GetPropertyWindow = function() { return this._propertyWindow; };
    SkinEditor.prototype.GetComponentsTree = function() { return this._componentsTree; };
    
    /**
     * Private method
     * Create panel
     * 
     * @param {string} html - html code of panel
     * @param {string} onCSSClass - enabled panel css style
     * @param {string} offCSSClass - disabled panel css style
     */
    SkinEditor.prototype._CreatePanel = function(html, onCSSClass, offCSSClass) {
        var instance = this,
            $ui      = $(html).appendTo(this._$uiMainContainer).draggable();
        
        $ui.find('.close-panel').click(function() {
            instance._ToogleClosePanel($(this).find('i'), 
                                       $(this).parent().find('.panel-content'), 
                                       onCSSClass, 
                                       offCSSClass);
        });
        
        return $ui.find('.panel-content');
    };
    
    /**
     * Private method
     * Toogle close panel
     * 
     * @param {*} $uiIcon - UI Icon
     * @param {*} $uiContent - UI Content
     * @param {*} onCSSClass - enabled panel css style
     * @param {*} offCSSClass - disabled panel css style
     */
    SkinEditor.prototype._ToogleClosePanel = function($uiIcon, $uiContent, onCSSClass, offCSSClass) {
        if($uiIcon     === null ||
           $uiContent  === null ||
           onCSSClass  === null ||
           offCSSClass === null) { return; }
        
        var changeClass = function($e, f, t) { $e.removeClass(f).addClass(t); };
        
        $uiContent.stop();
        
        if($uiContent.css('display') === 'none') {
            changeClass($uiIcon, onCSSClass, offCSSClass);
            $uiContent.show('slow');
        } else {
            changeClass($uiIcon, offCSSClass, onCSSClass);
            $uiContent.hide('slow');
        }
    };
    
    /**
     * Private method
     * Create content container
     */
    SkinEditor.prototype._CreateContentContainer = function() {
        var $ui = $(SkinEditor.UI_CONTENT_CONTAINER_HTML).appendTo(this._$uiMainContainer);
        
        return $ui;
    };
    
    /**
     * Private method
     * Create buttons
     */
    SkinEditor.prototype._CreateButtons = function() {
        var instance = this,
            $ui      = $(SkinEditor.UI_BUTTONS_HTML).appendTo(this._$uiMainContainer);
        
        $ui.find('.btn-save').click(function() {
            var components = ComponentsManager.GetInstance().SerializeAllComponents(),
                html       = instance._$uiContentContainer.html(),
                tree       = '"tree": ' +  instance._componentsTree.GetJSON(),
                data       = '{' + tree + ', ' + components + ', "body": "' + B64.encode($('body').attr('style')) + '"}';

            instance._$uiSaving.show();
            $.ajax({
                    url: getUpdateURL(),
                   type: 'POST',
                   data: { skinId: getSkinId(), data: data, html: html },
                success: function(data) {
                    if(data === null || data.status === 'error') { alert("ERROR"); }

                    instance._$uiSaving.hide();
                    instance._$uiSaved.show();
                    setTimeout(function() { instance._$uiSaved.hide(); }, 3000);
                }
            });
        });

        $ui.find('.btn-delete-component').click(function() {
            instance._componentsTree.RemoveSelectedComponent();
        });
        
        return $ui;
    };

    SkinEditor.prototype._CreateSavingDialog = function() {
        return $(SkinEditor.UI_SAVE_DIALOG_HTML).appendTo(this._$uiMainContainer);
    };

    SkinEditor.prototype._CreateSavedDialog = function() {
        return $(SkinEditor.UI_SAVED_DIALOG_HTML).appendTo(this._$uiMainContainer);
    };

    return SkinEditor;
})();