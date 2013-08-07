$(function() {
    var $addLabyrinthButtons = $('.add-labyrinth-btn'),
        mapsOptions          = '<option value="">Select Labyrinth...</option>';

    if('maps' in mapsJSON && mapsJSON.maps.length > 0) {
        var tmpName = '';
        for(var i = mapsJSON.maps.length; i--;) {
            tmpName      = mapsJSON.maps[i].name.replace(/\0/g,"");
            mapsOptions += '<option value="' + mapsJSON.maps[i].id + '">' + B64.decode($.trim(tmpName)) + '</option>';
        }
    }

    $addLabyrinthButtons.click(function() {
        var containerId   = $(this).attr('containerId'),
            $container    = null,
            maxItemNumber = 0,
            html          = '<div class="control-group labyrinth-item-%itemId%" itemNumber="%itemNumber%">' +
                                '<label for="s%containerForId%-labyrinth-%labelId%" class="control-label">Labyrinth #%number%</label>' +
                                '<div class="controls">' +
                                    '<select id="s%containerId%-labyrinth-%id%" name="s%containerName%-labyrinth-%name%" class="span6">' + mapsOptions + '</select> ' +
                                    '<button class="btn btn-danger remove-map"><i class="icon-trash"></i></button>' +
                                '</div>' +
                            '</div>';

        $labyrinthErrorEmpty.hide();

        if(containerId <= 0) return;

        $container    = $('#labyrinth-container-' + containerId);

        maxItemNumber = parseInt($container.children().last().attr('itemNumber'));
        if(isNaN(maxItemNumber)) {
            maxItemNumber = 0;
        }

        maxItemNumber += 1;

        html = html.replace('%itemId%'        , maxItemNumber)
                   .replace('%itemNumber%'    , maxItemNumber)
                   .replace('%number%'        , maxItemNumber)
                   .replace('%id%'            , maxItemNumber)
                   .replace('%labelId%'       , maxItemNumber)
                   .replace('%name%'          , maxItemNumber)
                   .replace('%containerForId%', containerId)
                   .replace('%containerId%'   , containerId)
                   .replace('%containerName%' , containerId);

        $container.append(html);
    });

    $('.remove-map').live('click', function() {
        var $container  = $(this).parent().parent().parent(),
            containerId = $container.attr('containerId');

        $(this).parent().parent().remove();

        $container.children().each(function(index, value) {
            $(this).attr('itemNumber', index + 1)
                   .children()
                   .first()
                   .text('Labyrinth #' + (index + 1));

            $(this).children()
                   .last()
                   .children()
                   .first()
                   .attr('name', 's' + containerId + '-labyrinth-' + (index + 1));
        });
    });

    var $labyrinthContainers = [$('#labyrinth-container-1'), $('#labyrinth-container-2'), $('#labyrinth-container-3')],
        $labyrinthErrorEmpty = $('.map-error-empty');

    $('.submit-webinar-btn').click(function() {
        $labyrinthErrorEmpty.hide();
        for(var i = $labyrinthContainers.length; i--;) {
            if($.trim($labyrinthContainers[i].html()).length <= 0) {
                $labyrinthErrorEmpty.show();
                return false;
            }
        }
    });
});