$(function () {
    'use strict';
    $('#fileupload').addClass('fileupload-processing');

    var fileCount = 0,
        fileUploadedCount = 0,
        redirectClosure = function () {
            if (fileUploadedCount >= fileCount) {
                $.ajaxSetup({async: true});
                window.location.href = jQuery('#redirect_url').val();
            }
        };

    $('#fileupload').fileupload({
        url: dataURL,
        done: function (e, data) {

            if (fileCount <= 0) {
                fileCount = $('#filesTable tr').length;
            }

            $.ajaxSetup({async: false});
            $.each(data.result, function (index, file) {
                if (file.hasOwnProperty('error') && file.error.length > 0) {
                    fileUploadedCount += 1;
                    alert(file.name + ': ' + file.error);
                    redirectClosure();
                } else {
                    $.post(replaceAction,
                        {
                            mapId: displayMapId,
                            fileName: file.name
                        },
                        function (d) {
                            fileUploadedCount += 1;
                            redirectClosure();
                        });
                }
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
$(document).ready(function () {
    $("#maincb").click(function () {
        if ($('#maincb').attr('checked')) {
            $('.check_box:enabled').attr('checked', true);
        } else {
            $('.check_box:enabled').attr('checked', false);
        }
    });
});
