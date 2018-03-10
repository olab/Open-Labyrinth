var Utils = function() {
    var self = this;
    
    var $aButtonContainer = $('#ve_additionalActionButton');

    self.ShowMessage = function(messageForm, messageObj, messageType, message, timeOut, hideObj, hide){
        messageForm.removeClass('alert-success');
        messageForm.removeClass('alert-error');
        messageForm.removeClass('alert-info');

        messageForm.addClass('alert-' + messageType);
        messageObj.text(message);

        var width = parseInt(messageForm.width());
        messageForm.css('margin-left', '-' + (width / 2) + 'px');
        messageForm.removeClass('hide');

        if (hideObj != null){
            if (hide == true){
                $aButtonContainer.hide();
                hideObj.addClass('hide');
            } else {
                hideObj.removeClass('hide');
            }
        }
        
        if (timeOut != null){
            setTimeout(function() {
                messageForm.addClass('hide');
                messageObj.text('');
            }, timeOut);
        }
    }
}

var utils = new Utils();