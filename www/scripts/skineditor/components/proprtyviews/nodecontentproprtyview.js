//TODO: Need refactoring
var NodeContentPropertyView = (function(parent) {
    inherit(parent, NodeContentPropertyView);

    function NodeContentPropertyView(viewModel) { 
        NodeContentPropertyView.super.constructor.apply(this);
        
        this._viewModel           = viewModel;
        
        this._$name               = null;
        this._$borderSize         = null;
        this._$borderColor        = null;
        this._$borderType         = null;
        this._$borderRadius       = null;
        this._$float              = null;
        this._$clear              = null;
        this._$fontFamily         = null;
        this._$fontSize           = null;
        this._$fontWeight         = null;
        this._$fontColor          = null;
        this._$marginTop          = null;
        this._$marginRight        = null;
        this._$marginBottom       = null;
        this._$marginLeft         = null;
        this._$position           = null;
        this._$left               = null;
        this._$top                = null;
        this._$right              = null;
        this._$bottom             = null;
    };
    
    NodeContentPropertyView.prototype.AppendTo = function($container) {
        if($container === null) { return; }
        
        this._AppendLabelInput($container, {                label: 'Border size', 
                                             viewModelProperyName: 'BorderSize', 
                                                modelPropertyName: 'BorderSize', 
                                                  cssPropertyName: 'border-width', 
                                                    viewComponent: '_$borderSize' });
        this._AppendColorLabelInput($container, {           label: 'Border color', 
                                             viewModelProperyName: 'BorderColor', 
                                                modelPropertyName: 'BorderColor', 
                                                  cssPropertyName: 'border-color', 
                                                    viewComponent: '_$borderColor' });
        this._AppendSelectLabelInput($container, {              label: 'Border type',
                                                              options: [{value: 'none',   text: 'none'},
                                                                        {value: 'hidden', text: 'hidden'},
                                                                        {value: 'dotted', text: 'dotted'},
                                                                        {value: 'dashed', text: 'dashed'},
                                                                        {value: 'solid',  text: 'solid'},
                                                                        {value: 'double', text: 'double'},
                                                                        {value: 'groove', text: 'groove'},
                                                                        {value: 'ridge',  text: 'ridge'},
                                                                        {value: 'inset',  text: 'inset'},
                                                                        {value: 'outset', text: 'outset'}],
                                                 viewModelProperyName: 'BorderType', 
                                                    modelPropertyName: 'BorderType', 
                                                      cssPropertyName: 'border-style', 
                                                        viewComponent: '_$borderType' });
        this._AppendLabelInput($container, {                label: 'Border radius', 
                                             viewModelProperyName: 'BorderRadius', 
                                                modelPropertyName: 'BorderRadius', 
                                                  cssPropertyName: 'borderRadius', 
                                                    viewComponent: '_$borderRadius'});
        this._AppendSelectLabelInput($container, {              label: 'Float',
                                                              options: [{value: 'none',    text: 'none'},
                                                                        {value: 'inherit', text: 'inherit'},
                                                                        {value: 'left',    text: 'left'},
                                                                        {value: 'right',   text: 'right'}],
                                                 viewModelProperyName: 'Float', 
                                                    modelPropertyName: 'Float', 
                                                      cssPropertyName: 'float', 
                                                        viewComponent: '_$float' });
        this._AppendSelectLabelInput($container, {              label: 'Clear',
                                                              options: [{value: 'none',    text: 'none'},
                                                                        {value: 'inherit', text: 'inherit'},
                                                                        {value: 'left',    text: 'left'},
                                                                        {value: 'right',   text: 'right'},
                                                                        {value: 'both',    text: 'both'}],
                                                 viewModelProperyName: 'Clear', 
                                                    modelPropertyName: 'Clear', 
                                                      cssPropertyName: 'clear', 
                                                        viewComponent: '_$clear' });
        this._AppendLabelAlignInput($container, {              label: 'Text align',
                                                viewModelProperyName: 'Align',
                                                   modelPropertyName: 'Align',
                                                     cssPropertyName: 'text-align',
                                                       viewComponent: '_$align' });
        this._AppendSelectLabelInput($container, {              label: 'Font family',
                                                              options: [{value: 'Arial', text: 'Arial'},
                                                                        {value: 'Time New Roman', text: 'Time New Roman'}],
                                                 viewModelProperyName: 'FontFamily', 
                                                    modelPropertyName: 'FontFamily', 
                                                      cssPropertyName: 'font-family', 
                                                        viewComponent: '_$fontFamily' });
        this._AppendLabelInput($container, {                label: 'Font size', 
                                             viewModelProperyName: 'FontSize', 
                                                modelPropertyName: 'FontSize', 
                                                  cssPropertyName: 'font-size', 
                                                    viewComponent: '_$fontSize' });
        this._AppendSelectLabelInput($container, {              label: 'Font weight',
                                                              options: [{value: 'normal', text: 'normal'},
                                                                        {value: 'lighter', text: 'lighter'},
                                                                        {value: 'bolder', text: 'bolder'},
                                                                        {value: 'bold', text: 'bold'},
                                                                        {value: '100', text: '100'},
                                                                        {value: '200', text: '200'},
                                                                        {value: '300', text: '300'},
                                                                        {value: '400', text: '400'},
                                                                        {value: '500', text: '500'},
                                                                        {value: '600', text: '600'},
                                                                        {value: '700', text: '700'},
                                                                        {value: '800', text: '800'},
                                                                        {value: '900', text: '900'}],
                                                 viewModelProperyName: 'FontWeight', 
                                                    modelPropertyName: 'FontWeight', 
                                                      cssPropertyName: 'font-weight', 
                                                        viewComponent: '_$fontWeight' });
        this._AppendColorLabelInput($container, {           label: 'Font color', 
                                             viewModelProperyName: 'FontColor', 
                                                modelPropertyName: 'FontColor', 
                                                  cssPropertyName: 'color', 
                                                    viewComponent: '_$fontColor' });
        this._AppendLabelNInput($container, {  label: 'Margin',
                                               width: '8%',
                                             options: [{               class: 'left',
                                                        viewModelProperyName: 'MarginLeft', 
                                                           modelPropertyName: 'MarginLeft', 
                                                             cssPropertyName: 'margin-left', 
                                                               viewComponent: '_$marginLeft'},
                                                       {               class: 'top',
                                                        viewModelProperyName: 'MarginTop', 
                                                           modelPropertyName: 'MarginTop', 
                                                             cssPropertyName: 'margin-top', 
                                                               viewComponent: '_$marginTop'},
                                                       {               class: 'right',
                                                        viewModelProperyName: 'MarginRight', 
                                                           modelPropertyName: 'MarginRight', 
                                                             cssPropertyName: 'margin-right', 
                                                               viewComponent: '_$marginRight'},
                                                       {               class: 'bottom',
                                                        viewModelProperyName: 'MarginBottom', 
                                                           modelPropertyName: 'MarginBottom', 
                                                             cssPropertyName: 'margin-bottom', 
                                                               viewComponent: '_$marginBottom'}]});
        this._AppendSelectLabelInput($container, {            label: 'Position type',
                                                            options:[{value: 'inherit',  text: 'inherit'},
                                                                     {value: 'static',   text: 'static'},
                                                                     {value: 'relative', text: 'relative'},
                                                                     {value: 'fixed',    text: 'fixed'},
                                                                     {value: 'absolute', text: 'absolute'}],
                                               viewModelProperyName: 'Position', 
                                                  modelPropertyName: 'Position', 
                                                    cssPropertyName: 'position', 
                                                      viewComponent: '_$position' });
        this._AppendLabelNInput($container, {  label: 'Position',
                                               width: '8%',
                                             options: [{               class: 'left',
                                                        viewModelProperyName: 'Left', 
                                                           modelPropertyName: 'Left', 
                                                             cssPropertyName: 'left', 
                                                               viewComponent: '_$left'},
                                                       {               class: 'top',
                                                        viewModelProperyName: 'Top', 
                                                           modelPropertyName: 'Top', 
                                                             cssPropertyName: 'top', 
                                                               viewComponent: '_$top'},
                                                       {               class: 'right',
                                                        viewModelProperyName: 'Right', 
                                                           modelPropertyName: 'Right', 
                                                             cssPropertyName: 'right', 
                                                               viewComponent: '_$right'},
                                                       {               class: 'bottom',
                                                        viewModelProperyName: 'Bottom', 
                                                           modelPropertyName: 'Bottom', 
                                                             cssPropertyName: 'bottom', 
                                                               viewComponent: '_$bottom'}]});
        
    };
    
    return NodeContentPropertyView;
})(BlockPropertyView);