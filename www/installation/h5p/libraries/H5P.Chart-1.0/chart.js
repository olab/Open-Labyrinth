/*global H5P,d3*/

/**
 * Graph Cake module
 * @external {jQuery} $ H5P.jQuery
 * @external {EventDispatcher} EventDispatcher H5P.EventDispatcher
 */
H5P.Chart = (function ($, EventDispatcher) {

  /**
   * Initialize module.
   *
   * @class
   * @param {Object} params Behavior settings
   * @param {Number} id Content identification
   */
  function Chart(params, id) {
    var self = this;

    // Inheritance
    EventDispatcher.call(self);

    // Set params and filter data set to make sure it's valid
    self.params = params;
    if (self.params.listOfTypes) {
      Chart.filterData(self.params.listOfTypes);
    }
    else {
      self.params.listOfTypes = [];
    }

    // Add example/default behavior
    if (!self.params.listOfTypes.length) {
      self.params.listOfTypes = [
        {
          text: 'Cat',
          value: 1,
          color: '#D3D3D3',
          fontColor: '#000'
        },
        {
          text: 'Dog',
          value: 2,
          color: '#ADD8E6',
          fontColor: '#000'
        },
        {
          text: 'Mouse',
          value: 3,
          color: '#90EE90',
          fontColor: '#000'
        }
      ];
    }

    // Keep track of type.
    self.type = (self.params.graphMode === 'pieChart' ? 'Pie' : 'Bar');
  }

  // Inheritance
  Chart.prototype = Object.create(EventDispatcher.prototype);
  Chart.prototype.constructor = Chart;

  /**
   * Make sure the data set has set the required text and value properties.
   *
   * @param {Array} dataSet
   */
  Chart.filterData = function (dataSet) {
    // Cycle through data set
    for (var i = 0; i < dataSet.length; i++) {
      var row = dataSet[i];
      if (row.text === undefined || row.value === undefined) {
        // Remove invalid data
        dataSet.splice(i, 1);
        i--;
        continue;
      }

      row.text = row.text.trim();
      row.value = parseFloat(row.value);
      if (row.text === '' || isNaN(row.value)) {
        // Remove invalid data
        dataSet.splice(i, 1);
        i--;
        continue;
      }
    }
  };

  /**
   * Append field to wrapper.
   *
   * @param {H5P.jQuery} $container
   */
  Chart.prototype.attach = function ($container) {
    var self = this;

    // Create chart on first attach
    if (self.$wrapper === undefined) {
      self.$wrapper = $('<div/>', {'class': 'h5p-chart-chart h5p-chart-' + self.type.toLowerCase()});
      self.chart = new H5P.Chart[self.type + 'Chart'](self.params.listOfTypes, self.$wrapper);
    }

    // Prepare container
    self.$container = $container.html('').addClass('h5p-chart').append(self.$wrapper);

    // Handle resizing
    self.on('resize', function () {
      if (!self.$container.is(':visible')) {
        return; // Only handle if visible
      }
      // Resize existing chart
      self.chart.resize();
    });
  };

  return Chart;
})(H5P.jQuery, H5P.EventDispatcher);
