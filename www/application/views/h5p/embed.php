<?php
/**
 * This view extends view from h5p-php-library
 * @link https://github.com/h5p/h5p-php-library
 * @link https://github.com/h5p/h5p-php-library/blob/master/embed.php
 */

/** @var string $lang */
/** @var array $content */
/** @var array $scripts */
/** @var array $styles */
/** @var array $integration */
?>
<!doctype html>
<html lang="<?php print $lang; ?>" class="h5p-iframe">
<head>
    <meta charset="utf-8">
    <title><?php print $content['title']; ?></title>
    <?php for ($i = 0, $s = count($scripts); $i < $s; $i++): ?>
        <script src="<?php print $scripts[$i]; ?>"></script>
    <?php endfor; ?>
    <?php for ($i = 0, $s = count($styles); $i < $s; $i++): ?>
        <link rel="stylesheet" href="<?php print $styles[$i]; ?>">
    <?php endfor; ?>
</head>
<body>
<div class="h5p-content" data-content-id="<?php print $content['id']; ?>"></div>
<script>
    H5PIntegration = <?php print json_encode($integration); ?>;
    H5P.externalDispatcher.on('xAPI', function (event) {
        console.log(event.data.statement);
        H5P.jQuery.post('/h5p/saveXAPIStatement', event.data.statement);
    });
</script>
</body>
</html>