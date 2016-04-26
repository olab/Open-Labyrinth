/*global Blob, saveAs, JSZipUtils, Docxgen */
var H5P = H5P || {};

/**
 * Class responsible for creating an export page
 */
H5P.ExportPage = (function ($) {

  var isMobile = {
    Android: function () {
      return (/Android/i).test(navigator.userAgent);
    },
    BlackBerry: function () {
      return (/BlackBerry/i).test(navigator.userAgent);
    },
    iOS: function () {
      return (/iPhone|iPad|iPod/i).test(navigator.userAgent);
    },
    ie9: function () {
      return (/MSIE 9/i).test(navigator.userAgent);
    },
    Windows: function () {
      return (/IEMobile/i).test(navigator.userAgent);
    },
    any: function () {
      return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Windows());
    }
  };

  /**
   * Display a pop-up containing an exportable text area with action buttons.
   *
   * @param {String} header Header message
   * @param {jQuery} $body The container which message dialog will be appended to
   * @param {String} selectAllTextLabel Select all text button label
   * @param {String} exportTextLabel Export text button label
   * @param {String} templatePath Path to docx template
   * @param {Object} templateContent Object containing template content
   */
  function ExportPage(header, $body, selectAllTextLabel, exportTextLabel, templatePath, templateContent) {
    var self = this;

    this.templatePath = templatePath;
    this.templateContent = templateContent;

    // Standard labels:
    var standardSelectAllTextLabel = 'Select all';
    var standardExportTextLabel = 'Export text';

    if (selectAllTextLabel !== undefined) {
      standardSelectAllTextLabel = selectAllTextLabel;
    }

    if (exportTextLabel !== undefined) {
      standardExportTextLabel = exportTextLabel;
    }

    var exportPageTemplate =
      '<div class="joubel-create-document">' +
      ' <div class="joubel-exportable-header">' +
      '   <div class="joubel-exportable-header-inner">' +
      '     <div class="joubel-exportable-header-text">' +
      '       <span>' + header + '</span>' +
      '     </div>' +
      '     <div class="joubel-export-page-close" title="Exit" role="button" tabindex="0"></div>' +
      '     <div class="joubel-exportable-copy-button" title ="' + standardSelectAllTextLabel + '" role="button" tabindex="0">' +
      '       <span>' + standardSelectAllTextLabel + '</span>' +
      '     </div>' +
      '     <div class="joubel-exportable-export-button" title="' + standardExportTextLabel + '" role="button" tabindex="0">' +
      '       <span>' + standardExportTextLabel + '</span>' +
      '     </div>' +
      '   </div>' +
      ' </div>' +
      ' <div class="joubel-exportable-body">' +
      '   <div class="joubel-exportable-area"></div>' +
      ' </div>' +
      '</div>';

    this.$inner = $(exportPageTemplate);

    // Replace newlines with html line breaks
    var $bodyReplacedLineBreaks = $body.replace(/(?:\r\n|\r|\n)/g, '<br />');

    // Append body to exportable area
    self.$exportableArea = $('.joubel-exportable-area', self.$inner).append($bodyReplacedLineBreaks);

    self.initExitExportPageButton();
    self.initExportButton();
    self.initSelectAllTextButton();

    // Does not work on IE9 and mobiles except android
    if (isMobile.ie9() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Windows()) {
      self.$exportButton.remove();
    }

    // Does not work on mobiles, but works on IE9
    if (isMobile.any()) {
      self.$selectAllTextButton.remove();
    }

    // Initialize resize listener for responsive design
    this.initResizeFunctionality();

    return this.$inner;
  }

  /**
   * Initialize exit export page button
   */
  ExportPage.prototype.initExitExportPageButton = function () {
    var self = this;
    // Exit export page event
    $('.joubel-export-page-close', self.$inner).click(function () {
      //Remove export page.
      self.$inner.remove();
      $(this).blur();
    }).keydown(function (e) {
      var keyPressed = e.which;
      // 32 - space
      if (keyPressed === 32) {
        $(this).click();
        e.preventDefault();
      }
    });
  };

  /**
   * Initialize export button interactions
   */
  ExportPage.prototype.initExportButton = function () {
    var self = this;
    // Export document button event
    self.$exportButton = $('.joubel-exportable-export-button', self.$inner).click(function () {
      self.saveText(self.$exportableArea.html());
      $(this).blur();
    }).keydown(function (e) {
      var keyPressed = e.which;
      // 32 - space
      if (keyPressed === 32) {
        $(this).click();
        e.preventDefault();
      }
    });
  };


  /**
   * Initialize select all text button interactions
   */
  ExportPage.prototype.initSelectAllTextButton = function () {
    var self = this;
    // Select all text button event
    self.$selectAllTextButton = $('.joubel-exportable-copy-button', self.$inner).click(function () {
      self.selectText(self.$exportableArea);
    }).keydown(function (e) {
      var keyPressed = e.which;
      // 32 - space
      if (keyPressed === 32) {
        $(this).click();
        e.preventDefault();
      }
    });
  };

  /**
   * Initializes listener for resize and performs initial resize when rendered
   */
  ExportPage.prototype.initResizeFunctionality = function () {
    var self = this;

    // Listen for window resize
    $(window).resize(function () {
      self.resize();
    });

    // Initialize responsive view when view is rendered
    setTimeout(function () {
      self.resize();
    }, 0);
  };

  /**
   * Select all text in container
   * @param {jQuery} $container Container containing selected text
   */
  ExportPage.prototype.selectText = function ($container) {
    var doc = document;
    var text = $container.get(0);
    var range;
    var selection;

    if (doc.body.createTextRange) {
      range = document.body.createTextRange();
      range.moveToElementText(text);
      range.select();
    } else if (window.getSelection) {
      selection = window.getSelection();
      range = document.createRange();
      range.selectNodeContents(text);
      selection.removeAllRanges();
      selection.addRange(range);
    }
  };

  /**
   * Save html string to file
   * @param {string} html html string
   */
  ExportPage.prototype.saveText = function (html) {
    var self = this;

    if (this.templatePath === undefined) {
      // Use old method

      // Save it as a file:
      var blob = new Blob([this.createDocContent(html)], {
        type: "application/msword;charset=utf-8"
      });
      saveAs(blob, 'exported-text.doc');
    }
    else {
      var loadFile = function (url, callback) {
        JSZipUtils.getBinaryContent(url, function (err, data) {
          callback(null, data);
        });
      };

      loadFile(self.templatePath, function (err, content) {
        var doc = new Docxgen(content);
        if (self.templateContent !== undefined) {
          doc.setData(self.templateContent); //set the templateVariables
        }
        doc.render(); //apply them
        var out = doc.getZip().generate({type: "blob"}); //Output the document using Data-URI
        saveAs(out, "exported-text.docx");
      });
    }



  };

  /**
   * Create doc content from html
   * @param {string} html Html content
   * @returns {string} html embedded content
   */
  ExportPage.prototype.createDocContent = function (html) {
    // Create HTML:
    // me + ta and other hacks to avoid that new relic injects script...
    return '<ht' + 'ml><he' + 'ad><me' + 'ta charset="UTF-8"></me' + 'ta></he' + 'ad><bo' + 'dy>' + html + '</bo' + 'dy></ht' + 'ml>';
  };

  /**
   * Responsive resize function
   */
  ExportPage.prototype.resize = function () {
    var self = this;
    var $innerTmp = self.$inner.clone()
      .css('position', 'absolute')
      .removeClass('responsive')
      .removeClass('no-title')
      .appendTo(self.$inner.parent());

    // Determine if view should be responsive
    var $headerInner = $('.joubel-exportable-header-inner', $innerTmp);
    var leftMargin = parseInt($('.joubel-exportable-header-text', $headerInner).css('font-size'), 10);
    var rightMargin = parseInt($('.joubel-export-page-close', $headerInner).css('font-size'), 10);

    var dynamicRemoveLabelsThreshold = this.calculateHeaderThreshold($innerTmp, (leftMargin + rightMargin));
    var headerWidth = $headerInner.width();

    if (headerWidth <= dynamicRemoveLabelsThreshold) {
      self.$inner.addClass('responsive');
      $innerTmp.addClass('responsive');
    } else {
      self.$inner.removeClass('responsive');
      $innerTmp.remove();
      return;
    }

    // Determine if view should have no title
    headerWidth = $headerInner.width();
    var dynamicRemoveTitleThreshold = this.calculateHeaderThreshold($innerTmp, leftMargin);

    if (headerWidth <= dynamicRemoveTitleThreshold) {
      self.$inner.addClass('no-title');
    } else {
      self.$inner.removeClass('no-title');
    }

    $innerTmp.remove();
  };

  /**
   * Calculates width of header elements
   */
  ExportPage.prototype.calculateHeaderThreshold = function ($container, margin) {
    var staticPadding = 1;

    if (margin === undefined || isNaN(margin)) {
      margin = 0;
    }

    // Calculate elements width
    var $exportButtonTmp = $('.joubel-exportable-export-button', $container);
    var $selectTextButtonTmp = $('.joubel-exportable-copy-button', $container);
    var $removeDialogButtonTmp = $('.joubel-export-page-close', $container);
    var $titleTmp = $('.joubel-exportable-header-text', $container);

    var dynamicThreshold = $exportButtonTmp.outerWidth() +
      $selectTextButtonTmp.outerWidth() +
      $removeDialogButtonTmp.outerWidth() +
      $titleTmp.outerWidth();

    return dynamicThreshold + margin + staticPadding;
  };

  return ExportPage;
}(H5P.jQuery));
