$(function() {
    var $addLabyrinthButtons = $('.add-labyrinth-btn'),
        mapsOptions          = '<option value="">Select Labyrinth...</option>',
        urlBase              = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port: '') + '/';

    if('maps' in mapsJSON && mapsJSON.maps.length > 0) {
        var tmpName = '';

        for (var i = 0; i<mapsJSON.maps.length; i++) {
            tmpName      = mapsJSON.maps[i].name.replace(/\0/g,"");
            tmpName      = B64.decode($.trim(tmpName));

            var section = mapsJSON.maps[i].section,
                display = section ? ' style="display:none;"' : '';

            mapsOptions += '<option value="' + section + mapsJSON.maps[i].id + '"' + display + '>' + tmpName + '</option>';
        }
    }

    $addLabyrinthButtons.live('click', function() {
        var containerId   = $(this).attr('containerId'),
            $container    = null,
            maxItemNumber = 0,
            html          = '<div class="control-group labyrinth-item-%itemId%" itemNumber="%itemNumber%">' +
                                '<label for="s%containerForId%-labyrinth-%labelId%" class="control-label">Labyrinth #%number%</label>' +
                                '<div class="controls">' +
                                    '<select id="s%containerId%-labyrinth-%id%" name="s%containerName%_labyrinths[]" class="span6" data-section="1">' + mapsOptions + '</select> ' +
                                    '<button class="btn btn-danger remove-map"><i class="icon-trash"></i></button>' +
                                    '<div class="poll-node-section">' +
                                    '<button type="button" class="btn btn-info poll-node-js">Add poll node</button>' +
                                    '</div>' +
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
                   .attr('name', 's' + containerId + '_labyrinth[]');
        });
    });

    var $labyrinthErrorEmpty = $('.map-error-empty');

    $('.submit-webinar-btn').click(function() {
        $labyrinthErrorEmpty.hide();
        for(var i = $labyrinthContainers.length; i--;) {
            if($.trim($labyrinthContainers[i].html()).length <= 0) {
                $labyrinthErrorEmpty.show();
                return false;
            }
        }
    });

    var $stepsContainer = $('#steps-container');
    $('.add-step-btn').click(function() {
        var stepContainerId = parseInt($stepsContainer.children().last().attr('stepId'));
        if(isNaN(stepContainerId)) {
            stepContainerId = 0;
        }

        stepContainerId += 1;
        var html = '<fieldset class="fieldset step-container-' + stepContainerId + '" stepId="' + stepContainerId + '">' +
                       '<legend>' +
                           'Step - <input type="text" name="s' + stepContainerId + '_name" value=""/> <button class="btn btn-danger btn-remove-step"><i class="icon-trash"></i></button>' +
                       '</legend>' +
                       '<div id="labyrinth-container-' + stepContainerId + '" containerId="' + stepContainerId + '"></div>' +

                       '<div>' +
                           '<button class="btn btn-info add-labyrinth-btn" type="button" containerId="' + stepContainerId + '"><i class="icon-plus-sign"></i>Add Map or section</button>' +
                       '</div>' +
                   '</fieldset>';

        $stepsContainer.append(html);
    });

    $('.btn-remove-step').live('click', function() {
        $(this).parent().parent().remove();
    });

    var $redirectContainer = $('.submitSettingsContainer');
    var $forumSelect = $('#forum');

    $('#use').click(function() {
        if($redirectContainer != null)
            $redirectContainer.removeClass('hide');
    });

    $('#notUse').click(function() {
        if($redirectContainer != null)
            $redirectContainer.addClass('hide');
    });

    $forumSelect.click(function() {
        var $s = $forumSelect.val(),
            forumTopics = $("#topics-" + $s);

        if (forumTopics.length) {
            $(".topics").addClass('hide');
            forumTopics.removeClass('hide');
        }
        else {
            $(".topics").addClass('hide');
        }
    });

    var selectLabyrinths = $('select[name$="labyrinths[]"]');

    selectLabyrinths.each(function(){
        var mapSelect   = $(this),
            sectionId   = mapSelect.data('id');
            section     = mapSelect.data('section');

        if (section) createSectionSelect(mapSelect, sectionId);
    });

    $(document).on('change' , 'select[name$="labyrinths[]"]', function(){
        createSectionSelect($(this), false);
        addIdForNode($(this));
    });

    function createSectionSelect (mapSelect, sectionId) {
        var mapId       = mapSelect.val(),
            URL         = urlBase + '/webinarManager/getSectionAJAX/' + mapId;

        $.get(
            URL,
            function(data){
                mapSelect.next('.section-js').remove();
                var section = '<select class="section-js"><option>Select section</option>'
                $.each($.parseJSON(data), function(name,id){
                    var selected = (sectionId == id) ? ' selected' : '';
                    section +='<option value="' + id + '"' + selected + '>' + name + '</option>'
                });
                section += '</select>';

                if (data.length > 2) mapSelect.after(section);
            }
        );
    }

    $(document).on('change', '.section-js', function(){
        var section     = $(this),
            sectionId   = 'section' + section.val();

        section.prev().val(sectionId);
    });

    $stepsContainer.on('click', '.poll-node-js', function(){
        var button = $(this),
            mapId = $(this).data('id');

        button.button('loading');
        $.get(
            urlBase + '/webinarManager/getNodesAjax/' + mapId,
            function(data){
                if (data.length < 3) return;

                var nodeSelect = '<select class="node-select-js" name="poll_nodes[]"><option value="0">Select poll node</option>';
                $.each($.parseJSON(data), function(title, id){
                    nodeSelect += '<option value="' + id + '">' + title + ' (id: ' + id + ')' + '</option>';
                });
                nodeSelect +=
                    '</select>' +
                    '<input type="text" value="60 sec" class="poll-node-indent" name="poll_nodes[]" required>' +
                    '<button class="btn btn-danger poll-node-indent delete-node-js"><i class="icon-trash"></i></button>';

                button.before(nodeSelect);
                button.button('reset');
            }
        );
    });

    $('.poll-node-section').on('click', '.delete-node-js', function (e){
        e.preventDefault();
        var nodeId = $(this).data('id');
        if(nodeId) {
            $.get(
                urlBase + '/webinarManager/deleteNodeAjax/' + nodeId,
                function(data){}
            );
        }
        $(this).prev().prev().remove();
        $(this).prev().remove();
        $(this).remove();
    });

    function addIdForNode (obj){
        var mapId = obj.val(),
            button = obj.parent().find('.poll-node-js');

        button.data('id', mapId);
    }
});
