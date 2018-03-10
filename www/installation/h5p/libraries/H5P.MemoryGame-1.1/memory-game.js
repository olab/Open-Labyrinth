H5P.MemoryGame = (function (EventDispatcher, $) {

  /**
   * Memory Game Constructor
   *
   * @class
   * @param {Object} parameters
   * @param {Number} id
   */
  function MemoryGame(parameters, id) {
    var self = this;

    // Initialize event inheritance
    EventDispatcher.call(self);

    var flipped, timer, counter, popup, $feedback;
    var cards = [];
    var removed = 0;

    /**
     * Check if these two cards belongs together.
     *
     * @private
     * @param {H5P.MemoryGame.Card} card
     * @param {H5P.MemoryGame.Card} mate
     * @param {H5P.MemoryGame.Card} correct
     */
    var check = function (card, mate, correct) {
      if (mate === correct) {
        // Remove them from the game.
        card.remove();
        mate.remove();

        removed += 2;

        var finished = (removed === cards.length);
        var desc = card.getDescription();

        if (desc !== undefined) {
          // Pause timer and show desciption.
          timer.stop();
          popup.show(desc, card.getImage(), function () {
            if (finished) {
              self.triggerXAPIScored(1, 1, 'completed');
              // Game has finished
              $feedback.addClass('h5p-show');
            }
            else {
              // Popup is closed, continue.
              timer.start();
            }
          });
        }
        else if (finished) {
          self.triggerXAPIScored(1, 1, 'completed');
          // Game has finished
          timer.stop();
          $feedback.addClass('h5p-show');
        }
      }
      else {
        // Flip them back
        card.flipBack();
        mate.flipBack();
      }
    };

    /**
     * Adds card to card list and set up a flip listener.
     *
     * @private
     * @param {H5P.MemoryGame.Card} card
     * @param {H5P.MemoryGame.Card} mate
     */
    var addCard = function (card, mate) {
      card.on('flip', function () {
        self.triggerXAPI('interacted');
        // Keep track of time spent
        timer.start();

        if (flipped !== undefined) {
          var matie = flipped;
          // Reset the flipped card.
          flipped = undefined;

          setTimeout(function () {
            check(card, matie, mate);
          }, 800);
        }
        else {
          // Keep track of the flipped card.
          flipped = card;
        }

        // Count number of cards turned
        counter.increment();
      });

      cards.push(card);
    };

    // Initialize cards.
    for (var i = 0; i < parameters.cards.length; i++) {
      // Add two of each card
      var cardOne = new MemoryGame.Card(parameters.cards[i], id);
      var cardTwo = new MemoryGame.Card(parameters.cards[i], id);
      addCard(cardOne, cardTwo);
      addCard(cardTwo, cardOne);
    }
    H5P.shuffleArray(cards);

    /**
     * Attach this game's html to the given container.
     *
     * @param {H5P.jQuery} $container
     */
    self.attach = function ($container) {
      this.triggerXAPI('attempted');
      // TODO: Only create on first!
      $container.addClass('h5p-memory-game').html('');

      // Add cards to list
      var $list = $('<ul/>');
      for (var i = 0; i < cards.length; i++) {
        cards[i].appendTo($list);
      }

      if ($list.children().length) {
        $list.appendTo($container);

        $feedback = $('<div class="h5p-feedback">' + parameters.l10n.feedback + '</div>').appendTo($container);

        // Add status bar
        var $status = $('<dl class="h5p-status">' +
                        '<dt>' + parameters.l10n.timeSpent + '</dt>' +
                        '<dd class="h5p-time-spent">0:00</dd>' +
                        '<dt>' + parameters.l10n.cardTurns + '</dt>' +
                        '<dd class="h5p-card-turns">0</dd>' +
                        '</dl>').appendTo($container);

        timer = new MemoryGame.Timer($status.find('.h5p-time-spent'));
        counter = new MemoryGame.Counter($status.find('.h5p-card-turns'));
        popup = new MemoryGame.Popup($container);

        $container.click(function () {
          popup.close();
        });
      }
    };
  }

  // Extends the event dispatcher
  MemoryGame.prototype = Object.create(EventDispatcher.prototype);
  MemoryGame.prototype.constructor = MemoryGame;

  return MemoryGame;
})(H5P.EventDispatcher, H5P.jQuery);
