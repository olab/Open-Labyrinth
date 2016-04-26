(function (MemoryGame) {

  /**
   * Keeps track of the time spent.
   *
   * @class H5P.MemoryGame.Timer
   * @param {H5P.jQuery} $container
   */
  MemoryGame.Timer = function ($container) {
    var self = this;
    var interval, started, totalTime = 0;

    /**
     * Make timer more readable for humans.
     * @private
     * @param {Number} seconds
     * @returns {String}
     */
    var humanizeTime = function (seconds) {
      var minutes = Math.floor(seconds / 60);
      var hours = Math.floor(minutes / 60);

      minutes = minutes % 60;
      seconds = Math.floor(seconds % 60);

      var time = '';

      if (hours !== 0) {
        time += hours + ':';

        if (minutes < 10) {
          time += '0';
        }
      }

      time += minutes + ':';

      if (seconds < 10) {
        time += '0';
      }

      time += seconds;

      return time;
    };

    /**
     * Update the timer element.
     *
     * @private
     * @param {boolean} last
     * @returns {number}
     */
    var update = function (last) {
      var currentTime = (new Date().getTime() - started);
      $container.text(humanizeTime(Math.floor((totalTime + currentTime) / 1000)));

      if (last === true) {
        // This is the last update, stop timing interval.
        clearTimeout(interval);
      }
      else {
        // Use setTimeout since setInterval isn't safe.
        interval = setTimeout(function () {
          update();
        }, 1000);
      }

      return currentTime;
    };

    /**
     * Starts the counter.
     */
    self.start = function () {
      if (started === undefined) {
        started = new Date();
        update();
      }
    };

    /**
     * Stops the counter.
     */
    self.stop = function () {
      if (started !== undefined) {
        totalTime += update(true);
        started = undefined;
      }
    };

  };
  
})(H5P.MemoryGame);
