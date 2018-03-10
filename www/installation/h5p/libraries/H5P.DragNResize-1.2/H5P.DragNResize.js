/*global H5P*/
H5P.DragNResize = (function ($, EventDispatcher) {

  /**
   * Constructor!
   *
   * @class H5P.DragNResize
   * @param {H5P.jQuery} $container
   */
  function C($container) {
    var self = this;
    this.$container = $container;

    EventDispatcher.call(this);

    // Override settings for snapping to grid, and locking aspect ratio.
    H5P.$body.keydown(function (event) {
      if (event.keyCode === 17) {
        // Ctrl
        self.revertSnap = true;
      }
      else if (event.keyCode === 16) {
        // Shift
        self.revertLock = true;
      }
    }).keyup(function (event) {
      if (event.keyCode === 17) {
        // Ctrl
        self.revertSnap = false;
      }
      else if (event.keyCode === 16) {
        // Shift
        self.revertLock = false;
      }
    });
  }

  // Inheritance
  C.prototype = Object.create(EventDispatcher.prototype);
  C.prototype.constructor = C;

  /**
   * Gives the given element a resize handle.
   *
   * @param {H5P.jQuery} $element
   * @param {Object} [options]
   * @param {boolean} [options.lock]
   */
  C.prototype.add = function ($element, options) {
    var that = this;

    // Array with position of handles
    var cornerPositions = ['nw', 'ne', 'sw', 'se'];
    var edgePositions = ['n', 'w', 'e', 's'];

    var addResizeHandle = function (position) {
      $('<div>', {
        'class': 'h5p-dragnresize-handle ' + position
      }).mousedown(function (event) {
        that.lock = (options && options.lock);
        that.$element = $element;
        that.press(event.clientX, event.clientY, position);
      }).data('position', position)
        .appendTo($element);
    };

    cornerPositions.forEach(function (pos) {
      addResizeHandle(pos);
    });

    // Add edge handles
    if (!options || !options.lock) {
      edgePositions.forEach(function (pos) {
        addResizeHandle(pos);
      });
    }
  };

  /**
   * Get paddings for the element
   */
  C.prototype.getElementPaddings = function () {
    return {
      horizontal: Number(this.$element.css('padding-left').replace("px", "")) + Number(this.$element.css('padding-right').replace("px", "")),
      vertical: Number(this.$element.css('padding-top').replace("px", "")) + Number(this.$element.css('padding-bottom').replace("px", ""))
    };
  };

  /**
   * Get borders for the element
   * @returns {{horizontal: number, vertical: number}}
   */
  C.prototype.getElementBorders = function () {
    return {
      horizontal: Number(this.$element.css('border-left-width').replace('px', '')) + Number(this.$element.css('border-right-width').replace('px', '')),
      vertical: Number(this.$element.css('border-top-width').replace('px', '')) + Number(this.$element.css('border-bottom-width').replace('px', ''))
    }
  };

  C.prototype.setContainerEm = function (containerEm) {
    this.containerEm = containerEm;
  };

  /**
   * Start resizing
   *
   * @param {number} x
   * @param {number} y
   * @param {String} [direction] Direction of resize
   */
  C.prototype.press = function (x, y, direction) {
    this.active = true;
    var eventData = {
      instance: this,
      direction: direction
    };

    H5P.$window
      .bind('mouseup', eventData, C.release)
      .mousemove(eventData, C.move);

    H5P.$body
      .css({
        '-moz-user-select': 'none',
        '-webkit-user-select': 'none',
        'user-select': 'none',
        '-ms-user-select': 'none'
      })
      .attr('unselectable', 'on')[0]
      .onselectstart = H5P.$body[0].ondragstart = function () {
        return false;
      };

    this.startX = x;
    this.startY = y;
    this.padding = this.getElementPaddings();
    this.borders = this.getElementBorders();
    this.startWidth = this.$element.outerWidth();
    this.startHeight = this.$element.outerHeight();
    this.ratio = (this.startWidth / this.startHeight);
    var position = this.$element.position();
    this.left = position.left;
    this.top = position.top;
    this.containerWidth = this.$container.width();
    this.containerHeight = this.$container.height();

    // Set default values
    this.newLeft = this.left;
    this.newTop = this.top;
    this.newWidth = this.startWidth;
    this.newHeight = this.startHeight;

    this.trigger('startResizing', eventData);

    // Show transform panel
    this.trigger('showTransformPanel');
  };

  /**
   * Resize events
   *
   * @param {Event} event
   */
  C.move = function (event) {
    var direction = (event.data.direction ? event.data.direction : 'se');
    var that = event.data.instance;
    var moveW = (direction === 'nw' || direction === 'sw' || direction === 'w');
    var moveN = (direction === 'nw' || direction === 'ne' || direction === 'n');
    var movesHorizontal = (direction === 'w' || direction === 'e');
    var movesVertical = (direction === 'n' || direction === 's');
    var deltaX = that.startX - event.clientX;
    var deltaY = that.startY - event.clientY;

    that.minLeft = that.left + that.startWidth - H5P.DragNResize.MIN_SIZE;
    that.minTop = that.top + that.startHeight - H5P.DragNResize.MIN_SIZE;

    // Moving west
    if (moveW) {
      that.newLeft = that.left - deltaX;
      that.newWidth = that.startWidth + deltaX;

      // Check edge cases
      if (that.newLeft < 0) {
        that.newLeft = 0;
        that.newWidth = that.left + that.startWidth;
      }
      else if (that.newLeft > that.minLeft) {
        that.newLeft = that.minLeft;
        that.newWidth = that.left - that.minLeft + that.startWidth;
      }

      // Snap west side
      if (that.snap && !that.revertSnap) {
        that.newLeft = Math.round(that.newLeft / that.snap) * that.snap;

        // Make sure element does not snap east
        if (that.newLeft > that.minLeft) {
          that.newLeft = Math.floor(that.minLeft / that.snap) * that.snap;
        }

        that.newWidth = (that.left - that.newLeft) + that.startWidth;
      }
    }
    else if (!movesVertical) {
      that.newWidth = that.startWidth - deltaX;

      // Snap width
      if (that.snap && !that.revertSnap) {
        that.newWidth = Math.round(that.newWidth / that.snap) * that.snap;
      }

      if (that.left + that.newWidth > that.containerWidth) {
        that.newWidth = that.containerWidth - that.left;
      }
    }

    // Moving north
    if (moveN) {
      that.newTop = that.top - deltaY;
      that.newHeight = that.startHeight + deltaY;

      // Check edge cases
      if (that.newTop < 0) {
        that.newTop = 0;
        that.newHeight = that.top + that.startHeight;
      }
      else if (that.newTop > that.minTop) {
        that.newTop = that.minTop;
        that.newHeight = that.top - that.minTop + that.startHeight;
      }

      // Snap north
      if (that.snap && !that.revertSnap) {
        that.newTop = Math.round(that.newTop / that.snap) * that.snap;

        // Make sure element does not snap south
        if (that.newTop > that.minTop) {
          that.newTop = Math.floor(that.minTop / that.snap) * that.snap;
        }

        that.newHeight = (that.top - that.newTop) + that.startHeight;
      }
    }
    else if (!movesHorizontal) {
      that.newHeight = that.startHeight - deltaY;

      // Snap height
      if (that.snap && !that.revertSnap) {
        that.newHeight = Math.round(that.newHeight / that.snap) * that.snap;
      }

      if (that.top + that.newHeight > that.containerHeight) {
        that.newHeight = that.containerHeight - that.top;
      }
    }

    // Set min size
    if (that.newWidth <= H5P.DragNResize.MIN_SIZE) {
      that.newWidth = H5P.DragNResize.MIN_SIZE;
    }

    if (that.newHeight <= H5P.DragNResize.MIN_SIZE) {
      that.newHeight = H5P.DragNResize.MIN_SIZE;
    }

    // Apply ratio lock
    var lock = (that.revertLock ? !that.lock : that.lock);
    if (lock) {
      that.lockDimensions(moveW, moveN, movesVertical, movesHorizontal);
    }

    // Reduce size by padding and borders
    var width = that.newWidth;
    var height = that.newHeight;
    if (that.$element.css('boxSizing') !== 'border-box') {
      width = width - that.padding.horizontal - that.borders.horizontal;
      height = height - that.padding.vertical - that.borders.vertical;
    }

    that.$element.css({
      width: (width / that.containerEm) + 'em',
      height: (height / that.containerEm) + 'em',
      left: ((that.newLeft / that.containerWidth) * 100) + '%',
      top: ((that.newTop / that.containerHeight) * 100) + '%'
    });

    that.trigger('moveResizing');
  };

  /**
   * Changes element values depending on moving direction of the element
   * @param isMovingWest
   * @param isMovingNorth
   * @param movesVertical
   * @param movesHorizontal
   */
  C.prototype.lockDimensions = function (isMovingWest, isMovingNorth, movesVertical, movesHorizontal) {
    var self = this;

    // Cap movement at top
    var lockTop = function (isMovingNorth) {
      if (!isMovingNorth) {
        return;
      }

      self.newTop = self.top - (self.newHeight - self.startHeight);

      // Edge case
      if (self.newTop <= 0) {
        self.newTop = 0;
      }
    };

    // Expand to longest edge
    if (movesVertical) {
      this.newWidth = this.newHeight * this.ratio;

      // Make sure locked ratio does not cause size to go below min size
      if (this.newWidth < H5P.DragNResize.MIN_SIZE) {
        this.newWidth = H5P.DragNResize.MIN_SIZE;
        this.newHeight = H5P.DragNResize.MIN_SIZE / this.ratio;
      }
    }
    else if (movesHorizontal) {
      this.newHeight = this.newWidth / this.ratio;

      // Make sure locked ratio does not cause size to go below min size
      if (this.newHeight < H5P.DragNResize.MIN_SIZE) {
        this.newHeight = H5P.DragNResize.MIN_SIZE;
        this.newWidth = H5P.DragNResize.MIN_SIZE * this.ratio;
      }
    }
    else if (this.newWidth / this.startWidth > this.newHeight / this.startHeight) {
      // Expand to width
      this.newHeight = this.newWidth / this.ratio;
    }
    else {
      // Expand to height
      this.newWidth = this.newHeight * this.ratio;
    }

    // Change top to match new height
    if (isMovingNorth) {
      lockTop(isMovingNorth);

      if (self.newTop <= 0) {
        self.newHeight = self.top + self.startHeight;
        self.newWidth = self.newHeight * self.ratio;
      }
    }
    else {
      // Too high
      if (this.top + this.newHeight > this.containerHeight) {
        this.newHeight = this.containerHeight - this.top;
        this.newWidth = this.newHeight * this.ratio;
      }
    }

    // Change left to match new width
    if (isMovingWest) {
      this.newLeft = this.left - (this.newWidth - this.startWidth);
      // Edge case
      if (this.newLeft <= 0) {
        this.newLeft = 0;
        this.newWidth = this.left + this.startWidth;
        this.newHeight = this.newWidth / this.ratio;
      }
    }
    else {
      // Too wide
      if (this.left + this.newWidth > this.containerWidth) {
        this.newWidth = this.containerWidth - this.left;
        this.newHeight = this.newWidth / this.ratio;
      }
    }

    // Need to re-lock top in case height changed
    lockTop(isMovingNorth);
  };

  /**
   * Stop resizing
   *
   * @param {Event} event
   */
  C.release = function (event) {
    var that = event.data.instance;
    that.active = false;

    H5P.$window
      .unbind('mouseup', C.release)
      .unbind('mousemove', C.move);

    H5P.$body
      .css({
        '-moz-user-select': '',
        '-webkit-user-select': '',
        'user-select': '',
        '-ms-user-select': ''
      })
      .removeAttr('unselectable')[0]
      .onselectstart = H5P.$body[0].ondragstart = null;

    // Stopped resizing send width and height in Ems
    that.trigger('stoppedResizing', {
      left: that.newLeft,
      top: that.newTop,
      width: that.newWidth / that.containerEm,
      height: that.newHeight / that.containerEm
    });

    // Refocus element after resizing it. Apply timeout since focus is lost at the end of mouse event.
    setTimeout(function () {
      that.$element.focus();
    }, 0);
  };

  /**
   * Convert px value to number.
   *
   * @param {String} px
   * @returns {Number}
   */
  var pxToNum = function (px) {
    return Number(px.replace('px', ''));
  };

  C.MIN_SIZE = 24;

  return C;
})(H5P.jQuery, H5P.EventDispatcher);
