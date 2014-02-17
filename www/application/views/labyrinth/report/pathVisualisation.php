<?php
/**
 * Open Labyrinth [ http://www.openlabyrinth.ca ]
 *
 * Open Labyrinth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Open Labyrinth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Open Labyrinth.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright Copyright 2012 Open Labyrinth. All Rights Reserved.
 *
 */
if (isset($templateData['map'])) { ?>
<div class="page-header">
    <div class="pull-right"><?php echo
        View::factory('labyrinth/report/menu')
            ->set('currentMapId', '/'.$templateData['map']->id)
            ->set('user_type_name', Auth::instance()->get_user()->type->name)
            ->set('current_action', Request::current()->action()); ?>
    </div>
    <h1><?php echo __('Path visualisation for "').$templateData['map']->name.'"'; ?></h1>
</div>

<div class="block" style="position: relative;" id="canvasContainer">
    <div id="ve_actionButton" class="canvas-action-buttons-container">
        <p><button type="button" class="round-btn" id="fullScreen" data-toggle="tooltip" data-original-title="Full&nbsp;screen" data-placement="right"><i class="ve-icon-fullscreen"></i></button></p>
        <p><button type="button" class="round-btn" id="zoomIn" data-toggle="tooltip" data-original-title="Zoom&nbsp;In" data-placement="right"><i class="ve-icon-zoom-in"></i></button></p>
        <p><button type="button" class="round-btn" id="zoomOut" data-toggle="tooltip" data-original-title="Zoom&nbsp;out" data-placement="right"><i class="ve-icon-zoom-out"></i></button></p>
    </div>

    <select id="path-select">
        <option href="<?php echo URL::base().'reportManager/pathVisualisation/'.$templateData['map']->id; ?>">Choose session</option>
        <?php foreach (Arr::get($templateData, 'sessions', array()) as $session) { ?>
        <option href="<?php echo URL::base().'reportManager/pathVisualisation/'.$templateData['map']->id.'/'.$session->id; ?>" <?php if (Arr::get($templateData, 'current_s') == $session->id) echo 'selected'; ?>>
            <?php echo date('Y.m.d H:i:s', $session->start_time).' '.$session->user->nickname; ?>
        </option>
        <?php } ?>
    </select>

    <canvas id="canvasPreview" width="200" height="200" tabindex='2'>Not supported</canvas>
    <canvas id="canvas" tabindex='1'>Not supported</canvas>
</div>

<script language="javascript" type="text/javascript" src="<?php echo URL::base().'scripts/tinymce/js/tinymce/tinymce.min.js'; ?>" xmlns="http://www.w3.org/1999/html"></script>
<script type="text/javascript">
    var mapJSON          = <?php echo Arr::get($templateData, 'mapJSON', 'null'); ?>;
    var mapType          = null;
    var selectedSession  = <?php echo Arr::get($templateData, 'selected_session', 'false'); ?>;
</script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/pathVisualisation/application.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/pathVisualisation/base64v1_0.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/pathVisualisation/link.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/pathVisualisation/mouse.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/pathVisualisation/node.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/pathVisualisation/paths.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/pathVisualisation/preview.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/pathVisualisation/transform.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/pathVisualisation/visualEditor.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/farbtastic/farbtastic.js'); ?>"></script>
<?php }; ?>