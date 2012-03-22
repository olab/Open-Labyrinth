<?php if(isset($templateData['map'])) { ?>
<p><a href="" target="_blank"><?php echo __('preview'); ?></a></p>

<p><a href="<?php echo URL::base().'labyrinthManager/editMap/'.$templateData['map']->id; ?>"><?php echo __('editor'); ?></a><br><br>

    - <a href="<?php echo URL::base().'labyrinthManager/global/'.$templateData['map']->id; ?>"><?php echo __('global'); ?></a><br>
    - <a href="<?php echo URL::base().'nodeManager/index/'.$templateData['map']->id; ?>"><?php echo __('nodes'); ?></a><br>
    - <a href="<?php echo URL::base().'nodeManager/grid/'.$templateData['map']->id; ?>"><?php echo __('node grid'); ?></a><br>
    - <a href="<?php echo URL::base().'nodeManager/sections/'.$templateData['map']->id; ?>"><?php echo __('sections'); ?></a><br>
    - <a href="<?php echo URL::base().'linkManager/index/'.$templateData['map']->id; ?>"><?php echo __('links'); ?></a><br>
    - <a href="<?php echo URL::base().'counterManager/index/'.$templateData['map']->id; ?>"><?php echo __('counters'); ?></a><br>
    - <a href="<?php echo URL::base().'counterManager/grid/'.$templateData['map']->id; ?>"><?php echo __('counter grid'); ?></a><br>
    - <a href="<?php echo URL::base().'questionManager/index/'.$templateData['map']->id; ?>"><?php echo __('questions'); ?></a><br>
    - <a href="<?php echo URL::base().'chatManager/index/'.$templateData['map']->id; ?>"><?php echo __('chats'); ?></a><br>
    - <a href="<?php echo URL::base().'fileManager/index/'.$templateData['map']->id; ?>"><?php echo __('files'); ?></a><br>
    - <a href="<?php echo URL::base().'mapUserManager/index/'.$templateData['map']->id; ?>"><?php echo __('users'); ?></a><br>
    - <a href="<?php echo URL::base().'avatarManager/index/'.$templateData['map']->id; ?>"><?php echo __('avatars'); ?></a><br>
    - <a href=""><?php echo __('elements'); ?></a><br>
    - <a href=""><?php echo __('clusters'); ?></a><br>
    - <a href="<?php echo URL::base().'feedbackManager/index/'.$templateData['map']->id; ?>"><?php echo __('feedback'); ?></a><br>
    - <a href=""><?php echo __('sessions'); ?></a><br>
    - <a href="" target="_blank"><?php echo __('visual editor'); ?></a>
</p>

<p><a href="" onClick="window.open('/openlabyrinth/devnotes.asp?mapid=', 'notes', 'toolbar=no, directories=no, location=no, status=no, menubar=no, resizable=yes, scrollbars=yes, width=500, height=400'); return false"><img src='<?php echo URL::base(); ?>images/notes.gif' border='0' alt='author notes'></a></p>
<?php } ?>