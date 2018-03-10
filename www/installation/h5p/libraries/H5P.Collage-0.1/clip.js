(function ($, Collage, EventDispatcher) {

  /**
   * Collage Clip
   *
   * @class H5P.Collage.Clip
   * @extends H5P.EventDispatcher
   * @param {H5P.jQuery} $container
   * @param {Object} content
   * @param {number} contentId
   */
  Collage.Clip = function ($container, content, contentId) {
    var self = this;

    // Initialize event inheritance
    EventDispatcher.call(self);

    // Photo wrapper
    self.$wrapper = $('<div/>', {
      'class': 'h5p-collage-photo',
      appendTo: $container
    });

    // Clip resource
    var $img;

    // Always available
    self.content = content;

    /**
     * @private
     */
    var positionImage = function (imageRatio) {
      // Find container raioratios
      var containerSize = window.getComputedStyle(self.$wrapper[0]);
      var containerRatio = (parseFloat(containerSize.width) / parseFloat(containerSize.height));

      // Make sure image covers the whole container
      if (isNaN(containerRatio) || imageRatio > containerRatio) {
        self.prop = 'height';
      }
      else {
        self.prop = 'width';
      }
      $img.css(self.prop, (content.scale * 100) + '%');

      // Pan image
      $img.css('margin', content.offset.top + '% 0 0 ' + content.offset.left + '%');
    };

    /**
     * Triggers the loading of the image.
     */
    self.load = function () {
      if (self.empty()) {
        self.$wrapper.addClass('h5p-collage-empty');
        return; // No image set
      }
      else {
        self.$wrapper.removeClass('h5p-collage-empty');
      }

      // Create image
      $img = $('<img/>', {
        'class': 'h5p-collage-image',
        alt: '',
        src: H5P.getPath(content.image.path, contentId),
        prependTo: self.$wrapper,
        on: {
          load: function () {
            // Make sure it's in the correct position
            positionImage(this.width / this.height);
          }
        }
      });
      setTimeout(function () {
        // Wait for next tick to make sure everything is visible
        positionImage((content.image.width && content.image.height ? content.image.width / content.image.height : undefined));
      }, 0);
      self.trigger('change', $img);
    };

    /**
     * Check if the current clip is empty or set.
     *
     * @returns {boolean}
     */
    self.empty = function () {
      return !content.image;
    };
  };

  // Extends the event dispatcher
  Collage.Clip.prototype = Object.create(EventDispatcher.prototype);
  Collage.Clip.prototype.constructor = Collage.Clip;

})(H5P.jQuery, H5P.Collage, H5P.EventDispatcher);
