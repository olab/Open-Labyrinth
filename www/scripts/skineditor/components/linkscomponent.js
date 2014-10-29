//TODO: Need refactoring
var LinksComponent = (function(parent) {
    inherit(parent, LinksComponent);

    LinksComponent.UI_HTML = '<div id="@ID@">{LINKS}</div>';

    function LinksComponent() {
        LinksComponent.super.constructor.apply(this);

        this._model        = new LinksPropertyModel();
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
        this.ButtonColor1       = new ObservableProperty();
        this.ButtonColor2       = new ObservableProperty();
        this.ButtonFontColor    = new ObservableProperty();
    };

    /**
     * Get block component name
     *
     * @return {string} - block component name
     */
    LinksComponent.prototype.GetName = function() { return this._model.Name; };

    LinksComponent.prototype.GetRelType = function() { return 'links'; };

    /**
     * Composite component is composite object
     *
     * @return {Boolean} - true if component composite
     */
    LinksComponent.prototype.IsComposite = function() { return false; };

    /**
     * Get block property view
     *
     * @return {PropertyView} - block property view with assign view model component
     */
    LinksComponent.prototype.GetPropertyView = function() { return new LinksPropertyView(this); };

    /**
     * Append block component to another component (component must be composite)
     *
     * @param {Component} component - composite component
     */
    LinksComponent.prototype.AppendTo = function(component) {
        var instance   = this,
            $container = null,
            $ui        = null;

        if(component === null || !(component instanceof Component)) {
            throw new Error('LinksComponent.AppendTo: component must be instance of "Component" and not be null');
        }

        LinksComponent.super.AppendTo.apply(this, [component]);

        if(!component.IsComposite()) { return; }

        $container = component.GetContainer();
        if($container === null) { return; }

        $ui = $(LinksComponent.UI_HTML.replace('@ID@', this.GetId())).appendTo($container);
        this.SetContainer($ui);
    };

    /**
     * Get component container
     *
     * @return {*} - component container or null
     */
    LinksComponent.prototype.GetContainer = function() { return this._$uiContainer; };

    /**
     * Set component container
     *
     * @param {*} $container - container
     */
    LinksComponent.prototype.SetContainer = function($container) {
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
    LinksComponent.prototype.Select = function() {
        if(this._$uiContainer !== null) { this._$uiContainer.addClass('component-selected'); }
    };

    /**
     * Deselect block component
     */
    LinksComponent.prototype.Deselect = function() {
        if(this._$uiContainer !== null) { this._$uiContainer.removeClass('component-selected'); }
    };

    /**
     * Get property by name
     */
    LinksComponent.prototype.GetProperty = function(name) {
        return this._model.hasOwnProperty(name) ? this._model[name]
            : null;
    };

    /**
     * Set propery for view model
     */
    LinksComponent.prototype.SetProperty = function(sender, args) {
        if(args === null || !('modelPropertyName' in args)) { return; }

        if(this._model.hasOwnProperty(args['modelPropertyName']) && 'newValue' in args) {
            this._model[args['modelPropertyName']] = args['newValue'];

            if(this._$uiContainer !== null && 'cssPropertyName' in args) {
                if(args['cssPropertyName'] == 'btn') {
                    this._$uiContainer.find('style').remove();
                    $('<style>' +
                        '#' + this.GetId() + ' .btn { color: ' + this._model['ButtonFontColor'] + ';' +
                        'background-image: -moz-linear-gradient(top, ' + this._model['ButtonColor2'] +', ' + this._model['ButtonColor1'] +');' +
                        'background-image: -webkit-gradient(linear, 0 0, 0 100%, from(' + this._model['ButtonColor2'] +'), to(' + this._model['ButtonColor1'] +'));' +
                        'background-image: -webkit-linear-gradient(top, ' + this._model['ButtonColor1'] +', ' + this._model['ButtonColor1'] +');' +
                        'background-image: -o-linear-gradient(top, ' + this._model['ButtonColor2'] +', ' + this._model['ButtonColor1'] +');' +
                        'background-image: linear-gradient(to bottom, ' + this._model['ButtonColor2'] +', ' + this._model['ButtonColor1'] +');} ' +

                        '#' + this.GetId() + ' .btn:hover {color: ' + this._model['ButtonFontColor'] + ';' +
                        'background-image: -moz-linear-gradient(top, ' + this._model['ButtonColor2'] + ', #e6e6e6);' +
                        'background-image: -webkit-gradient(linear, 0 0, 0 100%, from(' + this._model['ButtonColor2'] + '), to(#e6e6e6));' +
                        'background-image: -webkit-linear-gradient(top, ' + this._model['ButtonColor2'] + ', #e6e6e6);' +
                        'background-image: -o-linear-gradient(top, ' + this._model['ButtonColor2'] + ', #e6e6e6);' +
                        'background-image: linear-gradient(to bottom, ' + this._model['ButtonColor2'] + ', #e6e6e6);' +
                    '</style>').appendTo(this._$uiContainer);
                } else {
                    var object = JSON.parse(['{"', args['cssPropertyName'], '":"', this._model[args['modelPropertyName']], '"}'].join(''));
                    this._$uiContainer.css(object);
                }
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
    LinksComponent.prototype.SetObjectData = function(serializationInfo) {
        LinksComponent.super.SetObjectData.apply(this, [serializationInfo]);
        this._model.SetObjectData(serializationInfo);
    };

    /**
     * Virtual function
     * Populates a SerializationInfo with the data needed to serialize the target object
     *
     * @param {SerializationInfo} serializationInfo - The SerializationInfo to populate with data
     */
    LinksComponent.prototype.GetObjectData = function(serializationInfo) {
        LinksComponent.super.GetObjectData.apply(this, [serializationInfo]);
        serializationInfo.AddValue('type', 'links');
        this._model.GetObjectData(serializationInfo);
    };

    return LinksComponent;
})(Component);