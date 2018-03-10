/**
 * Inherit util function
 *
 * @param {*} parent - parent class
 * @param {*} child - child class
 * @type {Function}
 */
var inherit = this.inherit || function(parent, child) {
    if(parent == null || child == null) {
        throw new Error("inherit: Arguments can't be null");
    }

    var F = function() {};
    F.prototype                 = parent.prototype;
    child.prototype             = new F();
    child.prototype.constructor = child;
    child.super                 = parent.prototype;
};

/**
 * GUID class
 */
var GUID = (function() {
    /**
     * Default constructor
     */
    function GUID() { };
    
    /**
     * Random generator
     * 
     * @return {char} - random character
     */
    GUID._S4 = function() {
        return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
    };
    
    /**
     * Generate GUID
     * 
     * @return {string} - GUID
     */
    GUID.Get = function() {
        return (this._S4() + this._S4() + "-" + this._S4() + "-4" + this._S4().substr(0,3) + "-" + this._S4() + "-" + this._S4() + this._S4() + this._S4()).toLowerCase();
    };
    
    return GUID;
})();