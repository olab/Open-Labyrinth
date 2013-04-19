<?php defined('SYSPATH') OR die('No direct access allowed.');

$total_crumbs = count($breadcrumbs);

$title = (isset($templateData['title']) ? $templateData['title'] : __('OpenLabyrinth'));
if ($total_crumbs > 0) {
    $reverse = array_reverse($breadcrumbs);
    $result = array();
    foreach ($reverse as $key => $crumb) {
        $result[] = $crumb->get_title();
    }
    
    $title = implode(' > ', $result);
    $title .= ' | OpenLabyrinth';
}

echo $title;