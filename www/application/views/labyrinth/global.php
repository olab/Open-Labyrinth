<?php if (isset($templateData['map'])) { ?>
    <table width="100%" height="100%" cellpadding='6'>
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('edit Labyrinth ') . '"' . $templateData['map']->name . '"'; ?></h4>
                <table bgcolor="#ffffff"><tr><td align="left">
                            <form id="globalMapFrom" name="globalMapFrom" method="post" action=<?php echo URL::base().'labyrinthManager/saveGlobal/'.$templateData['map']->id; ?>>
                                <table width="100%" border="0" cellspacing="0" cellpadding="4">
                                    <tr>
                                        <td width="33%" align="right"><p><?php echo __('title'); ?></p></td>
                                        <td width="50%"><p><input name="title" type="text" id="mtitle" size="40" value="<?php echo $templateData['map']->name; ?>"></p></td>
                                    </tr>
                                    <tr>
                                        <td align="right"><p><?php echo __('description'); ?></p></td>
                                        <td align="left"><p><textarea name="description" cols="40" rows="5" id="mdesc"><?php echo $templateData['map']->abstract; ?></textarea></p></td>
                                    </tr>
                                    <tr>
                                        <td align="right"><p><?php echo __('contributors'); ?> [<a href=<?php echo URL::base().'labyrinthManager/addContributor/'.$templateData['map']->id; ?>><?php echo __('add'); ?></a>]</p></td>
                                        <td align="left" nowrap="">
                                            <?php if(isset($templateData['contributors'])) { ?>
                                            <?php foreach($templateData['contributors'] as $contributor) { ?>
                                            <table bgcolor="#dddddd">
                                                <?php if(isset($templateData['contributor_roles'])) { ?>
                                                <tr>
                                                    <td>
                                                        <p><?php echo __('role'); ?>:</p>
                                                    </td>
                                                    <td>
                                                        <select name="role_<?php echo $contributor->id; ?>">
                                                            <?php foreach($templateData['contributor_roles'] as $role) { ?>
                                                                <option value="<?php echo $role->id; ?>" <?php if($role->id == $contributor->role_id) echo 'selected=""'; ?>><?php echo $role->name; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                    <td><p><a href="<?php echo URL::base().'labyrinthManager/deleteContributor/'.$templateData['map']->id.'/'.$contributor->id; ?>">[delete]</a></p></td>
                                                </tr>
                                                <?php } ?>
                                                <tr>
                                                    <td>
                                                        <p><?php echo __('name'); ?>:</p>
                                                    </td>
                                                    <td>
                                                        <input type="text" size="50" name="cname_<?php echo $contributor->id; ?>" value="<?php echo $contributor->name; ?>">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <p><?php echo __('organization'); ?>:</p>
                                                    </td>
                                                    <td>
                                                        <input type="text" size="50" name="cnorg_<?php echo $contributor->id; ?>" value="<?php echo $contributor->organization; ?>">
                                                    </td>
                                                </tr>
                                            </table>
                                            <br/>
                                            <?php } ?>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right"><p><?php echo __('registered Labyrinth authors'); ?>:</p></td>
                                        <td align="left">
                                            <p>
                                                <?php if (isset($templateData['regUsers'])) { ?>
                                                    <?php foreach ($templateData['regUsers'] as $user) { ?>
                                                        <?php echo $user->nickname . ', '; ?>
                                                    <?php }
                                                } ?>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right"><p>keywords</p></td>
                                        <td align="left"><p>
                                                <input name="keywords" type="text" id="keywords" size="40" value="<?php echo $templateData['map']->keywords; ?>">
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <?php if (isset($templateData['types'])) { ?>
                                            <td align="right"><p>Labyrinth type</p></td>
                                            <td align="left"><p>
                                                    <label>
                                                        <select name="type">
                                                            <?php foreach ($templateData['types'] as $type) { ?>
                                                                <option value="<?php echo $type->id; ?>" <?php if ($type->id == $templateData['map']->type_id) echo 'selected=""'; ?> ><?php echo $type->name; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </label>
                                                </p>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <?php if (isset($templateData['skins'])) { ?>
                                            <td align="right"><p>Labyrinth Skin</p></td>
                                            <td align="left"><p>
                                                    <label>
                                                        <select name="skin">
                                                            <?php foreach ($templateData['skins'] as $skin) { ?>
                                                                <option value="<?php echo $skin->id; ?>" <?php if ($skin->id == $templateData['map']->skin_id) echo 'selected=""'; ?>><?php echo $skin->name; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </label>
                                                </p>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <td align="right"><p>timing</p></td>
                                        <td align="left"><p>
                                                timing off<input type="radio" name="timing" value=0 <?php if (!$templateData['map']->timing) echo 'checked=""'; ?>> : timing on <input type="radio" name="timing" value=1 <?php if ($templateData['map']->timing) echo 'checked=""'; ?>>
                                                <br><br>
                                                time delta (seconds)<input name="delta_time" type="text" id="delta_time" value="<?php if ($templateData['map']->delta_time > 0 and $templateData['map']->timing) echo $templateData['map']->delta_time; ?>" size="6">
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <?php if (isset($templateData['securities'])) { ?>
                                            <td align="right"><p>security</p></td>
                                            <td align="left"><p>
                                                    <?php foreach ($templateData['securities'] as $security) { ?>
                                                        <input type="radio" name="security" value=<?php echo $security->id; ?> <?php if ($security->id == $templateData['map']->security_id) echo 'checked=""'; ?>><?php echo $security->name; ?><br>
                                                    <?php } ?>
                                                        <a href="<?php echo URL::base().'labyrinthManager/editKeys/'.$templateData['map']->id; ?>">edit keys</a>
                                                    <br><br></p>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <?php if (isset($templateData['sections'])) { ?>
                                            <td align="right"><p>section browsing</p></td>
                                            <td align="left"><p> 
                                                    <?php foreach ($templateData['sections'] as $section) { ?>
                                                        <?php echo $section->name; ?><input type="radio" name="section" value=<?php echo $section->id; ?> <?php if ($section->id == $templateData['map']->section_id) echo 'checked=""'; ?>> |
                                                    <?php } ?>
                                                    <br><br></p>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <td align="left"><p>&nbsp;</p></td>
                                        <td align="left"><p>
                                                <label>
                                                    <input type="submit" name="GlobalSubmit" value="<?php echo __('submit'); ?>">
                                                </label>
                                            </p></td>
                                        <td align="left"><p>&nbsp;</p></td>
                                    </tr>
                                </table>
                            </form>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>
