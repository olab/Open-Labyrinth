<style>
    .highlight {
        background-color: yellow
    }
</style>
<form class="form-search" action="<?php echo URL::base(); ?>semanticsearch/doSearch" method="post">
    <input type="text" name="term" class="input-medium search-query"
           value="<?php if (isset($templateData["searchTerm"])) echo $templateData["searchTerm"]; ?>"/>
    <button type="submit" class="btn">Search</button>
</form>
<?php if (isset($templateData["searchTerm"])): ?>
    <h3>Results For "<?php echo $templateData["searchTerm"]; ?>"</h3>
    <?php if (isset($templateData["search_error"])): ?>
        <div class="alert">
            <a class="close" data-dismiss="alert">×</a>
            <?php echo $templateData["search_error"]; ?>
        </div>
    <?php endif ?>
<?php endif ?>
<?php if (isset($templateData["searchResults"])): ?>

    <ul>
        <?php foreach ($templateData["searchResults"] as $result): ?>

            <h4><?php echo $result["title"] ?></h4>

            <?php foreach ($result["nodes"] as $node): ?>

                <li>
                    <h6>
                        <span style="color:green">Node: </span><a target="_blank"
                                                                  href='<?php echo($node["uri"]) ?>'><?php echo($node["title"]) ?></a><br/>
                        <span style="color:orange">Matching synonym: </span><a target="_blank"
                                                                               href='<?php echo($node["termURI"]) ?>'><?php echo($node["term"]) ?></a>
                    </h6>

                    <div data-highlight="<?php echo($node["term"]) ?>" class="highlighted">
                        <?php echo($node["text"])?>
                    </div>

                </li>

            <?php endforeach ?>


        <?php endforeach; ?>

    </ul>

    <script src="<?php echo URL::base() . 'scripts/jquery.highlight-5.js'; ?>"></script>
    <script>
        $(document).ready(function () {
            $(".highlighted").each(function () {
                $(this).highlight($(this).data("highlight"));
            });

        });
    </script>

<?php endif; ?>
