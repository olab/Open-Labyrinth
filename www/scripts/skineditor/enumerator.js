/**
 * Abstract class
 * Basic enumerator class
 */
var Enumerator = (function() {

    function Enumerator() { };

    /**
     * Move to next item in collection
     *
     * @return {boolean} - false if end of collection or error
     */
    Enumerator.prototype.MoveNext = function() {};

    /**
     * Get current item
     *
     * @return {*} - current item
     */
    Enumerator.prototype.GetCurrentItem = function() {};

    /**
     * Setup current Enumerator to begin state
     */
    Enumerator.prototype.Begin = function() {};

    return Enumerator;
})();