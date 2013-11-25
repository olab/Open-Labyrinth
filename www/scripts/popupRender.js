$(function() {
    var $popupOutsideContainer = $('.popup-outside-container'),
        $popupInsideContainer  = $('.popup-inside-container'),
        assignTypeCheck        = { labyrinth: mapCheck,
                                   node: nodeCheck,
                                   section: sectionCheck };

    $.each($('.popup'), function() {
        var $popup     = $(this),
            assignType = $popup.attr('assign-type'),
            startTime  = parseInt($popup.attr('time-before')),
            endTime    = parseInt($popup.attr('time-length')),
            titleHide  = $popup.attr('title-hide'),
            background_color =          $popup.attr('background-color'),
            border_color =              $popup.attr('border-color'),
            is_background_transparent = $popup.attr('is-background-transparent'),
            background_transparent =    parseInt($popup.attr('background-transparent')),
            is_border_transparent  =    $popup.attr('is-border-transparent'),
            border_transparent =        parseInt($popup.attr('border-transparent')),
            $container = ($popup.attr('popup-position-type') == 'inside') ? $popupInsideContainer
                                                                          : $popupOutsideContainer;

        $popup.appendTo($container);

        if (titleHide == 1) $popup.children('.header').hide();

        if (is_background_transparent==0){ $popup.css('background', background_color); }
        else {
            var color = hexToRgb(background_color);
            $popup.css('background', 'rgba('+color.r+','+color.g+','+color.b+','+(1-(background_transparent/100))+')');
        }

        if (is_border_transparent==0){ $popup.css('border', '1px solid '+border_color); }
        else {
            var color = hexToRgb(border_color);
            $popup.css('border', '1px solid rgba('+color.r+','+color.g+','+color.b+','+(1-(border_transparent/100))+')');
        }

        if(isNaN(startTime)) { startTime = 0; }
        if(isNaN(endTime)) { endTime = 0; }

        startTime = (popupStart != 0) ? (startTime - timeForNode)
                                      : startTime;

        if(assignType in assignTypeCheck && assignTypeCheck[assignType]($popup)) {
            setTimeout(function() {
                $popup.removeClass('hide');
                setTimeout(function() {
                    $.post(shownPopups, {popupId: $popup.attr('popup-id')}, function(data) {
                        var redirectType = $popup.attr('redirect-type'),
                            redirectId   = $popup.attr('redirect-id');

                        if(redirectType && redirectType == reportRedirectType) {
                            window.location.href = showReport;
                        } else if(redirectId) {
                            window.location.href = redirectURL.replace('#node#', redirectId);
                        }

                        $popup.addClass('hide');
                    });
                }, endTime * 1000);
            }, startTime * 1000);
        }
    });

    function mapCheck    ($object) { return true; };
    function nodeCheck   ($object) { return $object.attr('assign-to-id') == nodeId; };
    function sectionCheck($object) {
        var assignTo = $object.attr('assign-to-id');
        for(var i = sections.length; i--;) {
            if(sections[i] == assignTo) { return true; }
        }

        return false;
    };
});

function hexToRgb(hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}