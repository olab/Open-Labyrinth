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
        currentSearchedMatchCount = 0,
        currentSearchedIndexOf = -1,
        scrollTop = 0,

        showHelpNext = true,
        leaveLink = null,
        unSaveData = false;

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
            $('<div id="temp_div" class="hide"></div>').appendTo($('body'));
            
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
            $findInput.bind('focus' , function() {focusOnFindInput()});
        }

        $('body').keyup(function(e) {BodyKeyupEvent(e);});
        
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
        var parent = $('.selected-text').parents('.editable-text');
        if ($currentSelected.length > 0){
            $.each($currentSelected, function(index, object) {
                $(object).after(replaceValue);
            });
            unSaveData = true;
        }

        $currentSelected.remove();
        EditableTextKeyUp(parent);
        if (!NextButtonClick(null)){
            currentSearchedElementIndex = -1;
            NextButtonClick(null);
        }
    } 
    
    var ReplaceAllButton = function(button) {
        if($replaceInput == null) return;
        
        var replaceValue = $replaceInput.val(),
            $currentSelected = null,
            parent;
        if(replaceValue == null || replaceValue.length <= 0) return;
        
        currentSearchedElementIndex = -1;
        while(NextButtonClick(null)) {
            $currentSelected = $('.selected-text');
            parent = $('.selected-text').parents('.editable-text');
            if ($currentSelected.length > 0){
                $.each($currentSelected, function(index, object) {
                    $(object).after(replaceValue);
                });
                unSaveData = true;
            }

            $currentSelected.remove();
            EditableTextKeyUp(parent);
        }
    }
    
    var NextButtonClick = function(button) {
        if (showHelpNext){
            $('#tipsForNextButton').tooltip('hide');
            showHelpNext = false;
        }
        var searchValue = $findInput.val(),
            value = null,
            textarea = null,
            regexp = /(^|>)[^><]+?(?=<|$)/gi,
            match = null,
            startSearch = 0,
            matchCount = 0,
            indexOf = -1,
            result = false;

        if(searchValue == null || searchValue.length <= 0 || currentSearchedElementIndex >= $editors.length) {
            RemoveAllSelections();
            var countEditable = $('.editable-text').length;
            currentSearchedElementIndex = countEditable;
            $($findInput).css('background', '#ff6666');
            return false;
        }

        $.each($editors, function(index, editor) {
            if(index < currentSearchedElementIndex) return;

            textarea = $(editor).next();
            value = textarea.val();

            if(value != null && value.length > 0) {
                startSearch = 0;
                matchCount = 0;
                while((match = regexp.exec(value.toLowerCase())) != null) {
                    if (currentSearchedElementIndex == index) {
                        if (matchCount >= currentSearchedMatchCount){
                            if (matchCount == currentSearchedMatchCount){
                                startSearch = currentSearchedIndexOf + 1;
                            } else {
                                startSearch = 0;
                            }
                        } else {
                            matchCount++;
                            continue;
                        }
                    }

                    indexOf = match[0].indexOf(searchValue.toLowerCase(), startSearch);

                    if(indexOf >= 0) {
                        RemoveAllSelections();

                        value = value.substring(0, match.index + indexOf) +
                                '<span class="selected-text">' + 
                                value.substring(match.index + indexOf, match.index + indexOf + searchValue.length) +
                                '</span>' + 
                                value.substring(match.index + indexOf + searchValue.length, value.length);

                        $(editor).html(value);

                        openDivAndScroll(editor);

                        currentSearchedElementIndex = index;
                        currentSearchedIndexOf = indexOf;
                        currentSearchedMatchCount = matchCount;

                        result = true;

                        return false;
                    }

                    matchCount++;
                }
            }
        });
        if (!result){
            RemoveAllSelections();
            var countEditable = $('.editable-text').length;
            currentSearchedElementIndex = countEditable;
        }
        $($findInput).css('background', (!result) ? '#ff6666' : '#FFFFFF');

        return result;
    }
    
    var PrevButtonClick = function(button) {
        var searchValue = $findInput.val(),
            reverseSearchValue = null,
            value = null,
            regexp = /(^|>)[^><]+?(?=<|$)/gi,
            match = null,
            indexOf = -1,
            i = 0,
            textarea = null,
            result = false;
            
        if(searchValue == null || searchValue.length <= 0 || currentSearchedElementIndex <= 0) {
            RemoveAllSelections();
            currentSearchedElementIndex = -1;
            $($findInput).css('background', '#ff6666');
            return;
        }
        
        searchValue        = searchValue.toLowerCase();
        reverseSearchValue = ReverseString(searchValue);
        
        i = currentSearchedElementIndex + 1;
        for(;i--;) {
            if(i > currentSearchedElementIndex) continue;

            textarea = $($editors.get(i)).next();
            value = textarea.val();

            if(value != null && value.length > 0) {
                while((match = regexp.exec(value.toLowerCase())) != null) {
                    indexOf = (currentSearchedElementIndex == i) ? 
                               GetNextMaxIndex(match[0], searchValue, currentSearchedIndexOf) : 
                               GetMaxIndex(match[0], reverseSearchValue);
                    
                    if(indexOf >= 0) {
                        RemoveAllSelections();

                        value = value.substring(0, match.index + indexOf) + 
                                '<span class="selected-text">' + 
                                value.substring(match.index + indexOf, match.index + indexOf + searchValue.length) + 
                                '</span>' + 
                                value.substring(match.index + indexOf + searchValue.length, value.length);

                        $($editors.get(i)).html(value);

                        openDivAndScroll($editors.get(i));
                        
                        currentSearchedElementIndex = i;
                        currentSearchedIndexOf = indexOf;

                        result = true;

                        break;
                    }
                }
            }

            if (result) break;
        }

        if (!result){
            RemoveAllSelections();
            currentSearchedElementIndex = -1;
        }
        $($findInput).css('background', (!result) ? '#ff6666' : '#FFFFFF');
    }
    
    var EditableTextKeyUp = function(element) {
        var textareadId = $(element).attr('textareadId');
        $('#temp_div').html($(element).html());

        var $selectedText = $('#temp_div .selected-text');
        $.each($selectedText, function(index, object){
            $(object).after($(object).html());
        });

        $selectedText.remove();

        $(textareadId).val($('#temp_div').html());
        unSaveData = true;
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

    var openDivAndScroll = function(editor){
        if (currentSearchedElementIndex != -1){
            $($editors.get(currentSearchedElementIndex)).css({'height':'50px', 'width':'220px'});
        }

        $(editor).css({'height':'auto', 'width':'auto', 'min-height':'50px'});

        scrollTop = $(".selected-text").offset().top - (screen.height / 2);
        $('html, body').stop().animate({
            scrollTop: scrollTop
        }, 1000);
    }

    var BodyKeyupEvent = function(e){
        if (e.keyCode == 27){
            clearAllSelections();
        }
    }

    var focusOnFindInput = function() {
        if (showHelpNext){
            $('#tipsForNextButton').tooltip('show');
        }
        clearAllSelections();
    }

    var clearAllSelections = function(){
        RemoveAllSelections();
        currentSearchedElementIndex = 0;
        currentSearchedMatchCount = 0;
        currentSearchedIndexOf = 0;
        $findInput.css('background', '#FFFFFF');
    }

    $('.breadcrumb a').click(function() { return leaveBox($(this)); });
    $('.navbar-inner .dropdown-menu a:not(.dropdown-toggle)').click(function() { return leaveBox($(this)); });
    $('.navbar-inner .nav a:not(.dropdown-toggle)').click(function() { return leaveBox($(this)); });
    $('.nav-list a').click(function() { return leaveBox($(this)); });

    function leaveBox($object) {
        if(unSaveData) {
            if($object != null) {
                leaveLink = $object.attr('href');
            }
            $('#leaveBox').modal();
            return false;
        }
        return true;
    }

    $('#uploadUnsaved').click(function() {
        $('#grid_from').submit();
    });

    $('#leave').click(function() {
        if(leaveLink != null) {
            $(location).attr('href', leaveLink);
        }
    });
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

