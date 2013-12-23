//TODO: Need refactoring
var BlockPropertyView = (function(parent) {
    inherit(parent, BlockPropertyView);
    
    BlockPropertyView.LABLE_INPUT_HTML         = '<div class="label-input-control">' +
                                                    '<label>@LABEL@: </label>' +
                                                    '<div><input type="text"/></div>' +
                                                 '</div>';
    BlockPropertyView.LABLE_NINPUT_HTML        = '<div class="label-input-control" style="clear: both">' +
                                                    '<label>@LABEL@: </label>' +
                                                    '<div class="inputs"></div>' +
                                                 '</div>';
    BlockPropertyView.INPUT_HTML               = '<input type="text"/>';
    BlockPropertyView.COLOR_LABLE_INPUT_HTML   = '<div class="label-input-control">' +
                                                    '<label>@LABEL@: </label>' +
                                                    '<div><input type="text"/><div class="picker"></div></div>' +
                                                 '</div>';
    BlockPropertyView.LABEL_SELECT_HTML        = '<div class="label-select-control">' +
                                                    '<label>@LABEL@: </label>' +
                                                    '<div><select>@OPTIONS@</select></div>' +
                                                 '</div>';
    BlockPropertyView.LABEL_SELECT_OPTION_HTML = '<option value="@VALUE@">@TEXT@</option>';
    BlockPropertyView.LABEL_FILE_INPUT_HTML    = '<div class="label-input-control file" style="margin-bottom: 10px">' +
                                                    '<label>@LABEL@: </label>' +
                                                    '<div><button class="btn">Add file</button><button class="btn btn-danger"><i class="icon-trash icon-white"></i></button><input type="file"/></div>' +
                                                 '</div>';
    BlockPropertyView.LABEL_ALIGN_INPUT_HTML   = '<div class="label-input-control">' +
                                                    '<label>@LABEL@: </label>' +
                                                    '<div class="btn-group">' +
                                                        '<label class="btn" value="left"><i class="icon-align-left"></i></label>' +
                                                        '<label class="btn" value="center"><i class="icon-align-center"></i></label>' +
                                                        '<label class="btn" value="right"><i class="icon-align-right"></i></label>' + 
                                                        '<label class="btn" value="justify"><i class="icon-align-justify"></i></label>' + 
                                                    '</div></div>';
    
    function BlockPropertyView(viewModel) { 
        BlockPropertyView.super.constructor.apply(this);
        
        this._viewModel           = viewModel;
        
        this._$name               = null;
        this._$width              = null;
        this._$minWidth           = null;
        this._$maxWidth           = null;
        this._$height             = null;
        this._$minHeight          = null;
        this._$maxHeight          = null;
        this._$borderSize         = null;
        this._$borderColor        = null;
        this._$borderType         = null;
        this._$borderRadius       = null;
        this._$float              = null;
        this._$clear              = null;
        this._$backgroundColor    = null;
        this._$backgroundURL      = null;
        this._$backgroundRepeat   = null;
        this._$backgroundPosition = null;
        this._$backgroundSize     = null;
        this._$marginTop          = null;
        this._$marginRight        = null;
        this._$marginBottom       = null;
        this._$marginLeft         = null;
        this._$paddingTop         = null;
        this._$paddingRight       = null;
        this._$paddingBottom      = null;
        this._$paddingLeft        = null;
        this._$align              = null;
        this._$position           = null;
        this._$left               = null;
        this._$top                = null;
        this._$right              = null;
        this._$bottom             = null;
    };
    
    BlockPropertyView.prototype.AppendTo = function($container) {
        if($container === null) { return; }
        
        this._AppendLabelInput($container, {                label: 'Width', 
                                             viewModelProperyName: 'Width', 
                                                modelPropertyName: 'Width', 
                                                  cssPropertyName: 'width', 
                                                    viewComponent: '_$width' });
        this._AppendLabelInput($container, {                label: 'Min. Width', 
                                             viewModelProperyName: 'MinWidth', 
                                                modelPropertyName: 'MinWidth', 
                                                  cssPropertyName: 'min-width', 
                                                    viewComponent: '_$minWidth' });
        this._AppendLabelInput($container, {                label: 'Max. Width', 
                                             viewModelProperyName: 'MinWidth', 
                                                modelPropertyName: 'MinWidth', 
                                                  cssPropertyName: 'max-width', 
                                                    viewComponent: '_$maxWidth' });
        this._AppendLabelInput($container, {                label: 'Height', 
                                             viewModelProperyName: 'Height', 
                                                modelPropertyName: 'Height', 
                                                  cssPropertyName: 'height', 
                                                    viewComponent: '_$height' });
        this._AppendLabelInput($container, {                label: 'Min. Height', 
                                             viewModelProperyName: 'MinHeight', 
                                                modelPropertyName: 'MinHeight', 
                                                  cssPropertyName: 'min-height', 
                                                    viewComponent: '_$minHeight' });
        this._AppendLabelInput($container, {                label: 'Max. Height', 
                                             viewModelProperyName: 'MaxHeight', 
                                                modelPropertyName: 'MaxHeight', 
                                                  cssPropertyName: 'max-height', 
                                                    viewComponent: '_$maxHeight' });
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
                                                              options: [{value: 'none',  text: 'none'},
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
        this._AppendColorLabelInput($container, {           label: 'Background color', 
                                             viewModelProperyName: 'BackgroundColor', 
                                                modelPropertyName: 'BackgroundColor', 
                                                  cssPropertyName: 'background-color', 
                                                    viewComponent: '_$backgroundColor' });
        this._AppendImageInput($container, {                label: 'Background image', 
                                             viewModelProperyName: 'BackgroundURL', 
                                                modelPropertyName: 'BackgroundURL', 
                                                  cssPropertyName: 'background-image', 
                                                    viewComponent: '_$backgroundURL' });
        this._AppendSelectLabelInput($container, {          label: 'Background repeat', 
                                                          options: [{value: 'no-repeat', text: 'no-repeat'},
                                                                    {value: 'repeat',    text: 'repeat'},
                                                                    {value: 'repeat-x',  text: 'repeat-x'},
                                                                    {value: 'repeat-y',  text: 'repeat-y'},
                                                                    {value: 'inherit',   text: 'inherit'}],
                                             viewModelProperyName: 'BackgroundRepeat', 
                                                modelPropertyName: 'BackgroundRepeat', 
                                                  cssPropertyName: 'background-repeat', 
                                                    viewComponent: '_$backgroundRepeat' });
        this._AppendLabelInput($container, {                label: 'Background size', 
                                             viewModelProperyName: 'BackgroundSize', 
                                                modelPropertyName: 'BackgroundSize', 
                                                  cssPropertyName: 'background-size', 
                                                    viewComponent: '_$backgroundSize'});
        this._AppendLabelInput($container, {                label: 'Background pos.', 
                                             viewModelProperyName: 'BackgroundPosition', 
                                                modelPropertyName: 'BackgroundPosition', 
                                                  cssPropertyName: 'background-position', 
                                                    viewComponent: '_$backgroundPosition'});
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
        this._AppendLabelNInput($container, {  label: 'Padding',
                                               width: '8%',
                                             options: [{               class: 'left',
                                                        viewModelProperyName: 'PaddingLeft', 
                                                           modelPropertyName: 'PaddingLeft', 
                                                             cssPropertyName: 'padding-left', 
                                                               viewComponent: '_$paddingLeft'},
                                                       {               class: 'top',
                                                        viewModelProperyName: 'PaddingTop', 
                                                           modelPropertyName: 'PaddingTop', 
                                                             cssPropertyName: 'padding-top', 
                                                               viewComponent: '_$paddingTop'},
                                                       {               class: 'right',
                                                        viewModelProperyName: 'PaddingRight', 
                                                           modelPropertyName: 'PaddingRight', 
                                                             cssPropertyName: 'padding-right', 
                                                               viewComponent: '_$paddingRight'},
                                                       {               class: 'bottom',
                                                        viewModelProperyName: 'PaddingBottom', 
                                                           modelPropertyName: 'PaddingBottom', 
                                                             cssPropertyName: 'padding-bottom', 
                                                               viewComponent: '_$paddingBottom'}]});
        this._AppendLabelAlignInput($container, {              label: 'Text align', 
                                                viewModelProperyName: 'Align', 
                                                   modelPropertyName: 'Align', 
                                                     cssPropertyName: 'text-align', 
                                                       viewComponent: '_$align' });
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
    
    BlockPropertyView.prototype._AppendLabelInput = function($container, parameters) {
        var instance = this,
            $ui      = null;
        
        if('label'                            in parameters && 
           'viewModelProperyName'             in parameters && 
           parameters['viewModelProperyName'] in this._viewModel &&
           'modelPropertyName'                in parameters &&
           'cssPropertyName'                  in parameters &&
           'viewComponent'                    in parameters) {
            $ui = $(BlockPropertyView.LABLE_INPUT_HTML.replace('@LABEL@', parameters['label'])).appendTo($container);
            this[parameters['viewComponent']] = $ui.find('input');
            this[parameters['viewComponent']].val(this._viewModel.GetProperty(parameters['viewModelProperyName']));
            this[parameters['viewComponent']].keyup(function(e) {
                var value = $(this).val();
                
                if(value.length <= 0) {
                    value = 'auto';
                } else if(instance._IsNumber(value) && value.indexOf('%') < 0 && value.indexOf('px') < 0) {
                    value += 'px';
                }
                
                instance._viewModel.SetProperty(instance, {
                    modelPropertyName: parameters['modelPropertyName'],
                      cssPropertyName: parameters['cssPropertyName'],
                          properyName: parameters['viewModelProperyName'], 
                             newValue: value, 
                        viewComponent: parameters['viewComponent']
                });
            });
            
            this._viewModel[parameters['viewModelProperyName']].UnsubscribeAll();
            this._viewModel[parameters['viewModelProperyName']].Subscribe(new Callback(instance.GetId(), function(sender, args) {         
                if(!('viewComponent' in args)) { args['viewComponent'] = parameters['viewComponent']; }
                instance.SetValue(sender, args);
            }));
        }
    };
    
    BlockPropertyView.prototype._AppendLabelNInput = function($container, parameters) {
        var instance         = this,
            $ui              = null,
            $inputsContainer = null,
            length           = 0;
        
        if('label' in parameters && 
           'options' in parameters) {
            $ui = $(BlockPropertyView.LABLE_NINPUT_HTML.replace('@LABEL@', parameters['label'])).appendTo($container);
            
            $inputsContainer = $ui.find('.inputs');
            length = parameters['options'].length;
            for(var i = 0; i < length; i++) {
                (function(i) {
                    if('viewModelProperyName'             in parameters['options'][i] && 
                       parameters['options'][i]['viewModelProperyName'] in instance._viewModel &&
                       'modelPropertyName'                in parameters['options'][i] &&
                       'cssPropertyName'                  in parameters['options'][i] &&
                       'viewComponent'                    in parameters['options'][i]) {
    
                        instance[parameters['options'][i]['viewComponent']] = $(BlockPropertyView.INPUT_HTML).appendTo($inputsContainer);
                        instance[parameters['options'][i]['viewComponent']].css('width', parameters['width']);
                        if('class' in parameters['options'][i]) { instance[parameters['options'][i]['viewComponent']].addClass(parameters['options'][i]['class']); }
                        
                        instance[parameters['options'][i]['viewComponent']].val(instance._viewModel.GetProperty(parameters['options'][i]['viewModelProperyName']));
                        
                        instance[parameters['options'][i]['viewComponent']].keyup(function(e) {
                            var value = $(this).val();
                            
                            if(value.length <= 0) {
                                value = 'auto';
                            } else if(instance._IsNumber(value) && value.indexOf('%') < 0 && value.indexOf('px') < 0) {
                                value += 'px';
                            }
                            
                            instance._viewModel.SetProperty(instance, {
                                modelPropertyName: parameters['options'][i]['modelPropertyName'],
                                  cssPropertyName: parameters['options'][i]['cssPropertyName'],
                                      properyName: parameters['options'][i]['viewModelProperyName'], 
                                         newValue: value, 
                                    viewComponent: parameters['options'][i]['viewComponent']
                            });
                        });
                    }
                })(i);
            }
        }
    };
    
    BlockPropertyView.prototype._AppendColorLabelInput = function($container, parameters) {
        var instance = this,
            $ui      = null,
            $picker  = null,
            isFirst  = true;
        
        if('label'                            in parameters && 
           'viewModelProperyName'             in parameters && 
           parameters['viewModelProperyName'] in this._viewModel &&
           'modelPropertyName'                in parameters &&
           'cssPropertyName'                  in parameters &&
           'viewComponent'                    in parameters) {
            $ui = $(BlockPropertyView.COLOR_LABLE_INPUT_HTML.replace('@LABEL@', parameters['label'])).appendTo($container);
            this[parameters['viewComponent']] = $ui.find('input');
            
            this[parameters['viewComponent']].val(this._viewModel.GetProperty(parameters['viewModelProperyName']));
            
            $picker = $ui.find('.picker');
            $picker.farbtastic(this[parameters['viewComponent']], function(color) {
                if(isFirst) { isFirst = false; return; }
                instance._viewModel.SetProperty(instance, {
                    modelPropertyName: parameters['modelPropertyName'],
                      cssPropertyName: parameters['cssPropertyName'],
                          properyName: parameters['viewModelProperyName'], 
                             newValue: color, 
                        viewComponent: parameters['viewComponent']
                });
            });

            this[parameters['viewComponent']].keyup(function(e) {
                var value = $(this).val();
                
                if(value.length <= 0) { 
                    value = 'transparent'; 
                    $(this).val(value)
                           .css({'background-color': value,
                                 'color': '#333'});
                }
                
                instance._viewModel.SetProperty(instance, {
                    modelPropertyName: parameters['modelPropertyName'],
                      cssPropertyName: parameters['cssPropertyName'],
                          properyName: parameters['viewModelProperyName'], 
                             newValue: value, 
                        viewComponent: parameters['viewComponent']
                });
            });
            
            this[parameters['viewComponent']].click(function() { $picker.show(); });
            this[parameters['viewComponent']].blur(function() { $picker.hide(); });
            
            this._viewModel[parameters['viewModelProperyName']].UnsubscribeAll();
            this._viewModel[parameters['viewModelProperyName']].Subscribe(new Callback(instance.GetId(), function(sender, args) {         
                if(!('viewComponent' in args)) { args['viewComponent'] = parameters['viewComponent']; }
                instance.SetValue(sender, args);
            }));
        }
    };
    
    BlockPropertyView.prototype._AppendSelectLabelInput = function($container, parameters) {
        var instance    = this,
            $ui         = null,
            optionsHTML = '';
        
        if('label'                            in parameters && 
           'options'                          in parameters &&
           'viewModelProperyName'             in parameters && 
           parameters['viewModelProperyName'] in this._viewModel &&
           'modelPropertyName'                in parameters &&
           'cssPropertyName'                  in parameters &&
           'viewComponent'                    in parameters) {
            for(var i = 0; i < parameters['options'].length; i++) {
                optionsHTML += BlockPropertyView.LABEL_SELECT_OPTION_HTML.replace('@VALUE@', parameters['options'][i].value)
                                                                         .replace('@TEXT@', parameters['options'][i].text);
            }
            
            $ui = $(BlockPropertyView.LABEL_SELECT_HTML.replace('@LABEL@', parameters['label'])
                                                       .replace('@OPTIONS@', optionsHTML)).appendTo($container);
            
            this[parameters['viewComponent']] = $ui.find('select');
            this[parameters['viewComponent']].val(this._viewModel.GetProperty(parameters['viewModelProperyName']));
            this[parameters['viewComponent']].change(function(e) {
                var value = $(this).val();
                
                instance._viewModel.SetProperty(instance, {
                    modelPropertyName: parameters['modelPropertyName'],
                      cssPropertyName: parameters['cssPropertyName'],
                          properyName: parameters['viewModelProperyName'], 
                             newValue: value, 
                        viewComponent: parameters['viewComponent']
                });
            });
            
            this._viewModel[parameters['viewModelProperyName']].UnsubscribeAll();
            this._viewModel[parameters['viewModelProperyName']].Subscribe(new Callback(instance.GetId(), function(sender, args) {         
                if(!('viewComponent' in args)) { args['viewComponent'] = parameters['viewComponent']; }
                instance.SetValue(sender, args);
            }));
        }
    };
    
    BlockPropertyView.prototype._AppendImageInput = function($container, parameters) {
        var instance = this,
            $ui      = null;
        
        if('label'                            in parameters && 
           'viewModelProperyName'             in parameters && 
           parameters['viewModelProperyName'] in this._viewModel &&
           'modelPropertyName'                in parameters &&
           'cssPropertyName'                  in parameters &&
           'viewComponent'                    in parameters) {
            
            
            $ui = $(BlockPropertyView.LABEL_FILE_INPUT_HTML.replace('@LABEL@', parameters['label'])).appendTo($container);
            
            this[parameters['viewComponent']] = $ui.find('input');
            this[parameters['viewComponent']].change(function(e) {
                if(!this.files[0]) { return; }
                
                var fileReader = new FileReader();
                
                fileReader.onload = function(e) {
                    instance._viewModel.SetProperty(instance, {
                        modelPropertyName: parameters['modelPropertyName'],
                          cssPropertyName: parameters['cssPropertyName'],
                              properyName: parameters['viewModelProperyName'], 
                                 newValue: ['url(', e.target.result, ')'].join(''), 
                            viewComponent: parameters['viewComponent']
                    });
                };
                
                fileReader.readAsDataURL(this.files[0]);
            });
            
            $ui.find('.btn-danger').click(function() {
                instance._viewModel.SetProperty(instance, {
                        modelPropertyName: parameters['modelPropertyName'],
                          cssPropertyName: parameters['cssPropertyName'],
                              properyName: parameters['viewModelProperyName'], 
                                 newValue: 'none', 
                            viewComponent: parameters['viewComponent']
                });
            });
        }
    };
    
    BlockPropertyView.prototype._AppendLabelAlignInput = function($contaienr, parameters) {
        var instance = this,
            $ui      = null,
            $inputs  = null,
            align    = null;
        
        if('label'                            in parameters && 
           'viewModelProperyName'             in parameters && 
           parameters['viewModelProperyName'] in this._viewModel &&
           'modelPropertyName'                in parameters &&
           'cssPropertyName'                  in parameters &&
           'viewComponent'                    in parameters) {
            $ui = $(BlockPropertyView.LABEL_ALIGN_INPUT_HTML.replace('@LABEL@', parameters['label'])).appendTo($contaienr);
            
            align   = this._viewModel.GetProperty('Align'); 
            $inputs = $ui.find('label');
            $inputs.each(function() {
                if($(this).attr('value') === align) { $(this).addClass('active'); }
                $(this).click(function() {
                    var value = $(this).attr('value');
                    
                    $inputs.removeClass('active');
                    $(this).addClass('active');
                    instance._viewModel.SetProperty(instance, {
                        modelPropertyName: parameters['modelPropertyName'],
                          cssPropertyName: parameters['cssPropertyName'],
                              properyName: parameters['viewModelProperyName'], 
                                 newValue: value, 
                            viewComponent: parameters['viewComponent']
                    });
                });
            });
        }
    };
    
    BlockPropertyView.prototype.SetValue = function(sender, args) {
        if(args !== null && 'newValue' in args && 'viewComponent' in args && this['viewComponent'] !== null) {
            this[args['viewComponent']].val(args['newValue']);
        }
    };
    
    BlockPropertyView.prototype._IsNumber = function(value) {
        return /\d+(%)?(px)?/gi.test(value);
    };
    
    return BlockPropertyView;
})(PropertyView);