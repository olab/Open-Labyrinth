//TODO: Need refactoring
var BookmarkComponent = (function(parent) {
    inherit(parent, BookmarkComponent);

    BookmarkComponent.UI_HTML = '<div id="@ID@">{BOOKMARK}</div>';

    function BookmarkComponent() {
        BookmarkComponent.super.constructor.apply(this);

        this._$uiContainer = null;
    };

    /**
     * Get image component name
     *
     * @return {string} - block component name
     */
    BookmarkComponent.prototype.GetName = function() { return 'Bookmark component'; };

    BookmarkComponent.prototype.GetRelType = function() { return 'bookmark'; };

    /**
     * Get image property view
     *
     * @return {PropertyView} - image property view with assign view model component
     */
    BookmarkComponent.prototype.GetPropertyView = function() { return null; };

    /**
     * Append block component to another component (component must be composite)
     *
     * @param {Component} component - composite component
     */
    BookmarkComponent.prototype.AppendTo = function(component) {
        var instance   = this,
            $container = null,
            $ui        = null;

        if(component === null || !(component instanceof Component)) {
            throw new Error('BookmarkComponent.AppendTo: component must be instance of "Component" and not be null');
        }

        BookmarkComponent.super.AppendTo.apply(this, [component]);

        if(!component.IsComposite()) { return; }

        $container = component.GetContainer();
        if($container === null) { return; }

        $ui = $(BookmarkComponent.UI_HTML.replace('@ID@', this.GetId())).appendTo($container);
        this.SetContainer($ui);
    };

    /**
     * Get component container
     *
     * @return {*} - component container or null
     */
    BookmarkComponent.prototype.GetContainer = function() { return this._$uiContainer; };

    /**
     * Set component container
     *
     * @param {*} $container - container
     */
    BookmarkComponent.prototype.SetContainer = function($container) {
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
    BookmarkComponent.prototype.IsComposite = function() { return false; };

    /**
     * Select block component
     */
    BookmarkComponent.prototype.Select = function() {
        if(this._$uiContainer !== null) { this._$uiContainer.addClass('component-selected'); }
    };

    /**
     * Deselect block component
     */
    BookmarkComponent.prototype.Deselect = function() {
        if(this._$uiContainer !== null) { this._$uiContainer.removeClass('component-selected'); }
    };

    /**
     * Virtual function
     * Set  a data with the SerializationInfo
     *
     * @param {SerializationInfo} serializationInfo - The SerializationInfo with object data
     */
    BookmarkComponent.prototype.SetObjectData = function(serializationInfo) {
        BookmarkComponent.super.SetObjectData.apply(this, [serializationInfo]);
    };

    /**
     * Virtual function
     * Populates a SerializationInfo with the data needed to serialize the target object
     *
     * @param {SerializationInfo} serializationInfo - The SerializationInfo to populate with data
     */
    BookmarkComponent.prototype.GetObjectData = function(serializationInfo) {
        BookmarkComponent.super.GetObjectData.apply(this, [serializationInfo]);
        serializationInfo.AddValue('type', 'bookmark');
    };

    return BookmarkComponent;
})(Component);