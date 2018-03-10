<?php
/**
 * Created by PhpStorm.
 * User: larjohns
 * Date: 17/5/2014
 * Time: 2:55 πμ
 */




?>

<style>

    .mesh-readings{
        background: ghostwhite ;

    }

    .readings-ul{
        padding: 0;
        list-style-type: none;
    }

    .reading-li{
        margin:10px;
        font-style: italic;
        font-size: x-small;
    }

    .reading-li a{

        font-style: italic;
        font-size: x-small;
    }
</style>

<div class="mesh-readings">
    <h5>Readings</h5>
    <ul class="readings-ul">
        <?php foreach($templateData['extra']["mesh"]["readings"] as $reading){

            ?>
            <li class="reading-li">
                <a target="_blank" href="<?php echo $reading["url"] ?>"><?php echo $reading["title"] ?></a>
            </li>


        <?php
        }?>
</div>


</ul>