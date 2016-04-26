H5P.DragNBar = (function (EventDispatcher) {

  /**
   * Constructor. Initializes the drag and drop menu bar.
   *
   * @class
   * @param {Array} buttons
   * @param {H5P.jQuery} $container
   * @param {H5P.jQuery} $dialogContainer
   * @param {object} [options] Collection of options
   * @param {boolean} [options.disableEditor=false] Determines if DragNBar should be displayed in view or editor mode
   * @param {H5P.jQuery} [options.$blurHandlers] When clicking these element(s) dnb focus will be lost
   */
  function DragNBar(buttons, $container, $dialogContainer, options) {
    var self = this;

    EventDispatcher.call(this);
    this.overflowThreshold = 13; // How many buttons to display before we add the more button.
    this.buttons = buttons;
    this.$container = $container;
    this.$dialogContainer = $dialogContainer;
    this.dnd = new H5P.DragNDrop(this, $container);
    this.dnd.snap = 10;
    this.newElement = false;
    var defaultOptions = {
      disableEditor: false
    };
    options = H5P.jQuery.extend(defaultOptions, options);
    this.isEditor = !options.disableEditor;
    this.$blurHandlers = options.$blurHandlers ? options.$blurHandlers : undefined;

    /**
     * Keeps track of created DragNBar elements
     * @type {Array}
     */
    this.elements = [];

    // Create a popup dialog
    this.dialog = new H5P.DragNBarDialog($dialogContainer, $container);

    // Fix for forcing redraw on $container, to avoid "artifcats" on safari
    this.$container.addClass('hardware-accelerated');

    if (this.isEditor) {
      this.initEditor();
      this.initClickListeners();

      H5P.$window.resize(function () {
        self.resize();
      });
    }
  }

  // Inherit support for events
  DragNBar.prototype = Object.create(EventDispatcher.prototype);
  DragNBar.prototype.constructor = DragNBar;

  return DragNBar;
})(H5P.EventDispatcher);

/**
 * Initializes editor functionality of DragNBar
 */
H5P.DragNBar.prototype.initEditor = function () {
  var that = this;
  this.dnr = new H5P.DragNResize(this.$container);
  this.dnr.snap = 10;

  // Update coordinates when element is resized
  this.dnr.on('moveResizing', function () {
    var offset = that.$element.offset();
    var position = that.$element.position();
    that.updateCoordinates(offset.left, offset.top, position.left, position.top);
  });

  // Set pressed to not lose focus at the end of resize
  this.dnr.on('stoppedResizing', function () {
    that.pressed = true;

    // Delete pressed after dnbelement has been refocused so it will lose focus on single click.
    setTimeout(function () {
      delete that.pressed;
    }, 10);
  });

  /**
   * Show transform panel listener
   */
  this.dnr.on('showTransformPanel', function () {
    if (that.focusedElement) {
      that.focusedElement.contextMenu.trigger('contextMenuTransform', {showTransformPanel: true});
    }
  });

  this.dnd.on('showTransformPanel', function () {
    // Get moving element and show transform panel
    if (that.focusedElement) {
      that.focusedElement.contextMenu.trigger('contextMenuTransform', {showTransformPanel: true});
    }
  });

  this.dnd.startMovingCallback = function (x, y) {
    that.dnd.min = {x: 0, y: 0};
    that.dnd.max = {
      x: that.$container.width() - that.$element.outerWidth(),
      y: that.$container.height() - that.$element.outerHeight()
    };

    if (that.newElement) {
      that.dnd.adjust.x = 10;
      that.dnd.adjust.y = 10;
      that.dnd.min.y -= that.$list.height();
    }

    return true;
  };

  this.dnd.stopMovingCallback = function (event) {
    var pos = {};

    if (that.newElement) {
      that.$container.css('overflow', '');
      if (Math.round(parseFloat(that.$element.css('top'))) < 0) {
        // Try to center element, but avoid overlapping
        pos.x = (that.dnd.max.x / 2);
        pos.y = (that.dnd.max.y / 2);
        that.avoidOverlapping(pos, that.$element);
      }
    }

    if (pos.x === undefined || pos.y === undefined ) {
      pos.x = Math.round(parseFloat(that.$element.css('left')));
      pos.y = Math.round(parseFloat(that.$element.css('top')));
    }

    that.stopMoving(pos.x, pos.y);
    that.newElement = false;

    delete that.dnd.min;
    delete that.dnd.max;
  };
};

/**
 * Tries to position the given element close to the requested coordinates.
 * Element can be skipped to check if spot is available.
 *
 * @param {object} pos
 * @param {number} pos.x
 * @param {number} pos.y
 * @param {(H5P.jQuery|Object)} element object with width&height if ran before insertion.
 */
H5P.DragNBar.prototype.avoidOverlapping = function (pos, $element) {
  // Determine size of element
  var size = $element;
  if (size instanceof H5P.jQuery) {
    size = window.getComputedStyle(size[0]);
    size = {
      width: parseFloat(size.width),
      height: parseFloat(size.height)
    };
  }
  else {
    $element = undefined;
  }

  // Determine how much they can be manuvered
  var containerStyle = window.getComputedStyle(this.$container[0]);
  var manX = parseFloat(containerStyle.width) - size.width;
  var manY = parseFloat(containerStyle.height) - size.height;

  var limit = 16;
  var attempts = 0;

  while (attempts < limit && this.elementOverlaps(pos.x, pos.y, $element)) {
    // Try to place randomly inside container
    if (manX > 0) {
      pos.x = Math.floor(Math.random() * manX);
    }
    if (manY > 0) {
      pos.y = Math.floor(Math.random() * manY);
    }
    attempts++;
  }
};

/**
 * Determine if moving the given element to its new position will cause it to
 * cover another element. This can make new or pasted elements difficult to see.
 * Element can be skipped to check if spot is available.
 *
 * @param {number} x
 * @param {number} y
 * @param {H5P.jQuery} [$element]
 * @returns {boolean}
 */
H5P.DragNBar.prototype.elementOverlaps = function (x, y, $element) {
  var self = this;

  // Use snap grid
  x = Math.round(x / 10);
  y = Math.round(y / 10);

  for (var i = 0; i < self.elements.length; i++) {
    var element = self.elements[i];
    if ($element !== undefined && element.$element === $element) {
      continue;
    }

    if (x === Math.round(parseFloat(element.$element.css('left')) / 10) &&
        y === Math.round(parseFloat(element.$element.css('top')) / 10)) {
      return true; // Stop loop
    }
  }

  return false;
};

/**
 * Initialize click listeners
 */
H5P.DragNBar.prototype.initClickListeners = function () {
  var self = this;

  // Key coordinates
  var CTRL = 17;
  var C = 67;
  var V = 86;

  // Keep track of key state
  var ctrlDown = false;

  // Register event listeners
  H5P.$body.keydown(function (event) {
    if (event.which === CTRL) {
      ctrlDown = true;

      if (self.dnd.snap !== undefined) {
        // Disable snapping
        delete self.dnd.snap;
      }
    }
    else if (event.which === C && ctrlDown && self.focusedElement && self.$container.is(':visible')) {
      // Copy element params to clipboard
      var elementSize = window.getComputedStyle(self.focusedElement.$element[0]);
      var width = parseFloat(elementSize.width);
      var height = parseFloat(elementSize.height) / width;
      width = width / (parseFloat(window.getComputedStyle(self.$container[0]).width) / 100);
      height *= width;

      self.focusedElement.toClipboard(width, height);
    }
    else if (event.which === V && ctrlDown && window.localStorage && self.$container.is(':visible')) {
      if (self.preventPaste || self.dialog.isOpen() || document.activeElement.contentEditable === 'true' || document.activeElement.value !== undefined) {
        // Don't allow paste if dialog is open or active element can be modified
        return;
      }

      var clipboardData = localStorage.getItem('h5pClipboard');
      if (clipboardData) {

        // Parse
        try {
          clipboardData = JSON.parse(clipboardData);
        }
        catch (err) {
          console.error('Unable to parse JSON from clipboard.', err);
          return;
        }

        // Update file URLs
        if (clipboardData.contentId !== H5PEditor.contentId) {
          var prefix = clipboardData.contentId ? '../' + clipboardData.contentId : '../../editor';
          H5P.DragNBar.updateFileUrls(clipboardData.specific, prefix);
        }

        if (clipboardData.generic) {
          // Use reference instead of key
          clipboardData.generic = clipboardData.specific[clipboardData.generic];

          // Avoid multiple content with same ID
          delete clipboardData.generic.subContentId;
        }

        self.trigger('paste', clipboardData);
      }
    }
  }).keyup(function (event) {
    if (event.which === CTRL) {
      // Update key state
      ctrlDown = false;

      // Enable snapping
      self.dnd.snap = 10;
    }
  }).click(function () {
    // Remove pressed on click
    delete self.pressed;
  });

  // Set blur handler element if option has been specified
  var $blurHandlers = this.$container;
  if (this.$blurHandlers) {
    $blurHandlers = this.$blurHandlers;
  }

  $blurHandlers.click(function () {
    // Remove coordinates picker if we didn't press an element.
    if (self.pressed !== undefined) {
      delete self.pressed;
    }
    else {
      self.blurAll();
      if (self.focusedElement !== undefined) {
        delete self.focusedElement;
      }
    }
  });
};

/**
 * Update file URLs. Useful when copying between different contents.
 *
 * @param {object} params Reference
 * @param {number} contentId From source
 */
H5P.DragNBar.updateFileUrls = function (params, prefix) {
  for (var prop in params) {
    if (params.hasOwnProperty(prop) && params[prop] instanceof Object) {
      var obj = params[prop];
      if (obj.path !== undefined && obj.mime !== undefined) {
        obj.path = prefix + '/' + obj.path;
      }
      else {
        H5P.DragNBar.updateFileUrls(obj, prefix);
      }
    }
  }
};

/**
 * Attaches the menu bar to the given wrapper.
 *
 * @param {jQuery} $wrapper
 * @returns {undefined}
 */
H5P.DragNBar.prototype.attach = function ($wrapper) {
  $wrapper.html('');
  $wrapper.addClass('h5peditor-dragnbar');

  var $list = H5P.jQuery('<ul class="h5p-dragnbar-ul"></ul>').appendTo($wrapper);
  this.$list = $list;

  for (var i = 0; i < this.buttons.length; i++) {
    var button = this.buttons[i];

    if (i === this.overflowThreshold) {
      $list = H5P.jQuery('<li class="h5p-dragnbar-li"><a href="#" title="' + 'More elements' + '" class="h5p-dragnbar-a h5p-dragnbar-more-button"></a><ul class="h5p-dragnbar-li-ul"></ul></li>')
        .appendTo($list)
        .click(function () {
          return false;
        })
        .hover(function () {
          $list.stop().slideToggle(300);
        }, function () {
          $list.stop().slideToggle(300);
        })
        .children(':first')
        .next();
    }

    this.addButton(button, $list);
  }
};

/**
 * Add button.
 *
 * @param {type} button
 * @param {Function} button.createElement Function for creating element
 * @param {type} $list
 * @returns {undefined}
 */
H5P.DragNBar.prototype.addButton = function (button, $list) {
  var that = this;

  H5P.jQuery('<li class="h5p-dragnbar-li"><a href="#" title="' + button.title + '" class="h5p-dragnbar-a h5p-dragnbar-' + button.id + '-button"></a></li>')
    .appendTo($list)
    .children()
    .click(function () {
      return false;
    }).mousedown(function (event) {
      if (event.which !== 1) {
        return;
      }

      that.newElement = true;
      that.pressed = true;
      var createdElement = button.createElement();
      that.$element = createdElement;
      that.$container.css('overflow', 'visible');
      that.dnd.press(that.$element, event.pageX, event.pageY);
      that.focus(that.$element);
    });
};

/**
 * Change container.
 *
 * @param {jQuery} $container
 * @returns {undefined}
 */
H5P.DragNBar.prototype.setContainer = function ($container) {
  this.$container = $container;
  this.dnd.$container = $container;
};

/**
 * Handler for when the dragging stops. Makes sure the element is inside its container.
 *
 * @param {Number} left
 * @param {Number} top
 * @returns {undefined}
 */
H5P.DragNBar.prototype.stopMoving = function (left, top) {
  // Calculate percentage
  top = top / (this.$container.height() / 100);
  left = left / (this.$container.width() / 100);
  this.$element.css({top: top + '%', left: left + '%'});

  // Give others the result
  if (this.stopMovingCallback !== undefined) {
    this.stopMovingCallback(left, top);
  }
};

/**
 * Makes it possible to focus and move the element around.
 * Must be inside $container.
 *
 * @param {H5P.jQuery} $element
 * @param {Object} [options]
 * @param {H5P.DragNBarElement} [options.dnbElement] Register new element with dnbelement
 * @param {boolean} [options.disableResize] Resize disabled
 * @param {boolean} [options.lock] Lock ratio during resize
 * @param {string} [clipboardData]
 * @returns {H5P.DragNBarElement} Reference to added dnbelement
 */
H5P.DragNBar.prototype.add = function ($element, clipboardData, options) {
  var self = this;
  options = options || {};
  if (this.isEditor && !options.disableResize) {
    this.dnr.add($element, options);
  }
  var newElement = null;

  // Check if element already exist
  if (options.dnbElement) {
    // Set element as added element
    options.dnbElement.setElement($element);
    newElement = options.dnbElement;
  }
  else {
    options.element = $element;
    newElement = new H5P.DragNBarElement(this, clipboardData, options);
    this.elements.push(newElement);
  }

  $element.addClass('h5p-dragnbar-element');

  if ($element.attr('tabindex') === undefined) {
    // Make it possible to tab between elements.
    $element.attr('tabindex', 1);
  }

  if (this.isEditor) {
    $element.mousedown(function (event) {
      if (event.which !== 1) {
        return;
      }

      self.pressed = true;
      self.focus($element);
      if (self.dnr.active !== true) { // Moving can be stopped if the mousedown is doing something else
        self.dnd.press($element, event.pageX, event.pageY);
      }
    });
  }

  $element.focus(function () {
    self.focus($element);
  });

  return newElement;
};

/**
 * Remove given element in the UI.
 *
 * @param {H5P.DragNBarElement} dnbElement
 */
H5P.DragNBar.prototype.removeElement = function (dnbElement) {
  dnbElement.removeElement();
};

/**
 * Select the given element in the UI.
 *
 * @param {jQuery} $element
 * @returns {undefined}
 */
H5P.DragNBar.prototype.focus = function ($element) {
  var self = this;

  // Blur last focused
  if (this.focusedElement && this.focusedElement.$element !== $element) {
    this.focusedElement.blur();
    this.focusedElement.hideContextMenu();
  }

  // Keep track of the element we have in focus
  self.$element = $element;

  // Show and update coordinates picker
  this.focusedElement = this.getDragNBarElement($element);

  if (this.focusedElement) {
    this.focusedElement.showContextMenu();
    this.focusedElement.focus();
    self.updateCoordinates();
  }

  // Wait for potential recreation of element
  setTimeout(function () {
    self.updateCoordinates();
    if (self.focusedElement && self.focusedElement.contextMenu && self.focusedElement.contextMenu.canResize) {
      self.focusedElement.contextMenu.updateDimensions();
    }
  }, 0);
};

/**
 * Get dnbElement from $element
 * @param {jQuery} $element
 * @returns {H5P.DragNBarElement} dnbElement with matching $element
 */
H5P.DragNBar.prototype.getDragNBarElement = function ($element) {
  var foundElement;
  // Find object with matching element
  this.elements.forEach(function (element) {
    if (element.getElement().is($element)) {
      foundElement = element;
    }
  });
  return foundElement;
};

/**
 * Deselect all elements in the UI.
 *
 * @returns {undefined}
 */
H5P.DragNBar.prototype.blurAll = function () {
  this.elements.forEach(function (element) {
    element.blur();
  });
  delete this.focusedElement;
};

/**
 * Resize DnB, make sure context menu is positioned correctly.
 */
H5P.DragNBar.prototype.resize = function () {
  var self = this;
  this.updateCoordinates();

  if (self.focusedElement) {
    self.focusedElement.resizeContextMenu(self.$element.offset().left - self.$element.parent().offset().left);
  }
};

/**
 * Update the coordinates of context menu.
 *
 * @param {Number} [left]
 * @param {Number} [top]
 * @param {Number} [x]
 * @param {Number} [y]
 * @returns {undefined}
 */
H5P.DragNBar.prototype.updateCoordinates = function (left, top, x, y) {
  if (!this.focusedElement) {
    return;
  }

  var containerPosition = this.$container.position();

  if (left && top && x && y) {
    left = x + containerPosition.left;
    top = y + containerPosition.top;
    this.focusedElement.updateCoordinates(left, top, x, y);
  }
  else {
    var position = this.$element.position();
    this.focusedElement.updateCoordinates(position.left + containerPosition.left, position.top + containerPosition.top, position.left, position.top);
  }
};

/**
 * Creates element data to store in the clipboard.
 *
 * @param {string} from Source of the element
 * @param {object} params Element options
 * @param {string} [generic] Which part of the parameters can be used by other libraries
 * @returns {string} JSON
 */
H5P.DragNBar.clipboardify = function (from, params, generic) {
  var clipboardData = {
    from: from,
    specific: params
  };

  if (H5PEditor.contentId) {
    clipboardData.contentId = H5PEditor.contentId;
  }

  // Add the generic part
  if (params[generic]) {
    clipboardData.generic = generic;
  }

  return clipboardData;
};

/**
 * @typedef SizeNPosition
 * @type Object
 * @property {number} width Width of the Element
 * @property {number} height Height of the Element
 * @property {number} left The X Coordinate
 * @property {number} top The Y Coordinate
 */

/**
 * Calculates position and size for the given element (in pixels)
 *
 * @throws Error if invalid type
 * @param {Element} element
 * @param {string} [type=inner] Possible values "inner" and "outer"
 * @returns {SizeNPosition}
 */
H5P.DragNBar.getSizeNPosition = function (element, type) {
  type = type || 'inner';
  var style;
  switch (type) {
    case 'inner':
      style = window.getComputedStyle(element);
      break;
    case 'outer':
      style = element.getBoundingClientRect();
      break;
    default:
      throw 'Unknown type';
  }

  return {
    width: parseFloat(style.width),
    height: parseFloat(style.height),
    left: parseFloat(style.left),
    top: parseFloat(style.top)
  };
};

/**
 * Make sure the given element is inside the container.
 *
 * @param {H5P.jQuery} $element
 * @param {SizeNPosition} containerSize
 * @returns {SizeNPosition} Only the properties which require change
 */
H5P.DragNBar.fitElementInside = function ($element, containerSize) {
  var elementSize = H5P.DragNBar.getSizeNPosition($element[0], 'outer');
  var elementPosition = H5P.DragNBar.getSizeNPosition($element[0], 'inner');
  var style = {};

  if (elementPosition.left < 0) {
    // Element sticks out of the left side
    style.left = elementPosition.left = 0;
  }

  if (elementSize.width + elementPosition.left > containerSize.width) {
    // Element sticks out of the right side
    style.left = containerSize.width - elementSize.width;
    if (style.left < 0) {
      // Element is wider than the container
      style.left = 0;
      style.width = containerSize.width;
    }
  }

  if (elementPosition.top < 0) {
    // Element sticks out of the top side
    style.top = elementPosition.top = 0;
  }

  if (elementSize.height + elementPosition.top > containerSize.height) {
    // Element sticks out of the bottom side
    style.top = containerSize.height - elementSize.height;
    if (style.top < 0) {
      // Element is higher than the container
      style.top = 0;
      style.height = containerSize.height;
    }
  }

  return style;
};

if (window.H5PEditor) {
  // Add translations
  H5PEditor.language['H5P.DragNBar'] = {
    libraryStrings: {
      transformLabel: 'Transform',
      editLabel: 'Edit',
      removeLabel: 'Remove',
      bringToFrontLabel: 'Bring to Front',
      unableToPaste: 'Cannot paste this object. Unfortunately, the object you are trying to paste is not supported by this content type or version.',
      sizeLabel: 'Size',
      positionLabel: 'Position'
    }
  };
}
