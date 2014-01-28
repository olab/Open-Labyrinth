/**
 * Created with JetBrains PhpStorm.
 * User: larjohns
 * Date: 11/11/2012
 * Time: 1:45 ??
 * To change this template use File | Settings | File Templates.
 */
if(typeof tinymce != 'undefined' && !tinymce.initialized){
tinyMCE.init({
    mode : "textareas",
    theme : "simple",
    editor_selector : "wysiwyg"
});}
$(document).ready(function () {

    loadEditor($(".textarea"));

    function loadEditor (elem){
        try{
        elem.tinymce(
            {

                theme: "modern",
                content_css: "<?php echo URL::base(); ?>scripts/tinymce/js/tinymce/plugins/rdface/css/rdface.css,<?php echo URL::base(); ?>scripts/tinymce/js/tinymce/plugins/rdface/schema_creator/schema_colors.css",
                entity_encoding: "raw",
                contextmenu: "link image inserttable | cell row column rdfaceMain",
                closed: /^(br|hr|input|meta|img|link|param|area|source)$/,
                valid_elements : "+*[*]",
                plugins: ["compat3x",
                    "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                    "searchreplace wordcount visualblocks visualchars code fullscreen",
                    "insertdatetime media nonbreaking save table contextmenu directionality",
                    "emoticons template paste textcolor layer advtextcolor rdface imgmap"
                ],
                toolbar1: "insertfile undo redo | styleselect | bold italic | fontselect fontsizeselect | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
                toolbar2: " link image imgmap|print preview media | forecolor backcolor emoticons ltr rtl layer restoredraft | rdfaceMain",
                image_advtab: true,
                templates: [

                ]

            }
        );
        }
        catch(ex) {}
    }




    $("body").on("click", ".remove", function (event) {
        $(event.target).closest("div").fadeOut(function(){
                $(event.target).closest("div").parent("div").remove();
            }

        );

    });

    $(".add").unbind('click').click(function (event) {
        var metadataname = $(event.target).closest(".control-group").attr("id");

        $(':focus').blur();
        $(event.target).children("i").toggleClass("icon-spinner icon-spin icon-plus-sign");
        var button = event.target;

        $.getJSON('../../metadata/api/ui?metadata=' + metadataname, function (data) {

            var div = document.createElement('div');
            $(div).append(data);
            $(div).addClass("input-append");
            $(div).addClass("span9");
            var div2 = document.createElement('div');
            $(div2).append(div);
            var a = document.createElement('a');
            $(a).addClass('remove');
            $(a).addClass('btn');
            $(a).addClass('btn-danger');
            $(a).append("<i class='icon-remove'></i>");
            $(a).append("Remove");

            $(div).append(a);
            $(event.target).closest(".control-group").children(".controls").append(div2);

            $(div).children("input").focus();
            $(button).children("i").toggleClass("icon-spinner icon-spin icon-plus-sign");
            $(div).children(".textarea").each(function(){
                    loadEditor($(this));

                }
            );
            $(div).children(".date").datepicker(
                {
                    format:"yyyy-mm-dd"
                }
            );

        });


    });



    $("form").submit(function (event) {
        var inlines = $(".metadata-container").find(".inlineobjectrecord");

        for(var i=0;i<inlines.length;i++){
            var inline_objects = $(inlines[i]).find(".inline-object").
                not($(inlines[i]).find(".inlineobjectrecord .inline-object"));

            for(var j=0;j<inline_objects.length;j++){

                var value = JSON.stringify(serialize_recursive(inline_objects[j]));
                var input = document.createElement('input');
                var suffix = "";
                if($(inline_objects[j]).hasClass("multi"))
                    suffix = "[]";
                $(input).attr("name", $(inline_objects[j]).attr("id") +suffix);
                $(input).attr("value", value);
                $(input).attr("type", "hidden");
                $(input).attr("class", "result");
                $(inlines[i]).append(input);


            }
            var value = JSON.stringify(serialize_recursive(inlines[i]));
            var input = document.createElement('input');
            var suffix = "";
            if($(inlines[i]).hasClass("multi"))
                suffix = "[]";
            $(input).attr("name", $(inlines[i]).attr("id") +suffix);
            $(input).attr("value", value);
            $(input).attr("type", "hidden");
            $(input).attr("class", "result");
            $(inlines[i]).append(input);

            $(inlines[i]).find(":input").not(".result").attr("disabled","disabled");

            $(inlines[i]).addClass("processed");
        }
        return true;


    });

    var serialize_recursive = function (element) {
        var inlines = $(element).find(".inlineobjectrecord").not(".processed");
        var value = {};
        if (inlines.length > 0) {
            for (var i = 0; i < inlines.length; i++) {
                var inline_objects = $(inlines[i]).find(".inline-object");
                var inline_values;
                if(inline_objects.length>1){
                    inline_values = [];
                    for(var j=0;j<inline_objects.length;j++){
                        inline_values.push(serialize_recursive(inline_objects[j]));
                    }
                }
                else{
                    inline_values = serialize_recursive(inline_objects[0]);
                }

                value[$(inlines[i]).attr("id")]=inline_values;

            }
        }

        var inputs = $(element).find(":input").
            not($(element).find(".inlineobjectrecord :input")).
            serializeArray();
        var inputsObj = {};
        for (i in inputs) {
            var name = inputs[i].name.replace("[]","");
            if (typeof inputsObj[name] != 'undefined') {
                inputsObj[name] = $.merge($.isArray(inputsObj[name])?inputsObj[name]:[inputsObj[name]],[inputs[i].value]);
            }
            else
                inputsObj[name] = inputs[i].value
        }

        value = $.extend(value,inputsObj);
        return value;

    };

initLegacyProperties();

    function initLegacyProperties(){
        if(typeof properties=="undefined")return;
        $("#legacy-property option").remove();
        $(properties[$("#legacy-class").val()]).each(function() {
            $("#legacy-property").append('<option value="' + this + '">' + this + '</option>');
        });
    }

$("#legacy-class").change(
    function(){
     initLegacyProperties();
    });



});