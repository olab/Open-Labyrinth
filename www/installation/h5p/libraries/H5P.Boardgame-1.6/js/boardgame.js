var H5P = H5P || {};

/**
 * Will render a Board game.
 *
 * @param {Array} options
 * @param {int} contentId
 * @returns {H5P.Boardgame} Instance
 */
H5P.Boardgame = function (options, contentId) {
  var $ = H5P.jQuery;
  if (!(this instanceof H5P.Boardgame)) {
    return new H5P.Boardgame(options, contentId);
  }
  this.contentId = contentId;
  H5P.EventDispatcher.call(this);


  var finished = false;
  var self = this;

  var texttemplate =
          '<div class="<%= classes %>">' +
          '  <div class="boardgame-intro open">' +
          '    <div class="bgi-content">' +
          '      <h1><%= title %></h1>' +
          '      <div class="h5p-bg-intro"><%= introduction.text %></div>' +
          '      <div class="buttons">' +
          '        <a class="button bgi-start"><%= introduction.startButtonText %></a>' +
          '      </div>' +
          '    </div>' +
          '  </div>' +
          '</div>';

  var overrideOptions = options.override;

  // An internal Object only available to Board games.
  function HotSpot(dom, hs_params) {
    H5P.EventDispatcher.call(this);
    var defaults = {
      title: 'Hotspot',
      image: undefined,
      passedImage: undefined,
      failedImage: undefined,
      action: ''
    };
    var that = this;
    var params = $.extend({}, defaults, hs_params); // TODO: Why not just extend defaults with hs_params? I don't think we need defaults later.
    this.passed = false;

    if (params.action.library === undefined) {
      return;
    }

    // Render HotSpot DOM elements
    var $hsd = $('<a class="hotspot" title="' + params.title + '" href="#" data-title="' + params.title + '"></a>');
    var HSDstyles = {
      left: hs_params.coords.x + 'px',
      top: hs_params.coords.y + 'px'
    };

    if (hs_params.image !== undefined) {
      HSDstyles.width = hs_params.image.width + 'px';
      HSDstyles.height = hs_params.image.height + 'px';
      HSDstyles.background = 'url("' + H5P.getPath(hs_params.image.path, contentId) + '") no-repeat';
    }
    else {
      $hsd.addClass('h5p-default');
    }

    // Insert DOM in BoardGame
    $('.boardgame', dom).append($hsd.css(HSDstyles));

    //Extend override settings for subcontent.
    $.extend(params.action.params, {
      override: overrideOptions,
    });
    this.action = H5P.newRunnable(params.action, contentId);

    // Attach event handlers
    $hsd.click(function () {
      // Start action
      // - Create container
      $('.boardgame', dom).append('<div class="action-container" id="action-container"></div>');

      // - Attach action
      that.action.attach('action-container');
      that.action.on('h5pQuestionSetFinished', function (event) {
        $('#action-container', dom).detach();
        that.action.reRender();
        // Update score in hotspot info
        $hsd.attr('title', $hsd.attr('data-title') + ': ' + event.data.score);
        // Switch background image to passed image.
        that.passed = event.data.passed;
        if (that.passed) {
          if (hs_params.passedImage !== undefined) {
            $hsd.css({backgroundImage: 'url("' + H5P.getPath(hs_params.passedImage.path, contentId) + '")'});
          }
          else {
            $hsd.addClass('h5p-passed').removeClass('h5p-failed');
          }
        } else {
          if (hs_params.failedImage !== undefined) {
            $hsd.css({backgroundImage: 'url("' + H5P.getPath(hs_params.failedImage.path, contentId) + '")'});
          }
          else {
            $hsd.addClass('h5p-failed').removeClass('h5p-passed');
          }
        }

        // Trigger further event to boardgame to calculate total score?
        that.trigger(event);
      });
      var $qs = $('.questionset', dom);
      $qs.children('.question-container').css('maxHeight', ($qs.height() - $qs.children('.qs-footer').height() - 20) + 'px');
      return false;
    });
  }
  HotSpot.prototype = Object.create(H5P.EventDispatcher.prototype);
  HotSpot.prototype.constructor = HotSpot;

  var defaults = {
    title: 'New game',
    introduction: {
      text: '',
      startButtonText: 'Start'
    },
    size: {
      x: 640,
      y: 320
    },
    hotspots: [],
    extras: [],
    progress: {
      enabled: false,
      coords: {
        x: 0,
        y: 0,
        w: 200,
        h: 100
      },
      images: []
    },
    endVideo: undefined,
    endResults: {
      text: "You scored @score of @total.<br/>That's @percentage%",
      solutionButtonText: 'Show solution',
      retryButtonText: 'Try more'
    },
    postUserStatistics: (H5P.postUserStatistics === true),
    shadow: true
  };

  var params = $.extend(true, {}, defaults, options);

  params.classes = 'boardgame';

  if (params.shadow) {
    params.classes += ' shadow';
  }

  var $myDom, $progress;
  var hotspots = [];
  var template = new EJS({text: texttemplate});

  if (H5P.trim(params.introduction.text) === '') {
    params.introduction.text = '&nbsp;';
  }

  // Update progress meter.
  var _updateProgress = function () {
    if (!$progress) {
      return;
    }

    var c = 0;
    for (var i = 0; i < hotspots.length; i++) {
      if (hotspots[i].passed) {
        c += 1;
      }
    }
    if (params.progress.images.length > c) {
      $progress.css({
        backgroundImage: 'url("' + H5P.getPath(params.progress.images[c].path, contentId) + '")',
        width: params.progress.images[c].width + 'px',
        height: params.progress.images[c].height + 'px'
      });
    }
  };

  var _checkIfFinished = function () {
    if (finished) {
      return false;
    }
    var c = 0;
    for (var i = 0; i < hotspots.length; i++) {
      if (hotspots[i].passed) {
        c += 1;
      }
    }
    if (c === hotspots.length) {
      // We're done. Start endgame
      _displayEndGame();
      finished = true;
    }
    return finished;
  };

  var _displayEndGame = function () {
    var displayResults = function () {
      // Calculate final scores
      var total = 0, score = 0, percentage;
      for (var i = 0; i < hotspots.length; i++) {
        var spot = hotspots[i];
        total += spot.action.totalScore();
        score += spot.action.getScore();
      }
      percentage = Math.floor(100*score/total);

      self.triggerXAPICompleted(score, total);

      var str = params.endResults.text.replace('@score', score).replace('@total', total).replace('@percentage', percentage);
      $('.h5p-bg-intro', $myDom).html(str);

      if (total > score) {
        // Knapp til fasit
        if ($('.bgi-solution', $myDom).length === 0) {
          $('<a class="button bgi-solution">' + params.endResults.solutionButtonText + '</a>').click(function () {
            for (var i = 0; i < params.hotspots.length; i++) {
              hotspots[i].action.showSolutions();
            }
            slideDown();
          }).appendTo('.bgi-content .buttons', $myDom);
        }

        // Knapp til å begynne på nytt
        $('.bgi-content .bgi-start', $myDom).text(params.endResults.retryButtonText);
      }
      else {
         $('.bgi-content .bgi-start', $myDom).hide();
      }

      // Slutt-text
      $('.boardgame-intro', $myDom).addClass('open').css('bottom', '');
    };
    // Show animation if present
    if (params.gameFinished !== undefined && params.gameFinished.video !== undefined) {
      var $videoContainer = $('<div class="video-container"></div>').appendTo($myDom.children('.boardgame'));

      var video = new H5P.Video({
        sources: params.gameFinished.video,
        fitToWrapper: true,
        controls: false,
        autoplay: true
      }, contentId);
      video.on('stateChange', function (event) {
        if (event.data === H5P.Video.ENDED) {
          displayResults();
          $videoContainer.hide();
        }
      });
      video.attach($videoContainer);

      if (params.gameFinished.allowSkipVideo) {
        $('<a class="button skip">' + params.gameFinished.skipButtonText + '</a>').click(function () {
          video.pause();
          $videoContainer.hide();
          displayResults();
        }).appendTo($videoContainer);
      }
    }
    else {
      // Show result page.
      displayResults();
    }
  };

  var slideDown = function () {
    var $bgiContent = $('.bgi-content', $myDom);
    var movePercent = ($bgiContent.height() - $bgiContent.children('h1').height()) / (params.size.height / 100);
    $('.boardgame-intro', $myDom).css('bottom', '-' + movePercent + '%').removeClass('open');
  };

  // Function for attaching to a DOM element.
  this.attach = function (target) {
    var $target;
    this.setActivityStarted();

    if (typeof(target) === 'string') {
      $target = $('#' + target);
    }
    else {
      $target = $(target);
    }
    // Render own DOM into target.
    $myDom = $target;
    $myDom.html(template.render(params));

    var boardgameStyles = {
      width: params.size.width,
      height: params.size.height
    };
    if (params.background !== undefined) {
      boardgameStyles.backgroundImage = 'url("' + H5P.getPath(params.background.path, contentId) + '")';
      boardgameStyles.backgroundRepeat = 'no-repeat';
      boardgameStyles.backgroundSize = params.size.width + 'px ' + params.size.height + 'px';
    }
    var $boardgame = $('.boardgame', $myDom).css(boardgameStyles);

    // Add click handler to start button.
    if (params.introduction) {
      $('.bgi-start', $boardgame).click(function () {
        slideDown();
      });
    }

    // Add hotspots.
    for (var i = 0; i < params.hotspots.length; i++) {
      var spot = new HotSpot($myDom, params.hotspots[i]);
      hotspots.push(spot);
      // Set event listeners.
      spot.on('h5pQuestionSetFinished', function (event) {
        _updateProgress();
        _checkIfFinished();
      });
    }

    // Start extras
    // for (var j = params.extras.length - 1; j >= 0; j--) {
    //   var a = (H5P.classFromName(params.extras[j].name))($myDom, params.extras[j].options);
    // }

    // Add progress field
    if (params.progress.enabled) {
      $progress = $('<div class="progress"></div>');
      $boardgame.append($progress);
      $progress.css({
        left: params.progress.coords.x + 'px',
        top: params.progress.coords.y + 'px'
      });
      _updateProgress();
    }

    this.trigger('resize');

    return this;
  };

  /**
   * Helper function for getting image copyrights.
   *
   * @param {Object} image
   * @returns {H5P.MediaCopyright}
   */
  var getImageCopyright = function (image) {
    if (image === undefined || image.copyright === undefined) {
      return;
    }

    var mediaCopyright = new H5P.MediaCopyright(image.copyright);
    mediaCopyright.setThumbnail(new H5P.Thumbnail(H5P.getPath(image.path, contentId), image.width, image.height));
    return mediaCopyright;
  };

  /**
   * Gather copyright information for the current content.
   *
   * @returns {H5P.ContentCopyrights}
   */
  this.getCopyrights = function () {
    var info = new H5P.ContentCopyrights();

    // Background
    info.addMedia(getImageCopyright(params.background));

    // Hotspots
    for (var i = 0; i < hotspots.length; i++) {
      var hotspot = hotspots[i];
      if (hotspot.action.getCopyrights !== undefined) {
        var hotRights = hotspot.action.getCopyrights();
        if (hotRights !== undefined) {
          hotRights.setLabel('Hotspot ' + (i+1)); // TODO: Change/translate

          var hps = params.hotspots[i];
          // Hotspot image
          hotRights.addMedia(getImageCopyright(hps.image));
          // Hotspot passed image
          hotRights.addMedia(getImageCopyright(hps.passedImage));
          // Hotspot failed image
          hotRights.addMedia(getImageCopyright(hps.failedImage));

          info.addContent(hotRights);
        }
      }
    }

    // Progress images
    for (var i = 0; i < params.progress.images.length; i++) {
       info.addMedia(getImageCopyright(params.progress.images[i]));
    }

    // Finished video
    if (params.gameFinished.video) {
      var video = params.gameFinished.video[0];
      if (video.copyright !== undefined) {
        info.addMedia(new H5P.MediaCopyright(video.copyright));
      }
    }

    return info;
  };
};

H5P.Boardgame.prototype = Object.create(H5P.EventDispatcher.prototype);
H5P.Boardgame.prototype.constructor = H5P.Boardgame;
