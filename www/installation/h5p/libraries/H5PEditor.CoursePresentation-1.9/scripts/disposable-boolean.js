// Create a new editor widget for boolean fields
ns.widgets.disposableBoolean = (function ($, EventDispatcher) {

  /**
   * A special boolean option that only can be used once.
   * Requires confirmation before checking it.
   *
   * @class
   * @param {object} parent
   * @param {object} field
   * @param {boolean} params
   * @param {function} setValue
   */
  function DisposableBoolean(parent, field, params, setValue) {
    var self = this;

    // Inherit event support
    EventDispatcher.call(self);

    var $element;
    var checked = (params !== undefined && params);
    setValue(field, checked);

    // Expose field props
    self.field = field;

    /**
     * Build the DOM element string and then create the HTML.
     *
     * @private
     * @returns {H5P.jQuery}
     */
    var createElement = function () {
      var html = '<label class="h5peditor-label"><input type="checkbox"';
      if (checked) {
        html += ' checked="checked" disabled="disabled"';
      }
      html += '/>';
      if (field.label !== 0) {
        html += (field.label === undefined ? field.name : field.label);
      }
      $element = $(ns.createItem(field.type, html + '</label>', field.description));
      if (checked) {
        $element.addClass('disabled');
      }
      var $input = $element.find('input').on('change', function () {
        if (confirm(H5PEditor.t('H5PEditor.CoursePresentation', 'activeSurfaceWarning'))) {
          checked = $input.is(':checked');
          setValue(field, checked);
          $input.attr('disabled', true);
          $element.addClass('disabled');
          self.trigger('checked');
        }
        else {
          // Reset
          $input.attr('checked', false);
        }
      });
    };

    /**
     * Appends this fields elements to the given container.
     *
     * @param {H5P.jQuery} $container
     */
    self.appendTo = function ($container) {
      if ($element === undefined) {
        createElement();
      }
      $element.appendTo($container);
    };

    /**
     * Checks to see if this fields value is valid.
     *
     * @returns {boolean}
     */
    self.validate = function () {
      return true; // Always valid
    };

    /**
     * Removes this field from the DOM.
     */
    self.remove = function () {
      $element.detach();
    };
  }

  DisposableBoolean.prototype = Object.create(EventDispatcher.prototype);
  DisposableBoolean.prototype.constructor = DisposableBoolean;

  return DisposableBoolean;
})(H5P.jQuery, H5P.EventDispatcher);
