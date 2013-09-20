
$(function () {
    'use strict';
    $('#fileupload').addClass('fileupload-processing');

    var fileCount = 0,
        fileUploadedCount = 0;

    $('#fileupload').fileupload({
        url: dataURL,
        done: function(e, data) {
            if(fileCount <= 0) {
                fileCount = $('#filesTable tr').length;
            }

            $.ajaxSetup({async: false});
            $.each(data.files, function (index, file) {
                $.post(replaceAction,
                       {
                           mapId: displayMapId,
                           fileName: file.name
                       },
                       function(d) {
                           fileUploadedCount += 1;
                           if(fileUploadedCount >= fileCount) {
                               $.ajaxSetup({async: true});
                               window.location.href = jQuery('#redirect_url').val();
                           }
                       });
            });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);

            $('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
        }
    });
});
