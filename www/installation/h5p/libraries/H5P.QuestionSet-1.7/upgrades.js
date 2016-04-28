var H5PUpgrades = H5PUpgrades || {};

H5PUpgrades['H5P.QuestionSet'] = (function ($) {
  return {
    1: {
      3: function (parameters, finished) {
        for (var i = 0; i < parameters.questions.length; i++) {
          if (parameters.questions[i].subContentId === undefined) {
            // NOTE: We avoid using H5P.createUUID since this is an upgrade script and H5P function may change in the
            // future
            parameters.questions[i].subContentId = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(char) {
              var random = Math.random()*16|0, newChar = char === 'x' ? random : (random&0x3|0x8);
              return newChar.toString(16);
            });
          }
        }
        finished(null, parameters);
      }
    }
  };
})(H5P.jQuery);
