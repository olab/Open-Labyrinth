(function (MemoryGame, EventDispatcher, $) {

  /**
   * Controls all the operations for each card.
   *
   * @class H5P.MemoryGame.Card
   * @param {Object} parameters
   * @param {Number} id
   */
  MemoryGame.Card = function (parameters, id) {
    var self = this;

    // Initialize event inheritance
    EventDispatcher.call(self);

    var path = H5P.getPath(parameters.image.path, id);
    var width, height, margin, $card;

    var a = 96;
    if (parameters.image.width !== undefined && parameters.image.height !== undefined) {
      if (parameters.image.width > parameters.image.height) {
        width = a;
        height = parameters.image.height * (width / parameters.image.width);
        margin = '' + ((a - height) / 2) + 'px 0 0 0';
      }
      else {
        height = a;
        width = parameters.image.width * (height / parameters.image.height);
        margin = '0 0 0 ' + ((a - width) / 2) + 'px';
      }
    }
    else {
      width = height = a;
    }

    /**
     * Flip card.
     */
    self.flip = function () {
      $card.addClass('h5p-flipped');
      self.trigger('flip');
    };

    /**
     * Flip card back.
     */
    self.flipBack = function () {
      $card.removeClass('h5p-flipped');
    };

    /**
     * Remove.
     */
    self.remove = function () {
      $card.addClass('h5p-matched');
    };

    /**
     * Get card description.
     *
     * @returns {string}
     */
    self.getDescription = function () {
      return parameters.description;
    };

    /**
     * Get image clone.
     *
     * @returns {H5P.jQuery}
     */
    self.getImage = function () {
      return $card.find('img').clone();
    };

    /**
     * Append card to the given container.
     *
     * @param {H5P.jQuery} $container
     */
    self.appendTo = function ($container) {
      // TODO: Translate alt attr
      $card = $('<li class="h5p-memory-card" role="button" tabindex="1">' +
                  '<div class="h5p-front"></div>' +
                  '<div class="h5p-back">' +
                    '<img src="' + path + '" alt="Memory Card" width="' + width + '" height="' + height + '"' + (margin === undefined ? '' : ' style="margin:' + margin + '"') + '/>' +
                  '</div>' +
                  '</li>')
        .appendTo($container)
        .children('.h5p-front')
          .click(function () {
            self.flip();
          })
          .end();
      };
  };

  // Extends the event dispatcher
  MemoryGame.Card.prototype = Object.create(EventDispatcher.prototype);
  MemoryGame.Card.prototype.constructor = MemoryGame.Card;

})(H5P.MemoryGame, H5P.EventDispatcher, H5P.jQuery);
