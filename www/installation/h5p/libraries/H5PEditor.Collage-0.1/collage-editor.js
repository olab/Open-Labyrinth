H5PEditor.Collage = (function ($, contentId, Collage) {

  /**
   * Collage Editor widget.
   *
   * @class H5P.CollageEditor
   * @param {Object} parent
   * @param {Object} field
   * @param {Object} params
   * @param {function} setValue
   */
  function CollageEditor(parent, field, params, setValue) {
    var self = this;

    // In case the editor is loaded before the Collage.
    Collage = H5P.Collage;

    // Get field options from semantics
    var layoutField = findField('template', field.fields);
    var optionsField = findField('options', field.fields);
    var heightField = findField('heightRatio', optionsField.fields);
    var spacingField = findField('spacing', optionsField.fields);
    var frameField = findField('frame', optionsField.fields);

    // Set params if none is given
    if (params === undefined) {
      params = {};
      setValue(field, params);
    }

    // Pass ready callbacks if the editor isn't ready yet
    var readyCallbacks = [];
    var passreadyCallbacks = true;
    parent.ready(function () {
      passreadyCallbacks = false;
    });

    // Editor wrapper
    $wrapper = $('<div/>', {
      'class': 'h5p-collage-editor-wrapper'
    });

    // Create the collage for live preview
    var collage = new Collage({collage: params}, contentId);
    var layoutSelector;

    // Handle clips being added to the collage.
    collage.on('clipAdded', function (event) {
      // Extend clip
      CollageEditor.Clip.call(event.data, layoutSelector, fileUpload);
    });

    H5P.$window.on('resize', function () {
      collage.trigger('resize');
    });

    /**
     * Converts em value to a more human value.
     *
     * @private
     * @param {number} value
     * @returns {number}
     */
    var humanInt = function (value) {
      return parseInt(value * 10);
    };

    /**
     * Make sure number appears with two decimals.
     *
     * @private
     * @param {number} value
     * @returns {string}
     */
    var humanFloat = function (value) {
      value = value.toString();
      var dot = value.indexOf('.');
      if (dot === -1) {
        value += '.00';
      }
      else if (value[dot + 2] === undefined) {
        value += '0';
      }
      return value;
    };

    /**
     * Inserts a number range selector.
     *
     * @private
     * @param {object} field Appearence
     * @param {string} field.name
     * @param {string} field.label
     * @param {number} field.min
     * @param {number} field.max
     * @param {function} change Callback when new value is selected
     * @param {number} step
     * @param {function} humanize Post processing of value
     * @returns {H5P.jQuery} Wrapper of the element
     */
    var rangeSelector = function (field, change, step, humanize) {
      var $itemWrapper = getItemWrapper(field.name, field.label);
      var $inner = $('<div/>', {
        'class': 'h5p-collage-inner-wrapper',
        appendTo: $itemWrapper
      });
      var last = humanize(params.options[field.name]);
      $('<input/>', {
        'class': 'h5p-collage-range-input',
        type: 'range',
        min: field.min,
        max: field.max,
        step: (field.max - field.min) / step,
        value: params.options[field.name],
        on: {
          change: function () {
            params.options[field.name] = this.value;
            last = humanize(this.value);
            $value.html(last);
            change(this.value);
          },
          input: function () {
            $value.html(humanize(this.value));
          }
        },
        appendTo: $inner
      });
      var $value = $('<div/>', {
        'class': 'h5p-collage-selector-preview',
        html: last,
        appendTo: $inner
      });
      return $itemWrapper;
    };

    /**
     * Will align all the fields by setting the same height.
     *
     * @private
     * @param {Array} fields List of H5P.jQuery objects
     */
    var sameHeight = function (fields) {
      var targetHeight = 0;
      var i;
      for (i = 0; i < fields.length; i++) {
        var fieldHeight = parseFloat(window.getComputedStyle(fields[i][0]).height);
        if (fieldHeight > targetHeight) {
          targetHeight = fieldHeight;
        }
      }

      if (!targetHeight) {
        return; // Skip giving all the fields no height
      }

      targetHeight += 'px';
      for (i = 0; i < fields.length; i++) {
        fields[i].css('height', targetHeight);
      }
    };

    /**
     * Make sure all the clips cover their containers.
     *
     * @private
     */
    var fitClips = function () {
      for (var i = 0; i < collage.clips.length; i++) {
        if (!collage.clips[i].empty()) {
          collage.clips[i].fit();
        }
      }
    };

    /**
     * Appends the collage editor widget
     *
     * @param {H5P.jQuery} $container
     */
    this.appendTo = function ($container) {
      // Add tiling layout selector
      var $layoutSelectorWrapper = getItemWrapper(layoutField.name, layoutField.label);
      layoutSelector = new CollageEditor.LayoutSelector($layoutSelectorWrapper, layoutField.options, params.template);
      layoutSelector.on('layoutChanged', function (event) {
        params.template = event.data;
        params.clips = [];
        collage.setLayout(params.template);
      });

      // Attach Collage preview
      var $collageWrapper = getItemWrapper(field.name, field.label);
      $preview = $('<div/>', {
        'class': 'h5p-collage-preview',
        appendTo: $collageWrapper
      });
      collage.attach($preview);
      $('<div/>', {
        'class': 'h5peditor-field-description',
        text: field.description,
        appendTo: $collageWrapper
      });

      // Keep track of all adjustments options so that they may be aligned
      var adjustmentOptions = [];

      // Add spacing selector
      adjustmentOptions.push(rangeSelector(spacingField, function (newSpacing) {
        collage.setSpacing(newSpacing);
        if (params.options.frame) {
          collage.setFrame(newSpacing);
        }
        fitClips();
      }, 20, humanInt));

      // Add frame options
      var $frameOptionWrapper = getItemWrapper(frameField.name, frameField.label);
      adjustmentOptions.push($frameOptionWrapper);
      $('<div class="h5p-collage-frame-selector"><label><input type="radio" name="h5p-collage-frame" value="1"' + (params.options.frame ? ' checked="checked"' : '') + '>' + CollageEditor.t('sameAsSpacing') + '</label><br/><label><input type="radio" name="h5p-collage-frame" value="0"' + (params.options.frame ? '' : ' checked="checked"') + '>' + CollageEditor.t('noFrame') + '</label></div>')
        .appendTo($frameOptionWrapper)
        .find('input').change(function () {
          params.options.frame = (this.value === '1');
          collage.setFrame(params.options.frame ? params.options.spacing : 0);
          fitClips();
        });

      // Add height adjustment
      adjustmentOptions.push(rangeSelector(heightField, function (newHeight) {
        collage.setHeight(newHeight);
        fitClips();
      }, 38, humanFloat));

      // Make sure all adjustment options have the same height
      self.ready(function () {
        sameHeight(adjustmentOptions);
      });

      // Attach wrapper to container
      $wrapper.appendTo($container);

      // Resize the collage
      collage.trigger('resize');
    };

    /**
     * Collect callbacks to run when the editor is done assembling.
     *
     * @param {function} ready callback
     */
    this.ready = function (ready) {
      if (passreadyCallbacks) {
        parent.ready(ready);
      }
      else {
        readyCallbacks.push(ready);
      }
    };

    /**
     * Checks if this field and all child fields are valid.
     *
     * @returns {boolean}
     */
    this.validate = function () {
      return true;
    };

    /**
     * Remove this field and all child fields from the editor.
     */
    this.remove = function () {
      $wrapper.remove();
    };

    /**
     * @param {string} name
     * @param {string} [label]
     * @param {string} [description]
     * @returns {H5P.jQuery}
     */
    var getItemWrapper = function (name, label) {
      var $itemWrapper = $('<div/>', {
        'class': 'h5p-collage-' + name + '-item',
        appendTo: $wrapper
      });

      if (label) {
        $('<label/>', {
          'class': 'h5peditor-label',
          text: label,
          appendTo: $itemWrapper
        });
      }

      return $itemWrapper;
    };
  }

  /**
   * Get translations from the CollageEditor namespace.
   *
   * @param {string} key
   * @param {Object} placeholders
   * @returns {string} UI text
   */
  CollageEditor.t = function (key, placeholders) {
    return H5PEditor.t('H5PEditor.Collage', key, placeholders);
  };

  /**
   * Look for field with given name in given collection.
   *
   * @private
   * @param {string} name of field
   * @param {Array} fields collection to look in
   * @returns {Object} field object
   */
  var findField = function (name, fields) {
    for (var i = 0; i < fields.length; i++) {
      if (fields[i].name === name) {
        return fields[i];
      }
    }
  };

  /**
   * New file upload.
   *
   * @private
   * @param {function} change
   * @param {function} done
   */
  var fileUpload = function (change, done) {
    getIframe(function (iframe) {
      onChange(iframe, change);
      onResponse(iframe, done);
      iframe.$file.click();
    });
  };

  var iframes = [];

  /**
   * Find available iframe for uploading.
   *
   * @private
   * @param {function} found
   */
  var getIframe = function (found) {
    // Find iframe
    for (var i = 0; i < iframes.length; i++) {
      var iframe = iframes[i];
      if (!iframe.inUse) {
        iframe.$element.unbind('load');
        iframe.$file.unbind('change');
        found(iframe);
        return;
      }
    }

    newIframe(found);
  };

  /**
   * Create new iframe for uploading.
   *
   * @private
   * @param {function} done
   */
  var newIframe = function (done) {
    var $element = $('<iframe/>', {
      css: {
        position: 'absolute',
        width: '1px',
        height: '1px',
        top: '-1px',
        border: 0,
        overflow: 'hidden'
      },
      one: {
        load: function () {
          // Create upload form
          var iframe = {
            inUse: false,
            $element: $(this),
            $form: $('<form/>', {
              method: 'post',
              enctype: 'multipart/form-data',
              action: H5PEditor.getAjaxUrl('files')
            })
          };

          var field = {
            name: 'collageClip',
            type: 'image'
          };

          // Determine allowed file mimes
          var mimes;
          if (field.mimes) {
            mimes = field.mimes.join(',');
          }
          else if (field.type === 'image') {
            mimes = 'image/jpeg,image/png,image/gif';
          }

          // Create input fields
          iframe.$file = $('<input/>', {
            type: 'file',
            name: 'file',
            accept: mimes,
            appendTo: iframe.$form
          });
          $('<input/>', {
            type: 'hidden',
            name: 'field',
            value: JSON.stringify(field),
            appendTo: iframe.$form
          });
          $('<input/>', {
            type: 'hidden',
            name: 'contentId',
            value: contentId ? contentId : 0,
            appendTo: iframe.$form
          });
          if (H5PEditor.uploadToken) {
            $('<input/>', {
              type: 'hidden',
              name: 'token',
              value: H5PEditor.uploadToken,
              appendTo: iframe.$form
            });
          }

          var $body = iframe.$element.contents().find('body');
          iframe.$form.appendTo($body);

          iframes.push(iframe);
          if (done) {
            done(iframe);
          }
        }
      },
      appendTo: 'body'
    });
  };

  /**
   * Handle iframe loaded events.
   *
   * @private
   * @param {HTMLElementObject} iframe
   * @param {function} done
   */
  var onResponse = function (iframe, done) {
    iframe.$element.on('load', function () {
      var $body = iframe.$element.contents().find('body');
      var response = $body.text();
      removeIframe(iframe);

      // Try to parse repsonse
      if (response) {
        try {
          var result = JSON.parse(response);
          if (result.error !== undefined) {
            throw(result.error);
          }

          // Return response
          done(null, result);
          return;
        }
        catch (err) {
          done(err);
        }
      }
    });
  };

  /**
   * Remove iframe after uploading.
   *
   * @private
   * @param {HTMLElementObject} iframe
   */
  var removeIframe = function (iframe) {
    iframe.$element.remove();
    for (var i = 0; i < iframes.length; i++) {
      if (iframes[i] === iframe)  {
        iframes.splice(i, 1);
        break;
      }
    }
    if (iframes.length === 0) {
      // Always keep an iframe ready
      newIframe();
    }
  };

  /**
   * Handle file selecting.
   *
   * @private
   * @param {HTMLElementObject} iframe
   * @param {function} change
   */
  var onChange = function (iframe, change) {
    iframe.$file.on('change', function () {
      iframe.inUse = true;
      change();
      iframe.$form.submit();
    });
  };

  // Init upload
  newIframe();

  return CollageEditor;
})(H5P.jQuery, H5PEditor.contentId);

// Register widget
H5PEditor.widgets.collage = H5PEditor.Collage;

// Add strings for l10n
H5PEditor.language['H5PEditor.Collage'] = {
  libraryStrings: {
    confirmReset: 'Are you sure you wish to change the layout? This will reset the preview.',
    sameAsSpacing: 'Same as spacing',
    noFrame: 'No frame',
    uploadError: 'Unable to upload image. The file is probably to large.',
    zoomIn: 'Zoom In',
    zoomOut: 'Zoom Out',
    noMoreZoom: 'No more zoom',
    addImage: 'Add Image',
    changeImage: 'Change Image'
  }
};
