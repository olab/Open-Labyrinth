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
defined('SYSPATH') or die('No direct script access.');

class Skin_Basic_Builder implements Skin_Builder {
    private $skin = '';

    public function __construct($skinSource) {
        $this->skin = $skinSource;
    }

    public function removeElements() {
        $this->skin = str_replace('<div class="ui-resizable-handle ui-resizable-e" style="z-index: 1000;"></div>', '', $this->skin);
        $this->skin = str_replace('<div class="ui-resizable-handle ui-resizable-s" style="z-index: 1000;"></div>', '', $this->skin);
        $this->skin = str_replace('<div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 1000;"></div>', '', $this->skin);
        $this->skin = str_replace(array('ui-resizable-handle', 'ui-resizable-se ui-icon', 'ui-icon-gripsmall-diagonal-se',
                                        'ui-resizable', 'component-selected'), '', $this->skin);
    }

    public function buildTitle() {
        $this->skin = str_replace('{NODE_TITLE}', '<?php echo Arr::get($templateData, \'node_title\'); ?>', $this->skin);
    }

    public function buildContent() {
        $this->skin = str_replace('{NODE_CONTENT}', '<?php echo Arr::get($templateData, "node_text");
                                                           if (isset($templateData["node_annotation"]) && $templateData["node_annotation"] != null) echo "<div class=\"annotation\">" . $templateData["node_annotation"] . "</div>"; ?>', $this->skin);
    }

    public function buildCounters() {
        $this->skin = str_replace('{COUNTERS}', '<?php if (isset($templateData[\'counters\'])) echo $templateData[\'counters\']; ?>', $this->skin);
    }

    public function buildLinks() {
        $this->skin = str_replace('{LINKS}', '<?php if(isset($templateData[\'links\'])) {
                                                        echo $templateData[\'links\'];} if(isset($templateData[\'undoLinks\'])){echo $templateData[\'undoLinks\'];
                                                    } ?>', $this->skin);
    }

    public function buildReview() {
        $this->skin = str_replace('{REVIEW}', '<div><a href="#" onclick="toggle_visibility(\'track\');"><p class=\'style2\'><strong>Review your pathway</strong></p></a></div><div id=\'track\' style=\'display:none\'><?php if(isset($templateData[\'trace_links\'])){echo $templateData[\'trace_links\'];}?></div>', $this->skin);
    }

    public function buildSection() {
        $this->skin = str_replace(
            '{SECTION_INFO}',
            '<?php if (isset($templateData[\'navigation\'])) echo $templateData[\'navigation\']; ?>',
            $this->skin);
    }

    public function buildMapInfo() {
        $this->skin = str_replace('{MAP_INFO}', '<?php if ($templateData[\'map\']->timing) { ?>
                                                    <div>Timer: <div id="timer"></div>
                                                    <br /><br />
                                                 <?php }?>
                                                 <div>
                                                    Map: <?php if(isset($templateData[\'map\'])) echo $templateData[\'map\']->name; ?> (<?php if (isset($templateData[\'map\'])) echo $templateData[\'map\']->id; ?>) <br/>
                                                    Node: <?php if (isset($templateData[\'node\'])) echo $templateData[\'node\']->id; ?><br/>
                                                 </div>', $this->skin);
    }

    public function buildBookmark() {
        $this->skin = str_replace('{BOOKMARK}', '<input type="button" onclick=\'ajaxBookmark();\'/>', $this->skin);
    }

    public function buildReset() {
        $this->skin = str_replace('{RESET}', '<p><a href=\'<?php echo URL::base(); ?>renderLabyrinth/reset/<?php echo $templateData[\'map\']->id; ?><?php if(isset($templateData[\'webinarId\']) && isset($templateData[\'webinarStep\'])) echo \'/\' . $templateData[\'webinarId\'] . \'/\' . $templateData[\'webinarStep\']; ?>\'>reset</a></p>', $this->skin);
    }

    public function getSkin() {
        return '<div class="popup-outside-container">' . $this->skin . '</div>';
    }
};