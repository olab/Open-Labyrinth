/**
 * Property window for component class
 */
var PropertyWindowUIComponent = (function(parent) {
    inherit(parent, PropertyWindowUIComponent);
    
    PropertyWindowUIComponent.UI_HTML = '<div class="skin-editor-property-window">' +
                                             '<div class="content-header">' +
                                                 'Properties <span class="component-name"></span>' +
                                             '</div><div class="content"></div>' +
                                        '</div>';
    
    /**
     * Default constructor
     * 
     * @param {SkinEditor} skinEditor - skin editor
     * @constructor
     */
    function PropertyWindowUIComponent(skinEditor) {
        PropertyWindowUIComponent.super.constructor.apply(this, [skinEditor]);
        
        this._component        = null;
        
        this._$ui              = null;
        this._$uiComponentName = null;
        this._$uiContent       = null;
    };
    
    /**
     * Bind component with property window
     * 
     * @param {Component} component - binding component
     */
    PropertyWindowUIComponent.prototype.Bind = function(component) {
        var view = null;
        
        if(component === null || !(component instanceof Component)) {
            throw new Error('PropertyWindowUIComponent.Bind: component must be instance of "Component" and not be null');
        }
        
        if(this._$ui              === null || 
           this._$uiContent       === null || 
           this._$uiComponentName === null) { return; }
        
        this.Unbind();
        
        this._component = component;
        
        this._$uiContent.empty();
        this._$uiComponentName.text([' - ', this._component.GetName()].join(''));
        
        view = this._component.GetPropertyView();
        if(view !== null) { view.AppendTo(this._$uiContent); }
    };
    
    /**
     * Unbind component from property window
     */
    PropertyWindowUIComponent.prototype.Unbind = function() {
        this._component = null;
        
        if(this._$uiContent !== null)       { this._$uiContent.empty(); }
        if(this._$uiComponentName !== null) { this._$uiComponentName.empty(); }
    };
    
    /**
     * Append Property Window UI Component to container
     * 
     * @param {*} $container - UI component container
     */
    PropertyWindowUIComponent.prototype.AppendTo = function($container) { 
        if($container === null) { return; }
        
        this._$ui              = $(PropertyWindowUIComponent.UI_HTML).appendTo($container);
        this._$uiContent       = this._$ui.find('.content');
        this._$uiComponentName = this._$ui.find('.component-name');
    };
    
    return PropertyWindowUIComponent;
})(UIComponent);