//TODO: Need refactoring
var NodeTitleComponent = (function(parent) {
    inherit(parent, NodeTitleComponent);
    
    NodeTitleComponent.UI_HTML = '<div id="@ID@">{NODE_TITLE}</div>';
    
    function NodeTitleComponent() {
        NodeTitleComponent.super.constructor.apply(this);
        
        this._model        = new NodeTitlePropertyModel();
        this._$uiContainer = null;
        
        /**
         * Properties of view model
         */
        this.Name               = new ObservableProperty();
        this.BorderSize         = new ObservableProperty();
        this.BorderColor        = new ObservableProperty();
        this.BorderType         = new ObservableProperty();
        this.BorderRadius       = new ObservableProperty();
        this.Float              = new ObservableProperty();
        this.Clear              = new ObservableProperty();
        this.Align              = new ObservableProperty();
        this.FontFamily         = new ObservableProperty();
        this.FontSize           = new ObservableProperty();
        this.FontWeight         = new ObservableProperty();
        this.FontColor          = new ObservableProperty();
        this.MarginTop          = new ObservableProperty();
        this.MarginRight        = new ObservableProperty();
        this.MarginBottom       = new ObservableProperty();
        this.MarginLeft         = new ObservableProperty();
        this.Position           = new ObservableProperty();
        this.Left               = new ObservableProperty();
        this.Top                = new ObservableProperty();
        this.Right              = new ObservableProperty();
        this.Bottom             = new ObservableProperty();
    };
    
    /**
     * Get image component name
     * 
     * @return {string} - block component name
     */
    NodeTitleComponent.prototype.GetName = function() { return this._model.Name; };

    NodeTitleComponent.prototype.GetRelType = function() { return 'node_title'; };
    
    /**
     * Get image property view
     * 
     * @return {PropertyView} - image property view with assign view model component
     */
    NodeTitleComponent.prototype.GetPropertyView = function() { return new NodeTitlePropertyView(this); };
    
    /**
     * Append block component to another component (component must be composite)
     * 
     * @param {Component} component - composite component
     */
    NodeTitleComponent.prototype.AppendTo = function(component) { 
        var instance   = this,
            $container = null,
            $ui        = null;
        
        if(component === null || !(component instanceof Component)) {
            throw new Error('ImageComponent.AppendTo: component must be instance of "Component" and not be null');
        }

        NodeTitleComponent.super.AppendTo.apply(this, [component]);
        
        if(!component.IsComposite()) { return; }
        
        $container = component.GetContainer();
        if($container === null) { return; }
        
        $ui = $(NodeTitleComponent.UI_HTML.replace('@ID@', this.GetId())).appendTo($container);
        this.SetContainer($ui);
        
        this._$uiContainer.css({width: 'auto', height: 'auto'});
        //this._$uiContainer.sortable({ revert: true });
    };
    
    /**
     * Get component container
     * 
     * @return {*} - component container or null
     */
    NodeTitleComponent.prototype.GetContainer = function() { return this._$uiContainer; };

    /**
     * Set component container
     *
     * @param {*} $container - container
     */
    NodeTitleComponent.prototype.SetContainer = function($container) {
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
    NodeTitleComponent.prototype.IsComposite = function() { return false; };
    
    /**
     * Select block component
     */
    NodeTitleComponent.prototype.Select = function() {
        if(this._$uiContainer !== null) { this._$uiContainer.addClass('component-selected'); }
    };
    
    /**
     * Deselect block component
     */
    NodeTitleComponent.prototype.Deselect = function() {
        if(this._$uiContainer !== null) { this._$uiContainer.removeClass('component-selected'); }
    };
    
    /**
     * Get property by name
     */
    NodeTitleComponent.prototype.GetProperty = function(name) {
        return this._model.hasOwnProperty(name) ? this._model[name] 
                                                : null;
    };
    
    /**
     * Set propery for view model
     */
    NodeTitleComponent.prototype.SetProperty = function(sender, args) {
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
    NodeTitleComponent.prototype.SetObjectData = function(serializationInfo) {
        NodeTitleComponent.super.SetObjectData.apply(this, [serializationInfo]);
        this._model.SetObjectData(serializationInfo);
    };

    /**
     * Virtual function
     * Populates a SerializationInfo with the data needed to serialize the target object
     *
     * @param {SerializationInfo} serializationInfo - The SerializationInfo to populate with data
     */
    NodeTitleComponent.prototype.GetObjectData = function(serializationInfo) {
        NodeTitleComponent.super.GetObjectData.apply(this, [serializationInfo]);
        serializationInfo.AddValue('type', 'nodetitle');
        this._model.GetObjectData(serializationInfo);
    };

    return NodeTitleComponent;
})(Component);