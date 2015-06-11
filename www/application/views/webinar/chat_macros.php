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
?>
<script>
    $(window).keydown(function(event) {
        if(event.ctrlKey || event.metaKey) {
            event.preventDefault();
        }
    });

    var macPattern = /macintosh/;
    var isMac = macPattern.test(navigator.userAgent.toLowerCase());
    var pressKey = isMac ? 'meta' : 'ctrl';
</script>
<?php
if(!empty($templateData['macros_list']) && count($templateData['macros_list']) > 0){
    foreach($templateData['macros_list'] as $macros){
        $hot_keys = $macros->hot_keys;
?>
        <span class="btn btn-default ttalk-macros" id="macros-<?php echo $macros->id ?>"><?php echo $macros->text ?></span>
        <?php if(!empty($hot_keys)){ ?>
        <script>
            $(document).ready(function(){
                $('textarea.ttalk-textarea').bind('keydown', pressKey + '+<?php echo $hot_keys ?>', function(){
                    macros($(this), '<?php echo $macros->text ?>');
                });
            });
        </script>
        <?php } ?>
    <?php
    }
}
?>