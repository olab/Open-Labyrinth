//TODO: Need refactoring
var ImageComponent = (function(parent) {
    inherit(parent, ImageComponent);
    
    ImageComponent.UI_HTML = '<img id="@ID@"/>';
    
    function ImageComponent() {
        ImageComponent.super.constructor.apply(this);
        
        this._model        = new ImagePropertyModel();
        this._$uiContainer = null;
        
        /**
         * Properties of view model
         */
        this.Width              = new ObservableProperty();
        this.MinWidth           = new ObservableProperty();
        this.MaxWidth           = new ObservableProperty();
        this.Height             = new ObservableProperty();
        this.MinHeight          = new ObservableProperty();
        this.MaxHeight          = new ObservableProperty();
        this.BorderSize         = new ObservableProperty();
        this.BorderColor        = new ObservableProperty();
        this.BorderType         = new ObservableProperty();
        this.BorderRadius       = new ObservableProperty();
        this.Float              = new ObservableProperty();
        this.MarginTop          = new ObservableProperty();
        this.MarginRight        = new ObservableProperty();
        this.MarginBottom       = new ObservableProperty();
        this.MarginLeft         = new ObservableProperty();
        this.Position           = new ObservableProperty();
        this.Left               = new ObservableProperty();
        this.Top                = new ObservableProperty();
        this.Right              = new ObservableProperty();
        this.Bottom             = new ObservableProperty();
        this.Src                = new ObservableProperty();
    };
    
    /**
     * Get image component name
     * 
     * @return {string} - block component name
     */
    ImageComponent.prototype.GetName = function() { return this._model.Name; };
    
    /**
     * Get image property view
     * 
     * @return {PropertyView} - image property view with assign view model component
     */
    ImageComponent.prototype.GetPropertyView = function() { return new ImagePropertyView(this); };
    
    /**
     * Append block component to another component (component must be composite)
     * 
     * @param {Component} component - composite component
     */
    ImageComponent.prototype.AppendTo = function(component) { 
        var instance   = this,
            $container = null,
            $ui        = null;
        
        if(component === null || !(component instanceof Component)) {
            throw new Error('ImageComponent.AppendTo: component must be instance of "Component" and not be null');
        }

        ImageComponent.super.AppendTo.apply(this, [component]);
        
        if(!component.IsComposite()) { return; }
        
        $container = component.GetContainer();
        if($container === null) { return; }

        $ui = $(ImageComponent.UI_HTML.replace('@ID@', this.GetId())).appendTo($container);
        this.SetContainer($ui);
        
        this.SetProperty(this, {modelPropertyName: 'Width', cssPropertyName: 'width', properyName: 'Width', newValue: '100'});
        this.SetProperty(this, {modelPropertyName: 'Height', cssPropertyName: 'height', properyName: 'Height', newValue: 'auto'});
        this.SetProperty(this, {modelPropertyName: 'Float', cssPropertyName: 'float', properyName: 'Float', newValue: 'none'});
        this.SetSrc(this._model.Src);
    };
    
    /**
     * Get component container
     * 
     * @return {*} - component container or null
     */
    ImageComponent.prototype.GetContainer = function() { return this._$uiContainer; };

    /**
     * Set component container
     *
     * @param {*} $container - container
     */
    ImageComponent.prototype.SetContainer = function($container) {
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
    ImageComponent.prototype.IsComposite = function() { return false; };
    
    /**
     * Select block component
     */
    ImageComponent.prototype.Select = function() {
        if(this._$uiContainer !== null) { this._$uiContainer.addClass('component-selected'); }
    };
    
    /**
     * Deselect block component
     */
    ImageComponent.prototype.Deselect = function() {
        if(this._$uiContainer !== null) { this._$uiContainer.removeClass('component-selected'); }
    };
    
    /**
     * Get property by name
     */
    ImageComponent.prototype.GetProperty = function(name) {
        return this._model.hasOwnProperty(name) ? this._model[name] 
                                                : null;
    };
    
    ImageComponent.prototype.SetSrc = function(src) {
        this._model.Src = src;
        this._$uiContainer.attr('src', src);
    };
    
    /**
     * Set propery for view model
     */
    ImageComponent.prototype.SetProperty = function(sender, args) {
        if(args === null || !('modelPropertyName' in args)) { return; }
        
        if(this._model.hasOwnProperty(args['modelPropertyName']) && 'newValue' in args) {
            this._model[args['modelPropertyName']] = args['newValue'];
            
            if(this._$uiContainer !== null && 'cssPropertyName' in args) { 
                var object = JSON.parse(['{"', args['cssPropertyName'], '":"', this._model[args['modelPropertyName']], '"}'].join(''));
                this._$uiContainer.css(object); 
            }
            
            if('properyName' in args && this.hasOwnProperty(args['properyName'])) {
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
    ImageComponent.prototype.SetObjectData = function(serializationInfo) {
        ImageComponent.super.SetObjectData.apply(this, [serializationInfo]);
        this._model.SetObjectData(serializationInfo);
    };

    /**
     * Virtual function
     * Populates a SerializationInfo with the data needed to serialize the target object
     *
     * @param {SerializationInfo} serializationInfo - The SerializationInfo to populate with data
     */
    ImageComponent.prototype.GetObjectData = function(serializationInfo) {
        ImageComponent.super.GetObjectData.apply(this, [serializationInfo]);
        serializationInfo.AddValue('type', 'image');
        this._model.GetObjectData(serializationInfo);
    };

    return ImageComponent;
})(Component);