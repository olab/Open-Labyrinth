<ul class="nav zero-margin gray nav-tabs" id="users_tabs">
    <li class="<?php echo ($current_action == 'index') ? 'active' : ''; ?>">
        <a class="active-link" href="<?php echo URL::base().'reportManager/index'.$currentMapId; ?>">
            <?php echo __('Sessions'); ?>
        </a>
    </li>
    <li class="<?php echo ($current_action == 'pathVisualisation') ? 'active' : ''; ?>">
        <a class="active-link" href="<?php echo URL::base().'reportManager/pathVisualisation'.$currentMapId; ?>">
            <?php echo __('Path visualisation'); ?>
        </a>
    </li>
    <li class="<?php echo ($current_action == 'summaryReport') ? 'active' : ''; ?>">
        <a class="active-link" href="<?php echo URL::base().'reportManager/summaryReport'.$currentMapId; ?>">
            <?php echo __('Aggregate report'); ?>
        </a>
    </li>
</ul>