var ComponentsTreeUIComponent = (function(parent) {
    inherit(parent, ComponentsTreeUIComponent);
    
    ComponentsTreeUIComponent.UI_HTML = '<div class="skin-editor-component-tree-container">' +
                                            '<div class="content-header">Components Tree</div>' +
                                            '<div class="content"></div>' +
                                        '</div>';
    
    /**
     * Default constructor
     * 
     * @param {SkinEditor} skinEditor - skin editor
     * @constructor
     */
    function ComponentsTreeUIComponent(skinEditor) {
        ComponentsTreeUIComponent.super.constructor.apply(this, [skinEditor]);
        
        this._$uiTreeContainer = null;
        
        this._selectedNode     = null;
        this._rootComponent    = null;
    };
    
    /**
     * Append Components Tree UI Component to container
     * 
     * @param {*} $container - UI component container
     */
    ComponentsTreeUIComponent.prototype.AppendTo = function($container, data) {
        if($container === null) { return; }
        
        var $ui = $(ComponentsTreeUIComponent.UI_HTML).appendTo($container);
        
        this._$uiTreeContainer = $ui.find('.content');
        this._CreateTree(data);
    };
    
    /**
     * Get selected component
     * 
     * @return {Component} - selected component object
     */
    ComponentsTreeUIComponent.prototype.GetSelectedComponent = function() {
        if(this._selectedNode === null) { return null; }
        
        return ComponentsManager.GetInstance().GetComponentById(this._selectedNode.attr('id'));
    };
    
    /**
     * Add component into tree
     * 
     * @param {Component} component - adding component
     * @param {Component} parentComponent - parent component
     */
    ComponentsTreeUIComponent.prototype.AddComponent = function(component, parentComponent) {
        if(component       === null || !(component       instanceof Component) ||
           parentComponent === null || !(parentComponent instanceof Component)) {
            throw new Error('ComponentsTreeUIComponent.AddComponent: component/parentComponent must be instance of "Component" and not be null');
        }
        
        if(this._$uiTreeContainer === null) { return; }
        
        this._$uiTreeContainer.jstree('create', $(['#', parentComponent.GetId()].join('')), 'last', {
            'data': component.GetName(),
            'attr': { 'id': component.GetId(),
             'rel': component.GetRelType() }
        }, false, true);
    };

    ComponentsTreeUIComponent.prototype.MoveComponent = function(component, parentComponent) {
        this._$uiTreeContainer.jstree('move_node', $(['#', component.GetId()].join('')), $(['#', parentComponent.GetId()].join('')), 'inside');
    };

    ComponentsTreeUIComponent.prototype.RemoveSelectedComponent = function() {
        if(this._selectedNode === null || this._selectedNode.attr('id') === this._rootComponent.GetId()) { return; }

        this._$uiTreeContainer.jstree('remove');
    };
    
    /**
     * Select component
     * 
     * @param {Component} component - selecting component
     */
    ComponentsTreeUIComponent.prototype.SelectComponent = function(component) {
        if(component === null) { return; }
        
        var componentId = ['#', component.GetId()].join('');
        
        this._$uiTreeContainer.jstree("deselect_all");
        this._$uiTreeContainer.jstree('select_node', componentId);
    };
    
    /**
     * Create basic tree
     */
    ComponentsTreeUIComponent.prototype._CreateTree = function(data) {
        if(this._$uiTreeContainer === null) { return; }

        var instance   = this,
            skinEditor = this.GetSkinEditor(),
            jsonData   = null,
            tmpObj     = null;

        if(skinEditor !== null) { this._rootComponent = skinEditor.GetRootComponent(); }
        if(this._rootComponent === null) { return; }

        jsonData = [
            {
                'data': this._rootComponent.GetName(),
                'attr': { 'id': this._rootComponent.GetId(), 'rel': 'root' }
            }
        ];

        if(typeof data !== 'undefined' && data !== null && data !== '') {
            tmpObj = JSON.parse(data);
            if('tree' in tmpObj) {
                jsonData = tmpObj.tree;
                this._AddRelTypeForOldVersion(jsonData);
                console.log(jsonData);
            }
        }
        
        this._$uiTreeContainer.jstree({
            'dnd': {
                'drop_target': false,
                'drag_target': false
            },
            'crrm': {
                'move': {
                    'check_move': function(m) { return instance._CheckMove(m); }
                }
            },
            'json_data': {
                'data': jsonData
            },
            'types': {
                'types': {
                    'root': {
                        'icon': {
                            'image': '../../../scripts/skineditor/css/root_icon.png'
                        }
                    },
                    'block': {
                        'icon': {
                            'image': '../../../scripts/skineditor/css/block_icon.png'
                        }
                    },
                    'bookmark': {
                        'icon': {
                            'image': '../../../scripts/skineditor/css/bookmark_icon.png'
                        }
                    },
                    'reset': {
                        'icon': {
                            'image': '../../../scripts/skineditor/css/reset_icon.png'
                        }
                    },
                    'info': {
                        'icon': {
                            'image': '../../../scripts/skineditor/css/map_info_icon.png'
                        }
                    },
                    'review': {
                        'icon': {
                            'image': '../../../scripts/skineditor/css/review_icon.png'
                        }
                    },
                    'links': {
                        'icon': {
                            'image': '../../../scripts/skineditor/css/links_icon.png'
                        }
                    },
                    'counters': {
                        'icon': {
                            'image': '../../../scripts/skineditor/css/counters_icon.png'
                        }
                    },
                    'node_title': {
                        'icon': {
                            'image': '../../../scripts/skineditor/css/node_title_icon.png'
                        }
                    },
                    'node_content': {
                        'icon': {
                            'image': '../../../scripts/skineditor/css/node_content_icon.png'
                        }
                    },
                    'image': {
                        'icon': {
                            'image': '../../../scripts/skineditor/css/image_icon.png'
                        }
                    }
                }
            },
            'plugins': ['json_data', 'ui', 'types', 'crrm', 'dnd']
        })
        .bind("loaded.jstree",      function(event, data) { instance._TreeLoaded(event, data); }) 
        .bind('select_node.jstree', function(event, data) { instance._SelectNode(event, data); })
        .bind('move_node.jstree',   function(event, data) { instance._MoveNode(event, data);   })
        .bind('remove.jstree',      function(event, data) { instance._RemoveNode(event, data); });
    };

    ComponentsTreeUIComponent.prototype._AddRelTypeForOldVersion = function(jsonData) {
        var component = null;

        for(var i = jsonData.length; i--;) {
            if('attr' in jsonData[i] && !('rel' in jsonData[i].attr)) {
                component = ComponentsManager.GetInstance().GetComponentById(jsonData[i].attr['id']);
                if(component != null) {
                    jsonData[i].attr['rel'] = component.GetRelType();
                }
            }

            if('children' in jsonData[i] && jsonData[i].children.length > 0) {
                this._AddRelTypeForOldVersion(jsonData[i].children);
            }
        }
    };
    
    /**
     * Check move event handler
     * 
     * @param {*} m - move data
     */
    ComponentsTreeUIComponent.prototype._CheckMove = function(m) {
        var result          = true,
            insideComponent = null;
        
        if(this._rootComponent                  === null                         ||
           (m.p === 'before' && m.or.attr('id') === this._rootComponent.GetId()) ||
           (m.p === 'after'  && m.op.attr('id') === this._rootComponent.GetId())) {
            result = false;
        }
        
        if(result && m.p === 'inside') {
            insideComponent = ComponentsManager.GetInstance().GetComponentById(m.np.attr('id'));
            if(insideComponent !== null && !insideComponent.IsComposite()) {
                result = false;
            }
        }

        return result;
    };
    
    /**
     * Tree loaded event handler
     * 
     * @param {*} event - event info
     * @param {*} data - event data
     */
    ComponentsTreeUIComponent.prototype._TreeLoaded = function(event, data) {
        this._$uiTreeContainer.jstree("open_all");
        this._$uiTreeContainer.jstree('select_node', ['#', this._rootComponent.GetId()].join(''));
    };
    
    /**
     * Select node event handler
     * 
     * @param {*} event - event info
     * @param {*} data - event data
     */
    ComponentsTreeUIComponent.prototype._SelectNode = function(event, data) {
        this._selectedNode = data.rslt.obj;
        var component      = ComponentsManager.GetInstance().GetComponentById(this._selectedNode.attr('id'));
        
        if(component !== null) { 
            component.OnClick(this, { component: component }); 
            ComponentsManager.GetInstance().DeselectAllComponents();
            component.Select();
        }
    };
    
    /**
     * Move node event handler
     * 
     * @param {*} event - event info
     * @param {*} data - event data 
     */
    ComponentsTreeUIComponent.prototype._MoveNode = function(event, data) {
        var component       = ComponentsManager.GetInstance().GetComponentById(data.rslt.o.attr('id')),
            parentComponent = null;
        
        if(component === null) { return; }

        switch(data.rslt.p)
        {
            case 'before':
                parentComponent = ComponentsManager.GetInstance().GetComponentById(data.rslt.or.attr('id'));
                component.AppendBefore(parentComponent);
                break;
            case 'after':
                parentComponent = ComponentsManager.GetInstance().GetComponentById(data.rslt.r.attr('id'));
                component.AppendAfter(parentComponent);
                break;
            default:
                parentComponent = ComponentsManager.GetInstance().GetComponentById(data.rslt.np.attr('id'));
                component.MoveTo(parentComponent);
                break;
        }

        return true;
    };

    ComponentsTreeUIComponent.prototype._RemoveNode = function(event, data) {
        data.rslt.obj.each(function() {
            ComponentsManager.GetInstance().RemoveComponentById(this.id);
        });
    };

    ComponentsTreeUIComponent.prototype.GetJSON = function() {
        return JSON.stringify(this._$uiTreeContainer.jstree("get_json", -1));
    };
    
    return ComponentsTreeUIComponent;
})(UIComponent);