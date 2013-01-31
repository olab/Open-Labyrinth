jQuery(document).ready(function() {
    var wordNodes = eval('(' + jQuery("#availableNodesText").text() + ')');
    var wordNodesId = eval('(' + jQuery("#availableNodesId").text() + ')');
    var wordCounters = eval('(' + jQuery("#availableCountersText").text() + ')');
    var wordCountersId = eval('(' + jQuery("#availableCountersId").text() + ')');

    var area = jQuery("#text");
    var areaCode = jQuery("#code");

    if (area.length & areaCode.length){
        var key;
        var word;
        var re;
        area.keyup(function(){
            changeTextInTextarea(this);
            convertToCode(this);
        });

        areaCode.keyup(function(){
            changeTextInTextarea(this);
            convertToWord(this);
        });

        changeTextInTextarea(areaCode);
        convertToWord(areaCode);
    }

    var view = jQuery('.changeCodeToText');

    if (view.length){
        view.each(function(){
            var text = jQuery(this).html();

            for(key in wordNodesId){
                word = wordNodesId[key];
                text = replaceValue(text, word, '<span class="label label-success">'+wordNodes[key]+'</span>');
            }

            for(key in wordCountersId){
                word = wordCountersId[key];
                text = replaceValue(text, word, '<span class="label label-info">'+wordCounters[key]+'</span>');
            }

            jQuery(this).html(text);
        });
    }

    function changeTextInTextarea(obj){
        var text = jQuery(obj).val();
        for(key in wordNodes){
            word = wordNodes[key];
            text = replaceValue(text, word, '<span class="label label-success">'+word+'</span>');
        }

        for(key in wordCounters){
            word = wordCounters[key];
            text = replaceValue(text, word, '<span class="label label-info">'+word+'</span>');
        }

        for(key in wordNodesId){
            word = wordNodesId[key];
            text = replaceValue(text, word, '<span class="label label-success">'+wordNodes[key]+'</span>');
        }

        for(key in wordCountersId){
            word = wordCountersId[key];
            text = replaceValue(text, word, '<span class="label label-info">'+wordCounters[key]+'</span>');
        }

        jQuery("#processed-rule").html(text);
    }

    function convertToCode(obj){
        var text = jQuery(obj).val();

        for(key in wordNodes){
            word = wordNodes[key];
            text = replaceValue(text, word, wordNodesId[key]);
        }

        for(key in wordCounters){
            word = wordCounters[key];
            text = replaceValue(text, word, wordCountersId[key]);
        }

        areaCode.val(text);
    }

    function convertToWord(obj){
        var text = jQuery(obj).val();

        for(key in wordNodesId){
            word = wordNodesId[key];
            text = replaceValue(text, word, wordNodes[key]);
        }

        for(key in wordCountersId){
            word = wordCountersId[key];
            text = replaceValue(text, word, wordCounters[key]);
        }

        area.val(text);
    }
});

function replaceValue(text, value, replace){
    var indexOf = text.indexOf(value);
    var subtext;
    if (indexOf !== -1){
        text = text.replace(value, replace);
        subtext = text.substr(indexOf + replace.length, text.length);
        text = text.substr(0, indexOf + replace.length);
        text += replaceValue(subtext, value, replace);
    }
    return text;
}