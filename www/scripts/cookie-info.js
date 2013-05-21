function setCookieInfo() {
    var browser = $.browser.chrome = /chrome/.test(navigator.userAgent.toLowerCase());
    if($.browser.mozilla)
        browser = 'firefox';
    else if($.browser.msie)
        browser = 'ie';
    else if($.browser.opera)
        browser = 'opera';
    else if($.browser.chrome)
        browser = 'chrome';
    else if($.browser.safari)
        browser = 'safari';
    else
        browser = 'unknown';

    document.cookie='resolution=' +
        Math.max(screen.width,screen.height) +
        ("devicePixelRatio" in window ? ","+devicePixelRatio : ",1") +
        ',' + Math.min(screen.width,screen.height)
        '; path=/';

    document.cookie='browser=' + browser + ', ' + $.browser.version + '; path=/';
}

setCookieInfo();