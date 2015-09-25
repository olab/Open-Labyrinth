function empty(mixed_var){
    return ( typeof(mixed_var) == 'undefined' || mixed_var === 0.0 || mixed_var === '' || mixed_var === 0
    || mixed_var === '0' || mixed_var === null || mixed_var === false || ($.isPlainObject(mixed_var) && $.isEmptyObject(mixed_var))
    || ((typeof(mixed_var)=='object' || $.isArray(mixed_var)) && mixed_var.length == 0) );
}