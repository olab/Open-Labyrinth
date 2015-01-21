var ImagePropertyModel = (function(parent) {
    inherit(parent, ImagePropertyModel);
    
    function ImagePropertyModel() {
        ImagePropertyModel.super.constructor.apply(this);
        
        this.Name               = 'Image component';
        this.Width              = null;
        this.MinWidth           = null;
        this.MaxWidth           = null;
        this.Height             = null;
        this.MinHeight          = null;
        this.MaxHeight          = null;
        this.BorderSize         = null;
        this.BorderColor        = 'transperent';
        this.BorderType         = null;
        this.BorderRadius       = null;
        this.Float              = null;
        this.MarginTop          = 'auto';
        this.MarginRight        = 'auto';
        this.MarginBottom       = 'auto';
        this.MarginLeft         = 'auto';
        this.Position           = 'inherit';
        this.Left               = 'auto';
        this.Top                = 'auto';
        this.Right              = 'auto';
        this.Bottom             = 'auto';
        this.Src                = '../../../scripts/skineditor/css/no.gif';
    };
    
    ImagePropertyModel.prototype.GetObjectData = function(serializationInfo) {
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
        serializationInfo.AddValue("MarginTop", this.MarginTop);
        serializationInfo.AddValue("MarginRight", this.MarginRight);
        serializationInfo.AddValue("MarginBottom", this.MarginBottom);
        serializationInfo.AddValue("MarginLeft", this.MarginLeft);
        serializationInfo.AddValue("Position", this.Position);
        serializationInfo.AddValue("Left", this.Left);
        serializationInfo.AddValue("Top", this.Top);
        serializationInfo.AddValue("Right", this.Right);
        serializationInfo.AddValue("Bottom", this.Bottom);
        serializationInfo.AddValue("Src", this.Src);
    };

    ImagePropertyModel.prototype.SetObjectData = function(serializationInfo) {
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
        this.MarginTop          = serializationInfo.GetValue("MarginTop");
        this.MarginRight        = serializationInfo.GetValue("MarginRight");
        this.MarginBottom       = serializationInfo.GetValue("MarginBottom");
        this.MarginLeft         = serializationInfo.GetValue("MarginLeft");
        this.Position           = serializationInfo.GetValue("Position");
        this.Left               = serializationInfo.GetValue("Left");
        this.Top                = serializationInfo.GetValue("Top");
        this.Right              = serializationInfo.GetValue("Right");
        this.Bottom             = serializationInfo.GetValue("Bottom");
    };
    
    return ImagePropertyModel;
})(Serializable);