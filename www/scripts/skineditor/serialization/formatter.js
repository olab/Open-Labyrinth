/**
 * Abstract class
 * Basic class for formatter
 */
var Formatter = (function() {
    function Formatter() {};

    /**
     * Serializes an object, or graph of objects with the given root to need object
     *
     * @param {* -> Serializable} object - serialization object
     * @return {*} - serialized object
     */
    Formatter.prototype.Serialize   = function(object) {};

    /**
     * Deserialize the data on the provided source
     *
     * @param {*} source - deserialize source data
     * @param {*} object - deserialize object
     */
    Formatter.prototype.Deserialize = function(source, object) {};

    return Formatter;
})();