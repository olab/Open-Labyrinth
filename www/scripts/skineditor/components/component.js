/**
 * Basic component class
 */
var Component = (function(parent) {
    inherit(parent, Component);
    
    /**
     * Default constructor
     * 
     * @constructor
     */
    function Component() { 
        Component.super.constructor.apply(this);

        this._parentId = null;
        this._onClick  = new CallbackChain();
    };

    /**
     * Set parent id
     *
     * @param {string} id - parent id
     */
    Component.prototype.SetParentId = function(id) { this._parentId = id; };

    /**
     * Get parent ID
     *
     * @returns {string} - parent id
     */
    Component.prototype.GetParentId = function() { return this._parentId; };
    
    /**
     * Get component name
     * 
     * @return {string} - component name
     */
    Component.prototype.GetName = function() { return null; };
    
    /**
     * Get property view
     * 
     * @return {PropertyView} - component property view with assign view model component
     */
    Component.prototype.GetPropertyView = function() { return null; };
    
    /**
     * Append component to another component
     * 
     * @param {Component} component - composite component
     */
    Component.prototype.AppendTo = function(component) {
        if(component !== null && component instanceof Component) { this.SetParentId(component.GetId()); }
    };

    Component.prototype.Remove = function() {
        var $container = this.GetContainer();
        if($container !== null) { $container.remove(); }
    };

    Component.prototype.AppendAfter = function(component) {
        var $container       = this.GetContainer(),
            $parentContainer = component.GetContainer();

        if($container !== null && $parentContainer !== null) {
            $container.insertAfter($parentContainer);
        }
    };

    Component.prototype.AppendBefore = function(component) {
        var $container       = this.GetContainer(),
            $parentContainer = component.GetContainer();

        if($container !== null && $parentContainer !== null) {
            $container.insertBefore($parentContainer);
        }
    };
    
    /**
     * Move component to another component
     * 
     * @param {Component} component - composite component
     */
    Component.prototype.MoveTo = function(component) { 
        if(component === null || 
           !(component instanceof Component) ||
           !component.IsComposite()) {
            return;
        }
        
        var container       = this.GetContainer(),
            parentContainer = component.GetContainer();
        
        if(container !== null && parentContainer !== null) {
            this.SetParentId(component.GetId());
            container.appendTo(parentContainer);
            /*parentContainer.sortable({ revert: true });*/
        }
    };
    
    /**
     * Is composite object
     * 
     * @return {Boolean} - true if component composite
     */
    Component.prototype.IsComposite = function() { return false; };
    
    /**
     * Get component container
     * 
     * @return {*} - component container or null
     */
    Component.prototype.GetContainer = function() { return null; };

    /**
     * Get component rel type
     *
     * @returns {string} - rel type
     */
    Component.prototype.GetRelType = function() { return ''; };

    /**
     * Set component container
     *
     * @param {*} $container - container
     */
    Component.prototype.SetContainer = function($container) { };
    
    /**
     * Select component
     */
    Component.prototype.Select = function() { };
    
    /**
     * Deselect component
     */
    Component.prototype.Deselect = function() { };
    
    /**
     * EVENTS
     */
    
    /**
     * Event for subsribe by on click event
     * 
     * @param {Callback} callback - subscribe callback
     */
    Component.prototype.AddOnClickCallback = function(callback) {
        if(callback === null || !(callback instanceof Callback)) {
            throw new Error('Component.OnClick: callback must be instance of "Callback" and not be null');
        }
        
        this._onClick.AddCallback(callback);
    };
    
    /**
     * On click event
     * 
     * @param {*} sender - sender of event
     * @param {*} args - event args
     */
    Component.prototype.OnClick = function(sender, args) {
        if(this._onClick === null || sender === null || !(sender instanceof UniqueObject)) { return; }
        
        var enumerator = this._onClick.GetEnumerator(),
            item       = null;
            
        if(enumerator === null) { return; }
        
        while(enumerator.MoveNext()) {
            item = enumerator.GetCurrentItem();
            if(item !== null) { item.Call(sender, args); }
        }
    };
    
    /**
     * Virtual function
     * Populates a SerializationInfo with the data needed to serialize the target object
     *
     * @param {SerializationInfo} serializationInfo - The SerializationInfo to populate with data
     */
    Component.prototype.GetObjectData = function(serializationInfo) {
        serializationInfo.AddValue('id', this.GetId());
        serializationInfo.AddValue('parentId', this.GetParentId());
    };

    /**
     * Virtual function
     * Set  a data with the SerializationInfo
     *
     * @param {SerializationInfo} serializationInfo - The SerializationInfo with object data
     */
    Component.prototype.SetObjectData = function(serializationInfo) {
        this.SetId(serializationInfo.GetValue('id'));
        this.SetParentId(serializationInfo.GetValue('parentId'));
    };
    
    return Component;
})(UniqueObject);