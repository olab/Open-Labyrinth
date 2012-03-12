<hr />

<form action="search.asp" method="POST">
    <p><input type="text" name="searchterm" size="10" />
        <input id="SeachSubmit" type="submit" value="Search" /><br />
        <?php echo __('title'); ?>:<input name="scope" type="radio" value="t" checked />&nbsp;&nbsp;<?php echo __('all'); ?>:<input name="scope" type="radio" value="a" />
    </p>
</form>

<hr />

<p><?php echo __('logged in as'); ?>&nbsp;<?php if(isset($templateData['username'])) echo $templateData['username']; ?>
    <br /><a href="changepassword.asp"><?php echo __('change password'); ?></a>
    <br /><a href="<?php echo URL::base(); ?>home/logout"><?php echo __('logout'); ?></a></p>