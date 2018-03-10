//TODO: Need refactoring
var RootPropertyView = (function(parent) {
    inherit(parent, RootPropertyView);

    function RootPropertyView(viewModel) {
        RootPropertyView.super.constructor.apply(this);

        this._viewModel           = viewModel;

        this._$name               = null;
        this._$backgroundColor    = null;
        this._$backgroundURL      = null;
        this._$backgroundRepeat   = null;
        this._$backgroundPosition = null;
        this._$backgroundSize     = null;
        this._$fontFamily         = null;
        this._$fontSize           = null;
        this._$fontWeight         = null;
        this._$fontColor          = null;
    };

    RootPropertyView.prototype.AppendTo = function($container) {
        if($container === null) { return; }

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
        this._AppendSelectLabelInput($container, {              label: 'Font family',
                                                              options: [{value: 'andale mono', text: 'Andale Mono'},
                                                                  {value: 'Arial', text: 'Arial'},
                                                                  {value: 'arial black', text: 'Arial Black'},
                                                                  {value: 'book antiqua', text: 'Book Antiqua'},
                                                                  {value: 'comic sans ms', text: 'Comic Sans MS'},
                                                                  {value: 'courier new', text: 'Courier New'},
                                                                  {value: 'georgia', text: 'Georgia'},
                                                                  {value: 'helvetica', text: 'Helvetica'},
                                                                  {value: 'impact', text: 'Impact'},
                                                                  {value: 'tahoma', text: 'Tahoma'},
                                                                  {value: 'terminal', text: 'Terminal'},
                                                                  {value: 'times new roman', text: 'Times New Roman'},
                                                                  {value: 'trebuchet ms', text: 'Trebuchet MS'},
                                                                  {value: 'verdana', text: 'Verdana'},
                                                                  {value: 'webdings', text: 'Webdings'},
                                                                  {value: 'wingdings', text: 'Wingdings'}],
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
    };

    return RootPropertyView;
})(BlockPropertyView);