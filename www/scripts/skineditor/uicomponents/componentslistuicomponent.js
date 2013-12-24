/**
 * Components list UI component class
 */
var ComponentsListUIComponent = (function(parent) {
    inherit(parent, ComponentsListUIComponent);
    
    ComponentsListUIComponent.UI_HTML                = '<div class="skin-editor-components-list">' +
                                                            '<div class="content-header">Components</div>' +
                                                            '<div class="content"></div>'
                                                       '</div>';
    ComponentsListUIComponent.UI_ITEM_CONTAINER_HTML = '<ul>@ITEMS@</ul>';
    ComponentsListUIComponent.UI_ITEM_HTML           = '<li>' +
                                                            '<a href="javascript:void(0)" class="skin-editor-add-component" component-name="@NAME@"><i class="@ICON-CLASS@"></i> @TITLE@</a>' + 
                                                       '</li>';
    
    /**
     * Default constructor
     * 
     * @param {SkinEditor} skinEditor - skin editor
     * @constructor
     */
    function ComponentsListUIComponent(skinEditor) {
        ComponentsListUIComponent.super.constructor.apply(this, [skinEditor]);
    };
    
    /**
     * Append Componentns List UI Component to container
     * 
     * @param {*} $container - UI component container
     */
    ComponentsListUIComponent.prototype.AppendTo = function($container) { 
        if($container === null) { return; }
        
        var $ui        = $(ComponentsListUIComponent.UI_HTML).appendTo($container),
            $uiContent = $ui.find('.content');
            itemsHTML  = ComponentsListUIComponent.UI_ITEM_CONTAINER_HTML.replace('@ITEMS@', this._CreateUIItemsHTML());
        
        $(itemsHTML).appendTo($uiContent);
        
        this._CreateEvents();
    };
    
    /**
     * Create Components UI Items 
     * 
     * @return {string} - Components UI Items HTML string
     */
    ComponentsListUIComponent.prototype._CreateUIItemsHTML = function() {
        var result     = [],
            components = ComponentsManager.GetInstance().GetComponentsList();
        
        for(var i = components.length; i--;) {
            result.push(ComponentsListUIComponent.UI_ITEM_HTML.replace('@NAME@',       components[i].name)
                                                              .replace('@ICON-CLASS@', components[i].icon)
                                                              .replace('@TITLE@',      components[i].title));
        }
        
        return result.join('');
    };
    
    /**
     * Create componets list events
     */
    ComponentsListUIComponent.prototype._CreateEvents = function() {
        var instance = this,
            skinEditor = this.GetSkinEditor();
        
        if(skinEditor === null) { return; }
        
        // Add component event
        $('.skin-editor-add-component').click(function() {
            var component         = ComponentsManager.GetInstance().CreateComponent($(this).attr('component-name')),
                propertyWindow    = skinEditor.GetPropertyWindow(),
                componentsTree    = skinEditor.GetComponentsTree(),
                selectedComponent = null;
            
            if(component      === null || 
               propertyWindow === null || 
               componentsTree === null) { return false; }
            
            selectedComponent = componentsTree.GetSelectedComponent();
            if(selectedComponent === null || !selectedComponent.IsComposite()) { return false; }

            component.AppendTo(selectedComponent);
            component.AddOnClickCallback(new Callback(propertyWindow.GetId(), function(sender, args) { if('component' in args) { propertyWindow.Bind(args['component']); } }));
            component.AddOnClickCallback(new Callback(componentsTree.GetId(), function(sender, args) { if('component' in args) { componentsTree.SelectComponent(args['component']); } }));

            componentsTree.AddComponent(component, selectedComponent);
        });
    };
    
    return ComponentsListUIComponent;
})(UIComponent);