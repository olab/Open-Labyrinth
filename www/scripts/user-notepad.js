var userNotepadText = $.trim($('#user_notepad').val());
tinymce.init({
    selector: "#user_notepad",
    height: "200",
    theme: "modern",
    entity_encoding: "raw",
    contextmenu: "link image inserttable | cell row column",
    menubar: false,
    closed: /^(br|hr|input|meta|img|link|param|area|source)$/,
    valid_elements: "+*[*]",
    plugins: ["compat3x",
        "advlist autolink lists link image charmap hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars code fullscreen",
        "insertdatetime nonbreaking save table contextmenu directionality",
        "template paste textcolor layer advtextcolor rdface"
    ],
    toolbar1: "undo redo | styleselect | bold italic | fontselect fontsizeselect | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
    image_advtab: true,
    templates: [],
    setup: function (editor) {
        editor.on('init', function () {
            valToTextarea();
        });
    }
});

setInterval(function () {
    saveUserNotepadChanges();
}, 2000);

function saveUserNotepadChanges() {
    var currentText = $.trim($('#user_notepad').val());

    if (currentText == userNotepadText) {
        return;
    }

    $.post(urlBase + 'renderlabyrinth/saveUserNote', {'text': currentText})
        .done(function () {
            userNotepadText = currentText;
        })
        .fail(function () {
            //
        });
}