<h1>User: <?php echo $templateData["user"]->nickname ?></h1>
<table class="table table-border table-striped">
    <thead>
    <tr>
        <th></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>nickname</td>
        <td><?php echo $templateData["user"]->nickname ?></td>
    </tr>
    <tr>
        <td>email</td>
        <td><?php echo $templateData["user"]->email ?></td>
    </tr>

    <?php


    $vars = $templateData["user"]->as_array();

    foreach ($vars as $property):?>    <?php if (Helper_Controller_Metadata::isMetadataRecord($property)): ?>
        <tr>
            <?php $view =  Helper_Controller_Metadata::getView($property); ?>
            <td><?php echo $view["label"]?></td>
            <td>

                <?php echo $view["body"]?>
            </td>
        </tr>  <?php endif; ?>
    <?php endforeach;?>
    </tbody>
</table>