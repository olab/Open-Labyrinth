<div>
    <h2>Metadata Manager</h2>
    <table border="1">
        <thead><tr><td colspan="9">Metadata Fields</td></tr>
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
                        <input type="submit" value="delete"/>
                    </form>
                </td>
            </tr>
            <?php endforeach;?>


        </tbody>
    </table>

    <div><h4>Add New</h4>
        <form method="post" action="<?php echo URL::base() . 'metadata/manager/add'; ?>">
        <table border="1">
            <thead>
            <tr>
                <td>Name</td>
                <td>Destination Model</td>
                <td>Type</td>
                <td>Label</td>
                <td>Comment</td>
                <td>Cardinality</td>
                <td>Extra Options</td>
            </tr>

            </thead>

            <tbody>
            <tr>

                <?php



                $models = $templateData["models"];

             ?>

                    <td>
                       <input type="text" name="name"/>
                    </td>
                    <td>
                        <select name='model'>
                            <?php foreach ($models as $key=>$model):?>
                            <option value="<?php echo $key?>"><?php echo $model?></option>
                            <?php  endforeach;?>
                        </select>
                    </td>
                    <td>
                        <select name='type'>
                            <option value="stringrecord">String</option>
                            <option value="textrecord">Text</option>
                            <option value="daterecord">Date</option>
                            <option value="listrecord">List</option>
                            <option value="referencerecord">Entity with URI from a list</option>
                            <option value="skosrecord">Class from a SKOS classification</option>
                            <option value="inlineobjectrecord">Complex object defined inline</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="label"/>
                    </td>
                    <td>
                        <input type="text" name="comment"/>
                    </td>
                    <td>
                        <select name='cardinality'>
                            <option value="1">Sigle value</option>
                            <option value="n">Multiple values</option>
                        </select>
                    </td>
                    <td>
                        <textarea name="options"></textarea>
                    </td>


            </tr>
            </tbody>
        </table>
            <input type="submit" value="Add">

        </form>

    </div>
</div>