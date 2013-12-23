//TODO: Need refactoring
var BlockComponent = (function(parent) {
    inherit(parent, BlockComponent);
    
    BlockComponent.UI_HTML = '<div id="@ID@"></div>';
    
    function BlockComponent() {
        BlockComponent.super.constructor.apply(this);
        
        this._model        = new BlockPropertyModel();
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
        this.Clear              = new ObservableProperty();
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
    };
    
    /**
     * Get block component name
     * 
     * @return {string} - block component name
     */
    BlockComponent.prototype.GetName = function() { return this._model.Name; };
    
    /**
     * Composite component is composite object
     * 
     * @return {Boolean} - true if component composite
     */
    BlockComponent.prototype.IsComposite = function() { return true; };
    
    /**
     * Get block property view
     * 
     * @return {PropertyView} - block property view with assign view model component
     */
    BlockComponent.prototype.GetPropertyView = function() { return new BlockPropertyView(this); };
    
    /**
     * Append block component to another component (component must be composite)
     * 
     * @param {Component} component - composite component
     */
    BlockComponent.prototype.AppendTo = function(component) { 
        var instance   = this,
            $container = null,
            $ui        = null;
        
        if(component === null || !(component instanceof Component)) {
            throw new Error('BlockComponent.AppendTo: component must be instance of "Component" and not be null');
        }

        BlockComponent.super.AppendTo.apply(this, [component]);
        
        if(!component.IsComposite()) { return; }
        
        $container = component.GetContainer();
        if($container === null) { return; }

        $ui = $(BlockComponent.UI_HTML.replace('@ID@', this.GetId())).appendTo($container);
        this.SetContainer($ui);
        
        this.SetProperty(this, {modelPropertyName: 'Width', cssPropertyName: 'width', propertyName: 'Width', newValue: '100%'});
        this.SetProperty(this, {modelPropertyName: 'Height', cssPropertyName: 'height', propertyName: 'Height', newValue: 30});
        this.SetProperty(this, {modelPropertyName: 'BorderSize', cssPropertyName: 'borderWidth', propertyName: 'BorderSize', newValue: 1});
        this.SetProperty(this, {modelPropertyName: 'BorderColor', cssPropertyName: 'borderColor', propertyName: 'BorderColor', newValue: '#504f4f'});
        this.SetProperty(this, {modelPropertyName: 'BorderType', cssPropertyName: 'borderStyle', propertyName: 'BorderType', newValue: 'dashed'});
        this.SetProperty(this, {modelPropertyName: 'Float', cssPropertyName: 'float', propertyName: 'Float', newValue: 'none'});
    };
    
    /**
     * Get component container
     * 
     * @return {*} - component container or null
     */
    BlockComponent.prototype.GetContainer = function() { return this._$uiContainer; };

    /**
     * Set component container
     *
     * @param {*} $container - container
     */
    BlockComponent.prototype.SetContainer = function($container) {
        var instance = this;

        this._$uiContainer = $container;

        this._$uiContainer.click(function(e) {
            instance.OnClick(instance, { component: instance, eventObject: e });
            ComponentsManager.GetInstance().DeselectAllComponents();
            instance.Select();

            return false;
        });

        this._$uiContainer.children('.ui-resizable-handle').remove();
        this._$uiContainer.resizable({ resize: function(event, ui) { instance._StopResize(event, ui); } });
    };
    
    /**
     * Select block component
     */
    BlockComponent.prototype.Select = function() {
        if(this._$uiContainer !== null) { this._$uiContainer.addClass('component-selected'); }
    };
    
    /**
     * Deselect block component
     */
    BlockComponent.prototype.Deselect = function() {
        if(this._$uiContainer !== null) { this._$uiContainer.removeClass('component-selected'); }
    };
    
    /**
     * Get property by name
     */
    BlockComponent.prototype.GetProperty = function(name) {
        return this._model.hasOwnProperty(name) ? this._model[name] 
                                                : null;
    };
    
    /**
     * Set propery for view model
     */
    BlockComponent.prototype.SetProperty = function(sender, args) {
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
     * Stop resize callback
     */
    BlockComponent.prototype._StopResize = function(event, ui) {
        this.SetProperty(this, {modelPropertyName: 'Width', cssPropertyName: 'width', properyName: 'Width', newValue: ui.size.width});
        this.SetProperty(this, {modelPropertyName: 'Height', cssPropertyName: 'height', properyName: 'Height', newValue: ui.size.height});
    };

    /**
     * Virtual function
     * Set  a data with the SerializationInfo
     *
     * @param {SerializationInfo} serializationInfo - The SerializationInfo with object data
     */
    BlockComponent.prototype.SetObjectData = function(serializationInfo) {
        BlockComponent.super.SetObjectData.apply(this, [serializationInfo]);
        this._model.SetObjectData(serializationInfo);
    };

    /**
     * Virtual function
     * Populates a SerializationInfo with the data needed to serialize the target object
     *
     * @param {SerializationInfo} serializationInfo - The SerializationInfo to populate with data
     */
    BlockComponent.prototype.GetObjectData = function(serializationInfo) {
        BlockComponent.super.GetObjectData.apply(this, [serializationInfo]);
        serializationInfo.AddValue('type', 'block');
        this._model.GetObjectData(serializationInfo);
    };

    return BlockComponent;
})(Component);