/**
 * Abstract enumberable class
 */
var Enumerable = (function() {
    /**
     * Default constructor
     */
    function Enumerable() { };
    
    /**
     * Virtual method
     * Return calss enumerator
     */
    Enumerable.prototype.GetEnumerator = function() { };
    
    return Enumerable;
})();