jQuery(document).ready(function(){
    var browserUpdateWarning = new BrowserUpdateWarning();
    var body = $('body');
    browserUpdateWarning.Check();
    
    //------------------Case Wizard--------------------//
    var wizard_button = jQuery('.wizard_body .wizard_button');
    if (wizard_button.length){
        wizard_button.click(function() {
            wizard_button.removeClass('selected');
            jQuery(this).addClass('selected');
        });
    }

    jQuery("#step1_w_button").click(function() {
        var id = jQuery(".wizard_button.selected").attr("id");
        jQuery("#labyrinthType").val(id);
        jQuery("#step1_form").submit();
    });

    jQuery("#step2_w_button").click(function() {
        jQuery("#step2_form").submit();
    });

    jQuery("#dialog-confirm").dialog({
        autoOpen: false,
        draggable: false,
        resizable: false,
        modal: true,
        buttons: {
            "I agree": function() {
                jQuery(this).dialog( "close" );
                jQuery('#uploadBtn').click();
            },
            Cancel: function() {
                jQuery(this).dialog( "close" );
            }
        }
    });

    jQuery("#opener").click(function() {
        $("#dialog-confirm").dialog( "open" );
        return false;
    });

    jQuery("#tabs").tabs();

	// Tooltip
	jQuery('a[rel=tooltip]').tooltip();

	// Popovers
	jQuery('[rel=popover]').popover();

	// Datepicker
	jQuery(".datepicker").datepicker();



    jQuery('input:radio#timing-on').change(function(){
        $('#delta_time_seconds').prop('disabled', false);
        $('#delta_time_minutes').prop('disabled', false);
        $('#reminder_msg').prop('disabled', false);
        $('#reminder_seconds').prop('disabled', false);
        $('#reminder_minutes').prop('disabled', false);
    });
    jQuery('input:radio#timing-off').change(function(){
        $('#delta_time_seconds').prop('disabled', true);
        $('#delta_time_minutes').prop('disabled', true);
        $('#reminder_msg').prop('disabled', true);
        $('#reminder_seconds').prop('disabled', true);
        $('#reminder_minutes').prop('disabled', true);
    });

    jQuery('select[name=security]').change(function(){
        if(this.value == 4) $("#edit_keys").show();
        else $("#edit_keys").hide();
    });

    jQuery('.toggle-all-on').click(function() {
        var id = jQuery(this).attr("id");
        jQuery(".chk_"+id).attr('checked', 'checked');
    });

    jQuery('.toggle-all-off').click(function() {
        var id = jQuery(this).attr("id");
        jQuery(".chk_"+id).removeAttr('checked');
    });

    jQuery('.toggle-reverse').click(function() {
        var id = jQuery(this).attr("id");
        var chk = jQuery(".chk_"+id);
        chk.each(function() {
            var check_value = jQuery(this).attr("checked");
            if (check_value){
                jQuery(this).removeAttr("checked");
            } else {
                jQuery(this).attr("checked", "checked");
            }
        });

    });
    
    function changeClothColor(changedColor) {
        var color = 'FFFFFF';
        var val = changedColor;
        if(val.length > 2) {
            color = val.substr(1, val.length - 1);
        }

        $('#clothcolor').val(color);
        $('#clothcolor').change();
    }
    
    function changeBgColor(changedColor) {
        var color = 'FFFFFF';
        var val = changedColor;
        if(val.length > 2) {
            color = val.substr(1, val.length - 1);
        }
        
        $('#bgcolor').val(color);
        $('#bgcolor').change();
    }

    jQuery('#clothcolor').click(function() {
        $('#clothColorContainer').show();
        $('#clothColorContainer').farbtastic(changeClothColor);
        var val = $(this).val();
        if(val.length > 0) {
            var picker = $.farbtastic('#clothColorContainer');
            picker.setColor('#' + val);
        }
    });

    jQuery('#clothcolor').blur(function() {
        $('#clothColorContainer').hide();
    });

    jQuery('#bgcolor').click(function() {
        $('#avBgPickerContainer').show();
        $('#avBgPickerContainer').farbtastic(changeBgColor);
        var val = $(this).val();
        if(val.length > 0) {
            var picker = $.farbtastic('#avBgPickerContainer');
            picker.setColor('#' + val);
        }
    });

    jQuery('#bgcolor').blur(function() {
        $('#avBgPickerContainer').hide();
    });

    var $chatQCont = $('#questionContainer');
    var gQuestionCounter = (typeof questionCount != 'undefined') ? questionCount : 0;
    var qHtml = null;
    jQuery('#addNewQuestion').click(function() {
        questionCount++;
        gQuestionCounter++;
        
        qHtml = '<fieldset class="fieldset" id="qDiv'+gQuestionCounter+'">'+
            '<legend>Question #'+questionCount+'</legend>'+
            '<div class="control-group cQuestion">'+
                '<label for="question'+gQuestionCounter+'" class="control-label">Question</label>'+
                '<div class="controls question">'+
                    '<input id="question'+gQuestionCounter+'" type="text" name="qarray['+gQuestionCounter+'][question]" value=""/>'+
                '</div>'+
            '</div>'+
            '<div class="control-group cResponce">'+
                '<label for="response'+gQuestionCounter+'" class="control-label">Response</label>'+
                '<div class="controls responce">'+
                    '<input id="response'+gQuestionCounter+'" type="text" name="qarray['+gQuestionCounter+'][response]" value=""/>'+
                '</div>'+
            '</div>'+
            '<div class="control-group cCounter">'+
                '<label for="counter'+gQuestionCounter+'" class="control-label">Counter</label>'+
                '<div class="controls counter">'+
                    '<input id="counter'+gQuestionCounter+'" type="text" name="qarray['+gQuestionCounter+'][counter]" value=""/>'+
                    '<span class="help-block">type +, - or = an integer - e.g. \'+1\' or \'=32\'</span>'+
                '</div>'+
            '</div>'+
            '<div class="form-actions">'+
                '<a class="btn btn-danger removeQuestionBtn" removeId="'+gQuestionCounter+'" href="javascript:void(0);">'+
                    '<i class="icon-minus-sign"></i>Remove</a>'+
            '</div>'+
        '</fieldset>';

        if($chatQCont != null)
            $chatQCont.append(qHtml);
        
        return false;
    });
    
    jQuery('.removeQuestionBtn').live('click', function() {
        var id = $(this).attr('removeId');
        if($chatQCont != null && id > 0) {
            $('#qDiv' + id).fadeOut(function(){
                $(this).remove();

                questionCount--;

                if($chatQCont != null) {
                    var i = 1;
                    $.each($chatQCont.children('fieldset'), function(index, obj) {
                        $(obj).children('legend').text('Question #' + i);
                        i++;
                    });
                }
            });
        }
        return false;
    });
    
    $('#forgot-password-submit').click(function() {
        $('#forgot-password-form').submit();
    });


    $('a.toggles').click(function() {
        $('a.toggles i').toggleClass('icon-chevron-left icon-chevron-right');

        $('#sidebar').animate({
            width: 'toggle'
        }, 0);
        $('#content').toggleClass('span12 span10');
        $('.to-hide').toggleClass('hide');
    });

    $(".code").mouseup(function() {
        $(this).select();
    });

    $('[data-toggle=tooltip]').tooltip({placement:"left"});


    jQuery('#nodeCountContainer button').click(function() {
        if($(this).attr('id') != 'applyCount')
            $('#nodeCount').attr('disabled', 'disabled');
    });
    
    jQuery('#nodeCountCustom').click(function() {
        $('#nodeCount').removeAttr('disabled');
    });
    
    jQuery('#copyQuestionBtn').click(function() {
        $('#copyQuestionModal').modal(); 
    });
    
    function UpdateTableHeaders() {
        $('.persist-area').each(function() {

            var el             = $(this),
                offset         = el.offset(),
                scrollTop      = $(window).scrollTop(),
                floatingHeader = $('.floating-header', this),
                content        = $('.persist-header');
                
            $.each(floatingHeader.children(), function(index, value) {
                $(value).css('width', $(content.first().children().get(index)).width());
            });

            if ((scrollTop > offset.top) && (scrollTop < offset.top + el.height())) {
                floatingHeader.css({
                    'visibility': 'visible'
                });
            } else {
                floatingHeader.css({
                    'visibility': 'hidden'
                });      
            }
        });
    }
    
    var clonedHeaderRow;

    $('.persist-area').each(function() {
        clonedHeaderRow = $('.persist-header', this);
        clonedHeaderRow.before(clonedHeaderRow.clone())
                       .css('width', clonedHeaderRow.width())
                       .addClass('floating-header');

    });

    $(window).scroll(UpdateTableHeaders)
             .trigger('scroll');
             
    $('.sort-btn').click(function() {
        $('#orderBy').val($(this).attr('orderBy'));
        $('#grid_from').submit();
    });

    var $gridFrom = $('#grid_from');
    $('.logic-btn').click(function() {
        if($(this).attr('on') == 'off') {
            $('#logicSort').val(0);
        } else {
            $('#logicSort').val(1);
        }

        $gridFrom.submit();
    });
    
    $('.main-edit-panel').draggable({handle: '.main-edit-panel .header', cursor: 'move', scroll: false});

    var parent;
    var id;
    var button;
    $(".radio_extended input[type=radio]").each(function(){
        if ($(this).is(':checked')){
            parent = $(this).parent('.radio_extended');
            id = $(this).attr('id');
            button = $(parent).find('label[for=' + id + ']');
            changeRadioBootstrap(button);
        }
    });

    $(".radio_extended .btn").live("click", function() {
        changeRadioBootstrap(this);
    });

    function changeRadioBootstrap(obj){
        $(obj).parent(".radio_extended").find(".btn").removeAttr('class').addClass('btn');
        $(obj).addClass('active');
        var additionClass = $(obj).attr('data-class');
        if (additionClass !== null){
            $(obj).addClass(additionClass);
        }
    }

    var $rootNodeMessage = jQuery('#rootNodeMessage'),
        rootAlertTimeout = null;
    jQuery('.show-root-error').click(function() {
        if(rootAlertTimeout != null) {
            clearTimeout(rootAlertTimeout);
        }

        if($rootNodeMessage != null) {
            $rootNodeMessage.removeClass('hide');
            rootAlertTimeout = setTimeout(function() { $rootNodeMessage.addClass('hide'); }, 5000);
        }
    });

    jQuery('.root-error-close').click(function() {
        if($rootNodeMessage != null) {
            $rootNodeMessage.addClass('hide');
            if(rootAlertTimeout != null) {
                clearTimeout(rootAlertTimeout);
            }
        }
    });

    $('.fieldset-verification .btn').click(function() {
        var verification = $(this).parents('.controls').find('div.verification');
        if ($(this).attr('data-value') == 'yes'){
            verification.removeClass('hide');
        } else {
            verification.addClass('hide');
        }
    });

    $('.verification .date').datepicker();

    jQuery('.contributors-list').sortable({
        axis: "y",
        cursor: "move",
        stop: function(event, ui) {
            recalculateContributorsOrder();
        }
    });

    recalculateContributorsOrder();

    function recalculateContributorsOrder() {
        $('.contributors-list input[type="hidden"]').each(function(index, value) {
            $(value).val(index + 1);
        });
    }

    body.on('click', '#createNewForum', function() {
        var url = $(this).attr('submit-url');
        $('form').attr('action', url).submit();
    });

    body.on('click', '.unassign-forum', function() {
        var url = $(this).attr('submit-url');
        $('form').attr('action', url).submit();
    });

    $.each(historyOfAllUsers, function(key, value) {
        if (value['username'] != currentUser && value['readonly'] != 1) {
            var links = $('a[href="' + value['href'] + '"]');
            var re = /(grid|visualManager)/i;
            var labyrinthIdRe = /\/\w+\/\w+\/(\d+)/i;
            var labyrinthId = labyrinthIdRe.exec(value['href']);

            $.each(links, function() {
                if (re.test(value['href'])) {
                    $('a[href="/counterManager/grid/' + labyrinthId[1] + '"]').attr({'href': 'javascript:void(0)'}).addClass('showCustomModalWindow').append('<span rel="tooltip" title="Checkin by ' + value['username'] + '" class="lock" id="/counterManager/grid/' + labyrinthId[1] + '" />').parent().css('position', 'relative');
                    $('a[href="/nodeManager/grid/' + labyrinthId[1] + '/1"]').attr({'href': 'javascript:void(0)'}).addClass('showCustomModalWindow').append('<span rel="tooltip" title="Checkin by ' + value['username'] + '" class="lock" id="/nodeManager/grid/' + labyrinthId[1] + '/1" />').parent().css('position', 'relative');
                    $('a[href="/visualManager/index/' + labyrinthId[1] + '"]').attr({'href': 'javascript:void(0)'}).addClass('showCustomModalWindow').append('<span rel="tooltip" title="Checkin by ' + value['username'] + '" class="lock" id="/visualManager/index/' + labyrinthId[1] + '" />').parent().css('position', 'relative');
                } else {
                    $(this).attr({'href': 'javascript:void(0)'}).addClass('showCustomModalWindow').append('<span rel="tooltip" title="Checkin by ' + value['username'] + '" class="lock" id="' + value['href'] + '" />').parent().css('position', 'relative');
                }
                var deleteButton = $(this).nextAll('.btn.btn-danger');
                if (deleteButton.length) {
                    deleteButton.remove();
                }
                var imageEditButton = $(this).nextAll('.btn.btn-inverse');
                if (imageEditButton.length) {
                    imageEditButton.remove();
                }
            });
        }
    });

    $(".showCustomModalWindow").click(function() {
        var url = $(this).children('.lock').attr('id');
        var obj = $('#readonly-notice');
        obj.find('a.btn').attr('href', url);
        obj.modal('show');

    });

    $(".lock").tooltip({placement: "right"});

    if (historyShowWarningPopup) {
        $('.row-fluid input, .row-fluid .btn, .row-fluid textarea, canvas, button, select').attr('disabled','disabled');
        $('.btn').attr('href', 'javascript:void(0)');
        $('.editable-text').attr('contenteditable', 'false');
        $('.row-fluid form').attr('action','');
        $('.row-fluid input').attr('onclick','javascript:void(0)');
    }

    var messageContainer = $('#collaboration_message'),
        messageTextContainer = $('#collaboration_message_text');

    var stopList = [],
        usernames = [],
        users = null;
    if (!currentUserReadOnly && userHasBlockedAccess) {
        setInterval(function() {
            $.get(historyAjaxCollaborationURL, function(data) {
                usernames = [];
                users = eval('(' + data + ')');
                $.each(users, function(key, value){
                    if (inArray(key, stopList)) {
                        delete users[key];
                    } else {
                        stopList.push(key);
                        usernames.push(value);
                    }
                });
                if (usernames.length) {
                    utils = new Utils();
                    utils.ShowMessage(messageContainer, messageTextContainer, 'info', 'User(s) ' + usernames.join(', ') + ' join you in this page in readonly mode', 7000);
                }
            });
        }, 30000);
    }

    function inArray(needle, haystack) {
        var length = haystack.length;
        for(var i = 0; i < length; i++) {
            if(haystack[i] == needle) return true;
        }
        return false;
    }

    if (historyShowWarningPopup && currentUserReadOnly) {
        utils = new Utils();
        utils.ShowMessage(messageContainer, messageTextContainer, 'info', 'You join edited page in readonly mode', 7000);
    }


    $('.add-condition-js').click(function(){
        var block = $('.add-condition-bl').last().clone().show();
        block.insertBefore($(this));
    });

    $('.add-labyrinth-js').click(function(){
        var block = $('.add-labyrinth-bl').last().clone().show();
        block.insertBefore($(this));
    });

    var assignIterator = 0;
    $('.add-assign-js').click(function(){
        var block = $('.add-assign-bl').last().clone().show(),
            radio = block.find('.assign-type');

        for (var i=0; i<radio.length; i++) radio.eq(i).attr('name','assign-type-'+assignIterator);

        block.insertBefore($(this));
        assignIterator++;
    });

    $('.remove-condition-js').live('click', function(){
        $(this).parent().remove();
    });

    $('#choose-patient').change(function(){
        window.location.href = $(this).find('option:selected').attr('data-href');
    });

    body.on('change', '.assign-type', function(){
        var mainBlock = $(this).parents('.condition-control-group'),
            selectUser = $('.assign-user').last().clone().show(),
            selectGroup = $('.assign-group').last().clone().show();

        if($(this).val() == 'users')
        {
            mainBlock.children('.assign-group').remove();
            mainBlock.append(selectUser);
        }
        if($(this).val() == 'groups')
        {
            mainBlock.children('.assign-user').remove();
            mainBlock.append(selectGroup);
        }
    });

    $('#assign-type').change(function(){
        var type        = $(this).find('option:selected').val(),
            assignField = $('.assign-bl-js'),
            assignBlocks = assignField.children('.condition-control-group');

        if (type == 2 || type == 4) assignField.removeClass('assign-same');
        if ((type == 1 || type == 3) && assignField.not('assign-same'))
        {
            assignIterator = 0;
            assignField.addClass('assign-same');
            for (var i=0; i<assignBlocks.length; i++)
            {
                var assignBlock = assignBlocks.eq(i);
                if (assignBlock.hasClass('add-assign-bl')) assignBlock.remove();
            }
        }
    });

    $('.add-r-patient-js').click(function(){
        var block = $('.r-patient-js').last().clone().show();
        block.insertBefore($(this));
        $(this).fadeToggle(0);
    });

    $('.remove-relation-js').live('click', function(){
        $(this).parent().remove();
        $('.add-r-patient-js').fadeToggle(0);
    });

    $('.add-contributor').live('click', function(e){
        e.preventDefault();
        var bl = $('.add-contributor-bl').last().clone().show();
        $('.add-contributor-parent').append(bl);
    });

    $('.del-contributor-js').live('click' ,function(){
        $(this).parents('.add-contributor-bl').remove();
    });

});
