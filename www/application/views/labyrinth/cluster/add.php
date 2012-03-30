<?php if (isset($templateData['map'])) { ?>
    <table width="100%" height="100%" cellpadding='6'>
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('data clusters for Labyrinth "') . $templateData['map']->name . '"'; ?></h4>
                <table bgcolor='#ffffff'>
                    <tr>
                        <td>
                            <form method='post' action='<?php echo URL::base(); ?>clusterManager/saveNewDam/<?php echo $templateData['map']->id; ?>'>
                            <p>Data cluster name: <input type='text' name='damname'><input type='submit' value='add' /></p>
                            </form>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>

