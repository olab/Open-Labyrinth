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
<style>
    .navbar select{margin-top:10px;}
</style>
<script>
    $(document).ready(function(){
        var webinar_id = $('#webinar_id');
        webinar_id.on('change', function(){
            var url = '<?php echo URL::base(true); ?>webinarManager/index/' + $(this).val();
            window.location.replace(url);
        });

        var choose_view = $('#choose_view');
        choose_view.on('change', function(){
            var url = $(this).val();
            window.location.replace(url);
        });
    });
</script>
<div class="navbar">
    <div class="navbar-inner">
        <ul class="nav" style="width: 100%">
            <li>
                <?php if(!empty($webinars) && count($webinars) > 0) { ?>
                    <select id="webinar_id">
                        <option value="0">- Choose Scenario -</option>
                        <?php foreach($webinars as $webinar) { ?>
                            <option value="<?php echo $webinar->id ?>" <?php if(!empty($scenario) && $webinar->id == $scenario->id) echo 'selected'; ?>><?php echo $webinar->title ?></option>
                        <?php } ?>
                    </select>
                <?php } ?>
            </li>


            <?php if(!empty($scenario)){ ?>
                <li>
                    <select id="choose_view">
                        <option value="">- Choose View -</option>
                        <option value="<?php echo URL::base().'webinarManager/progress/'.$scenario->id; ?>">View progress</option>
                        <option value="<?php echo URL::base().'webinarManager/chats/'.$scenario->id; ?>">View scenario monitor chat</option>
                    </select>
                </li>
                <?php if($scenario->forum_id) { ?>
                    <li>
                        <a href="<?php echo URL::base(); ?><?php if ($scenario->isForum) {?>dforumManager/viewForum/<?php } else {?>dtopicManager/viewTopic/<?php } ?><?php echo $scenario->forum_id; ?>">
                            <i class="icon-list-alt"></i>Forum
                        </a>
                    </li>
                <?php } ?>
                <li><a data-toggle="modal" href="javascript:void(0)" data-target="#change-step-<?php echo $scenario->id; ?>"><i class="icon-edit icon-white"></i>Change step</a></li>
                <li><a href="<?php echo URL::base().'webinarManager/edit/'.$scenario->id; ?>"><i class="icon-edit icon-white"></i>Edit</a></li>
                <li><a href="<?php echo URL::base().'webinarManager/visualEditor/'.$scenario->id; ?>"><i class="icon-edit icon-white"></i>Visual editor</a></li>
                <li><a href="<?php echo URL::base().'webinarManager/statistics/'.$scenario->id; ?>"><i class="icon-calendar icon-white"></i>Statistics</a></li>
                <li><a data-toggle="modal" href="javascript:void(0)" data-target="#reset-webinar-<?php echo $scenario->id; ?>">
                        <i class="icon-refresh icon-white"></i><?php echo __('Reset'); ?>
                    </a></li>
                <?php if (Auth::instance()->get_user()->type->name != 'Director') { ?>
                    <li><a data-toggle="modal" href="javascript:void(0)" data-target="#delete-node-<?php echo $scenario->id; ?>">
                            <i class="icon-trash icon-white"></i><?php echo __('Delete'); ?>
                        </a></li>
                <?php } ?>
            <?php } ?>


            <li style="float: right;">
                <a class="" href="<?php echo URL::base().'webinarManager/add'; ?>">
                    <i class="icon-plus-sign icon-white"></i>Create Scenario
                </a>
            </li>
            <li style="float: right;">
                <a class="" href="<?php echo URL::base().'webinarManager/allConditions'; ?>">
                    <i class="icon-plus-sign icon-white"></i>Conditions
                </a>
            </li>
        </ul>
    </div>
</div>

<?php if(!empty($scenario)){ ?>
    <div class="modal hide alert alert-block alert-error fade in" id="delete-node-<?php echo $scenario->id; ?>">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="alert-heading"><?php echo __('Caution! Are you sure?'); ?></h4>
        </div>
        <div class="modal-body">
            <p><?php echo __('You have just clicked the delete button, are you certain that you wish to proceed with deleting "' . $scenario->title . '"?'); ?></p>
            <p>
                <a class="btn btn-danger" href="<?php echo URL::base(); ?>webinarManager/delete/<?php echo $scenario->id; ?>"><?php echo __('Delete'); ?></a> <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
            </p>
        </div>
    </div>
    <div class="modal hide fade in" id="change-step-<?php echo $scenario->id; ?>">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="alert-heading"><?php echo __('Select step'); ?></h4>
        </div>
        <div class="modal-body">
            <?php
            if(count($scenario->steps)) {
                foreach($scenario->steps as $scenarioStep) { ?>
                    <div>
                    <input class="current-step-<?php echo $scenario->id; ?>" type="radio" name="currentStep<?php echo $scenario->id; ?>" value="<?php echo $scenarioStep->id; ?>" <?php if($scenario->current_step == $scenarioStep->id) echo 'checked'; ?>>
                    <?php echo $scenarioStep->name; ?>
                    </div><?php
                }
            } ?>
        </div>
        <div class="modal-footer">
            <a class="btn change-step-btn" href="<?php echo URL::base().'webinarManager/changeStep/'.$scenario->id.'/'; ?>" webinarId="<?php echo $scenario->id; ?>"><?php echo __('Change'); ?></a>
            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        </div>
    </div>

    <div class="modal hide fade in alert alert-block alert-danger" id="reset-webinar-<?php echo $scenario->id; ?>">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="alert-heading"><?php echo __('Reset scenario'); ?></h4>
        </div>
        <div class="modal-body">
            <p>
                <?php echo __('Warning! Do you really want to reset scenario? If you reset webinar all user sessions which created on playing this scenarion will be delete.'); ?>
            </p>
            <div>
                <a class="btn btn-danger" href="<?php echo URL::base().'webinarManager/reset/'.$scenario->id; ?>"><?php echo __('Reset'); ?></a>
                <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
            </div>
        </div>

    </div>
<?php } ?>
<script>
    $(function() {
        $('.change-step-btn').click(function() {
            var webinarId = $(this).attr('webinarId'),
                step      = $('.current-step-' + webinarId + ':checked').val();

            $(this).attr('href', $(this).attr('href') + step);
        });
    })
</script>