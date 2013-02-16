<div>
<script type="text/javascript">

    var properties =
    <?php echo json_encode($templateData["properties"]);?>
</script>
<script type="text/javascript" src='<?php echo URL::base();?>scripts/jquery/jquery-ui-1.9.1.custom.min.js'></script>
<script type="text/javascript" src='<?php echo URL::base();?>scripts/tinymce/jscripts/tiny_mce/tiny_mce.js'></script>
<script type="text/javascript"
        src='<?php echo URL::base();?>scripts/tinymce/jscripts/tiny_mce/jquery.tinymce.js'></script>
<script type="text/javascript" src='<?php echo URL::base();?>scripts/olab/inputHandler.js'></script>


<h1>Mappings Manager</h1>

<h3>Class Mappings</h3>
<table class="table table-bordered table-striped">

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
    foreach ($classMappings as $classMapping):?>
        <tr>
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
                    <input class="btn btn-danger" type="submit" value="delete"/>
                </form>
            </td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>


<div>

    <form class="form-horizontal" method="post"
          action="<?php echo URL::base() . 'vocabulary/mappings/manager/addclass'; ?>">
        <?php

        $models = $templateData["models"];
        $terms_classes = $templateData["terms_classes"];

        ?>
        <fieldset class="fieldset">
            <legend>Add new class mapping</legend>
            <div class="control-group">
                <label for="cl-class" class="control-label">Class Name</label>

                <div class="controls">
                    <select name='class' id="cl-class">
                        <?php foreach ($models as $key => $model): ?>
                            <option value="<?php echo $key?>"><?php echo $model?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label for="cl-term" class="control-label">Term</label>

                <div class="controls">
                    <select name='term_id' id="cl-term">
                        <?php foreach ($terms_classes as $id => $term): ?>
                            <option title="<?php echo $term["uri"];?>" value="<?php echo $id?>"><?php echo $term["label"];?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
        </fieldset>

        <input class="btn btn-primary" type="submit" value="Add">

    </form>

</div>

<!- ====================================================================================================== ->
<h3>Common Property Mappings</h3>
<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <td colspan="6"></td>
    </tr>
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
    foreach ($legacyPropertyMappings as $legacyPropertyMapping):?>
        <tr>
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
                    <input class="btn btn-danger" type="submit" value="delete"/>
                </form>
            </td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>


<div>

    <form class="form-horizontal" method="post"
          action="<?php echo URL::base() . 'vocabulary/mappings/manager/addlegacy'; ?>">

        <?php

        $models = $templateData["models"];
        $terms_properties = $templateData["terms_properties"];

        ?>

        <fieldset class="fieldset">
            <legend>Add new common property mapping</legend>
            <div class="control-group">
                <label for="legacy-class" class="control-label">Class Name</label>

                <div class="controls">
                    <select name='class' id="legacy-class">
                        <?php foreach ($models as $key => $model): ?>
                            <option value="<?php echo $key?>"><?php echo $model?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label for="legacy-property" class="control-label">Property</label>

                <div class="controls">
                    <select type="text" id="legacy-property" name="property"></select>
                </div>
            </div>
            <div class="control-group">
                <label for="legacy-term" class="control-label">Term</label>

                <div class="controls">
                    <select name='term_id' id="legacy-term">
                        <?php foreach ($terms_properties as $id => $term): ?>
                            <option title="<?php echo $term["uri"];?>" value="<?php echo $id?>"><?php echo $term["label"];?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label for="legacy-type" class="control-label">Type</label>

                <div class="controls">
                    <select name='type' id="legacy-type">
                        <option value="property">Property</option>
                        <option value="rel">Reference</option>
                        <option value="rev">Reverse reference</option>
                    </select>
                </div>
            </div>
        </fieldset>


        <input class="btn btn-primary" type="submit" value="Add">

    </form>

</div>

<!- ========================================================================================== ->

<h3>Metadata Mappings</h3>
<table class="table table-bordered table-striped">
    <thead>
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
    foreach ($metadataMappings as $metadataMapping):?>
        <tr>
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
                    <input class="btn btn-danger" type="submit" value="delete"/>
                </form>
            </td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>


<div>
    <form class="form-horizontal" method="post" action="<?php echo URL::base() . 'vocabulary/mappings/manager/addmetadata'; ?>">
        <?php

        $models = $templateData["models"];
        $terms_properties = $templateData["terms_properties"];
        $metadata = $templateData["metadata"];

        ?>
        <fieldset class="fieldset">
            <legend>Add new metadata mapping</legend>
            <div class="control-group">
                <label for="metadata_id" class="control-label">Metadata</label>

                <div class="controls">
                    <select name='metadata_id' id="metadata_id">
                        <?php foreach ($metadata as $field): ?>
                            <option value="<?php echo $field->id?>"><?php echo $field->label?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label for="m-term-id" class="control-label">Term</label>

                <div class="controls">
                    <select name='term_id' id="m-term-id">
                        <?php foreach ($terms_properties as $id => $term): ?>
                            <option title="<?php echo $term["uri"];?>" value="<?php echo $id?>"><?php echo $term["label"];?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label for="m-type" class="control-label">Type</label>

                <div class="controls">
                    <select name='type' id="m-type">
                        <option value="property">Property</option>
                        <option value="rel">Reference</option>
                        <option value="rev">Reverse reference</option>
                    </select>
                </div>
            </div>
        </fieldset>

        <input class="btn btn-primary" type="submit" value="Add">

    </form>

</div>


</div>