var BrowserUpdateWarning = function() {
    var self = this,
        browserName = '',
        options = {
            showPopup: true,

            // Allow user to close prompt and continue to website
            allowContinue: true,

            imagesDirectory: $('#browserWarningImages').val(),

            shade: true,
            opacity: 88,

            // Minimum Versions
            minVersion_ie: 9.0,
            minVersion_safari: 534.48,
            minVersion_firefox: 19,
            minVersion_chrome: 24,
            minVersion_opera: 12.1,

            // Update Links
            updateLink_ie: 'http://windows.microsoft.com/en-US/internet-explorer/downloads/ie',
            updateLink_safari: 'http://www.apple.com/safari/download/',
            updateLink_firefox: 'http://getfirefox.com/',
            updateLink_chrome: 'https://www.google.com/chrome',
            updateLink_opera: 'http://www.opera.com/download/',

            downloadOptions: ['ie','safari','firefox','chrome','opera']
        };
    
    this.Check = function() {
        var browsers = ['ie','safari','firefox','chrome','opera'],
            browser = 'unknown',
            version = $.browser.version,
            userAgent = navigator.userAgent.toLowerCase();
        
        $.browser.chrome = /chrome/.test(navigator.userAgent.toLowerCase()); 
        
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

        if(browser == 'chrome') {
            userAgent = userAgent.substring(userAgent.indexOf('chrome/') +7);
            userAgent = userAgent.substring(0,userAgent.indexOf('.'));
            version = userAgent;
        }
        
        version = parseFloat(version);
        browserName = browser;
        
        $.each(browsers, function(index, value){
            if(browser === value && version < options['minVersion_' + value]) {
                if (options.showPopup && $.cookie('plg_system_browserupdatewarning') === undefined) {
                    ShowBrowserUpdateWarring();
                }
                return;
            }
        }); 
    }
    
    var ShowBrowserUpdateWarring = function() {
        var updateLink = options['updateLink_'+browserName];
        
        html  = '<div id="BrowserUpdateWarningShade"></div>\
                 <div id="BrowserUpdateWarningWrapper"><div id="BrowserUpdateWarningContent">\
                     <h1>It\'s time to upgrade your browser.</h1>\
                     <div class="yourBrowser">\
                     <img src="'+options.imagesDirectory+'icon-'+browserName+'.png" />\n\
                     <a href="'+updateLink+'" target="_blank">Click here to update your current browser &raquo;</a>\
                 </div>\
                 <div class="otherBrowsers">';
        
        $.each(options.downloadOptions, function(index, key) {
            if (key != browserName) {
                html += '<a href="'+options['updateLink_'+key]+'">\
                            <img src="'+options.imagesDirectory+'icon-'+key+'.png" />' + key.toUpperCase() + ' &raquo;\
                         </a>';
            }
        });
        
        html += '</div>\
                 <div class="whyUpgrade">\
                     <h2>Why you should update:</h2>\
                     <ul>\
                        <li>Websites load faster</li>\
                        <li>Websites render correctly</li>\
                        <li>Safer Browsing</li>\
                        <li>Other great features</li>\
                    </ul>\
                 </div>';
        
        if (options.allowContinue) {
            html += '<a href="javascript:void(0);" class="continueToSite" onclick="\
                        $(\'#BrowserUpdateWarningWrapper\').css(\'display\',\'none\');';
            if (options.shade) html += '$(\'#BrowserUpdateWarningShade\').css(\'display\',\'none\');';
            html += '$.cookie(\'plg_system_browserupdatewarning\', 1);">CONTINUE &raquo;</a>';
        }
        
        html += '<div style="clear:both"></div></div></div>';
        
        $('body').append(html);
    }
}