<?php if (isset($templateData['map'])) { ?>
    <table width="100%" height="100%" cellpadding="6">
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('avatars for Labyrinth "') . $templateData['map']->name . '"'; ?></h4>
                <p>The following avatars have been created for this Labyrinth. Click the [edit] link to change their appearance. Copy and paste the wiki link (that looks like [[AV:1234567]]) into the content for a node.</p>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff">
                        <td>
                            <?php if (isset($templateData['avatars']) and count($templateData['avatars']) > 0) { ?>
                                <table>
                                    <?php foreach($templateData['avatars'] as $avatar) { ?>
                                    <tr>
                                        <td>
                                            <input type="text" size="20" value="[[AV:<?php echo $avatar->id; ?>]]">
                                        </td>
                                        <td align="center" valign="middle">
                                            <p><object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="300" height="300" id="avatar_<?php echo $avatar->id; ?>" align="middle">
                                                    <param name="allowScriptAccess" value="sameDomain">
                                                    <param name="movie" value="<?php echo URL::base(); ?>documents/avatar.swf">
                                                    <param name="quality" value="high">
                                                    <param name="flashVars" value="fSkin=<?php echo $avatar->skin_1; ?>&fSkinOut=<?php echo $avatar->skin_2; ?>&fBkd=<?php echo $avatar->bkd; ?>&fCloth=<?php echo $avatar->cloth; ?>&fNose=<?php echo $avatar->nose; ?>&fHair=<?php echo $avatar->hair; ?>&fAccessory1=<?php echo $avatar->accessory_1; ?>&fAccessory2=<?php echo $avatar->accessory_2; ?>&fAccessory3=<?php echo $avatar->accessory_3; ?>&fEnvironment=<?php echo $avatar->environment; ?>&fOutfit=<?php echo $avatar->outfit; ?>&fMouth=<?php echo $avatar->mouth; ?>&fSex=<?php echo $avatar->sex; ?>&fBubble=<?php echo $avatar->bubble; ?>&fBubbleText=<?php echo $avatar->bubble_text; ?>&fAge=<?php echo $avatar->age; ?>&fEyes=<?php echo $avatar->eyes; ?>&fWeather=<?php echo $avatar->weather; ?>&fHairColor=<?php echo $avatar->hair_color; ?>">
                                                    <embed src="<?php echo URL::base(); ?>documents/avatar.swf" flashvars="fSkin=<?php echo $avatar->skin_1; ?>&fSkinOut=<?php echo $avatar->skin_2; ?>&fBkd=<?php echo $avatar->bkd; ?>&fCloth=<?php echo $avatar->cloth; ?>&fNose=<?php echo $avatar->nose; ?>&fHair=<?php echo $avatar->hair; ?>&fAccessory1=<?php echo $avatar->accessory_1; ?>&fAccessory2=<?php echo $avatar->accessory_2; ?>&fAccessory3=<?php echo $avatar->accessory_3; ?>&fEnvironment=<?php echo $avatar->environment; ?>&fOutfit=<?php echo $avatar->outfit; ?>&fMouth=<?php echo $avatar->mouth; ?>&fSex=<?php echo $avatar->sex; ?>&fBubble=<?php echo $avatar->bubble; ?>&fBubbleText=<?php echo $avatar->bubble_text; ?>&fAge=<?php echo $avatar->age; ?>&fEyes=<?php echo $avatar->eyes; ?>&fWeather=<?php echo $avatar->weather; ?>&fHairColor=<?php echo $avatar->hair_color; ?>" quality="high" bgcolor="#ffffff" width="300" height="300" name="avatar_<?php echo $avatar->id; ?>" align="middle" allowscriptaccess="sameDomain" allowfullscreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">
                                                </object>
                                            </p>
                                        </td>
                                        <td>
                                            <p>[<a href="<?php echo URL::base().'avatarManager/editAvatar/'.$templateData['map']->id.'/'.$avatar->id; ?>">edit</a>] [<a href="<?php echo URL::base().'avatarManager/duplicateAvatar/'.$templateData['map']->id.'/'.$avatar->id; ?>">duplicate</a>] [<a href="<?php echo URL::base().'avatarManager/deleteAvatar/'.$templateData['map']->id.'/'.$avatar->id; ?>">delete</a>]</p></td>
                                    </tr>
                                    <tr><td colspan="3"><hr></td></tr>
                                    <?php } ?>
                                </table>
                            <?php } else { ?>
                                <p>there are no avatars in this Labyrinth</p>
                            <?php } ?>
                            <p><a href="<?php echo URL::base().'avatarManager/addAvatar/'.$templateData['map']->id; ?>">add an avatar</a></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>



