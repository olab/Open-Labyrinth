<?php defined('SYSPATH') OR die('No direct access allowed.');

$total_crumbs = count($breadcrumbs);
if ($total_crumbs > 0) {
    ?>
    <ul class="breadcrumb">
    <?php
    foreach ($breadcrumbs as $key => $crumb) {
        ?>
        <li<?php echo ((($key + 1) == $total_crumbs) ? ' class="active"' : ''); ?>>
            <?php
            echo ($key > 0 ? ' <span class="divider">/</span> ' : '');

            if (($crumb->get_url() !== NULL) && (($key + 1) < $total_crumbs)) {
                echo '<a href="' . $crumb->get_url() . '">' . $crumb->get_title() . '</a>';
            } else {
                echo $crumb->get_title();
            }
            ?>
        </li>
        <?php
    }
    ?>
    </ul>
    <?php
}