var CountersContainerPropertyModel = (function(parent) {
    inherit(parent, CountersContainerPropertyModel);

    function CountersContainerPropertyModel() {
        this.Name               = 'Counters container component';
        this.BorderSize         = null;
        this.BorderColor        = null;
        this.BorderType         = null;
        this.BorderRadius       = null;
        this.Float              = null;
        this.Clear              = null;
        this.FontFamily         = 'Arial';
        this.FontSize           = 12;
        this.FontWeight         = 'normal';
        this.FontColor          = '#000';
        this.MarginTop          = 'auto';
        this.MarginRight        = 'auto';
        this.MarginBottom       = 'auto';
        this.MarginLeft         = 'auto';
        this.Align              = 'justify';
        this.Position           = 'inherit';
        this.Left               = 'auto';
        this.Top                = 'auto';
        this.Right              = 'auto';
        this.Bottom             = 'auto';
    };

    CountersContainerPropertyModel.prototype.GetObjectData = function(serializationInfo) {
        if(serializationInfo == null || !(serializationInfo instanceof SerializationInfo)) {
            throw new Error('NodeTitlePropertyModel.GetObjectData: Serialization Info must be instance of object "SerializationInfo" and not be null');
        }

        serializationInfo.AddValue("BorderSize", this.BorderSize);
        serializationInfo.AddValue("BorderColor", this.BorderColor);
        serializationInfo.AddValue("BorderType", this.BorderType);
        serializationInfo.AddValue("BorderRadius", this.BorderRadius);
        serializationInfo.AddValue("Float", this.Float);
        serializationInfo.AddValue("Clear", this.Clear);
        serializationInfo.AddValue("FontFamily", this.FontFamily);
        serializationInfo.AddValue("FontSize", this.FontSize);
        serializationInfo.AddValue("FontWeight", this.FontWeight);
        serializationInfo.AddValue("FontColor", this.FontColor);
        serializationInfo.AddValue("MarginTop", this.MarginTop);
        serializationInfo.AddValue("MarginRight", this.MarginRight);
        serializationInfo.AddValue("MarginBottom", this.MarginBottom);
        serializationInfo.AddValue("MarginLeft", this.MarginLeft);
        serializationInfo.AddValue("Align", this.Align);
        serializationInfo.AddValue("Position", this.Position);
        serializationInfo.AddValue("Left", this.Left);
        serializationInfo.AddValue("Top", this.Top);
        serializationInfo.AddValue("Right", this.Right);
        serializationInfo.AddValue("Bottom", this.Bottom);
        serializationInfo.AddValue("Src", this.Src);
    };

    CountersContainerPropertyModel.prototype.SetObjectData = function(serializationInfo) {
        if(serializationInfo == null || !(serializationInfo instanceof SerializationInfo)) {
            throw new Error('NodeTitlePropertyModel.SetObjectData: Serialization Info must be instance of object "SerializationInfo" and not be null');
        }

        this.BorderSize         = serializationInfo.GetValue("BorderSize");
        this.BorderColor        = serializationInfo.GetValue("BorderColor");
        this.BorderType         = serializationInfo.GetValue("BorderType");
        this.BorderRadius       = serializationInfo.GetValue("BorderRadius");
        this.Float              = serializationInfo.GetValue("Float");
        this.Clear              = serializationInfo.GetValue("Clear");
        this.FontFamily         = serializationInfo.GetValue("FontFamily");
        this.FontSize           = serializationInfo.GetValue("FontSize");
        this.FontWeight         = serializationInfo.GetValue("FontWeight");
        this.FontColor          = serializationInfo.GetValue("FontColor");
        this.MarginTop          = serializationInfo.GetValue("MarginTop");
        this.MarginRight        = serializationInfo.GetValue("MarginRight");
        this.MarginBottom       = serializationInfo.GetValue("MarginBottom");
        this.MarginLeft         = serializationInfo.GetValue("MarginLeft");
        this.Align              = serializationInfo.GetValue("Align");
        this.Position           = serializationInfo.GetValue("Position");
        this.Left               = serializationInfo.GetValue("Left");
        this.Top                = serializationInfo.GetValue("Top");
        this.Right              = serializationInfo.GetValue("Right");
        this.Bottom             = serializationInfo.GetValue("Bottom");
    };
    
    return CountersContainerPropertyModel;
})(Serializable);