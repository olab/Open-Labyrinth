<table width="100%" bgcolor="#ffffff">
    <tr>
        <td>
            <h4>remote services</h4>
            <p>these are XML connectors to allow you to run Labyrinths remotely in other systems or contexts. Each service is mapped to a single server IP address and can have Labyrinths mapped to it. There are two service calls:</p>
            <p>- remote.asp - this will list the available Labyrinths registered to this service</p>
            <p>- mnode.asp?id=1176&amp;mode=remote - change x to the starting node in a Labyrinth, mode should always be set to remote</p>
            <hr>
            <?php if(isset($templateData['services']) and count($templateData['services']) > 0) { ?>
            <?php foreach($templateData['services'] as $service) { ?>
            <p>'<?php echo $service->name; ?>' : <a href="<?php echo URL::base(); ?>remoteServiceManager/editService/<?php echo $service->id; ?>">edit service</a> - <a href="<?php echo URL::base(); ?>remoteServiceManager/editServiceMap/<?php echo $service->id; ?>">add/edit Labyrinths</a></p>
            <?php } ?>
            <?php } ?>
            <hr>
            <p><a href="<?php echo URL::base(); ?>remoteServiceManager/addService">add a service</a></p>
        </td>
    </tr>
</table>