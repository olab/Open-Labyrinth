var H5PUpgrades = H5PUpgrades || {};

H5PUpgrades['H5P.MarkTheWords'] = (function ($) {
  return {
    1: {
      1: {
        contentUpgrade: function (parameters, finished) {
          // Moved all behavioural settings into "behaviour" group.
          parameters.behaviour = {
            enableRetry: parameters.enableRetry === undefined ? true: parameters.enableRetry,
            enableSolutionsButton: parameters.enableShowSolution === undefined ? true : parameters.enableShowSolution
          };
          delete parameters.enableRetry;
          delete parameters.enableShowSolution;
          finished(null, parameters);
        }
      },
      5: {
        contentUpgrade: function (parameters, finished) {
          if (parameters.textField !== undefined) {
            parameters.textField = parameters.textField.replace(/\n/g, "<br />");
          }
          finished(null, parameters);
        }
      }
    }
  };
})(H5P.jQuery);
