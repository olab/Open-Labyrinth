var H5PUpgrades = H5PUpgrades || {};

H5PUpgrades['H5P.MemoryGame'] = (function ($) {
  return {
    1: {
      1: {
        contentUpgrade: function (parameters, finished) {
          // Move card images into card objects, allows for additonal properties.
          for (var i = 0; i < parameters.cards.length; i++) {
            parameters.cards[i] = {
              image: parameters.cards[i]
            };
          }

          finished(null, parameters);
        }
      }
    }
  };
})(H5P.jQuery);