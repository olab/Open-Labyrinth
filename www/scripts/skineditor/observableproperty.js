/**
 * Observable property class
 */
var ObservableProperty = (function() {
    /**
     * Default constructor
     */
    function ObservableProperty() { 
        this._subscribers = new CallbackChain();
    };
    
    /**
     * Subscribe for this changing
     * 
     * @param {Callback} callback - subscriber callback
     */
    ObservableProperty.prototype.Subscribe = function(callback) {
        if(callback === null || !(callback instanceof Callback)) {
            throw new Error('ObservableProperty.Subscribe: callback must e instance of "Callback" and not be null');
        }
        
        this._subscribers.AddCallback(callback);
    };
    
    ObservableProperty.prototype.Unsubscribe = function(id) {
        this._subscribers.RemoveCallback(id);
    };
    
     ObservableProperty.prototype.UnsubscribeAll = function() {
        this._subscribers.Clear();
    }
    
    /**
     * Property changed event
     * 
     * @param {*} sender - sender object
     * @param {*} args - arguments
     */
    ObservableProperty.prototype.PropertyChanged = function(sender, args) {
        var iterator = null,
            item     = null;

        iterator = this._subscribers.GetEnumerator();
        while(iterator.MoveNext()) {
            item = iterator.GetCurrentItem();
            if(item !== null) { item.Call(sender, args); }
        }
    };
    
    return ObservableProperty;
})();