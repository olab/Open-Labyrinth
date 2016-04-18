(function (MemoryGame) {

  /**
   * Keeps track of the number of cards that has been turned
   *
   * @class H5P.MemoryGame.Counter
   * @param {H5P.jQuery} $container
   */
  MemoryGame.Counter = function ($container) {
    var self = this;

    var current = 0;

    /**
     * Increment the counter.
     */
    self.increment = function () {
      current++;
      $container.text(current);
    };
  };

})(H5P.MemoryGame);
