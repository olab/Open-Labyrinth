<?php
// Sanity check, install should only be checked from index.php
defined('SYSPATH') or exit('Install tests must be loaded from within index.php!');
session_start();

$changeBootstrap = true;
if(isset($_SESSION['base_url']) && !empty($_SESSION['base_url'])){
    $changeBootstrap = false;
    $baseUrl = $_SESSION['base_url'];
}

if ($changeBootstrap){
    if (!is_writable(DOCROOT . 'application/bootstrap.php')){
        echo 'Installation error: Please make "application/bootstrap.php" writable.';
        die();
    }

    $docrootArrayWindow = explode('\\', DOCROOT);
    $docrootArrayLinux = explode('/', DOCROOT);
    if (count($docrootArrayWindow) > count($docrootArrayLinux)){
        $docrootArray = $docrootArrayWindow;
    } else {
        $docrootArray = $docrootArrayLinux;
    }
    $url = parse_url($_SERVER['REQUEST_URI']);
    $parts = explode('/', $url['path']);
    $baseUrl = '/';
    if (count($parts) > 0){
        foreach($parts as $p){
            if ($p != ''){
                if (in_array($p, $docrootArray)){
                    $baseUrl .= $p . '/';
                }
            }
        }
    }

    if ($baseUrl != '/'){
        $content = '';
        $handle = fopen(DOCROOT . 'application/bootstrap.php', 'r');
        while (($buffer = fgets($handle)) !== false) {
            $content .= $buffer;
        }

        $content = str_replace("'base_url' => '/',", "'base_url' => '".$baseUrl."',", $content);
        file_put_contents(DOCROOT . 'application/bootstrap.php', $content);
    }

    $_SESSION['base_url'] = $baseUrl;
}

header('Location: '.$baseUrl.'installation/index.php');
exit;
?>
