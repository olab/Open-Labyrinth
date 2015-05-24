var baseURL = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port: '') + '/';

function tinyMceInit(selector, readOnly){
    tinymce.init({
        selector: selector,
        theme: "modern",
        content_css: baseURL + 'scripts/tinymce/js/tinymce/plugins/rdface/css/rdface.css,' + baseURL + 'scripts/tinymce/js/tinymce/plugins/rdface/schema_creator/schema_colors.css',
        entity_encoding: "raw",
        contextmenu: "link image inserttable | cell row column",
        closed: /^(br|hr|input|meta|img|link|param|area|source)$/,
        valid_elements : "+*[*]",
        plugins: ["compat3x",
            "advlist autolink lists link image charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "emoticons template paste textcolor layer advtextcolor rdface imgmap"
        ],
        toolbar1: "insertfile undo redo | styleselect | bold italic | fontselect fontsizeselect | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
        toolbar2: "link image imgmap | print preview media | forecolor backcolor emoticons ltr rtl layer restoredraft | rdfaceMain rdfaceRun",
        image_advtab: true,
        templates: [],
        convert_urls: false,
        readonly: readOnly
    });
}