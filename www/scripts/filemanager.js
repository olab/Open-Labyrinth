
$(function () {
    'use strict';
    $('#fileupload').addClass('fileupload-processing');
    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: dataURL,
        done: function(e, data) {
            $.each(data.files, function (index, file) {
                $.ajaxSetup({async: false});
                $.post(replaceAction,
                    {
                        mapId: displayMapId,
                        fileName: file.name
                    },
                    function(responseData) {
                        window.location.href = jQuery('#redirect_url').val();
                    });
                $.ajaxSetup({async: true});
            });

            //window.location.href = jQuery('#redirect_url').val();
        }
    });

    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );



});
