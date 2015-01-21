<div>
<script type="text/javascript">

    var properties =    <?php echo json_encode($templateData["properties"]);?>
</script>
<script type="text/javascript" src='<?php echo URL::base(); ?>scripts/jquery/jquery-ui-1.9.1.custom.min.js'></script>


<script type="text/javascript" src='<?php echo URL::base(); ?>scripts/olab/inputHandler.js'></script>


<h1>Mappings Manager</h1>
<div class="tabbable">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#pane1" data-toggle="tab">Class Mappings</a></li>
        <li><a href="#pane2" data-toggle="tab">Common Property Mappings</a></li>
        <li><a href="#pane3" data-toggle="tab">Metadata Mappings</a></li>
    </ul>
    <div class="tab-content">
        <div id="pane1" class="tab-pane active">
            <h3>Class Mappings</h3>
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Identifier</th>
                    <th>Class Name</th>
                    <th>Term</th>
                    <th>Operations</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $classMappings = $templateData["classMappings"];
                if (isset($classMappings)and count($classMappings) > 0):
                    foreach ($classMappings as $classMapping):?>
                        <tr>
                            <td>
                                <?php echo $classMapping->id; ?>
                            </td>
                            <td>

                                <?php echo $classMapping->class; ?>
                            </td>
                            <td>
                                <a target="_blank" href="<?php echo $classMapping->term->getFullRepresentation(); ?>">
                                    <?php echo $classMapping->term->term_label; ?></a>

                            </td>
                            <td>
                                <form method="post" action="<?php echo URL::base() . 'vocabulary/mappings/manager/deleteclass'; ?>">
                                    <input type="hidden" name="id" value="<?php echo $classMapping->id; ?>"/>
                                    <button class="btn btn-danger" type="submit"><i class="icon-trash"></i>Delete</button>

                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr class="info">
                        <td colspan="4">There are no class mappings yet. You may add one by using the form below.</td>
                    </tr>


                <?php endif; ?>
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
                                        <option value="<?php echo $key ?>"><?php echo $model ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="cl-term" class="control-label">Term</label>

                            <div class="controls">
                                <select name='term_id' id="cl-term">
                                    <?php foreach ($terms_classes as $vocab => $terms_classes_terms) { ?>
                                        <optgroup label="<?php echo $vocab;?>">
                                            <?php foreach ($terms_classes_terms as $id => $term) { ?>
                                                <option title="<?php echo $term["uri"]; ?>"
                                                        value="<?php echo $id ?>"><?php echo $term["label"]; ?></option>
                                            <?php } ?></optgroup>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                    <div class="form-actions">
                        <input class="btn btn-primary" type="submit" value="Add">
                    </div>
                </form>

            </div>

        </div>
        <div id="pane2" class="tab-pane">
            <h3>Common Property Mappings</h3>
            <table class="table table-bordered table-striped">
                <thead>

                <tr>
                    <th>Identifier</th>
                    <th>Class Name</th>
                    <th>Property Name</th>
                    <th>Term</th>
                    <th>Relation Type</th>
                    <th>Operations</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $legacyPropertyMappings = $templateData["legacyPropertyMappings"];
                if (isset($legacyPropertyMappings)and count($legacyPropertyMappings) > 0):
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
                                <a target="_blank" href="<?php echo $legacyPropertyMapping->term->getFullRepresentation(); ?>">
                                    <?php echo $legacyPropertyMapping->term->term_label; ?></a>
                            </td>
                            <td>
                                <?php echo $legacyPropertyMapping->type; ?>
                            </td>
                            <td>
                                <form method="post"
                                      action="<?php echo URL::base() . 'vocabulary/mappings/manager/deletelegacy'; ?>">
                                    <input type="hidden" name="id" value="<?php echo $legacyPropertyMapping->id; ?>"/>
                                    <button class="btn btn-danger" type="submit"><i class="icon-trash"></i> Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php else: ?>
                    <tr class="info">
                        <td colspan="6">There are no common property mappings yet. You may add one by using the form below.</td>
                    </tr>


                <?php endif; ?>
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
                                        <option value="<?php echo $key ?>"><?php echo $model ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="legacy-property" class="control-label">Property</label>

                            <div class="controls">
                                <select id="legacy-property" name="property"></select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="legacy-term" class="control-label">Term</label>

                            <div class="controls">
                                <select name='term_id' id="legacy-term">
                                    <?php foreach ($terms_properties as $vocab => $terms_properties_terms) { ?>
                                        <optgroup label="<?php echo $vocab;?>">
                                            <?php  foreach ($terms_properties_terms as $id => $term) { ?>
                                                <option title="<?php echo $term["uri"]; ?>"
                                                        value="<?php echo $id ?>"><?php echo $term["label"]; ?></option>
                                            <?php } ?>
                                        </optgroup>
                                    <?php } ?>
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

                    <div class="form-actions">
                        <input class="btn btn-primary" type="submit" value="Add">
                    </div>
                </form>

            </div>

            <!-- ========================================================================================== -->

        </div>
        <div id="pane3" class="tab-pane">
            <h3>Metadata Mappings</h3>
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Identifier</th>
                    <th>Metadata Name</th>
                    <th>Term</th>
                    <th>Relation Type</th>
                    <th>Operations</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $metadataMappings = $templateData["metadataMappings"];
                if (isset($metadataMappings)and count($metadataMappings) > 0):
                    foreach ($metadataMappings as $metadataMapping):?>
                        <tr>
                            <td>
                                <?php echo $metadataMapping->id; ?>
                            </td>
                            <td>
                                <?php echo $metadataMapping->metadata->label; ?>
                            </td>
                            <td>
                                <a target="_blank" href="<?php echo $metadataMapping->term->getFullRepresentation(); ?>">

                                    <?php echo $metadataMapping->term->term_label; ?></a>

                            </td>
                            <td>
                                <?php echo $metadataMapping->type; ?>
                            </td>
                            <td>
                                <form method="post"
                                      action="<?php echo URL::base() . 'vocabulary/mappings/manager/deletemetadata'; ?>">
                                    <input type="hidden" name="id" value="<?php echo $metadataMapping->id; ?>"/>
                                    <button class="btn btn-danger" type="submit"><i class="icon-trash"></i> Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr class="info">
                        <td colspan="5">There are no metadata mappings yet. You may add one by using the form below.</td>
                    </tr>


                <?php endif; ?>
                </tbody>
            </table>


            <div>
                <form class="form-horizontal" method="post"
                      action="<?php echo URL::base() . 'vocabulary/mappings/manager/addmetadata'; ?>">
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
                                        <option value="<?php echo $field->id ?>"><?php echo $field->label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="m-term-id" class="control-label">Term</label>

                            <div class="controls">
                                <select name='term_id' id="m-term-id">
                                    <?php foreach ($terms_properties as $vocab => $terms_properties_terms) { ?>
                                        <optgroup label="<?php echo $vocab;?>">
                                            <?php foreach ($terms_properties_terms as $id => $term) { ?>
                                                <option title="<?php  echo $term["uri"]; ?>"
                                                        value="<?php echo $id ?>"><?php echo $term["label"]; ?></option>
                                            <?php } ?>
                                        </optgroup>
                                    <?php } ?>
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
                    <div class="form-actions">
                        <input class="btn btn-primary" type="submit" value="Add">
                    </div>
                </form>

            </div>
        </div>
    </div><!-- /.tab-content -->
</div><!-- /.tabbable -->

<!-- ====================================================================================================== -->




</div>