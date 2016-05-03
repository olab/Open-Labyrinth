<script>
    var user_nickname = 'unauthorized user';
    var user_id = '0';
    var curios_video_player_domains = <?php echo get_option('curios_video_player_domains', '[]', false) ?>;

    <?php if (Auth::instance()->logged_in()) {?>
    user_nickname = '<?php echo Auth::instance()->get_user()->nickname ?>';
    user_id = '<?php echo Auth::instance()->get_user()->id ?>';
    <?php } ?>

    //IE use "attachEvent" and "onmessage"
    var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
    var eventer = window[eventMethod];
    var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";

    eventer(messageEvent, receiveMessage);

    function receiveMessage(event)
    {
        var origin = event.origin || event.originalEvent.origin; // For Chrome, the origin property is in the event.originalEvent object.
        var dataKey = event.message ? "message" : "data";
        var data = event[dataKey];
        var isOriginAllowed = false;

        $.each(curios_video_player_domains, function(index, value){
            if (origin === 'http://' + value || origin === 'https://' + value) {
                isOriginAllowed = true;
            }
        });

        console.log(event);
        console.log('parent says: ' + origin);

        if (!isOriginAllowed) {
            return;
        }

        switch (data.type) {
            case 'videoServiceLoaded':
                videoServiceLoaded(event);
                break;

            case 'xAPIStatement':
                receivedXAPIStatement(event, data);
                break;

            default:
                throw new CustomException('invalid data type');
                break;
        }

    }

    function videoServiceLoaded(event)
    {
        event.source.postMessage({
            type: 'changeConfig',
            actor: {
                name: user_nickname,
                account: {
                    homePage: urlBase,
                    name: user_id
                }
            }
        }, event.origin);

        console.log('parent says: videoServiceLoaded');
    }

    function receivedXAPIStatement(event, data)
    {
        $.post('/renderLabyrinth/saveVideoXAPIStatement', data.statement);
    }

    function CustomException(message) {
        this.message = message;
        this.name = "UserException";
    }
</script>