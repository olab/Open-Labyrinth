var H5P = H5P || {};
H5P.DocumentationTool = H5P.DocumentationTool || {};

/**
 * Naivgation Menu module
 * @external {jQuery} $ H5P.jQuery
 */
H5P.DocumentationTool.NavigationMenu = (function ($) {

  /**
   * Initialize module.
   * @param {Object} params Behavior settings
   * @param {Number} id Content identification
   * @returns {Object} NavigationMenu NavigationMenu instance
   */
  function NavigationMenu(docTool, navMenuLabel) {
    var self = this;
    this.$ = $(this);
    this.docTool = docTool;
    this.navMenuLabel = navMenuLabel;

    // Hide menu if body is clicked
    $('body').click(function () {
      self.$documentationToolContaner.removeClass('expanded');
    });
  }

  /**
   * Attach function called by H5P framework to insert H5P content into page.
   *
   * @param {jQuery} $container The container which will be appended to.
   */
  NavigationMenu.prototype.attach = function ($container) {
    var self = this;
    this.$documentationToolContaner = $container;

    var $navigationMenu = $('<div/>', {
      'class': 'h5p-navigation-menu'
    }).prependTo($container);

    var $navigationMenuHeader = $('<div>', {
      'class': 'h5p-navigation-menu-header'
    }).appendTo($navigationMenu);

    $('<span>', {
      'html': self.navMenuLabel
    }).appendTo($navigationMenuHeader);

    var $navigationMenuEntries = $('<div>', {
      'class': 'h5p-navigation-menu-entries'
    }).appendTo($navigationMenu);

    this.docTool.pageInstances.forEach(function (page, pageIndex) {
      var pageTitle = '';

      // Try to get page title
      try {
        pageTitle = page.getTitle();
      } catch (e) {
        throw new Error('Page does not have a getTitle() function - ' + e);
      }

      // Create page entry
      var $navigationMenuEntry = $('<div/>', {
        'class': 'h5p-navigation-menu-entry',
        'title': pageTitle,
        'role': 'button',
        'tabindex': '0'
      }).click(function () {
        self.docTool.movePage(pageIndex);
        $(this).blur();
      }).keydown(function (e) {
        var keyPressed = e.which;
        // 32 - space
        if (keyPressed === 32) {
          $(this).click();
          e.preventDefault();
        }
      }).data('pageTitle', pageTitle)
        .appendTo($navigationMenuEntries);

      $('<span>', {
        'html': pageTitle
      }).appendTo($navigationMenuEntry);

      // Add current class to the first item
      if (pageIndex === 0) {
        $navigationMenuEntry.addClass('current');
      }
    });

    this.$navigationMenuHeader = $navigationMenuHeader;
    this.$navigationMenu = $navigationMenu;
    this.$navigationMenuEntries = $navigationMenuEntries;
  };

  /**
   * Updates current navigation menu entry with proper style
   * @param {Number} currentPageIndex Current page index
   */
  NavigationMenu.prototype.updateNavigationMenu = function (currentPageIndex) {
    this.$navigationMenuEntries.children().each(function (entryIndex) {
      if (currentPageIndex === entryIndex) {
        $(this).addClass('current');
      } else {
        $(this).removeClass('current');
      }
    });
  };

  /**
   * Toggle responsive layout on or off
   * @param {boolean} isEnabled True to enable responsive layout
   */
  NavigationMenu.prototype.setResponsiveLayout = function (isEnabled) {
    var self = this;
    if (isEnabled) {
      // Bind click to expand navigation menu
      this.$navigationMenuHeader.unbind('click');
      this.$navigationMenuHeader.click(function () {
        self.$documentationToolContaner.toggleClass('expanded');
        return false;
      });
      // Add responsive class
      this.$documentationToolContaner.addClass('responsive');
    } else {
      // Remove click action and remove responsive classes
      this.$navigationMenuHeader.unbind('click');
      this.$documentationToolContaner.removeClass('expanded');
      this.$documentationToolContaner.removeClass('responsive');
    }
  };

  return NavigationMenu;

}(H5P.jQuery));
