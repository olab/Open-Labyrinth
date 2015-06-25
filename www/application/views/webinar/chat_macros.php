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

    $(document).ready(function(){
        var mousedownHappened = false;
        var macrosText = '';
        var textarea = $('textarea.ttalk-textarea');

        $('.ttalk-macros').on('mousedown', function(){
            mousedownHappened = true;
            macrosText = $(this).html();
        });

        textarea.on('blur', function(){
            if(mousedownHappened) {
                var thistextarea = $(this);
                macros(thistextarea, macrosText);
                thistextarea.focus();
                mousedownHappened = false;
            }
        });
    });

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
    ?>
    <table class="">
        <!--<thead>
        <tr>
            <th style="width:1%">Key</th>
            <th>Text</th>
        </tr>
        </thead>-->
        <tbody>
    <?php
    foreach($templateData['macros_list'] as $macros){
        $hot_keys = $macros->hot_keys;
?>
        <tr>
            <td><?php echo $hot_keys ?></td>
            <td><span class="btn btn-default ttalk-macros" id="macros-<?php echo $macros->id ?>"><?php echo $macros->text ?></span></td>
        </tr>
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
    ?>
        </tbody>
    </table>
        <?php
}
?>