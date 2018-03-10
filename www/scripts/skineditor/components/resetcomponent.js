//TODO: Need refactoring
var ResetComponent = (function(parent) {
    inherit(parent, ResetComponent);

    ResetComponent.UI_HTML = '<div id="@ID@">{RESET}</div>';

    function ResetComponent() {
        ResetComponent.super.constructor.apply(this);

        this._$uiContainer = null;
    };

    /**
     * Get image component name
     *
     * @return {string} - block component name
     */
    ResetComponent.prototype.GetName = function() { return 'Reset component'; };

    ResetComponent.prototype.GetRelType = function() { return 'reset'; };

    /**
     * Get image property view
     *
     * @return {PropertyView} - image property view with assign view model component
     */
    ResetComponent.prototype.GetPropertyView = function() { return null; };

    /**
     * Append block component to another component (component must be composite)
     *
     * @param {Component} component - composite component
     */
    ResetComponent.prototype.AppendTo = function(component) {
        var instance   = this,
            $container = null,
            $ui        = null;

        if(component === null || !(component instanceof Component)) {
            throw new Error('ResetComponent.AppendTo: component must be instance of "Component" and not be null');
        }

        ResetComponent.super.AppendTo.apply(this, [component]);

        if(!component.IsComposite()) { return; }

        $container = component.GetContainer();
        if($container === null) { return; }

        $ui = $(ResetComponent.UI_HTML.replace('@ID@', this.GetId())).appendTo($container);
        this.SetContainer($ui);
    };

    /**
     * Get component container
     *
     * @return {*} - component container or null
     */
    ResetComponent.prototype.GetContainer = function() { return this._$uiContainer; };

    /**
     * Set component container
     *
     * @param {*} $container - container
     */
    ResetComponent.prototype.SetContainer = function($container) {
        var instance = this;

        this._$uiContainer = $container;

        this._$uiContainer.click(function(e) {
            instance.OnClick(instance, { component: instance, eventObject: e });
            ComponentsManager.GetInstance().DeselectAllComponents();
            instance.Select();

            return false;
        });
    };

    /**
     * Is composite object
     *
     * @return {Boolean} - true if component composite
     */
    ResetComponent.prototype.IsComposite = function() { return false; };

    /**
     * Select block component
     */
    ResetComponent.prototype.Select = function() {
        if(this._$uiContainer !== null) { this._$uiContainer.addClass('component-selected'); }
    };

    /**
     * Deselect block component
     */
    ResetComponent.prototype.Deselect = function() {
        if(this._$uiContainer !== null) { this._$uiContainer.removeClass('component-selected'); }
    };

    /**
     * Virtual function
     * Set  a data with the SerializationInfo
     *
     * @param {SerializationInfo} serializationInfo - The SerializationInfo with object data
     */
    ResetComponent.prototype.SetObjectData = function(serializationInfo) {
        ResetComponent.super.SetObjectData.apply(this, [serializationInfo]);
    };

    /**
     * Virtual function
     * Populates a SerializationInfo with the data needed to serialize the target object
     *
     * @param {SerializationInfo} serializationInfo - The SerializationInfo to populate with data
     */
    ResetComponent.prototype.GetObjectData = function(serializationInfo) {
        ResetComponent.super.GetObjectData.apply(this, [serializationInfo]);
        serializationInfo.AddValue('type', 'reset');
    };

    return ResetComponent;
})(Component);