var Replacer = function() {
    var self = this,
        findInputId = null,
        replaceInputId = null,
        
        $findInput = null,
        $replaceInput = null,
        $nextButton = null,
        $prevButton = null,
        $replaceButton = null,
        $replaceAllButton = null,
        $textareas = null,
        $editors = null,
        
        currentSearchedElementIndex = -1,
        currentSearchedIndexOf = -1;
    
    this.Init = function(options) {
        if('findInputId' in options) {
            findInputId = options.findInputId;
            $findInput = $(findInputId);
        }
        
        if('replaceInputId' in options) {
            replaceInputId = options.replaceInputId;
            $replaceInput = $(replaceInputId);
        }
        
        if('searchElements' in options) {
            $textareas = $(options.searchElements);
            $.each($textareas, function(index, obj) {
                $('<div contenteditable="true" textareadId="#' + $(obj).attr('id') + '" class="editable-text">' + $(obj).val() + '</div>').insertBefore($(obj));
                $(obj).hide();
            });
            
            $editors = $('.editable-text');
            $editors.live('keyup', function() {EditableTextKeyUp(this);});
        }
        
        if('nextButtonId' in options) {
            $nextButton = $(options.nextButtonId);
        }
        
        if('prevButtonId' in options) {
            $prevButton = $(options.prevButtonId);
        }
        
        if('replaceButtonId' in options) {
            $replaceButton = $(options.replaceButtonId);
        }
        
        if('replaceAllButtonId' in options) {
            $replaceAllButton = $(options.replaceAllButtonId);
        }
        
        BindEvents();
    }
    
    var BindEvents = function() {
        if($findInput != null) {
            $findInput.keyup(function() {FindKeyupEvent(this);});
        }
        
        if($nextButton != null) {
            $nextButton.click(function() {NextButtonClick(this);});
        }
        
        if($prevButton != null) {
            $prevButton.click(function() {PrevButtonClick(this);});
        }
        
        if($replaceButton != null) {
            $replaceButton.click(function() {ReplaceButtonClick(this);});
        }
        
        if($replaceAllButton != null) {
            $replaceAllButton.click(function() {ReplaceAllButton(this);});
        }
    }
    
    var ReplaceButtonClick = function(button) {
        if($replaceInput == null) return;
        
        var replaceValue = $replaceInput.val();
        if(replaceValue == null || replaceValue.length <= 0) return;
        
        var $currentSelected = $('.selected-text');
        $.each($currentSelected, function(index, object) {
            $(object).after(replaceValue);
        });
        
        $currentSelected.remove();
        NextButtonClick(null);
    } 
    
    var ReplaceAllButton = function(button) {
        if($replaceInput == null) return;
        
        var replaceValue = $replaceInput.val(),
            $currentSelected = null;
        if(replaceValue == null || replaceValue.length <= 0) return;
        
        currentSearchedElementIndex = -1;
        while(NextButtonClick(null)) {
            $currentSelected = $('.selected-text');
            $.each($currentSelected, function(index, object) {
                $(object).after(replaceValue);
            });

            $currentSelected.remove();
        }
    }
    
    var NextButtonClick = function(button) {
        var searchValue = $findInput.val(),
            value = null,
            regexp = /(^|>)[^><]+?(?=<|$)/gi,
            match = null,
            indexOf = -1,
            result = false;
            
        if(searchValue == null || searchValue.length <= 0 || currentSearchedElementIndex >= $editors.length) return false;
        
        RemoveAllSelections();
        
        $.each($editors, function(index, editor) {
            if(index < currentSearchedElementIndex) return;
            
            value = $(editor).html();
            if(value != null && value.length > 0) {
                while((match = regexp.exec(value.toLowerCase())) != null) {
                    indexOf = match[0].indexOf(searchValue.toLowerCase(), (currentSearchedElementIndex == index) ? (currentSearchedIndexOf + 1) : 0);

                    if(indexOf >= 0) {
                        value = value.substring(0, match.index + indexOf) + 
                                '<span class="selected-text">' + 
                                value.substring(match.index + indexOf, match.index + indexOf + searchValue.length) + 
                                '</span>' + 
                                value.substring(match.index + indexOf + searchValue.length, value.length);

                        $(editor).html(value);
                        $(editor).focus();
                        $findInput.focus();
                        
                        currentSearchedElementIndex = index;
                        currentSearchedIndexOf = indexOf;
                        
                        result = true;
                        
                        return false;
                    }
                }
            }
        });
        
        return result;
    }
    
    var PrevButtonClick = function(button) {
        var searchValue = $findInput.val(),
            reverseSearchValue = null,
            value = null,
            regexp = /(^|>)[^><]+?(?=<|$)/gi,
            match = null,
            indexOf = -1,
            i = 0;
            
        if(searchValue == null || searchValue.length <= 0 || currentSearchedElementIndex <= 0) return;
        
        searchValue        = searchValue.toLowerCase();
        reverseSearchValue = ReverseString(searchValue);
        
        RemoveAllSelections();
        
        i = currentSearchedElementIndex + 1;
        for(;i--;) {
            if(i > currentSearchedElementIndex) continue;
            
            value = $($editors.get(i)).html();
            if(value != null && value.length > 0) {
                while((match = regexp.exec(value.toLowerCase())) != null) {
                    indexOf = (currentSearchedElementIndex == i) ? 
                               GetNextMaxIndex(match[0], searchValue, currentSearchedIndexOf) : 
                               GetMaxIndex(match[0], reverseSearchValue);
                    
                    if(indexOf >= 0) {
                        value = value.substring(0, match.index + indexOf) + 
                                '<span class="selected-text">' + 
                                value.substring(match.index + indexOf, match.index + indexOf + searchValue.length) + 
                                '</span>' + 
                                value.substring(match.index + indexOf + searchValue.length, value.length);

                        $($editors.get(i)).html(value);
                        $($editors.get(i)).focus();
                        $findInput.focus();
                        
                        currentSearchedElementIndex = i;
                        currentSearchedIndexOf = indexOf;
                        
                        return;
                    }
                }
            }
        }
    }
    
    var EditableTextKeyUp = function(element) {
        $($(element).attr('textareadId')).val($(element).text());
    }
    
    var FindKeyupEvent = function(findInputObj) {
        var searchValue = $(findInputObj).val(),
            value = null,
            regexp = /(^|>)[^><]+?(?=<|$)/gi,
            match = null,
            indexOf = -1;
        
        RemoveAllSelections();
        
        if(searchValue == null || searchValue.length <= 0) return;
        
        searchValue = searchValue.toLowerCase();

        $.each($editors, function(index, editor) {
            value = $(editor).html();
            if(value != null && value.length > 0) {
                while((match = regexp.exec(value.toLowerCase())) != null) {
                    indexOf = match[0].indexOf(searchValue);
                    if(indexOf >= 0) {
                        value = value.substring(0, match.index + indexOf) + 
                                '<span class="selected-text">' + 
                                value.substring(match.index + indexOf, match.index + indexOf + searchValue.length) + 
                                '</span>' + 
                                value.substring(match.index + indexOf + searchValue.length, value.length);

                        $(editor).html(value);
                        $(editor).focus();
                        $(findInputObj).focus();
                        
                        currentSearchedElementIndex = index;
                        currentSearchedIndexOf = match.index + indexOf;
                        
                        return false;
                    }
                }
            }
        });
    }
    
    var ReverseString = function(s){
        return s.split("").reverse().join("");
    }
    
    var GetNextMaxIndex = function(source, search, startIndex) {
        if(source == null || source.length <= 0 || search == null || search.length <= 0 || startIndex < 0) return -1;
        
        var testIndex = -1,
            oldIndex  = startIndex;
        
        while(testIndex < startIndex) {
            testIndex = source.indexOf(search, testIndex + 1);
            if(testIndex < 0) break;
            
            if(testIndex < startIndex) {
                oldIndex = testIndex;
            }
        }
        
        return (oldIndex < startIndex) ? oldIndex : -1;
    }
    
    var GetMaxIndex = function(source, search) {
        if(source == null || source.length <= 0 || search == null || search.length <= 0) return -1;
        
        var reverseString = ReverseString(source),
            indexOf = reverseString.indexOf(search);
            
        return (indexOf >= 0) ? (source.length - indexOf - search.length) : -1; 
    }
    
    var RemoveAllSelections = function() {
        var $selectedText = $('.selected-text');
        $.each($selectedText, function(index, object){
            $(object).after($(object).html());
        });
        
        $selectedText.remove();
    }
}

var replacer = new Replacer();
replacer.Init({
    findInputId: '#findWhat', 
    replaceInputId: '#replaceWith', 
    searchElements: '.search-textarea', 
    nextButtonId: '.next-btn', 
    prevButtonId: '.previous-btn',
    replaceButtonId: '.replace-btn',
    replaceAllButtonId: '.replace-all-btn'
});