<?php if (isset($templateData['map'])) { ?>
    <table width="100%" height="100%" cellpadding='6'>
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('Labyrinth information') . ' "' . $templateData['map']->name . '"'; ?></h4>
                <table width="100%" border="0" cellspacing="0" cellpadding="4" bgcolor="#ffffff">
                    <tr>
                        <td width="33%" align="right"><p><?php echo __('title'); ?></p></td>
                        <td width="50%" align="left"><p><?php echo $templateData['map']->name; ?>&nbsp;</p></td>
                    </tr>
                    <tr>
                        <td align="right"><p><?php echo __('authors'); ?></p></td>
                        <td align="left"><p>
                                <?php if(count($templateData['map']->authors) > 0) { ?>
                                <?php foreach($templateData['map']->authors as $author) { ?>
                                    <?php echo $author->user->nickname; ?> (<?php echo $author->user->username; ?>), 
                                <?php } ?>
                                <?php } ?>
                                &nbsp;</p></td>
                    </tr>
                    <tr>
                        <td align="right"><p><?php echo __('keywords'); ?></p></td>
                        <td align="left"><p><?php echo $templateData['map']->keywords; ?>&nbsp;</p></td>
                    </tr>
                    <tr>
                        <td align="right"><p><?php echo __('Labyrinth type'); ?></p></td>
                        <td align="left"><p><?php echo $templateData['map']->type->name; ?>&nbsp;</p></td>
                    </tr>
                    <tr>
                        <td align="right"><p><?php echo __('security'); ?></p></td>
                        <td align="left"><p><?php echo $templateData['map']->security->name; ?>&nbsp;</p></td>
                    </tr>
                    <tr>
                        <td align="right"><p><?php echo __('number of nodes'); ?></p></td>
                        <td align="left"><p>
                                <?php 
                                if(count($templateData['map']->nodes) > 0) { 
                                    echo count($templateData['map']->nodes);
                                } else {
                                    echo '0';
                                }
                                ?>
                                &nbsp;</p></td>
                    </tr>
                    <tr>
                        <td align="right"><p><?php echo __('number of links'); ?></p></td>
                        <td align="left"><p>0&nbsp;</p></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>

