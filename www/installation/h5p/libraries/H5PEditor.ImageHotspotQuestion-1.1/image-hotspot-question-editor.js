/*global H5P, H5PEditor*/
H5PEditor.widgets.imageHotspotQuestion = H5PEditor.ImageHotspotQuestion = (function ($) {
  /** @constant {string} */
  var RECTANGLE = 'rectangle';
  /** @constant {string} */
  var CIRCLE = 'circle';

  /**
   * Initial width and height of feedback circle in pixels
   * @constant {number}
   */
  var INITIAL_FEEDBACK_SIZE = 40;

  /**
   * Initialize image hotspot question editor.
   *
   * @class H5PEditor.ImageHotspotQuestion
   * @param {Object} parent
   * @param {Object} field
   * @param {Object} params
   * @param {Function} setValue
   */
  function ImageHotspotQuestionEditor(parent, field, params, setValue) {

    // Set default params
    params = $.extend(true, {
      hotspot:[]
    }, params);
    setValue(field, params);

    /**
     * Keeps track of parent container
     * @type {Object}
     */
    this.parent = parent;

    /**
     * Keeps track of class parameters
     * @type {Object}
     */
    this.params = params;

    /**
     * Keeps track of created hotspots
     * @type {Array}
     */
    this.elements = [];

    /**
     * Keeps track of the hotspot figures that will be created and available from the toolbar
     * @type {[]} Array containing hotspot figures
     */
    this.buttonTypes = [CIRCLE, RECTANGLE];

    /**
     * True if a hotspot settings dialog is open
     * @type {boolean}
     */
    this.dialogOpen = false;

    /**
     * Task description semantics. Used to create task description field in editor.
     * @type {*[]}
     */
    this.taskDescriptionSemantics = [H5P.cloneObject(field.fields[0], true)];

    /**
     * Feedback semantics when no hotspots have been selected.
     * @type {*[]}
     */
    this.noneSelectedFeedbackSemantics = [H5P.cloneObject(field.fields[2], true)];

    /**
     * Hotspot settings semantics, used to make the popup on hotspots.
     * @type {Object|Array}
     */
    this.elementFields = H5P.cloneObject(field.fields[1].field.fields[0].fields, true);

    this.initQuestion();
  }

  /**
   * Initiate question, create html and activate editor functionality.
   */
  ImageHotspotQuestionEditor.prototype.initQuestion = function () {
    var self = this;

    // Locate image field
    this.findImage(function (imageField) {
      self.imageField = imageField;

      // Remove hotspots and close dialog when changing image
      self.imageField.changes.push(function () {
        // Remove all hotspots when image is changed.
        self.elements.forEach(function (element) {
          element.$element.remove();
        });
        self.params.hotspot = [];
        self.elements = [];
        // Close dialog
        if (self.dialogOpen) {
          self.hideDialog();
        }
      });
    });

    // Make sure widget can pass readies (used when processing semantics)
    this.passReadies = true;
    this.parent.ready(function () {
      self.passReadies = false;
    });

    this.createEditor();

    H5P.$window.on('resize', function () {
      self.resize();
    });
  };

  /**
   * Find image from semantics if it has been chosen.
   * @param {function} callback Callback function with image as parameter
   */
  ImageHotspotQuestionEditor.prototype.findImage = function (callback) {
    var self = this;

    this.parent.ready(function () {
      var imageField = H5PEditor.findField('backgroundImageSettings/backgroundImage', self.parent);

      if (!imageField) {
        throw H5PEditor.t('core', 'unknownFieldPath', {':path': imageField});
      }

      callback(imageField);
    });
  };

  /**
   * Used to append this widget to a wrapper.
   * @param {H5P.jQuery} $wrapper Container widget will be appended to.
   */
  ImageHotspotQuestionEditor.prototype.appendTo = function ($wrapper) {

    this.$editor.appendTo($wrapper);
  };

  /**
   * Attach editor.
   */
  ImageHotspotQuestionEditor.prototype.createEditor = function () {
    var html =
      '<div class="h5p-image-hotspot-question-editor">' +
      ' <div class="error-message">' + H5PEditor.t('H5PEditor.ImageHotspotQuestion', 'noImage') + '</div>' +
      ' <div class="task-description"></div>' +
      ' <div class="gui-wrapper">' +
      '   <div class="disabling-overlay hidden"></div>' +
      '   <div class="image-hotspot-dnb-wrapper"></div>' +
      '   <div class="image-hotspot-gui"></div>' +
      ' </div>' +
      ' <div class="h5peditor-field-description gui-wrapper-description">' + H5PEditor.t('H5PEditor.ImageHotspotQuestion', 'guiDescription') + '</div>' +
      ' <div class="none-selected-feedback"></div>' +
      '</div>';

    /**
     * Wrapper for question editor
     * @type {H5P.jQuery}
     */
    this.$editor = $(html);

    /**
     * Wrapper for the complete graphical interface
     * @type {H5P.jQuery}
     */
    this.$guiWrapper = $('.gui-wrapper', this.$editor);

    /**
     * An overlay that disables any actions while a hotspot settings dialog is open.
     * @type {H5P.jQuery}
     */
    this.$disablingOverlay = $('.disabling-overlay', this.$editor);

    /**
     * The graphical interface that is on top of the image, where hotspots are created and manipulated.
     * @type {H5P.jQuery}
     */
    this.$gui = $('.image-hotspot-gui', this.$editor);

    var $taskDescription = $('.task-description', this.$editor);
    var $dnbWrapper = $('.image-hotspot-dnb-wrapper', this.$editor);
    var $noneSelectedFeedback = $('.none-selected-feedback', this.$editor);

    this.createToolbar($dnbWrapper);
    this.createDialog();
    this.createHotspots();

    // Disable actions while hotspot dialog is open
    this.$disablingOverlay.click(function () {
      return false;
    });

    // Create semantics
    H5PEditor.processSemanticsChunk(this.taskDescriptionSemantics, this.params, $taskDescription, this);
    H5PEditor.processSemanticsChunk(this.noneSelectedFeedbackSemantics, this.params, $noneSelectedFeedback, this);
  };

  /**
   * Initialize dialog for editing hotspots
   */
  ImageHotspotQuestionEditor.prototype.createDialog = function () {
    var self = this;
    var dialog =
      '<div class="h5peditor-fluid-dialog">' +
      '  <div class="h5peditor-fd-inner"></div>' +
      '  <div class="h5peditor-fd-buttons">' +
      '    <button class="h5peditor-fd-button h5peditor-done">' + H5PEditor.t('H5PEditor.ImageHotspotQuestion', 'done') + '</button>' +
      '    <button class="h5peditor-fd-button h5peditor-remove">' + H5PEditor.t('H5PEditor.ImageHotspotQuestion', 'remove') + '</button>' +
      '  </div>' +
      '</div>';

    this.$dialog = $(dialog);
    $('.h5peditor-done', this.$dialog).click(function () {
      if (self.doneCallback() !== false) {
        self.hideDialog();
        setTimeout(function () {
          // Put focus back on element
          self.toolbar.focus(self.$prevFocusElement);
        }, 0);
      }
      return false;
    });

    $('.h5peditor-remove', this.$dialog).click(function () {
      self.removeCallback();
      self.hideDialog();
    });

    this.$dialog.appendTo(this.$guiWrapper);
    this.$dialoginner = $('.h5peditor-fd-inner', this.$dialog);
    this.$dialog.addClass('hidden');
  };

  /**
   * Create all hotspots found in params.
   */
  ImageHotspotQuestionEditor.prototype.createHotspots = function () {
    var self = this;

    // Add Elements
    var i;
    for (i = 0; i <= this.params.hotspot.length - 1; i += 1) {
      self.insertElement(i);
    }
  };

  /**
   * Creates the toolbar and enables attachment of hotspots.
   */
  ImageHotspotQuestionEditor.prototype.createToolbar = function ($dnbWrapper) {
    // Create toolbar and attach it
    this.createDragToolbar($dnbWrapper);
  };

  /**
   * Create toolbar with draggable figures, and drag functionality.
   * @param {H5P.jQuery} $wrapper Container for toolbar
   */
  ImageHotspotQuestionEditor.prototype.createDragToolbar = function ($wrapper) {
    var self = this;

    if (!!this.toolbar) {
      return;
    }

    // Activate toolbar, add buttons and attach it to $wrapper
    this.toolbar = new H5P.DragNBar(this.createButtons(), this.$gui, this.$guiWrapper);

    // Must set containerEm
    self.toolbar.dnr.setContainerEm(parseFloat(self.$gui.css('font-size')));

    // Add event handling
    self.toolbar.dnr.on('stoppedResizing', function (event) {
      var id = self.toolbar.$element.data('id');
      var hotspotParams = self.params.hotspot[id];

      var fontSize = parseInt(self.$gui.css('font-size'), 10);
      hotspotParams.computedSettings.width = (event.data.width * fontSize)  / (self.$gui.width() / 100);
      hotspotParams.computedSettings.height = (event.data.height * fontSize)  / (self.$gui.height() / 100);
      self.toolbar.$element.css({
        width: hotspotParams.computedSettings.width + '%',
        height: hotspotParams.computedSettings.height + '%'
      });
    });

    this.toolbar.stopMovingCallback = function (x, y) {
      // Update params when the element is dropped.
      var id = self.toolbar.dnd.$element.data('id');
      var hotspotParams = self.params.hotspot[id];

      // Measure x and y in percentages
      hotspotParams.computedSettings.x = x;
      hotspotParams.computedSettings.y = y;
    };

    this.toolbar.dnd.releaseCallback = function () {
      if (self.toolbar.newElement) {
        var id = self.toolbar.dnd.$element.data('id');
        var hotspotParams = self.params.hotspot[id];

        // Make sure stop moving callback is run first to get final mouse positions.
        setTimeout(function () {

          // Close open dialog
          if (self.dialogOpen) {
            self.hideDialog();
          }

          // Set x and y to 50% of width and height.
          self.editElement(self.elements[id], hotspotParams.computedSettings.x, hotspotParams.computedSettings.y);
          self.toolbar.newElement = false;
        }, 0);
      }
    };

    self.toolbar.attach($wrapper);
  };

  /**
   * Create buttons from buttonTypes list.
   * @returns {Array} buttonArray An array containing the created buttons
   */
  ImageHotspotQuestionEditor.prototype.createButtons = function () {
    var self = this;
    var buttonArray = [];

    this.buttonTypes.forEach(function (buttonFigure) {
      buttonArray.push(self.createHotspotButton(buttonFigure));
    });

    return buttonArray;
  };

  /**
   * @typedef {Object} HotspotFigureButton Hotspot figure button, used to drag hotspot figures from toolbar to image.
   * @property {String} name of the hotspot figure
   * @property {String} title for the hotspot button
   * @property {Function} createElement Callback function when an element is created with this button.
   */

  /**
   * Creates a single button from string.
   * @param {string} figure A string describing the figure
   * @returns {HotspotFigureButton} Button object for creating a drag n bar button.
   */
  ImageHotspotQuestionEditor.prototype.createHotspotButton = function (figure) {
    var self = this;

    return {
      id: figure,
      title: H5PEditor.t('H5PEditor.ImageHotspotQuestion', figure),
      createElement: function () {
        // Push default parameters
        self.params.hotspot.push({
          userSettings: {
            correct: false,
            feedbackText: ''
          },
          computedSettings: {
            // 50% - half the size of feedback in percentages
            x: 50 - ((INITIAL_FEEDBACK_SIZE / 2) * 100 / self.$gui.width()),
            y: 50 - ((INITIAL_FEEDBACK_SIZE / 2) * 100 / self.$gui.height()),
            // 40 pixels in percentages of container.
            width: (INITIAL_FEEDBACK_SIZE * 100 / self.$gui.width()),
            height: (INITIAL_FEEDBACK_SIZE * 100 / self.$gui.height()),
            figure: figure
          }
        });

        return self.insertElement(self.params.hotspot.length - 1);
      }
    };
  };

  /**
   * Insert element at given params index.
   *
   * @param {int} index Index of the element that should be inserted
   * @returns {H5P.jQuery} The created element
   */
  ImageHotspotQuestionEditor.prototype.insertElement = function (index) {
    var self = this;

    var elementParams = this.params.hotspot[index];
    var element = this.generateForm(this.elementFields, elementParams.userSettings);

    element.$element = $('<div>', {
      'class': 'h5p-hotspot-wrapper'
    }).appendTo(this.$gui)
      .css({
        left: elementParams.computedSettings.x + '%',
        top: elementParams.computedSettings.y + '%',
        width: elementParams.computedSettings.width + '%',
        height: elementParams.computedSettings.height + '%'
      })
      .data('id', index)
      .dblclick(function (mouseEvent) {
        var offX = (mouseEvent.offsetX || mouseEvent.pageX - $(mouseEvent.target).offset().left);
        var offY = (mouseEvent.offsetY || mouseEvent.pageY - $(mouseEvent.target).offset().top);
        self.editElement(element, offX, offY);
      });

    // Create inner figure
    $('<div>', {
      'class': 'h5p-hotspot-element ' + elementParams.computedSettings.figure
    }).appendTo(element.$element);

    // Make it possible to focus and move element
    var dnbElement = this.toolbar.add(element.$element);

    dnbElement.contextMenu.on('contextMenuEdit', function () {
      self.editElement(element, elementParams.computedSettings.x, elementParams.computedSettings.y);
    });

    dnbElement.contextMenu.on('contextMenuRemove', function () {
      self.removeElement(element);
      dnbElement.blur();
    });

    dnbElement.contextMenu.on('contextMenuBringToFront', function () {
      // Add to top
      var oldZ = self.params.hotspot.indexOf(elementParams);
      self.params.hotspot.push(self.params.hotspot.splice(oldZ, 1)[0]);

      // Update visuals
      element.$element.appendTo(self.$gui);
    });

    this.elements[index] = element;
    return element.$element;
  };

  /**
   * Collect functions to execute once the tree is complete.
   *
   * @param {function} ready
   * @returns {undefined}
   */
  ImageHotspotQuestionEditor.prototype.ready = function (ready) {
    if (this.passReadies) {
      this.parent.ready(ready);
    } else {
      this.readies.push(ready);
    }
  };

  /**
   * Set callbacks and open dialog with the form for the given element.
   *
   * @param {Object} element
   * @param {number} elementPosX X-coordinate of where the dialog for editing element should be placed
   * @param {number} elementPosY Y-coordinate of where the dialog for editing element should be placed
   * @returns {undefined}
   */
  ImageHotspotQuestionEditor.prototype.editElement = function (element, elementPosX, elementPosY) {
    var self = this;

    this.doneCallback = function () {
      // Validate form
      var valid = true;
      element.children.forEach(function (child) {
        if (child.validate() === false) {
          valid = false;
        }
      });

      self.$prevFocusElement = element.$element;

      return valid;
    };

    this.removeCallback = function () {
      self.removeElement(element);
    };

    this.showDialog(element.$form, element, elementPosX, elementPosY);

    setTimeout(function () {
      self.toolbar.blurAll();
    }, 0);
  };

  /**
   * Removes the given element.
   * @param {Object} element
   */
  ImageHotspotQuestionEditor.prototype.removeElement = function (element) {
    var self = this;
    var id = element.$element.data('id');

    // Remove element form
    H5PEditor.removeChildren(element.children);

    // Remove element
    element.$element.remove();
    self.elements.splice(id, 1);
    self.params.hotspot.splice(id, 1);

    // Change data index for "all" elements (re-index)
    self.elements.forEach(function (element, index) {
      element.$element.data('id', index);
    });
  };

  /**
   * Generate sub forms that's ready to use in the dialog.
   *
   * @param {Object} semantics
   * @param {Object} params
   * @returns {Object}
   */
  ImageHotspotQuestionEditor.prototype.generateForm = function (semantics, params) {
    var $form = $('<div></div>');
    H5PEditor.processSemanticsChunk(semantics, params, $form, this);
    var $lib = $form.children('.library:first');
    if ($lib.length !== 0) {
      $lib.children('label, select, .h5peditor-field-description').hide().end().children('.libwrap').css('margin-top', '0');
    }

    return {
      $form: $form,
      children: this.children
    };
  };

  /**
   * Open hotspot settings dialog.
   *
   * @param {H5P.jQuery} $form
   * @param {Object} element Element object containing the hotspot settings
   * @param {Number} dialogPosX X-coordinate for where the dialog will be positioned
   * @param {Number} dialogPosY Y-coordinate for where the dialog will be positioned
   */
  ImageHotspotQuestionEditor.prototype.showDialog = function ($form, element, dialogPosX, dialogPosY) {
    // Threshold for placing dialog on side of image
    var roomForDialog = this.$editor.width() - this.$guiWrapper.width();

    if (this.dialogOpen) {
      return;
    }

    this.$disablingOverlay.removeClass('hidden');

    this.dialogOpen = true;

    // Attach form
    this.$currentForm = $form;
    $form.appendTo(this.$dialoginner);

    // Measure dialog size
    var $tmp = this.$dialog.clone()
      .removeClass('hidden')
      .addClass('outside-side')
      .appendTo(this.$gui);
    var dialogWidth = $tmp.outerWidth(true);
    var dialogHeight = $tmp.outerHeight(true);
    $tmp.remove();

    // Reset dialog position
    this.$dialog.css({
      left: 0,
      top: 0
    });

    // Place dialog on side, underneath or inside image
    if (roomForDialog >= dialogWidth) {

      // Append dialog to gui wrapper
      this.$dialog.addClass('outside-side')
        .insertAfter(this.$guiWrapper);

    } // Put dialog under picture if small height or width
    else if (dialogWidth > this.$gui.width()
      || this.$gui.height() < dialogHeight) {

      this.$dialog.addClass('outside-underneath')
        .insertAfter(this.$guiWrapper);

    }
    else {
      // Place dialog inside image, pos calculated from mouse click
      var xPos = dialogPosX;
      var yPos = dialogPosY;

      // Center dialog on mouse
      xPos -= dialogWidth / 2;
      yPos -= dialogHeight / 2;

      // Apply element offset
      xPos += element.$element.position().left;
      yPos += element.$element.position().top;

      // Edge cases
      if (dialogWidth >= this.$guiWrapper.width()) {
        this.$dialog.outerWidth(this.$guiWrapper.width());
        xPos = 0;
      } else if (xPos < 0) {
        xPos = 0;
      } else if (xPos + dialogWidth > this.$guiWrapper.width()) {
        xPos = this.$guiWrapper.width() - dialogWidth;
      }

      // Already checked if image is too small
      if (yPos < 0) {
        yPos = 0;
      } else if (yPos + dialogHeight > this.$gui.height()) {
        yPos = this.$gui.height() - dialogHeight;
      }

      // Position dialog inside image
      this.$dialog.css({
        left: xPos + 'px',
        top: yPos + 'px'
      }).addClass('inside')
        .appendTo(this.$guiWrapper);
    }

    // Show dialog
    this.$dialog.removeClass('hidden');
  };

  /**
   * Close hotspot settings dialog.
   */
  ImageHotspotQuestionEditor.prototype.hideDialog = function () {
    this.$currentForm.detach();
    this.$disablingOverlay.addClass('hidden');
    this.dialogOpen = false;
    this.$dialog.detach()
      .addClass('hidden')
      .removeClass('outside-side')
      .removeClass('outside-underneath')
      .removeClass('inside');
  };

  /**
   * Validate the current field, required widget function.
   *
   * @returns {Boolean}
   */
  ImageHotspotQuestionEditor.prototype.validate = function () {
    return true;
  };

  /**
   * Called when the tab we are on is set as active.
   */
  ImageHotspotQuestionEditor.prototype.setActive = function () {
    if (!!this.imageField.params) {
      // Remove error text
      this.$editor.removeClass('no-image');

      //Remove old picture
      if (this.$image) {
        this.$image.remove();
      }

      // Create editor content
      this.populateQuestion();

      this.toolbar.blurAll();

    } else {
      // Remove image and display error message
      if (this.$image) {
        this.$image.remove();
      }
      this.$editor.addClass('no-image');
    }
  };

  /**
   * Creates and attaches image to the editor.
   */
  ImageHotspotQuestionEditor.prototype.populateQuestion = function () {
    var self = this;

    // Add image
    this.$image = $('<img>', {
      'class': 'h5p-image-hotspot-question-image',
      'src': H5P.getPath(this.imageField.params.path, H5PEditor.contentId)
    }).appendTo(this.$gui);

    this.resize();
  };

  /**
   * Resize question
   */
  ImageHotspotQuestionEditor.prototype.resize = function () {
    if (!this.$image) {
      return;
    }

    // Scale image down if it is too wide
    if (this.$image.get(0).naturalWidth > this.$editor.width()) {
      this.$image.css('width', '100%');
    }
    else {
      this.$image.css('width', '');
    }

    // Set containerEm
    this.toolbar.dnr.setContainerEm(parseFloat(this.$gui.css('font-size')));
    this.toolbar.blurAll();
  };

  return ImageHotspotQuestionEditor;
}(H5P.jQuery));

// Default english translations
H5PEditor.language['H5PEditor.ImageHotspotQuestion'] = {
  libraryStrings: {
    noImage: 'You have not selected an image.',
    done: 'Done',
    remove: 'Remove hotspot',
    rectangle: 'Create rectangle',
    circle: 'Create circle',
    guiDescription: 'Drag and drop the desired figure from the toolbar to create a new hotspot. Double-click to edit an existing hotspot. Drag the hotspot to move it. Pull the resize handler in the lower right corner to resize.'
  }
};
