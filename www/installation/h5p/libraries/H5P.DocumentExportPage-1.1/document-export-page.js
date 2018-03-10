var H5P = H5P || {};

/**
 * Document Export Page module
 * @external {jQuery} $ H5P.jQuery
 */
H5P.DocumentExportPage = (function ($, JoubelUI) {
  // CSS Classes:
  var MAIN_CONTAINER = 'h5p-document-export-page';

  /**
   * Initialize module.
   * @param {Object} params Behavior settings
   * @param {Number} id Content identification
   * @returns {Object} DocumentExportPage DocumentExportPage instance
   */
  function DocumentExportPage(params, id) {
    this.$ = $(this);
    this.id = id;

    this.inputArray = [];
    this.exportTitle = '';
    this.requiredInputsAreFilled = true;


    // Set default behavior.
    this.params = $.extend({}, {
      title: 'Document export',
      description: '',
      createDocumentLabel: 'Create document',
      selectAllTextLabel: 'Select all text',
      exportTextLabel: 'Export text',
      requiresInputErrorMessage: 'One or more required input fields need to be filled.',
      helpTextLabel: 'Read more',
      helpText: 'Help text'
    }, params);
  }

  /**
   * Attach function called by H5P framework to insert H5P content into page.
   *
   * @param {jQuery} $container The container which will be appended to.
   */
  DocumentExportPage.prototype.attach = function ($container) {
    var self = this;

    this.$wrapper = $container;

    this.$inner = $('<div>', {
      'class': MAIN_CONTAINER
    }).prependTo($container);

    var documentExportTemplate =
        '<div class="export-header">' +
        ' <div role="button" tabindex="0" class="export-help-text">{{{helpTextLabel}}}</div>' +
        ' <div class="export-title">{{{title}}}</div>' +
        '</div>' +
        '<div class="export-description">{{{description}}}</div>' +
        '<div class="export-footer"></div>' +
        '<div class="export-error-message">{{{requiresInputErrorMessage}}}</div>';

    /*global Mustache */
    self.$inner.append(Mustache.render(documentExportTemplate, self.params));

    self.createHelpTextButton();

    var $footer = $('.export-footer', self.$inner);
    self.createDocumentExportButton().appendTo($footer);
  };

  /**
   * Creates button for creating a document from stored input array
   * @returns {jQuery} $exportDocumentButton Button element
   */
  DocumentExportPage.prototype.createDocumentExportButton = function () {
    var self = this;
    var $exportDocumentButton = JoubelUI.createSimpleRoundedButton(self.params.createDocumentLabel)
      .addClass('export-document-button')
      .click(function () {
        // Check if all required input fields are filled
        if (self.isRequiredInputsFilled()) {
          var exportDocument = new H5P.DocumentExportPage.CreateDocument(self.params, self.exportTitle, self.inputArray, self.inputGoals, self.getLibraryFilePath('exportTemplate.docx'));
          exportDocument.attach(self.$wrapper.parent().parent());
        }
      });

    return $exportDocumentButton;
  };

  /**
   * Create help text functionality for reading more about the task
   */
  DocumentExportPage.prototype.createHelpTextButton = function () {
    var self = this;

    if (this.params.helpText !== undefined && this.params.helpText.length) {

      // Create help button
      $('.export-help-text', this.$inner)
        .click(function () {
          var $helpTextDialog = new H5P.JoubelUI.createHelpTextDialog(self.params.title, self.params.helpText);
          $helpTextDialog.appendTo(self.$wrapper.parent().parent());
        }).keydown(function (e) {
          var keyPressed = e.which;
          // 32 - space
          if (keyPressed === 32) {
            $(this).click();
            e.preventDefault();
          }
          $(this).focus();
        });
    } else {
      $('.export-help-text', this.$inner).remove();
    }
  };

  DocumentExportPage.prototype.getTitle = function () {
    return this.params.title;
  };

  DocumentExportPage.prototype.setExportTitle = function (title) {
    this.exportTitle = title;
    return this;
  };

  DocumentExportPage.prototype.updateOutputFields = function (inputs) {
    this.inputArray = inputs;
    return this;
  };

  DocumentExportPage.prototype.updateExportableGoals = function (newGoals) {
    this.inputGoals = newGoals;
    return this;
  };

  DocumentExportPage.prototype.isRequiredInputsFilled = function () {
    return this.requiredInputsAreFilled;
  };

  DocumentExportPage.prototype.updateRequiredInputsFilled = function (requiredInputsAreFilled) {
    if (requiredInputsAreFilled) {
      this.$inner.removeClass('required-inputs-not-filled');
    } else {
      this.$inner.addClass('required-inputs-not-filled');
    }
    this.requiredInputsAreFilled = requiredInputsAreFilled;
    return this;
  };

  return DocumentExportPage;
}(H5P.jQuery, H5P.JoubelUI));
