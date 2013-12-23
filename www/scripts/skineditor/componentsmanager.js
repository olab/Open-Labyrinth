/**
 * Components manager class
 */
var ComponentsManager = (function() {
    /**
     * Default constructor
     * 
     * @constructor
     */
    function ComponentsManager() {
        this._components    = [];
        this._rootComponent = null;
    };
    
    ComponentsManager._instance = null;
    
    /**
     * Get current instance of manager
     * 
     * @return {ComponentsManager} - instance of components manager
     */
    ComponentsManager.GetInstance = function GetInstance() {
        if(ComponentsManager._instance === null) {
            ComponentsManager._instance = new ComponentsManager();
        }
        
        return ComponentsManager._instance;
    };
    
    /**
     * Get components list
     * { name, title, icon }
     * 
     * @return {array} - components list array
     */
    ComponentsManager.prototype.GetComponentsList = function() {
        return [
            { name: 'block',             title: 'Block Component',              icon: '' },
            { name: 'image',             title: 'Image Component',              icon: '' },
            { name: 'nodetitle',         title: 'Node Title Component',         icon: '' },
            { name: 'nodecontent',       title: 'Node Content Component',       icon: '' },
            { name: 'counterscontainer', title: 'Counters Container Component', icon: '' },
            { name: 'links',             title: 'Links Component',              icon: '' },
            { name: 'review',            title: 'Review Component',             icon: '' },
            { name: 'mapinfo',           title: 'Map Info Component',           icon: '' },
            { name: 'bookmark',          title: 'Bookmark Component',           icon: '' }
        ];
    };

    ComponentsManager.prototype.GetComponents = function() { return this._components; };
    
    /**
     * Return component by ID
     * 
     * @param {string} componentId - component ID
     * @return {Component} - component or null
     */
    ComponentsManager.prototype.GetComponentById = function(componentId) {
        var result = null;
        
        if(componentId === null) { return result; }
        
        for(var i = this._components.length; i--;) {
            if(this._components[i].GetId() === componentId) {
                result = this._components[i];
            }
        }
        
        return result;
    };
    
    /**
     * Create root component
     * 
     * @param {*} $container - root component container
     * @return {RootComponent -> Component} - return root component
     */
    ComponentsManager.prototype.CreateRootComponent = function($container) {
        if($container === null) {
            throw new Error('ComponentsManager.CreateRootComponent: $container can\'t be null');
        }

        this._rootComponent = new RootComponent($container);
        this._components.push(this._rootComponent);
        
        return this._rootComponent;
    };

    ComponentsManager.prototype.GetRootComponent = function() { return this._rootComponent; };
    
    ComponentsManager.prototype.SerializeAllComponents = function() {
        var formatter = new JSONFormatter(),
            result    = '',
            tmp       = null;

        for(var i = this._components.length; i--;) {
            tmp = formatter.Serialize(this._components[i]);
            if(tmp === null) {
                tmp = '{}';
            }

            result = result === '' ? tmp
                : [result, tmp].join(', ');
        }

        return ['"components": [', result, ']'].join('');
    };

    ComponentsManager.prototype.RemoveComponentById = function(id) {
        for(var i = this._components.length; i--;) {
            if(this._components[i].GetId() === id) {
                this._components[i].Remove();
                this._components.splice(i, 1);
                break;
            }
        }
    };

    ComponentsManager.prototype.DeserializeAllComponents = function(source, $rootContainer, skinEditor) {
        var formatter      = new JSONFormatter(),
            object         = null,
            root           = null,
            component      = null,
            i              = 0,
            propertyWindow = skinEditor.GetPropertyWindow(),
            componentsTree = skinEditor.GetComponentsTree();

        try {
            object = JSON.parse(source);

            if('html' in object) {  $(B64.decode(object.html)).appendTo($rootContainer); }

            if('components' in object) {
                for(i = object.components.length; i--;) {
                    component = (object.components[i].type === 'root') ? this.CreateRootComponent($rootContainer)
                                                                       : this.CreateComponent(object.components[i].type);
                    formatter.Deserialize(object.components[i], component);
                    component.SetContainer($('#' + component.GetId()));
                    component.AddOnClickCallback(new Callback(propertyWindow.GetId(), function(sender, args) { if('component' in args) { propertyWindow.Bind(args['component']); } }));
                    component.AddOnClickCallback(new Callback(componentsTree.GetId(), function(sender, args) { if('component' in args) { componentsTree.SelectComponent(args['component']); } }));
                }
            }
        } catch(e) {
            console.log(e);
        }
    };
    
    ComponentsManager.prototype.CreateComponent = function(name) {
        var component = null;
        switch(name) {
            case 'block':
                component = new BlockComponent();
                break;
            case 'image':
                component = new ImageComponent();
                break;
            case 'nodetitle':
                component = new NodeTitleComponent();
                break;
            case 'nodecontent':
                component = new NodeContentComponent();
                break;
            case 'counterscontainer':
                component = new CountersContainerComponent();
                break;
            case 'links':
                component = new LinksComponent();
                break;
            case 'review':
                component = new ReviewComponent();
                break;
            case 'mapinfo':
                component = new MapInfoComponent();
                break;
            case 'bookmark':
                component = new BookmarkComponent();
                break;
        }
        
        if(component !== null) { this._components.push(component); }
        
        return component;
    };
    
    ComponentsManager.prototype.SelectAllComponents = function() {
        for(var i = this._components.length; i--;) {
            this._components[i].Select();
        }
    };
    
    ComponentsManager.prototype.DeselectAllComponents = function() {
        for(var i = this._components.length; i--;) {
            this._components[i].Deselect();
        }
    };
    
    return ComponentsManager;
})();