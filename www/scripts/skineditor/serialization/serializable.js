/**
 * Abstract class
 * Allows an object to control its own serialization and deserialization
 */
var Serializable = (function() {
    function Serializable() { };

    /**
     * Virtual function
     * Populates a SerializationInfo with the data needed to serialize the target object
     *
     * @param {SerializationInfo} serializationInfo - The SerializationInfo to populate with data
     */
    Serializable.prototype.GetObjectData = function(serializationInfo) { };

    /**
     * Virtual function
     * Set  a data with the SerializationInfo
     *
     * @param {SerializationInfo} serializationInfo - The SerializationInfo with object data
     */
    Serializable.prototype.SetObjectData = function(serializationInfo) { };

    return Serializable;
})();