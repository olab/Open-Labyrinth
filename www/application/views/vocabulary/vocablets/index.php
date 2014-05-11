<?php
/**
 * Created by PhpStorm.
 * User: larjohns
 * Date: 5/5/2014
 * Time: 9:43 μμ
 */

if (isset($templateData)) {
//var_dump($templateData);die;
    ?>


    <h1 class="page-header">Available Semantic Extensions</h1>
    <table class="table table-bordered table-striped">
        <tbody>
        <?php foreach ($templateData['vocablets'] as $vocab): ?>
            <tr>

                <td>
                    <h4><?php echo $vocab["settings"]["info"]["title"] ?> (<?php echo $vocab["name"]; ?>)</h4>

                    <div><?php echo $vocab["settings"]["info"]["description"] ?> </div>
                    <h6>Fields: </h6>
                    <ul>
                        <?php
                        foreach ($vocab["settings"]["metadata"] as $metadata => $field_settings) {
?>
                            <li>


                            <?php
                            echo $field_settings["label"];?>
                            </li>

                            <?php
                        }

                        ?>

                    </ul>

                </td>
                <td>
                    <?php if($vocab["state"]!=NULL) { ?>
                        <form method="post"
                              action="<?php echo URL::base() . 'vocabulary/vocablets/manager/uninstall'; ?>">
                            <input type="hidden" name="guid" value="<?php echo $vocab["settings"]["info"]["guid"]; ?>"/>
                            <button class="btn btn-danger" type="submit"><i class="icon-trash"></i> Uninstall</button>
                        </form>

                        <?php if($vocab["state"]==false) { ?>
                            <!--a href="<?php echo URL::base() . "vocabulary/vocablets/manager/install?vocablet=" . $vocab["name"]; ?>"
                               class="btn btn-info">Enable</a-->

                        <?php } else {?>
                            <!--a href="<?php echo URL::base() . "vocabulary/vocablets/manager/install?vocablet=" . $vocab["name"]; ?>"
                               class="btn btn-info">Disable</a-->
                        <?php } ?>

                    <?php } else {?>
                    <a href="<?php echo URL::base() . "vocabulary/vocablets/manager/install?vocablet=" . $vocab["name"]; ?>"
                       class="btn btn-info">Install</a>

                   <?php } ?>
                </td>


            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>


<?php } ?>