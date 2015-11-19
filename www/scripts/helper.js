function empty(mixed_var){
    return ( typeof(mixed_var) == 'undefined' || mixed_var === 0.0 || mixed_var === '' || mixed_var === 0
    || mixed_var === '0' || mixed_var === null || mixed_var === false || ($.isPlainObject(mixed_var) && $.isEmptyObject(mixed_var))
    || ((typeof(mixed_var)=='object' || $.isArray(mixed_var)) && mixed_var.length == 0) );
}

function setJSCookie(cname, cvalue)
{
    var d = new Date();
    d.setTime(d.getTime() + (2*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires + "; path=/";
}

function getJSCookie(cname)
{
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}

function deleteJSCookie(cname)
{
    document.cookie = cname + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
}