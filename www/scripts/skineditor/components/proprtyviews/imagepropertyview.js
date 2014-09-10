//TODO: Need refactoring
var ImagePropertyView = (function(parent) {
    inherit(parent, ImagePropertyView);
    
    ImagePropertyView.LABEL_FILE_INPUT_HTML    = '<div class="label-input-control file" style="margin-bottom: 10px">' +
                                                    '<label>@LABEL@: </label>' +
                                                    '<div><button class="btn">Add file</button><button class="btn btn-danger"><i class="icon-trash icon-white"></i></button><input type="file"/></div>' +
                                                 '</div>';
    
    function ImagePropertyView(viewModel) { 
        ImagePropertyView.super.constructor.apply(this);
        
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
        this._$marginTop          = null;
        this._$marginRight        = null;
        this._$marginBottom       = null;
        this._$marginLeft         = null;
        this._$position           = null;
        this._$left               = null;
        this._$top                = null;
        this._$right              = null;
        this._$bottom             = null;
        this._$src                = null;
    }
    
    ImagePropertyView.prototype.AppendTo = function($container) {
        if($container === null) {
            return;
        }
        
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
                                                    viewComponent: '_$minWidth' });
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
        this._AppendImageInput($container, {        label: 'Image', 
                                            viewComponent: '_$src'});
        
    };
    
    ImagePropertyView.prototype._AppendImageInput = function($container, parameters) {
        var instance = this,
            $ui      = null;
        
        if('label'         in parameters &&
           'viewComponent' in parameters) {
            $ui = $(ImagePropertyView.LABEL_FILE_INPUT_HTML.replace('@LABEL@', parameters['label'])).appendTo($container);
            
            this[parameters['viewComponent']] = $ui.find('input');
            this[parameters['viewComponent']].change(function(e) {
                if( ! this.files[0]) {
                    return;
                }
                var fileReader = new FileReader(),
                    fileName   = this.files[0].name;
                
                fileReader.onload = function(e) {
                    $.ajax({
                        url: getUploadURL(),
                        type: 'POST',
                        data: { skinId: skinId, data: e.target.result, fileName: fileName},
                        success: function(data) {
                            console.log(data);
                            var object = JSON.parse(data);
                            if(object === null || object.status === 'error') {
                                alert("ERROR");
                            }

                            instance._viewModel.SetSrc(object.path);
                        }
                    });
                };
                
                fileReader.readAsDataURL(this.files[0]);
            });
            
            $ui.find('.btn-danger').click(function() {
                instance._viewModel.SetSrc('../../../scripts/skineditor/css/no.gif');
            });
        }
    };
    
    return ImagePropertyView;
})(BlockPropertyView);