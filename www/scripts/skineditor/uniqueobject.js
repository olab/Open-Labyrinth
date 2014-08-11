/**
 * Abstract class
 * Basic unique object
 */
var UniqueObject = (function() {
    /**
     * Default constructor
     * 
     * @constructor
     */
    function UniqueObject() {
        this._objectId = 'objectid-' + GUID.Get();
    }
    
    /**
     * Get unique object Id
     * 
     * @return {integer} - unique object Id
     */
    UniqueObject.prototype.GetId = function() { return this._objectId; };

    /**
     * Set new Id for object
     *
     * @param {string} newId - id
     */
    UniqueObject.prototype.SetId = function(newId) { this._objectId = newId; };
    
    return UniqueObject;
})();