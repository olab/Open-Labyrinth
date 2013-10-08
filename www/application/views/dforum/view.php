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
?>

<script>
    var updateNotificationURL = '<?php echo URL::base(); ?>dforumManager/ajaxUpdateNotification',
        updateTopicNotificationURL = '<?php echo URL::base(); ?>dforumManager/ajaxUpdateTopicNotification';
</script>

<div class="page-header">
    <div class="pull-right">
        <?php if(Auth::instance()->get_user()->type->name != 'learner' && Auth::instance()->get_user()->type->name != 'reviewer') { ?>
        <a class="btn btn-primary" href="<?php echo URL::base().'dforumManager/addForum'; ?>"><i class="icon-plus-sign"></i> <?php echo __('Add new forum'); ?></a>
        <?php } ?>
    </div>
    <h1><?php echo __('Forums'); ?></h1></div>


<table id="mainForum" class="table table-striped table-bordered">
    <colgroup>
        <col style="width: 5%" />
        <col style="width: 50%" />
        <col style="width: 15%" />
        <col style="width: 15%" />
        <col style="width: 15%" />
    </colgroup>
    <?php
    if(isset($templateData['forums']) and count($templateData['forums']) > 0) { ?>
        <tr>
        <th> </th>
        <th><a href="<?php echo URL::base(); ?>dforumManager/index/1/<?php echo ($templateData['typeSort'] == 0) ? '1' : '0'; ?>" >Forum name
               <div class="pull-right"><i class="icon-chevron-<?php if($templateData['typeSort'] == 0 && $templateData['sortBy'] == 1 ) echo 'down';  else  echo 'up'; ?> icon-black"></i></div></th>
        <th><a href="<?php echo URL::base(); ?>dforumManager/index/2/<?php echo ($templateData['typeSort'] == 0) ? '1' : '0'; ?>" >Users
               <div class="pull-right"><i class="icon-chevron-<?php if($templateData['typeSort'] == 0 && $templateData['sortBy'] == 2 ) echo 'down';  else  echo 'up'; ?> icon-black"></i></div></th>
        <th><a href="<?php echo URL::base(); ?>dforumManager/index/3/<?php echo ($templateData['typeSort'] == 0) ? '1' : '0'; ?>" >Comments
               <div class="pull-right"><i class="icon-chevron-<?php if($templateData['typeSort'] == 0 && $templateData['sortBy'] == 3 ) echo 'down';  else  echo 'up'; ?> icon-black"></i></div></th>
        <th><a href="<?php echo URL::base(); ?>dforumManager/index/4/<?php echo ($templateData['typeSort'] == 0) ? '1' : '0'; ?>" >Last
               <div class="pull-right"><i class="icon-chevron-<?php if($templateData['typeSort'] == 0 && $templateData['sortBy'] == 4 ) echo 'down';  else  echo 'up'; ?> icon-black"></i></div></th>
    </tr>
        <?php
        $isFirst = true;
        foreach($templateData['forums'] as $forum) {
    ?>
            <?php // SHOW FORUMS ?>

    <?php if($isFirst) { ?>
    <tr>
        <th></th>
        <th>Forum</th>
        <th>Count of users</th>
        <th>Count of comments</th>
        <th>Last comment</th>
    </tr>
    <?php $isFirst = false; } ?>
    <tr>
        <td> <?php if(count($forum['topics']) > 0) { ?> <a href="javascript:void(0);" id = "read" class="showMoreTopics" style="text-decoration: none" attr="<?php echo $forum['id']; ?>">&nbsp&nbsp&nbsp&nbsp<i id="icon-<?php echo $forum['id']; ?>" class="icon-chevron-right"></i></a><?php } ?> </td>
        <td>
            <div class="pull-right">
                <a href="<?php echo URL::base() . 'dtopicManager/addTopic/' . $forum['id']; ?>" rel="tooltip" title="Add new topic in this forum" class="btn btn-small btn-info"><i class="icon-plus-sign"></i> <?php echo __('Add topic'); ?></a>
                <?php if (Auth::instance()->get_user()->type->name == 'superuser' || Auth::instance()->get_user()->id == $forum['author_id']) { ?>
                <a href="<?php echo URL::base() . 'dforumManager/editForum/' . $forum['id']; ?>" rel="tooltip" title="Edit this forum" class="btn btn-small btn-info"><i class="icon-edit"></i> <?php echo __('Edit'); ?></a>
                <a data-toggle="modal" href="javascript:void(0);" data-target="#delete-labyrinth-<?php echo $forum['id'];  ?>" rel="tooltip" title="Delete this forum" class="btn btn-small btn-danger"><i class="icon-trash"></i> <?php echo __('Delete Forum'); ?></a>
                <div class="modal hide alert alert-block alert-error fade in" id="delete-labyrinth-<?php echo $forum['id'];  ?>">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="alert-heading"><?php echo __('Caution! Are you sure?'); ?></h4>
                    </div>
                    <div class="modal-body">
                        <p><?php echo __('You have just clicked the delete button, are you certain that you wish to proceed with deleting this Forum from OpenLabyrinth?'); ?></p>
                        <p>
                            <a class="btn btn-danger" href="<?php echo URL::base() . 'dforumManager/deleteForum/' . $forum['id']; ?>"><?php echo __('Delete Forum'); ?></a> <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                        </p>
                    </div>
                </div>
                <?php } else { ?>
                    <a href="javascript:void(0);" class="btn btn-small" data-toggle="modal" data-target="#forum-settings-<?php echo $forum['id']; ?>">Settings</a>
                    <div class="modal hide" id="forum-settings-<?php echo $forum['id']; ?>">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h3><?php echo __('Settings'); ?></h3>
                        </div>
                        <div class="modal-body">
                            <input type="checkbox" id="forum-notification-checkbox-<?php echo $forum['id']; ?>" <?php if(isset($templateData['userForumsInfo']) && isset($templateData['userForumsInfo'][$forum['id']]) && $templateData['userForumsInfo'][$forum['id']]->is_notificate == 1) echo 'checked="checked"'; ?>/> Sent notifications
                        </div>
                        <div class="modal-footer">
                            <img src="<?php echo URL::base(); ?>images/loading.gif" class="hide" id="sent-forum-notification-loader-<?php echo $forum['id']; ?>" width="20px"/>
                            <button class="btn sent-notification-forum-save-btn" forumId="<?php echo $forum['id']; ?>"><?php echo __('Save'); ?></button>
                            <button class="btn" data-dismiss="modal" aria-hidden="true" id="settings-forum-close-btn-<?php echo $forum['id']; ?>"><?php echo __('Cancel'); ?></button>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <a href="<?php if ($forum['status'] != 2 && $forum['status'] != 0 ) echo URL::base().'dforumManager/viewForum/' . $forum['id']; ?>"> <h4><?php echo $forum['name'];?></h4></a>
                <?php if ($forum['status'] == 0) {?>
                    <span class="label label-important"><?php echo 'Inactive' ?></span>
                <?php } elseif ($forum['status'] == 2) {?>
                    <span class="label label-important"><?php echo 'Closed' ?></span>
                <?php } elseif ($forum['status'] == 3) {?>
                    <span class="label label-warning"><?php echo 'Archived' ?></span>
                <?php } ?>
            <p>
                <a rel="tooltip" title="Author" class="label label-info" href="<?php echo URL::base().'usermanager/viewUser/' . $forum['author_id']; ?>"><?php echo $forum['author_name']; ?></a><br/>
                <span class="label label-info"><?php echo $forum['date']; ?></span>
            </p>
        </td>
        <td><?php echo $forum['users_count'];?> users</td>
        <td><?php echo $forum['messages_count'];?> comments</td>
        <td>
            <p>
                <a rel="tooltip" title="Author" class="label label-info" href="<?php echo URL::base().'usermanager/viewUser/' . $forum['message_id']; ?>"><?php echo $forum['message_nickname']; ?></a>
                <br/>
                <span class="label label-info"><?php echo $forum['message_date']; ?></span>
            </p>
        </td>
    </tr>
            <?php // SHOW TOPICS ?>

            <?php  if (count($forum['topics']) > 0 ) { ?>

                <tr class="showTopic-id-<?php echo $forum['id']; ?>" style="display: none;">
                    <th></th>
                    <th>Topics(<?php echo count($forum['topics']); ?>)</th>
                    <th>Count of users</th>
                    <th>Count of comments</th>
                    <th>Last comment</th>
                </tr>
                    <?php foreach($forum['topics'] as $topic) { ?>
                    <tr class="showTopic-id-<?php echo $forum['id']; ?>" style="display: none;">
                        <td></td>
                        <td>
                            <div class="pull-right">
                            <?php if (Auth::instance()->get_user()->type->name == 'superuser' || Auth::instance()->get_user()->id == $topic['author_id']) { ?>
                                <a href="<?php echo URL::base() . 'dtopicManager/editTopic/' . $forum['id'] . '/' . $topic['id']; ?>" rel="tooltip" title="Edit this topic" class="btn btn-small btn-info"><i class="icon-edit"></i> <?php echo __('Edit'); ?></a>
                                <a data-toggle="modal" href="javascript:void(0);" data-target="#delete-labyrinth-<?php echo $topic['id'];  ?>" rel="tooltip" title="Delete this topic" class="btn btn-small btn-danger"><i class="icon-trash"></i> <?php echo __('Delete Topic'); ?></a>

                                <div class="modal hide alert alert-block alert-error fade in" id="delete-labyrinth-<?php echo $topic['id'];  ?>">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="alert-heading"><?php echo __('Caution! Are you sure?'); ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <p><?php echo __('You have just clicked the delete button, are you certain that you wish to proceed with deleting this Topic from OpenLabyrinth?'); ?></p>
                                        <p>
                                            <a class="btn btn-danger" href="<?php echo URL::base() . 'dtopicManager/deleteTopic/' . $forum['id'] . '/' . $topic['id'] ?>"><?php echo __('Delete Topic'); ?></a> <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                                        </p>
                                    </div>
                                </div>

                            <?php } else { ?>
                                <a href="javascript:void(0);" class="btn btn-small" data-toggle="modal" data-target="#forum-topic-settings-<?php echo $topic['id']; ?>">Settings</a>
                                <div class="modal hide" id="forum-topic-settings-<?php echo $topic['id']; ?>">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h3><?php echo __('Settings'); ?></h3>
                                    </div>
                                    <div class="modal-body">
                                        <input type="checkbox" id="forum-topic-notification-checkbox-<?php echo $topic['id']; ?>" <?php if(isset($templateData['userTopicsInfo']) && isset($templateData['userTopicsInfo'][$topic['id']]) && $templateData['userTopicsInfo'][$topic['id']]->is_notificate == 1) echo 'checked="checked"'; ?>/> Sent notifications
                                    </div>
                                    <div class="modal-footer">
                                        <img src="<?php echo URL::base(); ?>images/loading.gif" class="hide" id="sent-forum-topic-notification-loader-<?php echo $topic['id']; ?>" width="20px"/>
                                        <button class="btn sent-notification-forum-topic-save-btn" forumTopicId="<?php echo $topic['id']; ?>"><?php echo __('Save'); ?></button>
                                        <button class="btn" data-dismiss="modal" aria-hidden="true" id="settings-forum-topic-close-btn-<?php echo $topic['id']; ?>"><?php echo __('Cancel'); ?></button>
                                    </div>
                                </div>
                            <?php } ?>
                            </div>

                            <a href="<?php if ($topic['status'] != 2 && $topic['status'] != 0 ) echo URL::base().'dtopicManager/viewTopic/' . $topic['id']; ?>"> <h5><?php echo $topic['name'];?></h5></a>

                            <?php if ($topic['status'] == 0) {?>
                                <span class="label label-important"><?php echo 'Inactive' ?></span>
                            <?php } elseif ($topic['status'] == 2) {?>
                                <span class="label label-important"><?php echo 'Closed' ?></span>
                            <?php } elseif ($topic['status'] == 3) {?>
                                <span class="label label-warning"><?php echo 'Archived' ?></span>
                            <?php } ?>

                            <p>
                                <a rel="tooltip" title="Author" class="label label-info" href="<?php echo URL::base().'usermanager/viewUser/' . $topic['author_id']; ?>"><?php echo $topic['author_name']; ?></a><br/>
                                <span class="label label-info"><?php echo $topic['date']; ?></span>
                            </p>
                        </td>
                        <td><?php echo $topic['users_count'];?> users</td>
                        <td><?php echo $topic['messages_count'];?> comments</td>
                        <td>
                            <p>
                                <a rel="tooltip" title="Author" class="label label-info" href="<?php echo URL::base().'usermanager/viewUser/' . $topic['message_id']; ?>"><?php echo $topic['message_nickname']; ?></a>
                                <br/>
                                <span class="label label-info"><?php echo $topic['message_date']; ?></span>
                            </p>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>
    <?php }
    } else { ?>
        <tr class="info">
            <td colspan="5">There are no forums. You may add one, clicking the button above.</td>
        </tr>
   <?php } ?>
</table>

<script src="<?php echo ScriptVersions::get(URL::base().'scripts/dforum.js'); ?>"></script>


