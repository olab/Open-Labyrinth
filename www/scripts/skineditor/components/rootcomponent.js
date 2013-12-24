/**
 * Root component class
 */
var RootComponent = (function(parent) {
    inherit(parent, RootComponent);

    RootComponent.UI_HTML = '<div class="skin-editor-content-container"></div>';
    
    /**
     * Default constructor
     * 
     * @param {*} $container - root container
     * @constructor
     */
    function RootComponent($container) {
        RootComponent.super.constructor.apply(this);

        this._$uiContainer = $container;
        this._$ui          = $('body');

        this._model        = new RootPropertyModel();

        this.Name               = new ObservableProperty();
        this.FontFamily         = new ObservableProperty();
        this.FontSize           = new ObservableProperty();
        this.FontWeight         = new ObservableProperty();
        this.FontColor          = new ObservableProperty();
        this.BackgroundColor    = new ObservableProperty();
        this.BackgroundURL      = new ObservableProperty();
        this.BackgroundRepeat   = new ObservableProperty();
        this.BackgroundPosition = new ObservableProperty();
        this.BackgroundSize     = new ObservableProperty();
    };
    
    /**
     * Return root name
     * 
     * @return {string} - root component name
     */
    RootComponent.prototype.GetName = function() { return 'Root'; };
    
    /**
     * Return that root is composite object
     * 
     * @return {Boolean} - root is composite object
     */
    RootComponent.prototype.IsComposite = function() { return true; };

    /**
     * Get block property view
     *
     * @return {PropertyView} - block property view with assign view model component
     */
    RootComponent.prototype.GetPropertyView = function() { return new RootPropertyView(this); };
    
    /**
     * Return root container
     * 
     * @return {*} - root container
     */
    RootComponent.prototype.GetContainer = function() { return this._$uiContainer; };

    RootComponent.prototype.SetObjectData = function(serializationInfo) {
        RootComponent.super.SetObjectData.apply(this, [serializationInfo]);
        this._model.SetObjectData(serializationInfo);
        this.SetProperty(this, { viewModelPropertyName: 'BackgroundColor',
                                     modelPropertyName: 'BackgroundColor',
                                              newValue: this._model.BackgroundColor,
                                       cssPropertyName: 'background-color'});
        this.SetProperty(this, { viewModelPropertyName: 'BackgroundRepeat',
            modelPropertyName: 'BackgroundRepeat',
            newValue: this._model.BackgroundRepeat,
            cssPropertyName: 'background-repeat'});
        this.SetProperty(this, { viewModelPropertyName: 'BackgroundSize',
            modelPropertyName: 'BackgroundSize',
            newValue: this._model.BackgroundSize,
            cssPropertyName: 'background-size'});
        this.SetProperty(this, { viewModelPropertyName: 'BackgroundPosition',
            modelPropertyName: 'BackgroundPosition',
            newValue: this._model.BackgroundPosition,
            cssPropertyName: 'background-position'});
        this.SetProperty(this, { viewModelPropertyName: 'FontFamily',
            modelPropertyName: 'FontFamily',
            newValue: this._model.FontFamily,
            cssPropertyName: 'font-family'});
        this.SetProperty(this, { viewModelPropertyName: 'FontSize',
            modelPropertyName: 'FontSize',
            newValue: this._model.FontSize,
            cssPropertyName: 'font-size'});
    };

    RootComponent.prototype.GetObjectData = function(serializationInfo) {
        RootComponent.super.GetObjectData.apply(this, [serializationInfo]);
        serializationInfo.AddValue('type', 'root');
        this._model.GetObjectData(serializationInfo);
    };

    /**
     * Get property by name
     */
    RootComponent.prototype.GetProperty = function(name) {
        return this._model.hasOwnProperty(name) ? this._model[name]
                                                : null;
    };

    /**
     * Set propery for view model
     */
    RootComponent.prototype.SetProperty = function(sender, args) {
        if(args === null || !('modelPropertyName' in args)) { return; }

        if(this._model.hasOwnProperty(args['modelPropertyName']) && 'newValue' in args) {
            this._model[args['modelPropertyName']] = args['newValue'];

            if(this._$ui !== null && 'cssPropertyName' in args) {
                var object = JSON.parse(['{"', args['cssPropertyName'], '":"', this._model[args['modelPropertyName']], '"}'].join(''));
                this._$ui.css(object);
            }
        }
    };
    
    return RootComponent;
})(Component);