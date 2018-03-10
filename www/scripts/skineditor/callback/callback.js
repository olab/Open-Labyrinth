/**
 * Represet unique callback
 * This is class immutable
 * This is generic class
 */
var Callback = (function() {
    /**
     * Default constructor
     * 
     * @param {*} id - callback Id
     * @param {*} callback - delegate
     * @constructor
     */
    function Callback(id, callback) {
        this._id       = id;
        this._callback = callback;
    };
    
    /**
     * Get callback Id
     * 
     * @return {*} - callback Id
     */
    Callback.prototype.GetId = function() { return this._id; };
    
    /**
     * Get delegate
     * 
     * @return {*} - delegate
     */
    Callback.prototype.GetCallback = function() { return this._callback; };
    
    Callback.prototype.Call = function(sender, args) {
        if(this._callback === null           || 
           sender         === null           || 
           !(sender instanceof UniqueObject) ||
           sender.GetId() === this._id) { return; }
        
        this._callback(sender, args);
    }
    
    /**
     * Equal callback
     * 
     * @param {Callback} callback - another callback
     * @return {boolean} - true if callbacks equal
     */
    Callback.prototype.Equal = function(callback) {
        if(callback === null || !(callback instanceof Callback)) { return false; }
        
        return (this.GetId() === callback.GetId());
    };
    
    return Callback;
})();