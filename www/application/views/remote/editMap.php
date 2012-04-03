<?php if (isset($templateData['service'])) { ?>
    <table width="100%" bgcolor="#ffffff"><tr><td>
                <table width="100%" bgcolor="#ffffff"><tr><td>
                            <h4>Labyrinths&nbsp;1</h4>
                            <p>service name&nbsp;: <?php echo $templateData['service']->name; ?></p>
                            <p>service IP mask: <?php echo $templateData['service']->ip; ?></p>
                            <p>Labyrinths:</p>
                            <?php if(count($templateData['service']->maps) > 0) { ?>
                            <?php foreach($templateData['service']->maps as $map) { ?>
                            <p><?php echo $map->map->name; ?> (<?php echo $map->map->id; ?>) [<a href="<?php echo URL::base(); ?>remoteServiceManager/deleteMap/<?php echo $templateData['service']->id; ?>/<?php echo $map->id; ?>">delete</a>]</p>
                            <?php } ?>
                            <?php } ?>
                            <hr>
                            <form id="form1" name="form1" method="post" action="<?php echo URL::base(); ?>remoteServiceManager/addMap/<?php echo $templateData['service']->id; ?>">
                                <table width="100%" border="0" cellspacing="0" cellpadding="4">
                                    <tr>
                                        <td><p>select a Labyrinth to add to this service</p></td>
                                        <td><select name="mapid" size="1">
                                                <?php if(isset($templateData['maps']) and count($templateData['maps']) > 0) { ?>
                                                <?php foreach($templateData['maps'] as $map) { ?>
                                                <option value="<?php echo $map->id; ?>"><?php echo $map->name; ?></option>
                                                <?php } ?>
                                                <?php } ?> 
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><p>&nbsp;</p></td>
                                        <td><input type="submit" name="Submit" value="submit"></td>
                                    </tr>
                                </table>
                            </form>
                            <hr>
                            <p><a href="remoteservices.asp">remote services</a></p>
                        </td></tr></table>
            </td></tr></table>
<?php } ?>