<?php
/**
 * Open Labyrinth [ http://www.openlabyrinth.ca ]
 *
 * Open Labyrinth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Open Labyrinth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Open Labyrinth.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright Copyright 2012 Open Labyrinth. All Rights Reserved.
 *
 */
echo View::factory('webinar/_topMenu')->set('scenario', $templateData['scenario'])->set('webinars', $templateData['webinars']);
$users = Arr::get($templateData, 'users', array());
$webinar_id = $templateData['webinar_id'];
$chats = $templateData['chats'];
?>
<style>
    .chat{width:12.1%;margin:0 2px;display:inline-block;float:left;}
    .chat .panel-heading{min-height:45px;}
    .user_id,.redirect_node_id{max-width:100%;width:auto;}
    .chat-textarea{max-width:100%;}
    .progress{height:10px;margin:5px 0 0}
    .progress-yellow .bar{
        background: #fefcea; /* Old browsers */
        background: -moz-linear-gradient(top,  #fefcea 0%, #f1da36 100%); /* FF3.6-15 */
        background: -webkit-linear-gradient(top,  #fefcea 0%,#f1da36 100%); /* Chrome10-25,Safari5.1-6 */
        background: linear-gradient(to bottom,  #fefcea 0%,#f1da36 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fefcea', endColorstr='#f1da36',GradientType=0 ); /* IE6-9 */
    }

    @media (max-width:1870px){
        .chat{width:12%;}
    }

    @media (max-width:1505px){
        .chat{width:11.9%;}
    }
</style>
<script>
    var urlBase = '<?php echo URL::base(true)?>';
    $(document).ready(function(){
        $( "#chats" ).sortable({
            connectWith: "#chats",
            handle: ".icon-move",
            update: function (event, ui) {
                saveChatsOrder($(this));
            }
        });

        setInterval(function(){
            showWaitingTime();
        }, 1000);

        var userLists = $('.user_id');
        userLists.on('change', function(){
            clearChat($(this));
            saveChosenUser($(this));
        });

        var ttalkButton = $('.ttalkButton');
        ttalkButton.on('click', function () {
            addChatMessage($(this), 0);
        });

        $('textarea.ttalk-textarea').on('keyup', function(e){
            if(!e.shiftKey && e.keyCode == 13) {
                ttalkButton.trigger('click');
            }
        });

        var ttalkRedirectButton = $('.ttalkRedirectButton');
        ttalkRedirectButton.on('click', function (e) {
            e.preventDefault();
            addChatMessage($(this), 0, 1);
        });

        var ttalkBell = $('.ttalkBell');
        ttalkBell.on('click', function (e) {
            e.preventDefault();
            addChatMessage($(this), 0, 2);
        });
    });
</script>
<input type="hidden" value="<?php echo $webinar_id ?>" id="webinar_id">
<div id="chats">
    <?php foreach($chats as $chat_id => $v){ ?>

    <div class="panel chat ttalk" id="<?php echo $chat_id ?>">
        <div class="panel-heading">
                <i class="icon icon-move" style="width:10%;"></i>
            <?php if(count($users) > 0){ ?>
                    <select class="user_id" style="display: inline-block;width:85%;margin-bottom:0">
                        <option value="">- choose User -</option>
                        <?php foreach($users as $user){ ?>
                            <option value="<?php echo $user->user->id ?>" <?php if(!empty($v['user_id']) && $user->user->id == $v['user_id']) echo 'selected'; ?>><?php echo $user->user->nickname ?></option>
                        <?php } ?>
                    </select>
            <?php } ?>
            <div title="Waiting time" class="progress hide">
                <div class="bar"></div>
            </div>
        </div>
        <div class="panel-body">
            <div class="chat-window" style="height:430px;" data-responses-counter="0"></div>
            <div style="border-bottom:1px solid #eee;padding:0 0 10px">
                <textarea class="chat-textarea ttalk-textarea" placeholder="Type your response..." style="height:60px;"></textarea>
                <button class="ttalkButton btn btn-primary" style="width:100%">Submit</button>
            </div>

            <div style="border-bottom:1px solid #eee;padding:0 0 10px">
                <button class="ttalkBell btn btn-block btn-warning" title="Bell">
                    <i class="icon icon-bell"></i>
                </button>
            </div>

            <div class="form-inline" style="border-bottom:1px solid #eee;padding:10px 0">
                <select class="redirect_node_id" disabled><option value="">- Redirect to... -</option></select>
                <i class="ttalkRedirectButton icon icon-arrow-right btn btn-small btn-success" title="Redirect !"></i>
            </div>

            <div>
                <div>NodeId: <b class="node_id"></b></div>
                <div>Node Title: <b class="node_title"></b></div>
                <input type="hidden" class="session_id" value="">
                <input type="hidden" class="question_id" value="">
            </div>
        </div>
    </div>
        <script>
            $(document).ready(function(){

                getLastNode('<?php echo $chat_id ?>');

                setInterval(function() {
                    getLastNode('<?php echo $chat_id ?>');
                }, 1500);

                setInterval(function() {
                    loadMessages('<?php echo $chat_id ?>', 0);
                }, 1500);

                setInterval(function() {
                    getNodeLinks('<?php echo $chat_id ?>');
                }, 1500);
            });
        </script>
    <?php } ?>
</div>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/turkTalk.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/jquery.hotkeys.js'); ?>"></script>