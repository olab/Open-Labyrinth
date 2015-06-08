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
$users = Arr::get($templateData, 'users', array());
$webinar_id = $templateData['webinar_id'];
?>
<style>
    .chat{width:12%;margin:0 2px;display: inline-block;float:left;height: 600px;}
    .user_id,.chat-textarea{max-width:100%;width:100%}
    .row{margin-left:0!important;}
</style>
<script>
    var urlBase = <?php echo URL::base(true)?>;
    $(document).ready(function(){
        $( "#chats" ).sortable({
            connectWith: "#chats",
            handle: ".icon-move"
        });
    });
</script>
<input type="hidden" value="<?php echo $webinar_id ?>" id="webinar_id">
<div id="chats" class="row">
    <?php for($i = 1; $i < 9; ++$i){ ?>
        <?php
            $chat_id = 'chat'.$i;
        ?>

    <div class="panel panel-default chat" id="<?php echo $chat_id ?>">
        <div class="panel-heading">
            <div class="row">
                    <i class="icon icon-move" style="width:10%;"></i>
                <?php if(count($users) > 0){ ?>
                        <select class="user_id" style="display: inline-block;width:85%;">
                            <option value="">- choose User -</option>
                            <?php foreach($users as $user){ ?>
                                <option value="<?php echo $user->user->id ?>"><?php echo $user->user->nickname ?></option>
                            <?php } ?>
                        </select>
                <?php } ?>
            </div>
        </div>
        <div class="panel-body">
            <div class="chat-window">

            </div>
            <textarea class="chat-textarea" placeholder="Put your message..."></textarea>
        </div>
    </div>
        <script>
            $(document).ready(function(){
                setInterval(function() {
                    loadMessages('<?php echo $chat_id ?>');
                }, 1500);
            });
        </script>
    <?php } ?>
</div>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/helper.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/turkTalk.js'); ?>"></script>