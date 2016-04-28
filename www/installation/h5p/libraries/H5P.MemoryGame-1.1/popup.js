(function (MemoryGame, $) {

  /**
   * A dialog for reading the description of a card.
   *
   * @class H5P.MemoryGame.Popup
   * @param {H5P.jQuery} $container
   */
  MemoryGame.Popup = function ($container) {
    var self = this;

    var closed;

    var $popup = $('<div class="h5p-memory-pop"><div class="h5p-memory-image"></div><div class="h5p-memory-desc"></div></div>').appendTo($container);
    var $desc = $popup.find('.h5p-memory-desc');
    var $image = $popup.find('.h5p-memory-image');

    /**
     * Show the popup.
     *
     * @param {string} desc
     * @param {H5P.jQuery} $img
     * @param {function} done
     */
    self.show = function (desc, $img, done) {
      $desc.html(desc);
      $img.appendTo($image.html(''));
      $popup.show();
      closed = done;
    };

    /**
     * Close the popup.
     */
    self.close = function () {
      if (closed !== undefined) {
        $popup.hide();
        closed();
        closed = undefined;
      }
    };
  };

})(H5P.MemoryGame, H5P.jQuery);
