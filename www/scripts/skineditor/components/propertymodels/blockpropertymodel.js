var BlockPropertyModel = (function(parent) {
    inherit(parent, BlockPropertyModel);
    
    function BlockPropertyModel() {
        BlockPropertyModel.super.constructor.apply(this);
        
        this.Name               = 'Block component';
        this.Width              = 'auto';
        this.MinWidth           = null;
        this.MaxWidth           = null;
        this.Height             = 'auto';
        this.MinHeight          = null;
        this.MaxHeight          = null;
        this.BorderSize         = null;
        this.BorderColor        = null;
        this.BorderType         = null;
        this.BorderRadius       = null;
        this.Float              = 'none';
        this.Clear              = null;
        this.BackgroundColor    = 'transparent';
        this.BackgroundURL      = null;
        this.BackgroundRepeat   = 'repeat';
        this.BackgroundPosition = '0% 0%';
        this.BackgroundSize     = '100% 100%';
        this.MarginTop          = 'auto';
        this.MarginRight        = 'auto';
        this.MarginBottom       = 'auto';
        this.MarginLeft         = 'auto';
        this.PaddingTop         = 'auto';
        this.PaddingRight       = 'auto';
        this.PaddingBottom      = 'auto';
        this.PaddingLeft        = 'auto';
        this.Align              = 'justify';
        this.Position           = 'inherit';
        this.Left               = 'auto';
        this.Top                = 'auto';
        this.Right              = 'auto';
        this.Bottom             = 'auto';
        this.IsPopupInside      = false;
    };
    
    BlockPropertyModel.prototype.GetObjectData = function(serializationInfo) {
        if(serializationInfo == null || !(serializationInfo instanceof SerializationInfo)) {
            throw new Error('BlockPropertyModel.GetObjectData: Serialization Info must be instance of object "SerializationInfo" and not be null');
        }

        serializationInfo.AddValue("Width", this.Width);
        serializationInfo.AddValue("MinWidth", this.MinWidth);
        serializationInfo.AddValue("MaxWidth", this.MaxWidth);
        serializationInfo.AddValue("Height", this.Height);
        serializationInfo.AddValue("MinHeight", this.MinHeight);
        serializationInfo.AddValue("MaxHeight", this.MaxHeight);
        serializationInfo.AddValue("BorderSize", this.BorderSize);
        serializationInfo.AddValue("BorderColor", this.BorderColor);
        serializationInfo.AddValue("BorderType", this.BorderType);
        serializationInfo.AddValue("BorderRadius", this.BorderRadius);
        serializationInfo.AddValue("Float", this.Float);
        serializationInfo.AddValue("Clear", this.Clear);
        serializationInfo.AddValue("BackgroundColor", this.BackgroundColor);
        serializationInfo.AddValue("BackgroundURL", this.BackgroundURL);
        serializationInfo.AddValue("BackgroundRepeat", this.BackgroundRepeat);
        serializationInfo.AddValue("BackgroundPosition", this.BackgroundPosition);
        serializationInfo.AddValue("MarginTop", this.MarginTop);
        serializationInfo.AddValue("MarginRight", this.MarginRight);
        serializationInfo.AddValue("MarginBottom", this.MarginBottom);
        serializationInfo.AddValue("MarginLeft", this.MarginLeft);
        serializationInfo.AddValue("PaddingTop", this.PaddingTop);
        serializationInfo.AddValue("PaddingRight", this.PaddingRight);
        serializationInfo.AddValue("PaddingBottom", this.PaddingBottom);
        serializationInfo.AddValue("PaddingLeft", this.PaddingLeft);
        serializationInfo.AddValue("Align", this.Align);
        serializationInfo.AddValue("Position", this.Position);
        serializationInfo.AddValue("Left", this.Left);
        serializationInfo.AddValue("Top", this.Top);
        serializationInfo.AddValue("Right", this.Right);
        serializationInfo.AddValue("Bottom", this.Bottom);
        serializationInfo.AddValue("IsPopupInside", this.IsPopupInside);
    };

    BlockPropertyModel.prototype.SetObjectData = function(serializationInfo) {
        if(serializationInfo == null || !(serializationInfo instanceof SerializationInfo)) {
            throw new Error('SerializableTestObject.SetObjectData: Serialization Info must be instance of object "SerializationInfo" and not be null');
        }

        this.Width              = serializationInfo.GetValue("Width");
        this.MinWidth           = serializationInfo.GetValue("MinWidth");
        this.MaxWidth           = serializationInfo.GetValue("MaxWidth");
        this.Height             = serializationInfo.GetValue("Height");
        this.MinHeight          = serializationInfo.GetValue("MinHeight");
        this.MaxHeight          = serializationInfo.GetValue("MaxHeight");
        this.BorderSize         = serializationInfo.GetValue("BorderSize");
        this.BorderColor        = serializationInfo.GetValue("BorderColor");
        this.BorderType         = serializationInfo.GetValue("BorderType");
        this.BorderRadius       = serializationInfo.GetValue("BorderRadius");
        this.Float              = serializationInfo.GetValue("Float");
        this.Clear              = serializationInfo.GetValue("Clear");
        this.BackgroundColor    = serializationInfo.GetValue("BackgroundColor");
        this.BackgroundURL      = serializationInfo.GetValue("BackgroundURL");
        this.BackgroundRepeat   = serializationInfo.GetValue("BackgroundRepeat");
        this.BackgroundPosition = serializationInfo.GetValue("BackgroundPosition");
        this.MarginTop          = serializationInfo.GetValue("MarginTop");
        this.MarginRight        = serializationInfo.GetValue("MarginRight");
        this.MarginBottom       = serializationInfo.GetValue("MarginBottom");
        this.MarginLeft         = serializationInfo.GetValue("MarginLeft");
        this.PaddingTop         = serializationInfo.GetValue("PaddingTop");
        this.PaddingRight       = serializationInfo.GetValue("PaddingRight");
        this.PaddingBottom      = serializationInfo.GetValue("PaddingBottom");
        this.PaddingLeft        = serializationInfo.GetValue("PaddingLeft");
        this.Align              = serializationInfo.GetValue("Align");
        this.Position           = serializationInfo.GetValue("Position");
        this.Left               = serializationInfo.GetValue("Left");
        this.Top                = serializationInfo.GetValue("Top");
        this.Right              = serializationInfo.GetValue("Right");
        this.Bottom             = serializationInfo.GetValue("Bottom");
        this.IsPopupInside      = serializationInfo.GetValue("IsPopupInside");
    };
    
    return BlockPropertyModel;
})(Serializable);