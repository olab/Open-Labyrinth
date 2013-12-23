var RootPropertyModel = (function(parent) {
    inherit(parent, RootPropertyModel);

    function RootPropertyModel() {
        RootPropertyModel.super.constructor.apply(this);

        this.Name               = 'Root component';
        this.FontFamily         = 'Arial';
        this.FontSize           = 12;
        this.FontWeight         = 'normal';
        this.FontColor          = '#000';
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
    };

    RootPropertyModel.prototype.GetObjectData = function(serializationInfo) {
        if(serializationInfo == null || !(serializationInfo instanceof SerializationInfo)) {
            throw new Error('RootPropertyModel.GetObjectData: Serialization Info must be instance of object "SerializationInfo" and not be null');
        }

        serializationInfo.AddValue("FontFamily", this.FontFamily);
        serializationInfo.AddValue("FontSize", this.FontSize);
        serializationInfo.AddValue("FontWeight", this.FontWeight);
        serializationInfo.AddValue("FontColor", this.FontColor);
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
    };

    RootPropertyModel.prototype.SetObjectData = function(serializationInfo) {
        if(serializationInfo == null || !(serializationInfo instanceof SerializationInfo)) {
            throw new Error('SerializableTestObject.SetObjectData: Serialization Info must be instance of object "SerializationInfo" and not be null');
        }

        this.FontFamily         = serializationInfo.GetValue("FontFamily");
        this.FontSize           = serializationInfo.GetValue("FontSize");
        this.FontWeight         = serializationInfo.GetValue("FontWeight");
        this.FontColor          = serializationInfo.GetValue("FontColor");
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
    };

    return LinksPropertyModel;
})(Serializable);