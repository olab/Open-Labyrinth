<script type="text/javascript">
    var extras = <?php echo json_encode($templateData["extras"]);?>;

    $(document).ready(function () {
        $("#metadata-type").change(
            function () {
                var selectedType = $(this).val();

                if(extras.hasOwnProperty(selectedType)){

                    $(".extras").remove();
                    for(var i=0; i<extras[selectedType].length;i++){

                        $("#addNew").append('<div class="control-group extras"><label class="control-label" for="'+extras[selectedType][i]+'">'+extras[selectedType][i]+'</label><div class="controls"><input type="text" id="'+extras[selectedType][i]+'" name="extras['+extras[selectedType][i]+']"/> </div></div>');

                    }


                }
            }
        )
    });


</script>
<h1>Metadata Manager</h1>
<h4>Metadata Fields</h4>
<table class="table table-striped table-hover" style="table-layout: fixed; word-wrap: break-word;">
    <colgroup>
        <col style="width: 5%" />
        <col style="width: 15%" />
        <col style="width: 10%" />
        <col style="width: 10%" />
        <col style="width: 10%" />
        <col style="width: 10%" />
        <col style="width: 10%" />
        <col style="width: 10%" />
        <col style="width: 10%" />
    </colgroup>
    <thead >
    <tr>
        <th>Identifier</th>
        <th>Name</th>
        <th>Destination Model</th>
        <th>Type</th>
        <th>Label</th>
        <th>Comment</th>
        <th>Cardinality</th>
        <th>Extra Options</th>
        <th>Operations</th>
    </tr>

    </thead>

    <tbody>


    <?php


    $metadata = $templateData["metadata"];
if(isset($metadata) and count($metadata)>0){
    foreach ($metadata as $field):?>
        <tr>
        <td>
            <?php echo $field->id; ?>
        </td>
        <td>
            <?php echo $field->name; ?>
        </td>
        <td>
            <?php echo $field->model; ?>
        </td>
        <td>
            <?php echo $field->type; ?>
        </td>
        <td>
            <?php echo $field->label; ?>
        </td>
        <td>
            <?php echo $field->comment; ?>
        </td>
        <td>
            <?php echo $field->cardinality; ?>
        </td>
        <td>
            <ul>
            <?php
            $extras = json_decode($field->options);
            if(isset($extras))
                foreach ($extras as $extra => $value) {
                    echo "<li>".$extra.": ".$value."</li>";
                }

             ?>
            </ul>
        </td>
        <td>
            <form method="post" action="<?php echo URL::base() . 'metadata/manager/delete'; ?>">
                <input type="hidden" name="name" value="<?php echo $field->name; ?>"/>
                <button class="btn btn-danger" type="submit"><i class="icon-trash"></i> Delete</button>
            </form>
        </td>
        </tr>
    <?php endforeach;?>

<?php } else{?>
    <tr class="info"><td colspan="9">There are no metadata defined yet. You may add a metadata field, using the form below</td></tr>

<?php } ?>


    </tbody>
</table>


<form method="post" class="form-horizontal" action="<?php echo URL::base() . 'metadata/manager/add'; ?>">
    <fieldset class="fieldset" id="addNew">
        <legend>Add New</legend>
        <div class="control-group">
            <label class="control-label" for="name">Name</label>

            <div class="controls">
                <?php



                $models = $templateData["models"];

                ?>
                <td>
                    <input type="text" id="name" name="name"/>

            </div>

        </div>
        <div class="control-group">
            <label class="control-label" for="model">Destination Model</label>

            <div class="controls">
                <select id="model" name='model'>
                    <?php foreach ($models as $key => $model): ?>
                        <option value="<?php echo $key ?>"><?php echo $model?></option>
                    <?php endforeach;?>
                </select>
            </div>

        </div>
        <div class="control-group">
            <label class="control-label" for="metadata-type">Type</label>

            <div class="controls">
                <select name='type' id="metadata-type">
                    <option value="stringrecord">Short text</option>
                    <option value="textrecord">Rich Text</option>
                    <option value="daterecord">Date</option>
                    <option value="referencerecord">Entity with URI from a list</option>
                    <option value="skosrecord">Class from a SKOS classification</option>
                    <option value="inlineobjectrecord">Complex object defined inline</option>
                </select>
            </div>

        </div>
        <div class="control-group">
            <label class="control-label" for="label">Label</label>

            <div class="controls">
                <input type="text" id="label" name="label"/>
            </div>

        </div>
        <div class="control-group">
            <label class="control-label" for="comment">Comment</label>

            <div class="controls">
                <input type="text" id="comment" name="comment"/>
            </div>

        </div>
        <div class="control-group">
            <label class="control-label" for="cardinality">Cardinality</label>

            <div class="controls">
                <select name='cardinality' id="cardinality">
                    <option value="1">Single value</option>
                    <option value="n">Multiple values</option>
                </select>
            </div>

        </div>

    </fieldset>
<div class="form-actions">
    <input class="btn btn-primary" type="submit" value="Add">
    </div>
</form>

