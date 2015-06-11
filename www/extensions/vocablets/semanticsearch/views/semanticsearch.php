<style>
    .highlight { background-color: yellow }
</style>
<form class="form-search" action="<?php echo URL::base(); ?>semanticsearch/doSearch" method="post">
    <input type="text" name="term" class="input-medium search-query" value="<?php if(isset( $templateData["searchTerm"]))echo  $templateData["searchTerm"];?>"/>
    <button type="submit" class="btn">Search</button>
</form>



<?php if(isset($templateData["searchResults"])): ?>
   <h3>Results For "<?php echo $templateData["searchTerm"]; ?>"</h3>
<ul>
    <?php foreach($templateData["searchResults"] as $result):?>

        <li>
            <h6>
                <span style="color:green">Matching synonym: </span><a target="_blank" href='<?php echo($result["graph"])?>'><?php echo($result["object"])?></a>
            </h6>

            <div data-highlight="<?php echo($result["object"])?>" class="highlighted">
                <?php echo($result["text"])?>
            </div>

        </li>



    <?php endforeach;?>

</ul>

    <script src="<?php echo URL::base() . 'scripts/jquery.highlight-5.js'; ?>"></script>
    <script>
        $(document).ready(function(){
            $(".highlighted").each(function () {
                $(this).highlight($(this).data("highlight"));
            });

        });
    </script>

<?php endif;?>
