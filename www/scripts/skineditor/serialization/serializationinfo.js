/**
 * Stores all the data needed to serialize or deserialize an object
 * This class cannot be inherited
 */
var SerializationInfo = (function(parent) {
    inherit(parent, SerializationInfo);
    
    /**
     * Private serialization info iterator class
     */
    var SerializationInfoEnumetor = (function(parent) {
        inherit(parent, SerializationInfoEnumetor);

        function SerializationInfoEnumetor(info) {
            this._info         = info;
            this._keys         = Object.keys(this._info._values);
            this._currentIndex = -1;
            this._length       = this._keys.length;
        };

        SerializationInfoEnumetor.prototype.MoveNext = function() {
            this._currentIndex++;

            return (this._currentIndex >= 0 && this._currentIndex < this._length);
        };

        SerializationInfoEnumetor.prototype.GetCurrentItem = function() {
            return (this._currentIndex >= 0 && this._currentIndex < this._length) ? {key: this._keys[this._currentIndex], value: this._info.GetValue(this._keys[this._currentIndex])}
                                                                                  : null;
        };

        SerializationInfoEnumetor.prototype.Begin = function() {
            this._keys         = Object.keys(this._info._values);
            this._currentIndex = -1;
            this._length       = this._keys.length;
        };

        return SerializationInfoEnumetor;
    })(Enumerator);

    /**
     * Default constructor
     *
     * @constructor
     */
    function SerializationInfo() {
        this._values = {};
    };

    /**
     * Add value to serialization info
     *
     * @param {string} name - serialization info key
     * @param {*} value - serialization info value
     * @exceptions - Argument exception (name)
     */
    SerializationInfo.prototype.AddValue = function(name, value) {
        if(name === null || typeof name !== 'string' || name.length <= 0) {
            throw new Error("SerializationInfo.AddValue: Name argument must be string and must be not null or empty");
        }

        this._values[name] = value;
    };

    /**
     * Get value from serialization info object
     *
     * @param {string} name - serialization info key
     * @return {*} - serialization info value
     */
    SerializationInfo.prototype.GetValue = function(name) {
        return (name in this._values) ? this._values[name] : null;
    };

    /**
     * Get new serialization info iterator
     *
     * @return {SerializationInfoIterator} - serialization info iterator
     * @exceptions - Null Argument exception (_values)
     */
    SerializationInfo.prototype.GetEnumerator = function() {
        if(this._values === null) {
            throw new Error("SerializationInfo._values: Must be object not null");
        }

        return new SerializationInfoEnumetor(this);
    };

    return SerializationInfo;
})(Enumerable);