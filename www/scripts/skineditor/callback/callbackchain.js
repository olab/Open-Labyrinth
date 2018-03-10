/**
 * Callback chain class
 */
var CallbackChain = (function(parent) {
    inherit(parent, CallbackChain);
    
    /**
     * Private callback chain iterator class
     */
    var CallbackChainEnumetor = (function(parent) {
        inherit(parent, CallbackChainEnumetor);
        
        function CallbackChainEnumetor(chain) {
            this._chain        = chain;
            this._length       = chain.length;
            this._currentIndex = -1;
        };
        
        CallbackChainEnumetor.prototype.MoveNext = function() {
            this._currentIndex++;

            return (this._currentIndex >= 0 && this._currentIndex < this._length);
        };

        CallbackChainEnumetor.prototype.GetCurrentItem = function() {
            return (this._currentIndex >= 0 && this._currentIndex < this._length) ? this._chain[this._currentIndex]
                                                                                  : null;
        };

        CallbackChainEnumetor.prototype.Begin = function() {
            this._currentIndex = -1;
            this._length       = this._chain.length;
        };
        
        return CallbackChainEnumetor;
    })(Enumerator);
    
    /**
     * Default constructor
     * 
     * @constructor
     */
    function CallbackChain() {
        this._callbacks = [];
    };
    
    /**
     * Add callback into chain
     * 
     * @param {Callback} callback - callback
     */
    CallbackChain.prototype.AddCallback = function(callback) {
        if(callback === null || !(callback instanceof Callback)) {
            throw new Error('CallbackChain.AddCallback: callback must be intance of "Callback" and not be null');
        }
        
        this._callbacks.push(callback);
    };
    
    CallbackChain.prototype.RemoveCallback = function(id) {
        for(var i = this._callbacks.length; i--;) {
            if(this._callbacks[i].GetId() === id) {
                this._callbacks.splice(i, 1);
                break;
            }
        }
    };
    
    CallbackChain.prototype.Clear = function() {
        this._callbacks = [];
    }
    
    /**
     * Get callback chain iterator
     * 
     * @return {* -> Iterator} - callback chain iterator
     */
    CallbackChain.prototype.GetEnumerator = function() {
        if(this._callbacks === null) {
            throw new Error('CallbackChain._callbacks: Must be object not null');
        }
        
        return new CallbackChainEnumetor(this._callbacks);
    }
    
    return CallbackChain;
})(Enumerable);