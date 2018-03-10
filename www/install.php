<?php
// Sanity check, install should only be checked from index.php
defined('SYSPATH') or exit('Install tests must be loaded from within index.php!');
session_start();

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

$_SESSION['base_url'] = $baseUrl;

header('Location: '.$baseUrl.'installation/index.php');
exit;
?>
