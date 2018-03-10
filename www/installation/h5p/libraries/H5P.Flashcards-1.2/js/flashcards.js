var H5P = H5P || {};

/**
 * Flashcards module.
 *
 * @param {jQuery} $
 */
H5P.Flashcards = (function ($) {

  /**
   * Initialize module.
   *
   * @param {Object} options Run parameters
   * @param {Number} id Content identification
   */
  function C(options, id) {
    H5P.EventDispatcher.call(this);
    this.score = 0;
    this.numAnswered = 0;
    this.contentId = this.id = id;
    this.options = $.extend({}, {
      description: "What does the card mean?",
      progressText: "Card @card of @total",
      next: "Next",
      previous: "Previous",
      checkAnswerText: "Check answer",
      showSolutionsRequiresInput: true
    }, options);
    this.$images = [];
    this.on('resize', this.resize, this);
  }

  C.prototype = Object.create(H5P.EventDispatcher.prototype);
  C.prototype.constructor = C;

  /**
   * Append field to wrapper.
   *
   * @param {jQuery} $container
   */
  C.prototype.attach = function ($container) {
    var that = this;

    this.$container = $container.addClass('h5p-flashcards').html('<div class="h5p-loading">Loading, please wait...</div>');

    // Load card images. (we need their size before we can create the task)
    var loaded = 0;
    for (var i = 0; i < this.options.cards.length; i++) {
      var card = this.options.cards[i];
      var load = function () {
        loaded++;
        if (loaded === that.options.cards.length) {
          that.cardsLoaded();
        }
      };
      if (card.image !== undefined) {
        var $image = $('<img class="h5p-clue" src="' + H5P.getPath(card.image.path, this.id) + '"/>').load(load);
        this.$images[i] = $image;
      }
      else {
        this.$images[i] = $('<div class="h5p-clue"></div>');
      }
      if (card.image === undefined || $image.get().complete) {
        // Image cached
        load();
      }
    }
  };

  /**
   * Called when all cards has been loaded.
   */
  C.prototype.cardsLoaded = function () {
    var that = this;
    var $inner = this.$container.html('<div class="h5p-description">' + this.options.description + '</div><div class="h5p-inner"></div><div class="h5p-navigation"><button type="button" class="h5p-button h5p-previous h5p-hidden" tabindex="3" title="' + this.options.previous + '"></button><button type="button" class="h5p-button h5p-next" tabindex="4" title="' + this.options.next + '"></button><div class="h5p-progress"></div>').children('.h5p-inner');
    this.$progress = this.$container.find('.h5p-progress');

    // Add cards
    for (var i = 0; i < this.options.cards.length; i++) {
      this.addCard(i, $inner);
    }

    // Find highest image and set task height.
    var height = 180;
    for (var i = 0; i < this.$images.length; i++) {
      var $image = this.$images[i];

      if ($image === undefined) {
        continue;
      }

      var imageHeight = $image.height();
      if (imageHeight > height) {
        height = imageHeight;
      }
    }

    // Center images
    for (var i = 0; i < this.$images.length; i++) {
      var $image = this.$images[i];
      if ($image === undefined) {
        continue;
      }
    }

    // Set height
    $inner.css('height', height + 286); // TODO: Avoid magic numbers - 122?

    // Active buttons
    var $buttonWrapper = $inner.next();
    this.$nextButton = $buttonWrapper.children('.h5p-next').click(function () {
      that.next();
    });
    this.$prevButton = $buttonWrapper.children('.h5p-previous').click(function () {
      that.previous();
    });

    if (this.options.cards.length < 2) {
      this.$nextButton.hide();
    }

    this.$inner = $inner;

    this.setProgress();

    this.trigger('resize');
  };

  C.prototype.addCard = function (index, $inner) {
    var that = this;

    var card = this.options.cards[index];
    var imageText = (card.text !== undefined ? '<div class="h5p-imagetext">' + card.text + '</div>' : '');
    var $card = $('<div class="h5p-card h5p-animate' + (index === 0 ? ' h5p-current' : '') + '"> ' +
      '<div class="h5p-cardholder">' +
      '<div class="h5p-imageholder"></div>' +
      '<div class="h5p-foot">' + imageText + '<div class="h5p-answer">' +
      '<div class="h5p-input"><input type="text" class="h5p-textinput" tabindex="-1"/>' +
      '<button type="button" class="h5p-button" tabindex="-1">' + this.options.checkAnswerText + '</button></div></div></div></div></div>')
      .appendTo($inner);
    $card.find('.h5p-imageholder').prepend(this.$images[index]);

    // Add tip if tip exists
    if (card.tip !== undefined && card.tip.trim().length > 0) {
      $('.h5p-input', $card).append(H5P.JoubelUI.createTip(card.tip)).addClass('has-tip');
    }

    var $button = $card.find('.h5p-button').click(function () {
      var $input = $card.find('.h5p-textinput');
      var correctAnswer = that.options.cards[index].answer;
      if (correctAnswer === undefined) {
        correctAnswer = '';
      }
      var correct = correctAnswer.toLowerCase().split('/');
      var userAnswer = H5P.trim($input.val()).toLowerCase();
      var userCorrect = false;
      for (var i = 0; i < correct.length; i++) {
        if (H5P.trim(correct[i]) === userAnswer) {
          that.score++;
          userCorrect = true;
          break;
        }
      }

      that.numAnswered++;
      if (that.numAnswered >= that.options.cards.length) {
        that.triggerXAPICompleted(that.score, that.numAnswered);
      }

      if (!that.options.showSolutionsRequiresInput || userAnswer !== '' || userCorrect) {
        $input.add(this).attr('disabled', true);

        if (userCorrect) {
          $input.parent().addClass('h5p-correct');
        }
        else {
          $input.parent().addClass('h5p-wrong');
        }

        that.$images[index].addClass('h5p-collapse');
        setTimeout(function () {
          that.$images[index].removeClass('h5p-collapse');
        }, 150);

        var $solution = $('<div class="h5p-solution h5p-hidden"><span>' + correctAnswer + '</span></div>').appendTo($card.find('.h5p-imageholder'));
        setTimeout(function () {
          $solution.removeClass('h5p-hidden');
        }, 150);
      }
    });
    $card.find('.h5p-textinput').keypress(function (event) {
      if (event.keyCode === 13) {
        $button.click();
        return false;
      }
    });

    if (index === 0) {
      this.setCurrent($card);
    }
  };

  C.prototype.setProgress = function () {
    var index = this.$current.index();
    this.$progress.text(this.options.progressText.replace('@card', index + 1).replace('@total', this.options.cards.length));
  };

  /**
   * Set card as current card.
   *
   * Adjusts classes and tabindexes for existing current card and new
   * card.
   *
   * @param {jQuery-object} $card
   * @param {string} newClassForOldCurrentCard
   *   Class to add to existing current card.
   */
  C.prototype.setCurrent = function ($card, newClassForOldCurrentCard) {
    // Remove from existing card.
    if (this.$current) {
      this.$current.removeClass('h5p-current');
      this.$current.find('input, button').attr('tabindex', '-1');
      if (newClassForOldCurrentCard) {
        this.$current.addClass(newClassForOldCurrentCard);
      }
    }

    this.$current = $card;
    $card.addClass('h5p-current');
    $card.find('.h5p-textinput').attr('tabindex', '1');
    $card.find('.h5p-button').attr('tabindex', '2');
    $card.removeClass('h5p-previous');
  };

  /**
   * Display next card.
   */
  C.prototype.next = function () {
    var that = this;
    var $next = this.$current.next();
    if (!$next.length) {
      return;
    }

    setTimeout(function () {
      that.setCurrent($next, 'h5p-previous');

      if (!that.$current.next().length) {
        that.$nextButton.addClass('h5p-hidden');
      }
      that.$prevButton.removeClass('h5p-hidden');
      that.setProgress();
    }, 10);
  };

  /**
   * Display previous card.
   */
  C.prototype.previous = function () {
    var that = this;
    var $prev = this.$current.prev();
    if (!$prev.length) {
      return;
    }

    setTimeout(function () {
      that.setCurrent($prev);

      if (!that.$current.prev().length) {
        that.$prevButton.addClass('h5p-hidden');
      }
      that.$nextButton.removeClass('h5p-hidden');
      that.setProgress();
    }, 10);
  };

  /**
   * Gather copyright information from cards.
   *
   * @returns {H5P.ContentCopyrights}
   */
  C.prototype.getCopyrights = function () {
    var info = new H5P.ContentCopyrights();

    // Go through cards
    for (var i = 0; i < this.options.cards.length; i++) {
      var image = this.options.cards[i].image;
      if (image !== undefined && image.copyright !== undefined) {
        var rights = new H5P.MediaCopyright(image.copyright);
        rights.setThumbnail(new H5P.Thumbnail(H5P.getPath(image.path, this.id), image.width, image.height));
        info.addMedia(rights);
      }
    }

    return info;
  };

  /**
   * Update the dimensions and imagesizes of the task.
   */
  C.prototype.resize = function () {
    var self = this;
    if (self.$inner === undefined) {
      return;
    }
    var maxHeight = 0;
    var maxHeightImage = 0;
    var imageHolderWidth = self.$inner.find('.h5p-imageholder').width();
    var minPadding = parseFloat(self.$inner.css('font-size'));

    //Resize all images and find max height.
    self.$images.forEach(function (image) {
      var $image = image;
      var imageHeight = 0;
      $image.css({
        'height': 'initial',
        'width': 'initial'
      });

      //Resize image if it is too big.
      if (($image[0].naturalWidth + (minPadding * 2)) > imageHolderWidth ||
        ($image[0].naturalHeight + (minPadding * 2)) > imageHolderWidth) {
        var ratio = $image[0].naturalHeight / $image[0].naturalWidth;

        //Landscape image
        if( $image[0].naturalWidth >= $image[0].naturalHeight) {
          $image.css({
            'width': imageHolderWidth - (minPadding * 2),
            'height': 'auto'
          });
          imageHeight = (imageHolderWidth - (minPadding * 2)) * ratio;
        }
        //Portrait image
        else {
          $image.css({
            'height': imageHolderWidth - minPadding * 2,
            'width': 'auto'
          });
          imageHeight = imageHolderWidth - minPadding * 2;
        }
      }
      //Else use source dimensions
      else {
        $image.css({
          'height': 'initial',
          'width': 'initial'
        });
        imageHeight = $image.outerHeight();
      }
      //Keep max height
      maxHeightImage = imageHeight + minPadding * 2 > maxHeightImage ? imageHeight + minPadding * 2 : maxHeightImage;
    });

    //Find container dimensions needed to encapsule image and text.
    self.$inner.children().each( function (cardWrapper) {
      var cardholderHeight = maxHeightImage + $(this).find('.h5p-foot').outerHeight();
      maxHeight = cardholderHeight > maxHeight ? cardholderHeight : maxHeight;
    });

    //Resize containers to fit image and text.
    self.$inner.find('.h5p-imageholder').css('height', maxHeightImage + 'px');
    self.$inner.css('height', maxHeight + minPadding * 2 +'px');
  };

  return C;
})(H5P.jQuery);
