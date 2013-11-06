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
            $container = ($popup.attr('popup-position-type') == 'inside') ? $popupInsideContainer
                                                                          : $popupOutsideContainer;

        $popup.appendTo($container);

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