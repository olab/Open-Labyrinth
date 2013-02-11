    <h1>Metadata Manager</h1>
    <h4>Metadata Fields</h4>
    <table class="table table-bordered table-striped" >
        <thead></tr>
        <tr>
            <td>Identifier</td>
            <td>Name</td>
            <td>Destination Model</td>
            <td>Type</td>
            <td>Label</td>
            <td>Comment</td>
            <td>Cardinality</td>
            <td>Extra Options</td>
            <td>Operations</td>
        </tr>

        </thead>

        <tbody>


            <?php


            $metadata = $templateData["metadata"];

            foreach ($metadata as $field):?><tr>
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
                    <?php echo $field->options; ?>
                </td>
                <td>
                    <form method="post" action="<?php echo URL::base() . 'metadata/manager/delete'; ?>">
                        <input type="hidden" name="name" value="<?php echo $field->name; ?>"/>
                        <input class="btn btn-danger" type="submit" value="delete"/>
                    </form>
                </td>
            </tr>
            <?php endforeach;?>


        </tbody>
    </table>


        <form method="post" class="form-horizontal" action="<?php echo URL::base() . 'metadata/manager/add'; ?>">
            <fieldset class="fieldset">
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
                            <?php foreach ($models as $key=>$model):?>
                                <option value="<?php echo $key?>"><?php echo $model?></option>
                            <?php  endforeach;?>
                        </select>
                    </div>

                </div>
                <div class="control-group">
                    <label class="control-label" for="type">Type</label>

                    <div class="controls">
                        <select name='type' id="type">
                            <option value="stringrecord">String</option>
                            <option value="textrecord">Text</option>
                            <option value="daterecord">Date</option>
                            <option value="listrecord">List</option>
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
                            <option value="1">Sigle value</option>
                            <option value="n">Multiple values</option>
                        </select>
                    </div>

                </div>
                <div class="control-group">
                    <label class="control-label" for="options">Extra Options</label>

                    <div class="controls">
                        <textarea id="options" name="options"></textarea>
                    </div>

                </div>

            </fieldset>

            <input class="btn btn-primary" type="submit" value="Add">

        </form>

