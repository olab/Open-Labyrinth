<?php if (isset($templateData['map'])) { ?>
    <table width="100%" height="100%" cellpadding="6">
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('Add Counter'); ?></h4>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff"><td align="left">
                            <form id="form1" name="form1" method="post" action="<?php echo URL::base().'counterManager/saveNewCounter/'.$templateData['map']->id; ?>">

                                <table bgcolor="#ffffff" cellpadding="6" width="80%">
                                    <tr><td><p>counter name</p></td><td colspan="2"><input type="text" name="cName" size="40" value=""></td></tr>
                                    <tr><td><p>counter description (optional)</p></td><td colspan="2"><textarea name="cDesc" rows="6" cols="40"></textarea></td></tr>
                                    <tr><td><p>counter image (optional)</p></td><td colspan="2">
                                            <select name="cIconId">
                                                <option value="" selected="">no image</option>
                                                <?php if(isset($templateData['images']) and count($templateData['images']) > 0) { ?>
                                                    <?php foreach($templateData['images'] as $image) { ?>
                                                        <option value="<?php echo $image->id; ?>"><?php echo $image->name; ?> (ID:<?php echo $image->id; ?>)</option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </td></tr>
                                    <tr><td><p>starting value (optional)</p></td><td><input type="text" name="cStartV" size="4" value=""></td><td></td></tr>
                                    <tr><td><p>visible</p></td><td><select name="cVisible"><option value="1" selected="">show</option><option value="0">don't show</option></select></td><td></td></tr>
                                    <tr><td colspan="3"><input type="submit" name="Submit" value="submit"></td></tr>
                                </table>
                            </form>
                            <br>
                            <br>
                        </td></tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>