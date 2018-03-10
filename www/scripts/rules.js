$(document).ready(function() {
    var wordNodes           = eval('(' + $("#availableNodesText").text() + ')'),
        wordNodesId         = eval('(' + $("#availableNodesId").text() + ')'),
        wordCounters        = eval('(' + $("#availableCountersText").text() + ')'),
        wordCountersId      = eval('(' + $("#availableCountersId").text() + ')'),
        wordConditions      = eval('(' + $("#availableConditionsText").text() + ')'),
        wordConditionsId    = eval('(' + $("#availableConditionsId").text() + ')'),
        wordSteps           = eval('(' + $("#availableStepsText").text() + ')'),
        wordStepsId         = eval('(' + $("#availableStepsId").text() + ')'),
        area                = $("#text"),
        areaCode            = $("#code"),
        view                = $('.changeCodeToText');

    if (area.length && areaCode.length){
        var key,
            word;

        area.keyup(function(){
            changeTextInTextarea(this);
            convertToCode(this);
            resetCheck();
        });

        areaCode.keyup(function(){
            changeTextInTextarea(this);
            convertToWord(this);
            resetCheck();
        });

        changeTextInTextarea(areaCode);
        convertToWord(areaCode);
    }

    if (view.length){
        view.each(function(){
            var text = jQuery(this).html();

            for(key in wordNodesId){
                word = wordNodesId[key];
                text = replaceValue(text, word, '<span class="label label-success">' + wordNodes[key] + '</span>');
            }

            for(key in wordCountersId){
                word = wordCountersId[key];
                text = replaceValue(text, word, '<span class="label label-info">' + wordCounters[key] + '</span>');
            }

            for(key in wordConditionsId){
                word = wordConditionsId[key];
                text = replaceValue(text, word, '<span class="label label-important">' + wordConditions[key] + '</span>');
            }

            for(key in wordStepsId){
                word = wordStepsId[key];
                text = replaceValue(text, word, '<span class="label label-warning">' + wordSteps[key] + '</span>');
            }

            $(this).html(text);
        });
    }

    function changeTextInTextarea(obj){
        var text = $(obj).val();

        for(key in wordNodes){
            word = wordNodes[key];
            text = replaceValue(text, word, '<span class="label label-success">'+word+'</span>');
        }

        for(key in wordCounters){
            word = wordCounters[key];
            text = replaceValue(text, word, '<span class="label label-info">'+word+'</span>');
        }

        for(key in wordConditions){
            word = wordConditions[key];
            text = replaceValue(text, word, '<span class="label label-important">'+word+'</span>');
        }

        for(key in wordSteps){
            word = wordSteps[key];
            text = replaceValue(text, word, '<span class="label label-warning">'+word+'</span>');
        }

        for(key in wordNodesId){
            word = wordNodesId[key];
            text = replaceValue(text, word, '<span class="label label-success">'+wordNodes[key]+'</span>');
        }

        for(key in wordCountersId){
            word = wordCountersId[key];
            text = replaceValue(text, word, '<span class="label label-info">'+wordCounters[key]+'</span>');
        }

        for(key in wordConditionsId){
            word = wordConditionsId[key];
            text = replaceValue(text, word, '<span class="label label-important">'+wordConditions[key]+'</span>');
        }

        for(key in wordStepsId){
            word = wordStepsId[key];
            text = replaceValue(text, word, '<span class="label label-warning">'+wordSteps[key]+'</span>');
        }

        $("#processed-rule").html(text);
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

        for(key in wordConditions){
            word = wordConditions[key];
            text = replaceValue(text, word, wordConditionsId[key]);
        }

        for(key in wordSteps){
            word = wordSteps[key];
            text = replaceValue(text, word, wordStepsId[key]);
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

        for(key in wordConditionsId){
            word = wordConditionsId[key];
            text = replaceValue(text, word, wordConditions[key]);
        }

        for(key in wordStepsId){
            word = wordStepsId[key];
            text = replaceValue(text, word, wordSteps[key]);
        }

        area.val(text);
    }
});

function replaceValue(text, value, replace){
    if (typeof text === 'undefined' || text === '') return '';

    var indexOf = text.indexOf(value),
        subtext;

    if (indexOf !== -1){
        text = text.replace(value, replace);
        subtext = text.substr(indexOf + replace.length, text.length);
        text = text.substr(0, indexOf + replace.length);
        text += replaceValue(subtext, value, replace);
    }
    return text;
}