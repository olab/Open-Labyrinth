<form class="form-search" action="<?php echo URL::base(); ?>semanticsearch/doSearch" method="post">
    <input type="text" name="term" class="input-medium search-query">
    <button type="submit" class="btn">Search</button>
</form>

<img src="<?php echo URL::base(); ?>images/openlabyrinth-large.png" alt="" class="brand-large" />


<?php if(isset($templateData["searchResults"])): ?>
<ul>
    <?php foreach($templateData["searchResults"] as $result):?>

        <li>
            <a target="_blank" href='<?php echo($result["graph"])?>'><?php echo($result["object"])?></a>


        </li>



    <?php endforeach;?>

</ul>


<?php endif;?>
