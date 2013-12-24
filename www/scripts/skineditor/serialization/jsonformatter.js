/**
 * JSON Formatter class
 */
var JSONFormatter = (function (parent) {
    inherit(parent, JSONFormatter);

    function JSONFormatter() {};

    /**
     * @return {string} - JSON string
     */
    JSONFormatter.prototype.Serialize = function(object) {
        var iterator          = null,
            currentItem       = null,
            serializationInfo = null,
            result            = {};

        if(object === null || typeof object.GetObjectData !== 'function') {
            throw new Error('JSONFormatter.Serialize: must not be null');
        }

        serializationInfo = new SerializationInfo();
        object.GetObjectData(serializationInfo);

        iterator = serializationInfo.GetEnumerator();
        while(iterator.MoveNext()) {
            currentItem = iterator.GetCurrentItem();
            if(currentItem == null) { continue; }

            result[currentItem.key] = currentItem.value;
        }

        return JSON.stringify(result);
    };

    /**
     * @param {string} source - JSON string
     */
    JSONFormatter.prototype.Deserialize = function(source, object) {
        var jsonObj           = null,
            serializationInfo = null,
            i                 = null;

        serializationInfo = new SerializationInfo();
        jsonObj           = (typeof source === 'string') ? JSON.parse(source)
                                                         : source;

        if(jsonObj !== null) {
            for(i in jsonObj) {
                serializationInfo.AddValue(i, jsonObj[i]);
            }
        }

        object.SetObjectData(serializationInfo);
    };

    return JSONFormatter;
})(Formatter);