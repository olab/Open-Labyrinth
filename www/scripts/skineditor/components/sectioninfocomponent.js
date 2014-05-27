var SectionInfoComponent = (function(parent){
    inherit(parent, SectionInfoComponent);

    SectionInfoComponent.UI_HTML = '<div id="@ID@">{SECTION}</div>';

    function SectionInfoComponent() {
        SectionInfoComponent.super.constructor.apply(this);

        this._model        = new SectionInfoPropertyModel();
        this._$uiContainer = null;

        /**
         * Properties of view model
         */
        this.BorderSize         = new ObservableProperty();
        this.BorderColor        = new ObservableProperty();
        this.BorderType         = new ObservableProperty();
        this.BorderRadius       = new ObservableProperty();
        this.Float              = new ObservableProperty();
        this.Clear              = new ObservableProperty();
        this.FontFamily         = new ObservableProperty();
        this.FontSize           = new ObservableProperty();
        this.FontWeight         = new ObservableProperty();
        this.FontColor          = new ObservableProperty();
        this.BackgroundColor    = new ObservableProperty();
        this.BackgroundURL      = new ObservableProperty();
        this.BackgroundRepeat   = new ObservableProperty();
        this.BackgroundPosition = new ObservableProperty();
        this.BackgroundSize     = new ObservableProperty();
        this.MarginTop          = new ObservableProperty();
        this.MarginRight        = new ObservableProperty();
        this.MarginBottom       = new ObservableProperty();
        this.MarginLeft         = new ObservableProperty();
        this.PaddingTop         = new ObservableProperty();
        this.PaddingRight       = new ObservableProperty();
        this.PaddingBottom      = new ObservableProperty();
        this.PaddingLeft        = new ObservableProperty();
        this.Align              = new ObservableProperty();
        this.Position           = new ObservableProperty();
        this.Left               = new ObservableProperty();
        this.Top                = new ObservableProperty();
        this.Right              = new ObservableProperty();
        this.Bottom             = new ObservableProperty();
    }

    /**
     * Get block component name
     *
     * @return {string} - block component name
     */
    SectionInfoComponent.prototype.GetName = function() { return this._model.Name; };

    SectionInfoComponent.prototype.GetRelType = function() { return 'section'; };

    /**
     * Composite component is composite object
     *
     * @return {Boolean} - true if component composite
     */
    SectionInfoComponent.prototype.IsComposite = function() { return false; };

    /**
     * Get block property view
     *
     * @return {PropertyView} - block property view with assign view model component
     */
    SectionInfoComponent.prototype.GetPropertyView = function() { return new SectionInfoPropertyView(this); };

    /**
     * Append block component to another component (component must be composite)
     *
     * @param {Component} component - composite component
     */
    SectionInfoComponent.prototype.AppendTo = function(component) {
        if(component === null || !(component instanceof Component)) {
            throw new Error('SectionInfoComponent.AppendTo: component must be instance of "Component" and not be null');
        }

        SectionInfoComponent.super.AppendTo.apply(this, [component]);

        if(!component.IsComposite()) { return; }

        var $container = component.GetContainer();
        if($container === null) { return; }

        var $ui = $(SectionInfoComponent.UI_HTML.replace('@ID@', this.GetId())).appendTo($container);
        this.SetContainer($ui);
    };

    /**
     * Get component container
     *
     * @return {*} - component container or null
     */
    SectionInfoComponent.prototype.GetContainer = function() { return this._$uiContainer; };

    /**
     * Set component container
     *
     * @param {*} $container - container
     */
    SectionInfoComponent.prototype.SetContainer = function($container) {
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
     * Select block component
     */
    SectionInfoComponent.prototype.Select = function() {
        if(this._$uiContainer !== null) { this._$uiContainer.addClass('component-selected'); }
    };

    /**
     * Deselect block component
     */
    SectionInfoComponent.prototype.Deselect = function() {
        if(this._$uiContainer !== null) { this._$uiContainer.removeClass('component-selected'); }
    };

    /**
     * Get property by name
     */
    SectionInfoComponent.prototype.GetProperty = function(name) {
        return this._model.hasOwnProperty(name) ? this._model[name]
            : null;
    };

    /**
     * Set propery for view model
     */
    SectionInfoComponent.prototype.SetProperty = function(sender, args) {
        if(args === null || !('modelPropertyName' in args)) { return; }

        if(this._model.hasOwnProperty(args['modelPropertyName']) && 'newValue' in args) {
            this._model[args['modelPropertyName']] = args['newValue'];

            if(this._$uiContainer !== null && 'cssPropertyName' in args) {
                var object = JSON.parse(['{"', args['cssPropertyName'], '":"', this._model[args['modelPropertyName']], '"}'].join(''));
                this._$uiContainer.css(object);
            }

            if('properyName' in args && this.hasOwnProperty(args['propertyName'])) {
                this[args['properyName']].PropertyChanged(sender, args);
            }
        }
    };

    /**
     * Virtual function
     * Set  a data with the SerializationInfo
     *
     * @param {SerializationInfo} serializationInfo - The SerializationInfo with object data
     */
    SectionInfoComponent.prototype.SetObjectData = function(serializationInfo) {
        SectionInfoComponent.super.SetObjectData.apply(this, [serializationInfo]);
        this._model.SetObjectData(serializationInfo);
    };

    /**
     * Virtual function
     * Populates a SerializationInfo with the data needed to serialize the target object
     *
     * @param {SerializationInfo} serializationInfo - The SerializationInfo to populate with data
     */
    SectionInfoComponent.prototype.GetObjectData = function(serializationInfo) {
        SectionInfoComponent.super.GetObjectData.apply(this, [serializationInfo]);
        serializationInfo.AddValue('type', 'mapinfo');
        this._model.GetObjectData(serializationInfo);
    };

    return SectionInfoComponent;
})(Component);