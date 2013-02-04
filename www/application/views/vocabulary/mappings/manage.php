<div>
    <h3>Mappings Manager</h3>
    <table border="1">
        <thead><tr><td colspan="4">Class Mappings</td></tr>
        <tr>
            <td>Identifier</td>
            <td>Class Name</td>
            <td>Term</td>
            <td>Operations</td>
        </tr>
        </thead>
        <tbody>
            <?php
            $classMappings = $templateData["classMappings"];
            foreach ($classMappings as $classMapping):?><tr>
                <td>
                    <?php echo $classMapping->id; ?>
                </td>
                <td>
                    <?php echo $classMapping->class; ?>
                </td>
                <td>
                    <?php echo $classMapping->term->getFullRepresentation(); ?>
                </td>
                  <td>
                    <form method="post" action="<?php echo URL::base() . 'vocabulary/mappings/manager/deleteclass'; ?>">
                        <input type="hidden" name="id" value="<?php echo $classMapping->id; ?>"/>
                        <input type="submit" value="delete"/>
                    </form>
                </td>
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>


    <div><h5>Add new class mapping</h5>
        <form method="post" action="<?php echo URL::base() . 'vocabulary/mappings/manager/addclass'; ?>">
            <table border="1">
                <thead>
                <tr>
                    <td>Class Name</td>
                    <td>Term</td>
                    <td>Type</td>
                </tr>

                </thead>

                <tbody>
                <tr>

                    <?php

                    $models = $templateData["models"];
                    $terms = $templateData["terms"];

                    ?>

                    <td>
                        <select name='class'>
                            <?php foreach ($models as $key=>$model):?>
                                <option value="<?php echo $key?>"><?php echo $model?></option>
                            <?php  endforeach;?>
                        </select>
                    </td>
                    <td>
                        <select name='term_id'>
                            <?php foreach ($terms as $id=>$label):?>
                                <option value="<?php echo $id?>"><?php echo $label?></option>
                            <?php  endforeach;?>
                        </select>
                    </td>
                    <td>
                        <select name='type'>
                            <option value="property">Property</option>
                            <option value="rel">Reference</option>
                            <option value="rev">Reverse reference</option>
                                          </select>
                    </td>
                </tr>
                </tbody>
            </table>
            <input type="submit" value="Add">

        </form>

    </div>

    <!- ====================================================================================================== ->

    <table border="1">
        <thead><tr><td colspan="6">Common Property Mappings</td></tr>
        <tr>
            <td>Identifier</td>
            <td>Class Name</td>
            <td>Property Name</td>
            <td>Term</td>
            <td>Relation Type</td>
            <td>Operations</td>
        </tr>
        </thead>
        <tbody>
        <?php
        $legacyPropertyMappings = $templateData["legacyPropertyMappings"];
        foreach ($legacyPropertyMappings as $legacyPropertyMapping):?><tr>
            <td>
                <?php echo $legacyPropertyMapping->id; ?>
            </td>
            <td>
                <?php echo $legacyPropertyMapping->class; ?>
            </td>
            <td>
                <?php echo $legacyPropertyMapping->property; ?>
            </td>
            <td>
                <?php echo $legacyPropertyMapping->term->getFullRepresentation(); ?>
            </td>
            <td>
                <?php echo $legacyPropertyMapping->type; ?>
            </td>
            <td>
                <form method="post" action="<?php echo URL::base() . 'vocabulary/mappings/manager/deletelegacy'; ?>">
                    <input type="hidden" name="id" value="<?php echo $legacyPropertyMapping->id; ?>"/>
                    <input type="submit" value="delete"/>
                </form>
            </td>
        </tr>
        <?php endforeach;?>
        </tbody>
    </table>


    <div><h5>Add new common property mapping</h5>
        <form method="post" action="<?php echo URL::base() . 'vocabulary/mappings/manager/addlegacy'; ?>">
            <table border="1">
                <thead>
                <tr>
                    <td>Class Name</td>
                    <td>Property</td>
                    <td>Term</td>
                    <td>Type</td>
                </tr>

                </thead>

                <tbody>
                <tr>

                    <?php

                    $models = $templateData["models"];
                    $terms = $templateData["terms"];

                    ?>

                    <td>
                        <select name='class'>
                            <?php foreach ($models as $key=>$model):?>
                                <option value="<?php echo $key?>"><?php echo $model?></option>
                            <?php  endforeach;?>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="property"/>
                    </td>
                    <td>
                        <select name='term_id'>
                            <?php foreach ($terms as $id=>$label):?>
                                <option value="<?php echo $id?>"><?php echo $label?></option>
                            <?php  endforeach;?>
                        </select>
                    </td>
                    <td>
                        <select name='type'>
                            <option value="property">Property</option>
                            <option value="rel">Reference</option>
                            <option value="rev">Reverse reference</option>
                        </select>
                    </td>
                </tr>
                </tbody>
            </table>
            <input type="submit" value="Add">

        </form>

    </div>

<!- ========================================================================================== ->


    <table border="1">
        <thead><tr><td colspan="5">Metadata Mappings</td></tr>
        <tr>
            <td>Identifier</td>
            <td>Metadata Name</td>
            <td>Term</td>
            <td>Relation Type</td>
            <td>Operations</td>
        </tr>
        </thead>
        <tbody>
        <?php
        $metadataMappings = $templateData["metadataMappings"];
        foreach ($metadataMappings as $metadataMapping):?><tr>
            <td>
                <?php echo $metadataMapping->id; ?>
            </td>
            <td>
                <?php echo $metadataMapping->metadata->label; ?>
            </td>
            <td>
                <?php echo $metadataMapping->term->getFullRepresentation(); ?>
            </td>
            <td>
                <?php echo $metadataMapping->type; ?>
            </td>
            <td>
                <form method="post" action="<?php echo URL::base() . 'vocabulary/mappings/manager/deletemetadata'; ?>">
                    <input type="hidden" name="id" value="<?php echo $metadataMapping->id; ?>"/>
                    <input type="submit" value="delete"/>
                </form>
            </td>
        </tr>
        <?php endforeach;?>
        </tbody>
    </table>


    <div><h5>Add new metadata mapping</h5>
        <form method="post" action="<?php echo URL::base() . 'vocabulary/mappings/manager/addmetadata'; ?>">
            <table border="1">
                <thead>
                <tr>
                    <td>Metadata</td>
                    <td>Term</td>
                    <td>Type</td>

                </tr>

                </thead>

                <tbody>
                <tr>

                    <?php

                    $models = $templateData["models"];
                    $terms = $templateData["terms"];
                    $metadata = $templateData["metadata"];

                    ?>
                    <td>
                        <select name='metadata_id'>
                            <?php foreach ($metadata as $field):?>
                                <option value="<?php echo $field->id?>"><?php echo $field->label?></option>
                            <?php  endforeach;?>
                        </select>
                    </td>


                    <td>
                        <select name='term_id'>
                            <?php foreach ($terms as $id=>$label):?>
                                <option value="<?php echo $id?>"><?php echo $label?></option>
                            <?php  endforeach;?>
                        </select>
                    </td>
                    <td>
                        <select name='type'>
                            <option value="property">Property</option>
                            <option value="rel">Reference</option>
                            <option value="rev">Reverse reference</option>
                        </select>
                    </td>
                </tr>
                </tbody>
            </table>
            <input type="submit" value="Add">

        </form>

    </div>


</div>