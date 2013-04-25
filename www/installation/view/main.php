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
    $(document).ready(function() {
        $("#next-step").click(function() {
            $("#adminForm").submit();
        });

        $("#previous-step").click(function() {
            $("#previousStep").submit();
        });

        jQuery('#skipInstallationButton').click(function() {
            $('#skipInstallationPopUp').modal();
        });

        $("#skipInstallation").click(function() {
            $("#skipInstallationForm").submit();
        });

        var value;
        $(".summary_email").change(function(){
            value = $(this).val();
            if (value == 1){
                $("#div_email_passwords").removeClass('hide');
            } else {
                $("#div_email_passwords").addClass('hide');
            }
        });

        var parent;
        var id;
        var button;
        $(".radio_extended input[type=radio]").each(function(){
            if ($(this).is(':checked')){
                parent = $(this).parent('.radio_extended');
                id = $(this).attr('id');
                button = $(parent).find('label[for=' + id + ']');
                changeRadioBootstrap(button);
            }
        });

        $(".radio_extended .btn").live("click", function() {
            changeRadioBootstrap(this);
        });

        function changeRadioBootstrap(obj){
            $(obj).parent(".radio_extended").find(".btn").removeAttr('class').addClass('btn');
            $(obj).addClass('active');
            var additionClass = $(obj).attr('data-class');
            if (additionClass !== null){
                $(obj).addClass(additionClass);
            }
        }
    });
</script>
<div class="installation" style="">
    <div class="row-fluid">
        <h1 style="text-align: center;">Welcome to installation of <span class="text-info">OpenLabyrinth</span></h1>
    </div>
    <?php
        $message = Notice::get();
        if (count($message) > 0){
    ?>
    <div id="system-message" class="alert alert-warning">
        <div>
            <?php
            foreach($message as $m){
                echo '<p>' . $m . '</p>';
            }
            ?>
        </div>
    </div>
            <?php
        }
    ?>
    <ul class="nav nav-tabs">
        <?php
        $steps = array(1 => 'Pre-Installation Check', 2 => 'Configuration', 3 => 'Database', 4 => 'Overview');
        foreach($steps as $key => $step){
            echo '<li class="step ';
            if ($templateData['stepIndex'] == $key){
                echo 'active"><a href="javascript:void(0);">';
                if ($key != 1){
                    echo '<span class="badge">' . ($key - 1) . '</span> ';
                }
                echo $step . '</a>';
            } else {
                echo '"><span>';
                if ($key != 1){
                    echo '<span class="badge">' . ($key - 1) . '</span> ';
                }
                echo $step . '</span>';
            }
            echo '</li>';
        }
        ?>
    </ul>
    <div class="box">
        <?php if (isset($templateData['stepIndex'])) include_once(INST_DOCROOT.'view/step'.$templateData['stepIndex'].'.php'); ?>
    </div>
</div>