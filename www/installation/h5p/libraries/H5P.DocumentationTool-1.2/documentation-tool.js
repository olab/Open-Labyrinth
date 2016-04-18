var H5P = H5P || {};

/**
 * Documentation tool module
 * @external {jQuery} $ H5P.jQuery
 */
H5P.DocumentationTool = (function ($, NavigationMenu, JoubelUI, EventDispatcher) {
  // CSS Classes:
  var MAIN_CONTAINER = 'h5p-documentation-tool';
  var PAGES_CONTAINER = 'h5p-documentation-tool-page-container';
  var PAGE_INSTANCE = 'h5p-documentation-tool-page';
  var FOOTER = 'h5p-documentation-tool-footer';

  /**
   * Initialize module.
   * @param {Object} params Behavior settings
   * @param {Number} id Content identification
   * @returns {Object} DocumentationTool DocumentationTool instance
   */
  function DocumentationTool(params, id) {
    var self = this;
    this.$ = $(this);
    this.id = id;

    // Set default behavior.
    this.params = $.extend({}, {
      taskDescription: 'Documentation Tool',
      pagesList: []
    }, params);

    if (params.taskDescription === undefined && params.navMenuLabel !== undefined) {
      this.params.taskDescription = params.navMenuLabel;
    }

    EventDispatcher.call(this);

    this.on('resize', self.resize, self);
  }

  DocumentationTool.prototype = Object.create(EventDispatcher.prototype);
  DocumentationTool.prototype.constructor = DocumentationTool;

  /**
   * Attach function called by H5P framework to insert H5P content into page.
   *
   * @param {jQuery} $container The container which will be appended to.
   */
  DocumentationTool.prototype.attach = function ($container) {
    var self = this;
    this.pageInstances = [];
    this.currentPageIndex = 0;

    this.$inner = $container.addClass(MAIN_CONTAINER);

    this.$mainContent = $('<div/>', {
      'class': 'h5p-documentation-tool-main-content'
    }).appendTo(this.$inner);

    // Create pages
    var $pagesContainer = self.createPages().appendTo(this.$mainContent);
    self.$pagesArray = $pagesContainer.children();

    // Create navigation menu
    var navigationMenu = new NavigationMenu(self, this.params.taskDescription);
    navigationMenu.attach(this.$mainContent);

    if (this.$inner.children().length) {
      self.$pagesArray.eq(self.currentPageIndex).addClass('current');
    }

    this.navigationMenu = navigationMenu;

    self.resize();
  };

  /**
   * Creates the footer.
   * @returns {jQuery} $footer Footer element
   */
  DocumentationTool.prototype.createFooter = function () {
    var $footer = $('<div>', {
      'class': FOOTER
    });

    // Next page button
    this.createNavigationButton(1)
      .appendTo($footer);

    // Previous page button
    this.createNavigationButton(-1)
      .appendTo($footer);

    return $footer;
  };

  /**
   * Create navigation button
   * @param {Number} moveDirection An integer for how many pages the button will move, and in which direction
   * @returns {*}
   */
  DocumentationTool.prototype.createNavigationButton = function (moveDirection) {
    var self = this;
    var navigationText = 'next';
    if (moveDirection === -1) {
      navigationText = 'prev';
    }

    var $navButton = JoubelUI.createSimpleRoundedButton()
      .addClass('h5p-navigation-button-' + navigationText)
      .click(function () {
        self.movePage(self.currentPageIndex + moveDirection);
      });

    return $navButton;
  };

  /**
   * Populate container and array with page instances.
   * @returns {jQuery} Container
   */
  DocumentationTool.prototype.createPages = function () {
    var self = this;

    var $pagesContainer = $('<div>', {
      'class': PAGES_CONTAINER
    });

    this.params.pagesList.forEach(function (page) {
      var $pageInstance = $('<div>', {
        'class': PAGE_INSTANCE
      }).appendTo($pagesContainer);

      var singlePage = H5P.newRunnable(page, self.id);
      if (singlePage.libraryInfo.machineName === 'H5P.DocumentExportPage') {
        singlePage.setExportTitle(self.params.taskDescription);
      }
      singlePage.attach($pageInstance);
      self.createFooter().appendTo($pageInstance);
      self.pageInstances.push(singlePage);

      singlePage.on('resize', function () {
        self.trigger('resize');
      });
    });

    return $pagesContainer;
  };

  /**
   * Moves the documentation tool to the specified page
   * @param {Number} toPageIndex Move to this page index
   */
  DocumentationTool.prototype.movePage = function (toPageIndex) {
    var self = this;

    // Invalid value
    if ((toPageIndex + 1 > this.$pagesArray.length) || (toPageIndex < 0)) {
      return;
    }

    var assessmentGoals = self.getGoalAssessments(self.pageInstances);
    var newGoals = self.getGoals(self.pageInstances);
    assessmentGoals.forEach(function (assessmentPage) {
      newGoals = self.mergeGoals(newGoals, assessmentPage);
    });

    // Update page depending on what page type it is
    self.updatePage(toPageIndex, newGoals);

    this.$pagesArray.eq(this.currentPageIndex).removeClass('current');
    this.currentPageIndex = toPageIndex;
    this.$pagesArray.eq(this.currentPageIndex).addClass('current');

    // Update navigation menu
    this.navigationMenu.updateNavigationMenu(this.currentPageIndex);

    // Scroll to top
    this.scrollToTop();
  };

  /**
   * Scroll to top if changing page and below y position is above threshold
   */
  DocumentationTool.prototype.scrollToTop = function () {
    var staticScrollToTopPadding = 90;
    var yPositionThreshold = 75;

    // Scroll to top of content type if above y threshold
    if ($(window).scrollTop() - $(this.$inner).offset().top > yPositionThreshold) {
      $(window).scrollTop(this.$inner.offset().top - staticScrollToTopPadding);
    }
  };

  /**
   * Update page depending on what type of page it is
   * @param {Object} toPageIndex Page object that will be updated
   * @param {Array} newGoals Array containing updated goals
   */
  DocumentationTool.prototype.updatePage = function (toPageIndex, newGoals) {
    var self = this;
    var pageInstance = self.pageInstances[toPageIndex];

    if (pageInstance.libraryInfo.machineName === 'H5P.GoalsAssessmentPage') {
      self.setGoals(self.pageInstances, newGoals);
    } else if (pageInstance.libraryInfo.machineName === 'H5P.DocumentExportPage') {

      // Check if all required input fields are filled
      var allRequiredInputsAreFilled = self.checkIfAllRequiredInputsAreFilled(self.pageInstances);
      self.setRequiredInputsFilled(self.pageInstances, allRequiredInputsAreFilled);

      // Get all input fields, goals and goal assessments
      var allInputs = self.getDocumentExportInputs(self.pageInstances);
      self.setDocumentExportOutputs(self.pageInstances, allInputs);
      self.setDocumentExportGoals(self.pageInstances, newGoals);
    }
  };

  /**
   * Merge assessment goals and newly created goals
   *
   * @returns {Array} newGoals Merged goals list with updated assessments
   */
  DocumentationTool.prototype.mergeGoals = function (newGoals, assessmentGoals) {
    // Not an assessment page
    if (!assessmentGoals.length) {
      return newGoals;
    }
    newGoals.forEach(function (goalPage, pageIndex) {
      goalPage.forEach(function (goalInstance) {
        var result = $.grep(assessmentGoals[pageIndex], function (assessmentInstance) {
          return assessmentInstance.getUniqueId() === goalInstance.getUniqueId();
        });
        if (result.length) {
          goalInstance.goalAnswer(result[0].goalAnswer());
        }
      });
    });
    return newGoals;
  };

  /**
   * Gets goals assessments from all goals assessment pages and returns update goals list.
   *
   * @param {Array} pageInstances Array of pages contained within the documentation tool
   * @returns {Array} goals Updated goals list
   */
  DocumentationTool.prototype.getGoalAssessments = function (pageInstances) {
    var goals = [];
    pageInstances.forEach(function (page) {
      if (page.libraryInfo.machineName === 'H5P.GoalsAssessmentPage') {
        goals.push(page.getAssessedGoals());
      }
    });
    return goals;
  };

  /**
   * Retrieves all input fields from the documentation tool
   * @returns {Array} inputArray Array containing all inputs of the documentation tool
   */
  DocumentationTool.prototype.getDocumentExportInputs = function (pageInstances) {
    var inputArray = [];
    pageInstances.forEach(function (page) {
      var pageInstanceInput = [];
      var title = '';
      if (page.libraryInfo.machineName === 'H5P.StandardPage') {
        pageInstanceInput = page.getInputArray();
        title = page.getTitle();
      }
      inputArray.push({inputArray: pageInstanceInput, title: title});
    });

    return inputArray;
  };

  /**
   * Checks if all required inputs are filled
   * @returns {boolean} True if all required inputs are filled
   */
  DocumentationTool.prototype.checkIfAllRequiredInputsAreFilled = function (pageInstances) {
    var allRequiredInputsAreFilled = true;
    pageInstances.forEach(function (page) {
      if (page.libraryInfo.machineName === 'H5P.StandardPage') {
        if (!page.requiredInputsIsFilled()) {
          allRequiredInputsAreFilled = false;
        }
      }
    });

    return allRequiredInputsAreFilled;
  };

  /**
   * Gets goals from all goal pages and returns updated goals list.
   *
   * @param {Array} pageInstances Array containing all pages.
   * @returns {Array} goals Updated goals list.
   */
  DocumentationTool.prototype.getGoals = function (pageInstances) {
    var goals = [];
    pageInstances.forEach(function (page) {
      if (page.libraryInfo.machineName === 'H5P.GoalsPage') {
        goals.push(page.getGoals());
      }
    });
    return goals;
  };

  /**
   * Insert goals to all goal assessment pages.
   * @param {Array} pageInstances Page instances
   * @param {Array} goals Array of goals.
   */
  DocumentationTool.prototype.setGoals = function (pageInstances, goals) {
    pageInstances.forEach(function (page) {
      if (page.libraryInfo.machineName === 'H5P.GoalsAssessmentPage') {
        page.updateAssessmentGoals(goals);
      }
    });
  };

  /**
   * Sets the output for all document export pages
   * @param {Array} inputs Array of input strings
   */
  DocumentationTool.prototype.setDocumentExportOutputs  = function (pageInstances, inputs) {
    pageInstances.forEach(function (page) {
      if (page.libraryInfo.machineName === 'H5P.DocumentExportPage') {
        page.updateOutputFields(inputs);
      }
    });
  };

  /**
   * Sets the output for all document export pages
   * @param {Array} inputs Array of input strings
   */
  DocumentationTool.prototype.setDocumentExportGoals  = function (pageInstances, newGoals) {
    var assessmentPageTitle = '';
    pageInstances.forEach(function (page) {
      if (page.libraryInfo.machineName === 'H5P.GoalsAssessmentPage') {
        assessmentPageTitle = page.getTitle();
      }
    });

    pageInstances.forEach(function (page) {
      if (page.libraryInfo.machineName === 'H5P.DocumentExportPage') {
        page.updateExportableGoals({inputArray: newGoals, title: assessmentPageTitle});
      }
    });
  };

  /**
   * Sets the required inputs filled boolean in all document export pages
   * @param {boolean} isRequiredInputsFilled True if all required inputs are filled
   */
  DocumentationTool.prototype.setRequiredInputsFilled  = function (pageInstances, isRequiredInputsFilled) {
    pageInstances.forEach(function (page) {
      if (page.libraryInfo.machineName === 'H5P.DocumentExportPage') {
        page.updateRequiredInputsFilled(isRequiredInputsFilled);
      }
    });
  };

  /**
   * Resize function for responsiveness.
   */
  DocumentationTool.prototype.resize = function () {
    // Width calculations
    this.adjustDocumentationToolWidth();
    this.adjustNavBarHeight();
  };

  /**
   * Adjusts navigation menu minimum height
   */
  DocumentationTool.prototype.adjustNavBarHeight = function () {
    var headerHeight = this.navigationMenu.$navigationMenuHeader.get(0).getBoundingClientRect().height +
        parseFloat(this.navigationMenu.$navigationMenuHeader.css('margin-top')) +
        parseFloat(this.navigationMenu.$navigationMenuHeader.css('margin-bottom'));
    var entriesHeight = this.navigationMenu.$navigationMenuEntries.get(0).getBoundingClientRect().height;
    var minHeight = headerHeight + entriesHeight;
    this.$mainContent.css('min-height', minHeight + 'px');
  };

  /**
   * Resizes navigation menu depending on task width
   */
  DocumentationTool.prototype.adjustDocumentationToolWidth = function () {
    // Show responsive design when width relative to font size is less than static threshold
    var staticResponsiveLayoutThreshold = 40;
    var relativeWidthOfContainer = this.$inner.width() / parseInt(this.$inner.css('font-size'), 10);
    var responsiveLayoutRequirement = relativeWidthOfContainer < staticResponsiveLayoutThreshold;
    this.navigationMenu.setResponsiveLayout(responsiveLayoutRequirement);
  };

  return DocumentationTool;
}(H5P.jQuery, H5P.DocumentationTool.NavigationMenu, H5P.JoubelUI, H5P.EventDispatcher));
