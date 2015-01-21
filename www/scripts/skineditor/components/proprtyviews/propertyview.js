var PropertyView = (function(parent) {
    inherit(parent, PropertyView);
    
    /**
     * Default constructor
     */
    function PropertyView() { 
        PropertyView.super.constructor.apply(this);
    };
    
    /**
     * Appent property view to container
     * 
     * @param {*} $container - append container
     */
    PropertyView.prototype.AppendTo = function($container) { };
    
    return PropertyView;
})(UniqueObject);