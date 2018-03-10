(function ($) {
  H5PEditor.init = function () {
    H5PEditor.$ = H5P.jQuery;
    H5PEditor.basePath = H5PIntegration.editor.libraryUrl;
    H5PEditor.fileIcon = H5PIntegration.editor.fileIcon;
    H5PEditor.ajaxPath = H5PIntegration.editor.ajaxPath;
    H5PEditor.filesPath = H5PIntegration.editor.filesPath;
    H5PEditor.uploadToken = H5PIntegration.editor.uploadToken;

    // Semantics describing what copyright information can be stored for media.
    H5PEditor.copyrightSemantics = H5PIntegration.editor.copyrightSemantics;

    // Required styles and scripts for the editor
    H5PEditor.assets = H5PIntegration.editor.assets;

    // Required for assets
    H5PEditor.baseUrl = '';

    if (H5PIntegration.editor.nodeVersionId !== undefined) {
      H5PEditor.contentId = H5PIntegration.editor.nodeVersionId;
    }

    var h5peditor;
    var $type = $('input[name="action"]');
    var $upload = $('.h5p-upload');
    var $create = $('.h5p-create').hide();
    var $editor = $('.h5p-editor');
    var $library = $('input[name="library"]');
    var $params = $('input[name="parameters"]');
    var library = $library.val();

    $type.change(function () {
      if ($type.filter(':checked').val() === 'upload') {
        $create.hide();
        $upload.show();
      }
      else {
        $upload.hide();
        if (h5peditor === undefined) {
          h5peditor = new ns.Editor(library, $params.val(), $editor[0]);
        }
        $create.show();
      }
    });

    if ($type.filter(':checked').val() === 'upload') {
      $type.change();
    }
    else {
      $type.filter('input[value="create"]').attr('checked', true).change();
    }

    $('#h5p-content-form').submit(function () {
      if (h5peditor !== undefined) {
        var params = h5peditor.getParams();
        if (params !== undefined) {
          $library.val(h5peditor.getLibrary());
          $params.val(JSON.stringify(params));
        }
      }
    });

    // Title label
    var $title = $('#h5p-content-form #title');
    var $label = $title.prev();
    $title.focus(function () {
      $label.addClass('screen-reader-text');
    }).blur(function () {
      if ($title.val() === '') {
        $label.removeClass('screen-reader-text');
      }
    }).focus();

    // Delete confirm
    $('.submitdelete').click(function () {
      return confirm(H5PIntegration.editor.deleteMessage);
    });

  };

  H5PEditor.getAjaxUrl = function (action, parameters) {
    var url = H5PIntegration.editor.ajaxPath + action;

    if (parameters !== undefined) {
      for (var property in parameters) {
        if (parameters.hasOwnProperty(property)) {
          url += (url.indexOf('?') === -1 ? '?' : '&') + property + '=' + parameters[property];
        }
      }
    }

    return url;
  };

  $(document).ready(H5PEditor.init);
})(H5P.jQuery);
