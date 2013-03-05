/**
 * Created with JetBrains PhpStorm.
 * User: larjohns
 * Date: 11/11/2012
 * Time: 1:45 ??
 * To change this template use File | Settings | File Templates.
 */
if(!tinymce.initialized){
tinyMCE.init({
    mode : "textareas",
    theme : "simple",
    editor_selector : "wysiwyg"
});}
$(document).ready(function () {


    $('.textarea').tinymce(
        {
            mode : "textareas",
            theme : "advanced",
            plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
            theme_advanced_buttons3_add : "tablecontrols",
            skin: 'bootstrap',
            entity_encoding : "raw"

        }
    );


    $("body").on("click", ".remove", function (event) {
        $(event.target).parent().remove();
    });

    $(".add").unbind('click').click(function (event) {
        var metadataname = $(event.target).parent().attr("id");

        var add_link = event.target;


        $.getJSON('../../metadata/api/ui?metadata=' + metadataname, function (data) {

            var div = document.createElement('div');
            $(div).append(data);

            var a = document.createElement('a');
            $(a).addClass('remove');
            $(a).addClass('btn');
            $(a).addClass('btn-danger');
            $(a).append("remove");

            $(div).append(a);

            $(add_link).before(div);

        });

    });

    $.fn.serializeObject = function()
    {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    $("form").submit(function (event) {
        var inlines = $(".metadata-container").find(".inlineobjectrecord");

        for(var i=0;i<inlines.length;i++){
            var inline_objects = $(inlines[i]).find(".inline-object").
                not($(inlines[i]).find(".inlineobjectrecord .inline-object"));

            for(var j=0;j<inline_objects.length;j++){

                var value = JSON.stringify(serialize_recursive(inline_objects[j]));
                var input = document.createElement('input');
                var suffix = "";
                if($(inlines[i]).hasClass("multi"))
                    suffix = "[]";
                $(input).attr("name", $(inlines[i]).attr("id") +suffix);
                $(input).attr("value", value);
                $(input).attr("type", "hidden");
                $(input).attr("class", "result");
                $(inlines[i]).append(input);


            }
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
                inputsObj[name] = $.merge([inputsObj[name]],[inputs[i].value]);
            }
            else
                inputsObj[name] = inputs[i].value
        }

        value = $.extend(value,inputsObj);
        return value;

    }

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