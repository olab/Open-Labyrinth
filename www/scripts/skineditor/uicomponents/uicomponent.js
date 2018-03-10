/**
 * Basic UI Component class
 */
var UIComponent = (function(parent) {
    inherit(parent, UIComponent);
    
    /**
     * Default constructor
     * 
     * @param {SkinEditor} skinEditor - skin editor
     */
    function UIComponent(skinEditor) {
        UIComponent.super.constructor.apply(this);
        
        this._skinEditor = skinEditor;
    };
    
    /**
     * Get sking editor
     * 
     * @return {SkinEditor} - skin editor
     */
    UIComponent.prototype.GetSkinEditor = function() { return this._skinEditor; };
    
    /**
     * Append UI Component to container
     * 
     * @param {*} $container - UI component container
     */
    UIComponent.prototype.AppendTo = function($container) { };
    
    return UIComponent;
})(UniqueObject);