$(document).ready(function(){
    //------------------Case Wizard--------------------//
    var wizard_button = $('.wizard_body .wizard_button');
    if (wizard_button.length){
        wizard_button.click(function() {
            wizard_button.removeClass('selected');
            $(this).addClass('selected');
        });
    }

    $("#step1_w_button").click(function() {
        var id = $(".wizard_button.selected").attr("id");
        $("#labyrinthType").val(id);
        $("#step1_form").submit();
    });

    $("#step2_w_button").click(function() {
        $("#step2_form").submit();
    });

    $("textarea, input[type=text]:not('.not-autocomplete')")
        // don't navigate away from the field on tab when selecting an item
        .bind( "keydown", function( event ) {
            if ( event.keyCode === jQuery.ui.keyCode.TAB &&
                $( this ).data( "autocomplete" ).menu.active ) {
                event.preventDefault();
            }
        })
        .autocomplete({
            source: function( request, response ) {
                var term = extractTerm( this );
                if (term){
                    jQuery.getJSON( "/dictionaryManager/getjson/", {
                        term: term
                    }, response );
                }
            },
            search: function() {
                // custom minLength
                var term = extractTerm( this );
                if ( term.length < 2 ) {
                    return false;
                }
            },
            focus: function() {
                // prevent value inserted on focus
                return false;
            },
            select: function( event, ui ) {
                var term = extractTerm( this );
                var caretPos = GetCaretPosition(this);
                insertAtCursor(this, ui.item.value, term.length);
                setCaretPosition(this, caretPos + (ui.item.value.length - term.length));
                return false;
            }
        });

    function extractTerm(el) {
        if (!el.value){
            el = el.element[0];
        }
        var caretPos = GetCaretPosition(el);
        var value = el.value;
        var char = value.charAt(caretPos);
        if ((value.length == caretPos) || (char == ' ')){
            var word = ReturnWord(el.value, caretPos);
            if (word != null) {
                return word;
            }
        }
        return false;
    }

    function setCaretPosition(el, caretPos) {
        if(el.createTextRange) {
            var range = el.createTextRange();
            range.move('character', caretPos);
            range.select();
        }
        else {
            if(el.selectionStart) {
                el.focus();
                el.setSelectionRange(caretPos, caretPos);
            }
            else
                el.focus();
        }
    }

    function GetCaretPosition(ctrl) {
        var CaretPos = 0;   // IE Support
        if (document.selection) {
            ctrl.focus();
            var Sel = document.selection.createRange();
            Sel.moveStart('character', -ctrl.value.length);
            CaretPos = Sel.text.length;
        }
        // Firefox support
        else if (ctrl.selectionStart || ctrl.selectionStart == '0')
            CaretPos = ctrl.selectionStart;
        return (CaretPos);
    }

    function ReturnWord(text, caretPos) {
        var preText = text.substring(0, caretPos);
        if (preText.indexOf(" ") > 0) {
            var words = preText.split(" ");
            return words[words.length - 1]; //return last word
        }
        else {
            return preText;
        }
    }

    function insertAtCursor(myField, myValue, deleteChars) {
        //IE support
        if (document.selection) {
            myField.focus();
            var sel = document.selection.createRange();
            sel.text = myValue;
        }
        //MOZILLA and others
        else if (myField.selectionStart || myField.selectionStart == '0') {
            var startPos = myField.selectionStart;
            var endPos = myField.selectionEnd;
            myField.value = myField.value.substring(0, startPos - deleteChars)
                + myValue
                + myField.value.substring(endPos, myField.value.length);
        } else {
            myField.value += myValue;
        }
    }

    $("#dialog-confirm").dialog({
        autoOpen: false,
        draggable: false,
        resizable: false,
        modal: true,
        buttons: {
            "I agree": function() {
                $(this).dialog( "close" );
                $('#upload-form').submit();
            },
            Cancel: function() {
                $(this).dialog( "close" );
            }
        }
    });

    $("#opener").click(function() {
        $("#dialog-confirm").dialog( "open" );
        return false;
    });

    $("#tabs").tabs();

	// Tooltip
	$('a[rel=tooltip]').tooltip();

	// Popovers
	$('[rel=popover]').popover();

	// Datepicker
	$(".datepicker").datepicker();
});