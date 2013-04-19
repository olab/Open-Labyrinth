/*
---

name: BrowserUpdateWarning

description: Browser Version Check and Warn

version: 1.0.6

license: MIT-style license

authors:
  - Andy Fleming

requires: [Core/Class, Core/Object, Core/Element.Event]

provides: [BrowserUpdateWarning]

...
*/

//##################################################################################################################
//	BrowserUpdateWarning Class
//##################################################################################################################

	var BrowserUpdateWarning = new Class({

		Implements: [Options, Events],

	// ------------------------------------------------------------------------------------
	//	Options
	// ------------------------------------------------------------------------------------

		options: {

			showPopup: true,

			// Allow user to close prompt and continue to website
			allowContinue: false,

			imagesDirectory: 'images/',

			shade: true,
			opacity: 88,

			// Minimum Versions
			minVersion_ie: 9,
			minVersion_safari: 5.1,
			minVersion_firefox: 19,
			minVersion_chrome: 26,
			minVersion_opera: 12.1,

			// Update Links
			updateLink_ie: 'http://windows.microsoft.com/en-US/internet-explorer/downloads/ie',
			updateLink_safari: 'http://www.apple.com/safari/download/',
			updateLink_firefox: 'http://getfirefox.com/',
			updateLink_chrome: 'https://www.google.com/chrome',
			updateLink_opera: 'http://www.opera.com/download/',


			// A list of update options to show to the user
			downloadOptions: ['ie','safari','firefox','chrome','opera']

			// Events
			//onFailure: $empty
			//onSuccess: $empty

		},

	// ------------------------------------------------------------------------------------
	//	Initialize
	// ------------------------------------------------------------------------------------

		// Init
		initialize: function (options) {
			this.setOptions(options);
		},


	// ------------------------------------------------------------------------------------
	//	Check (and warn if necessary)
	// ------------------------------------------------------------------------------------

		check: function() {

			var self = this;

			var browsers = ['ie','safari','firefox','chrome','opera'];

			browsers.each(function(key,index) {

				if (Browser.name == key && Browser.version < self.options['minVersion_'+key]) {
					if (self.options.showPopup) { self.showBrowserUpdateWarning(); }
					self.fireEvent('onFailure');
					return;
				}
			});

			// If all requirements were met, fire success event
			self.fireEvent('onSuccess');


		},


	// ------------------------------------------------------------------------------------
	//	showBrowserUpdateWarning
	//		Shows warning; called in check()
	// ------------------------------------------------------------------------------------

		showBrowserUpdateWarning: function() {

			var self = this;

			// If the shade is turned on, show it
			if (self.options.shade) { self.showShade(); }

			var updateLink = self.options['updateLink_'+Browser.name];

			html  = '<div id="BrowserUpdateWarningContent">';
				html += '<h1>'+ window.plg_system_browserupdatewarning_language.TIMETOUPGRADE +'</h1>';
				html += '<div class="yourBrowser">';
					html += '<img src="'+self.options.imagesDirectory+'icon-'+Browser.name+'.png" />';
					html += '<a href="'+updateLink+'" target="_blank">'+ window.plg_system_browserupdatewarning_language.UPDATECURRENT +' &raquo;</a>';
				html += '</div>';
				html += '<div class="otherBrowsers">';
					this.options.downloadOptions.each(function(key,index) {
						if (key != Browser.name) {
							html += '<a href="'+self.options['updateLink_'+key]+'">';
							html += '<img src="'+self.options.imagesDirectory+'icon-'+key+'.png" />';
                                                        html += window.plg_system_browserupdatewarning_language[key.toUpperCase()];
							html += ' &raquo;</a>';
						}
					});
				html += '</div>';
				html += '<div class="whyUpgrade">';
					html += '<h2>'+ window.plg_system_browserupdatewarning_language.WHYSHOULDI +'</h2>';
					html += '<ul>';
						html += '<li>'+ window.plg_system_browserupdatewarning_language.WHYFASTER +'</li>';
						html += '<li>'+ window.plg_system_browserupdatewarning_language.WHYRENDER +'</li>';
						html += '<li>'+ window.plg_system_browserupdatewarning_language.WHYSAFER +'</li>';
						html += '<li>'+ window.plg_system_browserupdatewarning_language.WHYMORE +'</li>';
					html += '</ul>';
				html += '</div>';
				if (self.options.allowContinue) {
					html += '<a href="javascript:void(0);" class="continueToSite" onclick="';
					html += 'document.id(\'BrowserUpdateWarningWrapper\').setStyle(\'display\',\'none\');';
					if (self.options.shade) html += 'document.id(\'BrowserUpdateWarningShade\').setStyle(\'display\',\'none\');';
					html += 'var plg_system_browserupdatewarning_cookie = Cookie.write(\'plg_system_browserupdatewarning\', 1);';
                                        html += '">'+ window.plg_system_browserupdatewarning_language.CONTINUE +' &raquo;</a>';
				}
				html += '<div style="clear:both"></div>';



			html += '</div>';



			// Create DIV element and inject into body
			var div  = new Element('div', {id: 'BrowserUpdateWarningWrapper'});
			div.set('html',html).inject(document.body);

		},

	// ------------------------------------------------------------------------------------
	//	showShade
	// ------------------------------------------------------------------------------------

		showShade: function() {

			var opacity = this.options.opacity;

			// Create shade and append to body
			var shade = new Element('div',{id: 'BrowserUpdateWarningShade'});
			shade.setStyle('opacity',(opacity/100));
			shade.setStyle('-ms-filter','"progid:DXImageTransform.Microsoft.Alpha(Opacity='+opacity+')"');
			shade.setStyle('filter','alpha(opacity = '+opacity+')');

			shade.inject(document.body);

		}



	}); // End of Class: BrowserUpdateWarning