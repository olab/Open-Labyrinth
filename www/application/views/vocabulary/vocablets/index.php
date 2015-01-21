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
                    <h4>
                        <a id="V<?php echo $vocab["settings"]["info"]["guid"] ?>"><?php echo $vocab["settings"]["info"]["title"] ?>
                            (<?php echo $vocab["name"]; ?>)</a></h4>

                    <div><?php echo $vocab["settings"]["info"]["description"] ?> </div>
                    <?php if (isset($vocab["settings"]["metadata"]) && !empty($vocab["settings"]["metadata"])) { ?>
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
                    <?php } ?>
                    <?php if (isset($vocab["settings"]["dependencies"]) && !empty($vocab["settings"]["dependencies"])) { ?>

                        <h6>Dependencies: </h6>
                        <ul>
                            <?php

                            foreach ($vocab["settings"]["dependencies"] as $dependency => $info) {
                                ?>
                                <li>

                                    <a href="#V<?php echo $info["guid"] ?>">
                                        <?php
                                        echo $info ["title"];?>
                                    </a>

                                </li>

                            <?php
                            }

                            ?>

                        </ul>
                    <?php } ?>

                </td>
                <td>
                    <?php if ($vocab["state"] != NULL) { ?>
                        <form method="post"
                              action="<?php echo URL::base() . 'vocabulary/vocablets/manager/uninstall'; ?>">
                            <input type="hidden" name="guid" value="<?php echo $vocab["settings"]["info"]["guid"]; ?>"/>
                            <button class="btn btn-danger" type="submit"><i class="icon-trash"></i> Disable</button>
                        </form>

                        <?php if ($vocab["state"] == false) { ?>
                            <!--a href="<?php echo URL::base() . "vocabulary/vocablets/manager/install?vocablet=" . $vocab["name"]; ?>"
                               class="btn btn-info">Enable</a-->

                        <?php } else { ?>
                            <!--a href="<?php echo URL::base() . "vocabulary/vocablets/manager/install?vocablet=" . $vocab["name"]; ?>"
                               class="btn btn-info">Disable</a-->
                        <?php } ?>

                    <?php
                    } else {
                        $canInstall = true;
                        if (isset($vocab["settings"]["dependencies"]))

                            foreach ($vocab["settings"]["dependencies"] as $dependency => $info) {
                                if (!array_key_exists($info["guid"], $templateData["vocablets"])) {
                                    $canInstall = false;
                                    break;
                                }

                            }


                        if ($canInstall) {
                            ?>
                            <a href="<?php echo URL::base() . "vocabulary/vocablets/manager/install?vocablet=" . $vocab["name"]; ?>"
                               class="btn btn-success"><i class="icon-check"></i> Enable</a>

                        <?php
                        } else {
                            ?>
                            Enable the dependency extensions first, to be able to install this module.

                        <?php
                        }
                    } ?>
                </td>


            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>


<?php } ?>