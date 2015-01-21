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
        this._blocks        = [];
        this._rootComponent = null;
    }
    
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
            { name: 'block',             title: 'Block Component',              icon: 'icon-block' },
            { name: 'image',             title: 'Image Component',              icon: 'icon-image' },
            { name: 'nodetitle',         title: 'Node Title Component',         icon: 'icon-title' },
            { name: 'nodecontent',       title: 'Node Content Component',       icon: 'icon-content' },
            { name: 'counterscontainer', title: 'Counters Container Component', icon: 'icon-counters' },
            { name: 'links',             title: 'Links Component',              icon: 'icon-links' },
            { name: 'review',            title: 'Review Component',             icon: 'icon-review' },
            { name: 'sectioninfo',       title: 'Section',                      icon: 'icon-info' },
            { name: 'mapinfo',           title: 'Map Info Component',           icon: 'icon-info' },
            { name: 'bookmark',          title: 'Bookmark Component',           icon: 'icon-bookmark' },
            { name: 'reset',             title: 'Reset Component',              icon: 'icon-reset' }
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
            html           = getSkinHTML(),
            propertyWindow = skinEditor.GetPropertyWindow(),
            componentsTree = skinEditor.GetComponentsTree();

        try {
            object = JSON.parse(source);

            if(html !== '') { $(B64.decode(html)).appendTo($rootContainer); }

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
                this._blocks.push(component);
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
            case 'section':
                component = new SectionInfoComponent();
                break;
            case 'bookmark':
                component = new BookmarkComponent();
                break;
            case 'reset':
                component = new ResetComponent();
                break;
        }
        
        if (component !== null) {

            this._components.push(component); }
        
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

    ComponentsManager.prototype.GetAllBlocks = function() {
        return this._blocks;
    };
    
    return ComponentsManager;
})();