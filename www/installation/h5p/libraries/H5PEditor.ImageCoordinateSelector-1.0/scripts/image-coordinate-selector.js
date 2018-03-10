/**
 * ImageCoordinateSelector widget module
 *
 * @param {H5P.jQuery} $
 */
H5PEditor.widgets.imageCoordinateSelector = H5PEditor.ImageCoordinateSelector = (function ($) {

  /**
   * Creates an image coordinate selector.
   *
   * @class H5PEditor.ImageCoordinateSelector
   *
   * @param {Object} parent
   * @param {Object} field
   * @param {Object} params
   * @param {function} setValue
   *
   * @throws {Error} If no image field is found
   */
  function ImageCoordinateSelector(parent, field, params, setValue) {
    var self = this;

    this.parent = parent;
    this.field = field;
    this.params = params;
    this.setValue = setValue;

    this.imageField = H5PEditor.findField(this.field.imageFieldPath, this.parent);

    if (this.imageField === undefined) {
      throw new Error('I need an image field to do my job');
    }

    self.$container = $('<div>', {
      'class': 'field text h5p-image-coordinate-selector no-image'
    });

    // Add header:
    $('<span>', {
      'class': 'h5peditor-label',
      html: self.field.label
    }).appendTo(self.$container);

    self.$imgContainer = $('<div>', {
      'class': 'image-coordinate-selector',
      click: function (e) {
        var offset = $(this).offset();
        var x = e.pageX - offset.left - 5;
        var y = e.pageY - offset.top - 5;

        var xInPercent = self.fixPercent((x/$(this).width())*100);
        var yInPercent = self.fixPercent((y/$(this).height())*100);

        // Save the value
        self.saveCoordinate(xInPercent, yInPercent);
      }
    }).appendTo(self.$container);

    self.$hotspot = $('<div>', {
      'class': 'image-coordinate-hotspot'
    }).appendTo(self.$imgContainer);

    // Add description:
    $('<span>', {
      'class': 'h5peditor-field-description',
      html: self.field.description
    }).appendTo(self.$container);


    // H5PEditor.followField() does not work for the first element in list.
    // At least not thwe way it is used in Image Hotspots. Teherfore using changes
    // array directly.
    this.imageField.changes.push(function () {
      var params = self.imageField.params;
      if (params === undefined) {
        return self.clearImage();
      }

      self.updateImage(params.path);
    });

    if(self.imageField.params && self.imageField.params.path) {
      self.updateImage(self.imageField.params.path);
    }

    // If params not set, use default values:
    if (params === undefined || params.x === undefined || params.y === undefined) {
      this.saveCoordinate(45, 45);
    }
    else {
      self.updateHotspot(self.params.x, self.params.y);
    }
  }

  /**
   * Append the field to the wrapper.
   *
   * @param {H5P.jQuery} $wrapper
   */
  ImageCoordinateSelector.prototype.appendTo = function ($wrapper) {
    this.$container.appendTo($wrapper);
  };

  /**
   * Save coordinates
   *
   * @param {Number} x Value in percent
   * @param {Number} y Value in percent
   */
  ImageCoordinateSelector.prototype.saveCoordinate = function (x, y) {
    // Save the value
    this.params = {x: x, y: y};
    this.setValue(this.field, this.params);

    // Set visual element
    this.updateHotspot(x, y);
  };

  /**
   * Update image
   *
   * @param {String} path Image path
   */
  ImageCoordinateSelector.prototype.updateImage = function (path) {
    if (this.imgPath === path) {
      return;
    }
    this.imgPath = path;

    // Remove image if present
    this.clearImage();
    // Create image
    this.$imgContainer.append('<img src="' + H5P.getPath(path, H5PEditor.contentId) + '">');
    this.$container.removeClass('no-image');
  };

  /**
   * Remove image
   */
  ImageCoordinateSelector.prototype.clearImage = function () {
    this.$imgContainer.find('img').remove();
    this.$container.addClass('no-image');
  };

  /**
   * Update visual hotspot placement
   *
   * @param {Number} x Value in percent
   * @param {Number} y Value in percent
   */
  ImageCoordinateSelector.prototype.updateHotspot = function (x, y) {
    // Set visual element
    this.$hotspot.css({
      left: x + '%',
      top: y + '%',
      display: 'block'
    });
  };

  /**
   * Making sure percent is an integer between 0 and 100
   *
   * @param {Number} percent
   * @returns {Number}
   */
  ImageCoordinateSelector.prototype.fixPercent = function (percent) {
    percent = parseInt(percent);
    return percent < 0 ? 0 : (percent > 100 ? 100 : percent);
  };


  /**
   * Validate the current values. Invoked by core
   *
   * @returns {Boolean} Valid or not
   */
  ImageCoordinateSelector.prototype.validate = function () {
    return this.params !== undefined && this.params.x !== undefined && this.params.y !== undefined &&
           this.params.x >= 0 && this.params.x <= 100 &&
           this.params.y >= 0 && this.params.y <= 100;
  };

  /**
   * Remove me. Invoked by core
   */
  ImageCoordinateSelector.prototype.remove = function () {
    this.$imgContainer.remove();
  };

  return ImageCoordinateSelector;
})(H5P.jQuery);
