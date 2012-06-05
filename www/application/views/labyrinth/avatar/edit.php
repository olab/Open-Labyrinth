<?php
/**
 * Open Labyrinth [ http://www.openlabyrinth.ca ]
 *
 * Open Labyrinth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Open Labyrinth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Open Labyrinth.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright Copyright 2012 Open Labyrinth. All Rights Reserved.
 *
 */
if (isset($templateData['map']) and isset($templateData['avatar'])) { ?>
<script type="text/javascript" language="Javascript" src="<?php echo URL::base() ?>scripts/picker.js"></script>
<script type="text/javascript" language="JavaScript">
    var cp = new ColorPicker('window'); // Popup window
    var cp2 = new ColorPicker(); // DIV style

    function OnChange(dropdown) {
        document.forms['avatar_form'].submit();
    }
</script>
    <table width="100%" height="100%" cellpadding="6">
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h3>Avatar Editor: "<?php echo $templateData['avatar']->id; ?>"</h3>
                <p>Preview: 
                    <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="300" height="300" id="avatar_<?php echo $templateData['avatar']->id; ?>" align="middle">
                        <param name="allowScriptAccess" value="sameDomain">
                        <param name="movie" value="<?php echo URL::base(); ?>documents/avatar.swf">
                        <param name="quality" value="high">
                        <param name="flashVars" value="fSkin=<?php echo $templateData['avatar']->skin_1; ?>&fSkinOut=<?php echo $templateData['avatar']->skin_2; ?>&fBkd=<?php echo $templateData['avatar']->bkd; ?>&fCloth=<?php echo $templateData['avatar']->cloth; ?>&fNose=<?php echo $templateData['avatar']->nose; ?>&fHair=<?php echo $templateData['avatar']->hair; ?>&fAccessory1=<?php echo $templateData['avatar']->accessory_1; ?>&fAccessory2=<?php echo $templateData['avatar']->accessory_2; ?>&fAccessory3=<?php echo $templateData['avatar']->accessory_3; ?>&fEnvironment=<?php echo $templateData['avatar']->environment; ?>&fOutfit=<?php echo $templateData['avatar']->outfit; ?>&fMouth=<?php echo $templateData['avatar']->mouth; ?>&fSex=<?php echo $templateData['avatar']->sex; ?>&fBubble=<?php echo $templateData['avatar']->bubble; ?>&fBubbleText=<?php echo $templateData['avatar']->bubble_text; ?>&fAge=<?php echo $templateData['avatar']->age; ?>&fEyes=<?php echo $templateData['avatar']->eyes; ?>&fWeather=<?php echo $templateData['avatar']->weather; ?>&fHairColor=<?php echo $templateData['avatar']->hair_color; ?>">
                        <embed src="<?php echo URL::base(); ?>documents/avatar.swf" flashvars="fSkin=<?php echo $templateData['avatar']->skin_1; ?>&fSkinOut=<?php echo $templateData['avatar']->skin_2; ?>&fBkd=<?php echo $templateData['avatar']->bkd; ?>&fCloth=<?php echo $templateData['avatar']->cloth; ?>&fNose=<?php echo $templateData['avatar']->nose; ?>&fHair=<?php echo $templateData['avatar']->hair; ?>&fAccessory1=<?php echo $templateData['avatar']->accessory_1; ?>&fAccessory2=<?php echo $templateData['avatar']->accessory_2; ?>&fAccessory3=<?php echo $templateData['avatar']->accessory_3; ?>&fEnvironment=<?php echo $templateData['avatar']->environment; ?>&fOutfit=<?php echo $templateData['avatar']->outfit; ?>&fMouth=<?php echo $templateData['avatar']->mouth; ?>&fSex=<?php echo $templateData['avatar']->sex; ?>&fBubble=<?php echo $templateData['avatar']->bubble; ?>&fBubbleText=<?php echo $templateData['avatar']->bubble_text; ?>&fAge=<?php echo $templateData['avatar']->age; ?>&fEyes=<?php echo $templateData['avatar']->eyes; ?>&fWeather=<?php echo $templateData['avatar']->weather; ?>&fHairColor=<?php echo $templateData['avatar']->hair_color; ?>" quality="high" bgcolor="#ffffff" width="300" height="300" name="avatar_<?php echo $templateData['avatar']->id; ?>" align="middle" allowscriptaccess="sameDomain" allowfullscreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">
                    </object>
                </p>
                <hr/>
                <form method="POST" action="<?php echo URL::base().'avatarManager/updateAvatar/'.$templateData['map']->id.'/'.$templateData['avatar']->id; ?>" name="avatar_form">
					<table width="100%" border="0" cellspacing="0" cellpadding="6">
                        <tr bgcolor="#FFFFFF">
                            <td><p>Sex</p></td><td>
                                <select name="avsex" size="1" onchange="OnChange();">
                                    <option value="A" <?php if($templateData['avatar']->sex == 'A') echo 'selected=""'; ?>>Male</option>
                                    <option value="B" <?php if($templateData['avatar']->sex == 'B') echo 'selected=""'; ?>>Female</option>
                                </select></td>
                            <td><p>Mouth Shape</p></td><td>
                                <select name="avmouth" size="1" onchange="OnChange();">
                                    <option value="A" <?php if($templateData['avatar']->mouth == 'A') echo 'selected=""'; ?>>Smile</option>
                                    <option value="B" <?php if($templateData['avatar']->mouth == 'B') echo 'selected=""'; ?>>Indifferent</option>
                                    <option value="C" <?php if($templateData['avatar']->mouth == 'C') echo 'selected=""'; ?>>Frown</option>
                                </select></td>
                        </tr>

                        <tr bgcolor="#EEEEEE">
                            <td><p>Age</p></td><td>
                                <select name="avage" size="1" onchange="OnChange();">
                                    <option value="A" <?php if($templateData['avatar']->age == 'A') echo 'selected=""'; ?>>20+</option>
                                    <option value="B" <?php if($templateData['avatar']->age == 'B') echo 'selected=""'; ?>>40+</option>
                                    <option value="C" <?php if($templateData['avatar']->age == 'C') echo 'selected=""'; ?>>60+</option>
                                </select></td>
                            <td><p>Eyes</p></td><td>
                                <select name="aveyes" size="1" onchange="OnChange();">
                                    <option value="A" <?php if($templateData['avatar']->eyes == 'A') echo 'selected=""'; ?>>Open</option>
                                    <option value="B" <?php if($templateData['avatar']->eyes == 'B') echo 'selected=""'; ?>>Closed</option>
                                </select></td>
                        </tr>

                        <tr bgcolor="#FFFFFF">
                            <td><p>Outfit</p></td><td>
                                <select name="avoutfit" size="1" onchange="OnChange();">
                                    <option value="A" <?php if($templateData['avatar']->outfit == 'A') echo 'selected=""'; ?>>Naked</option>
                                    <option value="B" <?php if($templateData['avatar']->outfit == 'B') echo 'selected=""'; ?>>Wooly Jumper</option>
                                    <option value="C" <?php if($templateData['avatar']->outfit == 'C') echo 'selected=""'; ?>>Shirt and Tie</option>
                                    <option value="D" <?php if($templateData['avatar']->outfit == 'D') echo 'selected=""'; ?>>Nurse Uniform</option>
                                    <option value="E" <?php if($templateData['avatar']->outfit == 'E') echo 'selected=""'; ?>>Scrubs (Blue)</option>
                                    <option value="F" <?php if($templateData['avatar']->outfit == 'F') echo 'selected=""'; ?>>Scrubs (Green)</option>
                                    <option value="G" <?php if($templateData['avatar']->outfit == 'G') echo 'selected=""'; ?>>Vest</option>
                                    <option value="H" <?php if($templateData['avatar']->outfit == 'H') echo 'selected=""'; ?>>Gown (White)</option>
                                    <option value="I" <?php if($templateData['avatar']->outfit == 'I') echo 'selected=""'; ?>>Pyjamas (Female)</option>
                                    <option value="J" <?php if($templateData['avatar']->outfit == 'J') echo 'selected=""'; ?>>Pyjamas (Male)</option>
                                    <option value="K" <?php if($templateData['avatar']->outfit == 'K') echo 'selected=""'; ?>>Doctor (Male)</option>
                                    <option value="L" <?php if($templateData['avatar']->outfit == 'L') echo 'selected=""'; ?>>Doctor (Female)</option>
                                    <option value="M" <?php if($templateData['avatar']->outfit == 'M') echo 'selected=""'; ?>>Pattern turtle neck</option>
                                    <option value="N" <?php if($templateData['avatar']->outfit == 'N') echo 'selected=""'; ?>>Black striped shirt</option>
                                    <option value="O" <?php if($templateData['avatar']->outfit == 'O') echo 'selected=""'; ?>>Winter jacket</option>
                                    <option value="P" <?php if($templateData['avatar']->outfit == 'P') echo 'selected=""'; ?>>V-neck</option>
                                    <option value="Q" <?php if($templateData['avatar']->outfit == 'Q') echo 'selected=""'; ?>>Fleece</option>
                                    <option value="R" <?php if($templateData['avatar']->outfit == 'R') echo 'selected=""'; ?>>Sweater</option>
                                </select></td>
                            <td><p>Cloth Color <a href="/help.asp?id=hex" target="_blank" title="Hex Color Values: FAQ">?</a></p></td>
                            <td><input type="text" name="avcloth" size="6" value="<?php echo $templateData['avatar']->cloth; ?>">
                                <a href="javascript:TCP.popup(document.forms['avatar_form'].elements['avcloth'])"><img width="15" height="13" border="0" alt="Click here to pick a color" src="<?php echo URL::base(); ?>images/cpiksel.gif"></a>      
                            </td>
                        </tr>

                        <tr bgcolor="#EEEEEE">
                            <td><p>Nose Type</p></td><td>
                                <select name="avnose" size="1" onchange="OnChange();">
                                    <option value="A" <?php if($templateData['avatar']->nose == 'A') echo 'selected=""'; ?>>Nostrils Only</option>
                                    <option value="B" <?php if($templateData['avatar']->nose == 'B') echo 'selected=""'; ?>>Petit</option>
                                    <option value="C" <?php if($templateData['avatar']->nose == 'C') echo 'selected=""'; ?>>Wide</option>
                                </select></td>
                            <td></td><td></td>
                        </tr>

                        <tr bgcolor="#FFFFFF">
                            <td><p>Hair Type</p></td><td>
                                <select name="avhair" size="1" onchange="OnChange();">
                                    <option value="A" <?php if($templateData['avatar']->hair == 'A') echo 'selected=""'; ?>>Bald</option>
                                    <option value="B" <?php if($templateData['avatar']->hair == 'B') echo 'selected=""'; ?>>Shaved</option>
                                    <option value="C" <?php if($templateData['avatar']->hair == 'C') echo 'selected=""'; ?>>Long/Blonde</option>
                                    <option value="D" <?php if($templateData['avatar']->hair == 'D') echo 'selected=""'; ?>>Short</option>
                                    <option value="E" <?php if($templateData['avatar']->hair == 'E') echo 'selected=""'; ?>>Curly</option>
                                    <option value="F" <?php if($templateData['avatar']->hair == 'F') echo 'selected=""'; ?>>Bob</option>
                                    <option value="G" <?php if($templateData['avatar']->hair == 'G') echo 'selected=""'; ?>>Long/Red</option>
                                    <option value="H" <?php if($templateData['avatar']->hair == 'H') echo 'selected=""'; ?>>Grandpa</option>
                                    <option value="I" <?php if($templateData['avatar']->hair == 'I') echo 'selected=""'; ?>>Granny</option>
                                    <option value="K" <?php if($templateData['avatar']->hair == 'K') echo 'selected=""'; ?>>Young man</option>
                                    <option value="L" <?php if($templateData['avatar']->hair == 'L') echo 'selected=""'; ?>>Long</option>
                                </select></td>
                            <td><p>Hair Color</p></td><td>
                                <select name="avhaircolor" size="1" onchange="OnChange();">
                                    <optgroup label="Blonde">
                                        <option value="FFFFCC" <?php if($templateData['avatar']->hair_color == 'FFFFCC') echo 'selected=""'; ?>>BlondLight</option>
                                        <option value="C5AB7A" <?php if($templateData['avatar']->hair_color == 'C5AB7A') echo 'selected=""'; ?>>BlondMedium</option>
                                        <option value="987F56" <?php if($templateData['avatar']->hair_color == '987F56') echo 'selected=""'; ?>>BlondDark</option>
                                        <option value="B89778" <?php if($templateData['avatar']->hair_color == 'B89778') echo 'selected=""'; ?>>honey blonde</option>
                                        <option value="E6CEA8" <?php if($templateData['avatar']->hair_color == 'E6CEA8') echo 'selected=""'; ?>>straw blonde</option>
                                        <option value="FFF5E1" <?php if($templateData['avatar']->hair_color == 'FFF5E1') echo 'selected=""'; ?>>white blonde</option>
                                    </optgroup>
                                    <optgroup label="Brunette">
                                        <option value="4B2601" <?php if($templateData['avatar']->hair_color == '4B2601') echo 'selected=""'; ?>>BrunettLight</option>
                                        <option value="330E00" <?php if($templateData['avatar']->hair_color == '330E00') echo 'selected=""'; ?>>BrunettMedium</option>
                                        <option value="1A0D00" <?php if($templateData['avatar']->hair_color == '1A0D00') echo 'selected=""'; ?>>BrunettDark</option>
                                        <option value="3B3024" <?php if($templateData['avatar']->hair_color == '3B3024') echo 'selected=""'; ?>>Dark brown</option>
                                        <option value="6A4E42" <?php if($templateData['avatar']->hair_color == '6A4E42') echo 'selected=""'; ?>>Chestnut</option>
                                        <option value="977961" <?php if($templateData['avatar']->hair_color == '977961') echo 'selected=""'; ?>>Ash brown</option>
                                    </optgroup>
                                    <optgroup label="Auburn">
                                        <option value="CC6633" <?php if($templateData['avatar']->hair_color == 'CC6633') echo 'selected=""'; ?>>Auburn - Light</option>
                                        <option value="CC3300" <?php if($templateData['avatar']->hair_color == 'CC3300') echo 'selected=""'; ?>>Auburn - Medium</option>
                                        <option value="990000" <?php if($templateData['avatar']->hair_color == '990000') echo 'selected=""'; ?>>Auburn - Dark</option>
                                        <option value="91553D" <?php if($templateData['avatar']->hair_color == '91553D') echo 'selected=""'; ?>>Pure auburn</option>
                                        <option value="8D4A43" <?php if($templateData['avatar']->hair_color == '8D4A43') echo 'selected=""'; ?>>Pure russet</option>
                                    </optgroup>
                                    <optgroup label="Monochrome">
                                        <option value="FFFFFF" <?php if($templateData['avatar']->hair_color == 'FFFFFF') echo 'selected=""'; ?>>White</option>
                                        <option value="CCCCCC" <?php if($templateData['avatar']->hair_color == 'CCCCCC') echo 'selected=""'; ?>>Grey - Light</option>
                                        <option value="877575" <?php if($templateData['avatar']->hair_color == '877575') echo 'selected=""'; ?>>Grey - Medium</option>
                                        <option value="484040" <?php if($templateData['avatar']->hair_color == '484040') echo 'selected=""'; ?>>Grey - Dark</option>
                                        <option value="71635A" <?php if($templateData['avatar']->hair_color == '71635A') echo 'selected=""'; ?>>Dark grey 2</option>
                                        <option value="B7A69E" <?php if($templateData['avatar']->hair_color == 'B7A69E') echo 'selected=""'; ?>>Mid gray 2</option>
                                        <option value="D6C4C2" <?php if($templateData['avatar']->hair_color == 'D6C4C2') echo 'selected=""'; ?>>Light gray 2</option>
                                        <option value="090806" <?php if($templateData['avatar']->hair_color == '090806') echo 'selected=""'; ?>>Black</option>
                                    </optgroup></select></td>
                        </tr>

                        <tr bgcolor="#EEEEEE">
                            <td><p>Accessories</p></td>
                            <td>
                                <select name="avaccessory1" size="1" onchange="OnChange();">
                                    <optgroup label="Personal">
                                        <option value="A" <?php if($templateData['avatar']->accessory_1 == 'A') echo 'selected=""'; ?>>None</option>
                                        <option value="B" <?php if($templateData['avatar']->accessory_1 == 'B') echo 'selected=""'; ?>>Glasses</option>
                                        <option value="T" <?php if($templateData['avatar']->accessory_1 == 'I') echo 'selected=""'; ?>>Sunglasses</option>
                                        <option value="C" <?php if($templateData['avatar']->accessory_1 == 'C') echo 'selected=""'; ?>>Bindi</option>
                                        <option value="D" <?php if($templateData['avatar']->accessory_1 == 'D') echo 'selected=""'; ?>>Moustache</option>
                                        <option value="E" <?php if($templateData['avatar']->accessory_1 == 'E') echo 'selected=""'; ?>>Freckles</option>
                                        <option value="G" <?php if($templateData['avatar']->accessory_1 == 'G') echo 'selected=""'; ?>>Blusher</option>
                                        <option value="H" <?php if($templateData['avatar']->accessory_1 == 'H') echo 'selected=""'; ?>>Earrings</option>
                                        <option value="I" <?php if($templateData['avatar']->accessory_1 == 'I') echo 'selected=""'; ?>>Beads</option>
                                        <option value="J" <?php if($templateData['avatar']->accessory_1 == 'J') echo 'selected=""'; ?>>Neckerchief</option>
                                        <option value="V" <?php if($templateData['avatar']->accessory_1 == 'V') echo 'selected=""'; ?>>Red scarf</option>
                                        <option value="Y" <?php if($templateData['avatar']->accessory_1 == 'Y') echo 'selected=""'; ?>>Beanie - regular</option>
                                        <option value="AA" <?php if($templateData['avatar']->accessory_1 == 'AA') echo 'selected=""'; ?>>Button scarf</option>
                                        <option value="CC" <?php if($templateData['avatar']->accessory_1 == 'CC') echo 'selected=""'; ?>>Baseball cap</option>
                                        <option value="DD" <?php if($templateData['avatar']->accessory_1 == 'DD') echo 'selected=""'; ?>>Winter hat</option>
                                    </optgroup>
                                    <optgroup label="Clinical">
                                        <option value="F" <?php if($templateData['avatar']->accessory_1 == 'F') echo 'selected=""'; ?>>Mask</option>
                                        <option value="K" <?php if($templateData['avatar']->accessory_1 == 'K') echo 'selected=""'; ?>>Stethoscope</option>
                                        <option value="L" <?php if($templateData['avatar']->accessory_1 == 'L') echo 'selected=""'; ?>>Oxygen Mask</option>
                                        <option value="M" <?php if($templateData['avatar']->accessory_1 == 'M') echo 'selected=""'; ?>>Surgeon Cap</option>
                                    </optgroup><optgroup label="Injuries">
                                        <option value="N" <?php if($templateData['avatar']->accessory_1 == 'N') echo 'selected=""'; ?>>Eye Patch</option>
                                        <option value="O" <?php if($templateData['avatar']->accessory_1 == 'O') echo 'selected=""'; ?>>Scratches</option>
                                        <option value="P" <?php if($templateData['avatar']->accessory_1 == 'P') echo 'selected=""'; ?>>Split Lip</option>
                                        <option value="Q" <?php if($templateData['avatar']->accessory_1 == 'Q') echo 'selected=""'; ?>>Black Eye (Left)</option>
                                        <option value="R" <?php if($templateData['avatar']->accessory_1 == 'R') echo 'selected=""'; ?>>Black Eye (Right)</option>
                                        <option value="S" <?php if($templateData['avatar']->accessory_1 == 'S') echo 'selected=""'; ?>>Head Bandage</option>
                                        <option value="T" <?php if($templateData['avatar']->accessory_1 == 'T') echo 'selected=""'; ?>>Sunglasses</option>
                                        <option value="U" <?php if($templateData['avatar']->accessory_1 == 'U') echo 'selected=""'; ?>>Neck Brace</option>
                                        <option value="W" <?php if($templateData['avatar']->accessory_1 == 'W') echo 'selected=""'; ?>>Tears - small</option>
                                        <option value="BB" <?php if($templateData['avatar']->accessory_1 == 'BB') echo 'selected=""'; ?>>Tears - large</option>
                                        <option value="X" <?php if($templateData['avatar']->accessory_1 == 'X') echo 'selected=""'; ?>>Sweat</option>
                                    </optgroup>
                                </select><p><i>Bottom Layer</i></p></td>
                            <td>
                                <select name="avaccessory2" size="1" onchange="OnChange();">
                                    <optgroup label="Personal">
                                        <option value="A" <?php if($templateData['avatar']->accessory_2 == 'A') echo 'selected=""'; ?>>None</option>
                                        <option value="B" <?php if($templateData['avatar']->accessory_2 == 'B') echo 'selected=""'; ?>>Glasses</option>
                                        <option value="T" <?php if($templateData['avatar']->accessory_2 == 'T') echo 'selected=""'; ?>>Sunglasses</option>
                                        <option value="C" <?php if($templateData['avatar']->accessory_2 == 'C') echo 'selected=""'; ?>>Bindi</option>
                                        <option value="D" <?php if($templateData['avatar']->accessory_2 == 'D') echo 'selected=""'; ?>>Moustache</option>
                                        <option value="E" <?php if($templateData['avatar']->accessory_2 == 'E') echo 'selected=""'; ?>>Freckles</option>
                                        <option value="G" <?php if($templateData['avatar']->accessory_2 == 'G') echo 'selected=""'; ?>>Blusher</option>
                                        <option value="H" <?php if($templateData['avatar']->accessory_2 == 'H') echo 'selected=""'; ?>>Earrings</option>
                                        <option value="I" <?php if($templateData['avatar']->accessory_2 == 'I') echo 'selected=""'; ?>>Beads</option>
                                        <option value="J" <?php if($templateData['avatar']->accessory_2 == 'J') echo 'selected=""'; ?>>Neckerchief</option>
                                        <option value="V" <?php if($templateData['avatar']->accessory_2 == 'V') echo 'selected=""'; ?>>Red scarf</option>
                                        <option value="Y" <?php if($templateData['avatar']->accessory_2 == 'Y') echo 'selected=""'; ?>>Beanie - regular</option>
                                        <option value="AA" <?php if($templateData['avatar']->accessory_2 == 'AA') echo 'selected=""'; ?>>Button scarf</option>
                                        <option value="CC" <?php if($templateData['avatar']->accessory_2 == 'CC') echo 'selected=""'; ?>>Baseball cap</option>
                                        <option value="DD" <?php if($templateData['avatar']->accessory_2 == 'DD') echo 'selected=""'; ?>>Winter hat</option>
                                    </optgroup>
                                    <optgroup label="Clinical">
                                        <option value="F" <?php if($templateData['avatar']->accessory_2 == 'F') echo 'selected=""'; ?>>Mask</option>
                                        <option value="K" <?php if($templateData['avatar']->accessory_2 == 'K') echo 'selected=""'; ?>>Stethoscope</option>
                                        <option value="L" <?php if($templateData['avatar']->accessory_2 == 'L') echo 'selected=""'; ?>>Oxygen Mask</option>
                                        <option value="M" <?php if($templateData['avatar']->accessory_2 == 'M') echo 'selected=""'; ?>>Surgeon Cap</option>
                                    </optgroup>
                                    <optgroup label="Injuries">
                                        <option value="N" <?php if($templateData['avatar']->accessory_2 == 'N') echo 'selected=""'; ?>>Eye Patch</option>
                                        <option value="O" <?php if($templateData['avatar']->accessory_2 == 'O') echo 'selected=""'; ?>>Scratches</option>
                                        <option value="P" <?php if($templateData['avatar']->accessory_2 == 'P') echo 'selected=""'; ?>>Split Lip</option>
                                        <option value="Q" <?php if($templateData['avatar']->accessory_2 == 'Q') echo 'selected=""'; ?>>Black Eye (Left)</option>
                                        <option value="R" <?php if($templateData['avatar']->accessory_2 == 'R') echo 'selected=""'; ?>>Black Eye (Right)</option>
                                        <option value="S" <?php if($templateData['avatar']->accessory_2 == 'S') echo 'selected=""'; ?>>Head Bandage</option>
                                        <option value="T" <?php if($templateData['avatar']->accessory_2 == 'T') echo 'selected=""'; ?>>Sunglasses</option>
                                        <option value="U" <?php if($templateData['avatar']->accessory_2 == 'U') echo 'selected=""'; ?>>Neck Brace</option>
                                        <option value="W" <?php if($templateData['avatar']->accessory_2 == 'W') echo 'selected=""'; ?>>Tears - small</option>
                                        <option value="BB" <?php if($templateData['avatar']->accessory_2 == 'BB') echo 'selected=""'; ?>>Tears - large</option>
                                        <option value="X" <?php if($templateData['avatar']->accessory_2 == 'X') echo 'selected=""'; ?>>Sweat</option>
                                    </optgroup>
                                </select><p><i>Middle Layer</i></p></td>
                            <td>
                                <select name="avaccessory3" size="1" onchange="OnChange();">
                                    <optgroup label="Personal">
                                        <option value="A" <?php if($templateData['avatar']->accessory_3 == 'A') echo 'selected=""'; ?>>None</option>
                                        <option value="B" <?php if($templateData['avatar']->accessory_3 == 'B') echo 'selected=""'; ?>>Glasses</option>
                                        <option value="T" <?php if($templateData['avatar']->accessory_3 == 'T') echo 'selected=""'; ?>>Sunglasses</option>
                                        <option value="C" <?php if($templateData['avatar']->accessory_3 == 'C') echo 'selected=""'; ?>>Bindi</option>
                                        <option value="D" <?php if($templateData['avatar']->accessory_3 == 'D') echo 'selected=""'; ?>>Moustache</option>
                                        <option value="E" <?php if($templateData['avatar']->accessory_3 == 'E') echo 'selected=""'; ?>>Freckles</option>
                                        <option value="G" <?php if($templateData['avatar']->accessory_3 == 'G') echo 'selected=""'; ?>>Blusher</option>
                                        <option value="H" <?php if($templateData['avatar']->accessory_3 == 'H') echo 'selected=""'; ?>>Earrings</option>
                                        <option value="I" <?php if($templateData['avatar']->accessory_3 == 'I') echo 'selected=""'; ?>>Beads</option>
                                        <option value="J" <?php if($templateData['avatar']->accessory_3 == 'J') echo 'selected=""'; ?>>Neckerchief</option>
                                        <option value="V" <?php if($templateData['avatar']->accessory_3 == 'V') echo 'selected=""'; ?>>Red scarf</option>
                                        <option value="Y" <?php if($templateData['avatar']->accessory_3 == 'Y') echo 'selected=""'; ?>>Beanie - regular</option>
                                        <option value="AA" <?php if($templateData['avatar']->accessory_3 == 'AA') echo 'selected=""'; ?>>Button scarf</option>
                                        <option value="CC" <?php if($templateData['avatar']->accessory_3 == 'CC') echo 'selected=""'; ?>>Baseball cap</option>
                                        <option value="DD" <?php if($templateData['avatar']->accessory_3 == 'DD') echo 'selected=""'; ?>>Winter hat</option>
                                    </optgroup>
                                    <optgroup label="Clinical">
                                        <option value="F" <?php if($templateData['avatar']->accessory_3 == 'F') echo 'selected=""'; ?>>Mask</option>
                                        <option value="K" <?php if($templateData['avatar']->accessory_3 == 'K') echo 'selected=""'; ?>>Stethoscope</option>
                                        <option value="L" <?php if($templateData['avatar']->accessory_3 == 'L') echo 'selected=""'; ?>>Oxygen Mask</option>
                                        <option value="M" <?php if($templateData['avatar']->accessory_3 == 'M') echo 'selected=""'; ?>>Surgeon Cap</option>
                                    </optgroup>
                                    <optgroup label="Injuries">
                                        <option value="N" <?php if($templateData['avatar']->accessory_3 == 'N') echo 'selected=""'; ?>>Eye Patch</option>
                                        <option value="O" <?php if($templateData['avatar']->accessory_3 == 'O') echo 'selected=""'; ?>>Scratches</option>
                                        <option value="P" <?php if($templateData['avatar']->accessory_3 == 'P') echo 'selected=""'; ?>>Split Lip</option>
                                        <option value="Q" <?php if($templateData['avatar']->accessory_3 == 'Q') echo 'selected=""'; ?>>Black Eye (Left)</option>
                                        <option value="R" <?php if($templateData['avatar']->accessory_3 == 'R') echo 'selected=""'; ?>>Black Eye (Right)</option>
                                        <option value="S" <?php if($templateData['avatar']->accessory_3 == 'S') echo 'selected=""'; ?>>Head Bandage</option>
                                        <option value="T" <?php if($templateData['avatar']->accessory_3 == 'T') echo 'selected=""'; ?>>Sunglasses</option>
                                        <option value="U" <?php if($templateData['avatar']->accessory_3 == 'U') echo 'selected=""'; ?>>Neck Brace</option>
                                        <option value="W" <?php if($templateData['avatar']->accessory_3 == 'W') echo 'selected=""'; ?>>Tears - small</option>
                                        <option value="BB" <?php if($templateData['avatar']->accessory_3 == 'BB') echo 'selected=""'; ?>>Tears - large</option>
                                        <option value="X" <?php if($templateData['avatar']->accessory_3 == 'X') echo 'selected=""'; ?>>Sweat</option>
                                    </optgroup>
                                </select><p><i>Top Layer</i></p></td>
                        </tr>

                        <tr bgcolor="#FFFFFF">
                            <td nowrap=""><p>Skin Tone (Fill)</p></td>
                            <td>
                                <select name="avskin1" size="1" onchange="OnChange();">
                                    <option value="563522" <?php if($templateData['avatar']->skin_1 == '563522') echo 'selected=""'; ?>>Dark</option>
                                    <option value="D9B39A" <?php if($templateData['avatar']->skin_1 == 'D9B39A') echo 'selected=""'; ?>>Light</option>
                                    <option value="AA8458" <?php if($templateData['avatar']->skin_1 == 'AA8458') echo 'selected=""'; ?>>Medium</option>
                                    <option value="EDE4C8" <?php if($templateData['avatar']->skin_1 == 'EDE4C8') echo 'selected=""'; ?>>Pale 1</option>
                                    <option value="FFDCB1" <?php if($templateData['avatar']->skin_1 == 'FFDCB1') echo 'selected=""'; ?>>Pale 2</option>
                                    <option value="EFD6BD" <?php if($templateData['avatar']->skin_1 == 'EFD6BD') echo 'selected=""'; ?>>Pale 3</option>
                                    <option value="E1ADA4" <?php if($templateData['avatar']->skin_1 == 'E1ADA4') echo 'selected=""'; ?>>Pale 4</option>
                                    <option value="E2B98F" <?php if($templateData['avatar']->skin_1 == 'E2B98F') echo 'selected=""'; ?>>Medium 1</option>
                                    <option value="BB6D4A" <?php if($templateData['avatar']->skin_1 == 'BB6D4A') echo 'selected=""'; ?>>Medium 2</option>
                                    <option value="E0B184" <?php if($templateData['avatar']->skin_1 == 'E0B184') echo 'selected=""'; ?>>Medium 3</option>
                                    <option value="BD9778" <?php if($templateData['avatar']->skin_1 == 'BD9778') echo 'selected=""'; ?>>Medium 4</option>
                                    <option value="A58869" <?php if($templateData['avatar']->skin_1 == 'A58869') echo 'selected=""'; ?>>Dark 1</option>
                                    <option value="710200" <?php if($templateData['avatar']->skin_1 == '710200') echo 'selected=""'; ?>>Dark 2</option>
                                    <option value="720000" <?php if($templateData['avatar']->skin_1 == '720000') echo 'selected=""'; ?>>Dark 3</option>
                                    <option value="380000" <?php if($templateData['avatar']->skin_1 == '380000') echo 'selected=""'; ?>>Dark 4</option>
                                    <option value="FFCC66" <?php if($templateData['avatar']->skin_1 == 'FFCC66') echo 'selected=""'; ?>>Jaundice light</option>
                                    <option value="CC9900" <?php if($templateData['avatar']->skin_1 == 'CC9900') echo 'selected=""'; ?>>Jaundice medium</option>
                                    <option value="996600" <?php if($templateData['avatar']->skin_1 == '996600') echo 'selected=""'; ?>>Jaundice dark</option>
                                    <option value="FFCCEE" <?php if($templateData['avatar']->skin_1 == 'FFCCEE') echo 'selected=""'; ?>>Cyanosed light</option>
                                    <option value="CC9999" <?php if($templateData['avatar']->skin_1 == 'CC9999') echo 'selected=""'; ?>>Cyanosed medium</option>
                                    <option value="996666" <?php if($templateData['avatar']->skin_1 == '996666') echo 'selected=""'; ?>>Cyanosed dark</option>
                                </select></td>


                            <td nowrap=""><p>Skin Tone (Outline)</p></td><td>
                                <select name="avskin2" size="1" onchange="OnChange();">
                                    <option value="330E00" <?php if($templateData['avatar']->skin_2 == '330E00') echo 'selected=""'; ?>>Dark</option>
                                    <option value="AD7863" <?php if($templateData['avatar']->skin_2 == 'AD7863') echo 'selected=""'; ?>>Light</option>
                                    <option value="8C572F" <?php if($templateData['avatar']->skin_2 == '8C572F') echo 'selected=""'; ?>>Medium</option>
                                </select></td>
                        </tr>

                        <tr bgcolor="#EEEEEE">
                            <td nowrap=""><p>Background Color <a href="/help.asp?id=hex" target="_blank" title="Hex Color Values: FAQ">?</a></p></td><td><input type="text" name="avbkd" size="6" value="<?php echo $templateData['avatar']->bkd; ?>">
                                <a href="javascript:TCP.popup(document.forms['avatar_form'].elements['avbkd'])"><img width="15" height="13" border="0" alt="Click here to pick a color" src="<?php echo URL::base(); ?>images/cpiksel.gif"></a>
                            </td>
                            <td><p>Environment</p></td><td>
                                <select name="avenvironment" size="1" onchange="OnChange();">
                                    <option value="A">None</option>
                                    <optgroup label="Clinical">
                                        <option value="B" <?php if($templateData['avatar']->environment == 'B') echo 'selected=""'; ?>>Ambulance Bay</option>
                                        <option value="F" <?php if($templateData['avatar']->environment == 'F') echo 'selected=""'; ?>>Bed/Pillow</option>
                                        <option value="G" <?php if($templateData['avatar']->environment == 'G') echo 'selected=""'; ?>>Hospital Corridor</option>
                                        <option value="H" <?php if($templateData['avatar']->environment == 'H') echo 'selected=""'; ?>>Waiting Room</option>
                                        <option value="J" <?php if($templateData['avatar']->environment == 'J') echo 'selected=""'; ?>>Inside Ambulance</option>
                                        <option value="O" <?php if($templateData['avatar']->environment == 'O') echo 'selected=""'; ?>>X-ray</option>
                                        <option value="R" <?php if($templateData['avatar']->environment == 'R') echo 'selected=""'; ?>>CA ambulance</option>
                                        <option value="S" <?php if($templateData['avatar']->environment == 'S') echo 'selected=""'; ?>>Medivac helicopter</option>
                                        <option value="V" <?php if($templateData['avatar']->environment == 'V') echo 'selected=""'; ?>>Heart monitor</option>
                                        <option value="BB" <?php if($templateData['avatar']->environment == 'BB') echo 'selected=""'; ?>>Sign</option>
                                        <option value="CC" <?php if($templateData['avatar']->environment == 'CC') echo 'selected=""'; ?>>Bedside</option>
                                        <option value="DD" <?php if($templateData['avatar']->environment == 'DD') echo 'selected=""'; ?>>Ambulance 2</option>
                                        <option value="FF" <?php if($templateData['avatar']->environment == 'FF') echo 'selected=""'; ?>>Machine</option>
                                    </optgroup>
                                    <optgroup label="General - inside">
                                        <option value="D" <?php if($templateData['avatar']->environment == 'D') echo 'selected=""'; ?>>Living Room</option>
                                        <option value="K" <?php if($templateData['avatar']->environment == 'K') echo 'selected=""'; ?>>Basic office</option>
                                        <option value="N" <?php if($templateData['avatar']->environment == 'N') echo 'selected=""'; ?>>Basic room</option>
                                        <option value="Y" <?php if($templateData['avatar']->environment == 'Y') echo 'selected=""'; ?>>Corridor</option>
                                        <option value="AA" <?php if($templateData['avatar']->environment == 'AA') echo 'selected=""'; ?>>Room</option>
                                        <option value="GG" <?php if($templateData['avatar']->environment == 'GG') echo 'selected=""'; ?>>Pillow B</option>
                                        <option value="JJ" <?php if($templateData['avatar']->environment == 'JJ') echo 'selected=""'; ?>>Concourse</option>
                                        <option value="KK" <?php if($templateData['avatar']->environment == 'KK') echo 'selected=""'; ?>>Office cubicle</option>
                                    </optgroup>
                                    <optgroup label="General = outside">
                                        <option value="C" <?php if($templateData['avatar']->environment == 'C') echo 'selected=""'; ?>>Residential Street</option>
                                        <option value="E" <?php if($templateData['avatar']->environment == 'E') echo 'selected=""'; ?>>High Street</option>
                                        <option value="I" <?php if($templateData['avatar']->environment == 'I') echo 'selected=""'; ?>>City Skyline</option>
                                        <option value="L" <?php if($templateData['avatar']->environment == 'L') echo 'selected=""'; ?>>Lakeside</option>
                                        <option value="M" <?php if($templateData['avatar']->environment == 'M') echo 'selected=""'; ?>>Suburbs</option>
                                        <option value="T" <?php if($templateData['avatar']->environment == 'T') echo 'selected=""'; ?>>Summer</option>
                                        <option value="U" <?php if($templateData['avatar']->environment == 'U') echo 'selected=""'; ?>>Long road</option>
                                        <option value="P" <?php if($templateData['avatar']->environment == 'P') echo 'selected=""'; ?>>Downtown</option>
                                        <option value="Q" <?php if($templateData['avatar']->environment == 'Q') echo 'selected=""'; ?>>Winter</option>
                                        <option value="W" <?php if($templateData['avatar']->environment == 'W') echo 'selected=""'; ?>>Outside lake</option>
                                        <option value="X" <?php if($templateData['avatar']->environment == 'X') echo 'selected=""'; ?>>Field</option>
                                        <option value="Z" <?php if($templateData['avatar']->environment == 'Z') echo 'selected=""'; ?>>Roadside</option>
                                        <option value="HH" <?php if($templateData['avatar']->environment == 'HH') echo 'selected=""'; ?>>Forest river</option>
                                        <option value="II" <?php if($templateData['avatar']->environment == 'II') echo 'selected=""'; ?>>Parking lot</option>
                                        <option value="BB" <?php if($templateData['avatar']->environment == 'BB') echo 'selected=""'; ?>>Stop sign</option>
                                        <option value="EE" <?php if($templateData['avatar']->environment == 'EE') echo 'selected=""'; ?>>Yield sign</option>
                                    </optgroup>
                                </select></td>
                        </tr>

                        <tr bgcolor="#FFFFFF">
                            <td></td><td></td>
                            <td><p>Weather</p></td><td>
                                <select name="avweather" size="1" onchange="OnChange();">
                                    <option value="A" <?php if($templateData['avatar']->weather == 'A') echo 'selected=""'; ?>>None</option>
                                    <option value="B" <?php if($templateData['avatar']->weather == 'B') echo 'selected=""'; ?>>Rainy Day</option>
                                    <option value="C" <?php if($templateData['avatar']->weather == 'C') echo 'selected=""'; ?>>Windy Day</option>
                                    <option value="D" <?php if($templateData['avatar']->weather == 'D') echo 'selected=""'; ?>>Snowy Day</option>
                                </select></td>
                        </tr>

                        <tr bgcolor="#EEEEEE">
                            <td><p>Speech Bubble</p></td><td>
                                <select name="avbubble" size="1" onchange="OnChange();">
                                    <option value="A" <?php if($templateData['avatar']->bubble == 'A') echo 'selected=""'; ?>>None</option>
                                    <option value="B" <?php if($templateData['avatar']->bubble == 'B') echo 'selected=""'; ?>>Normal</option>
                                    <option value="C" <?php if($templateData['avatar']->bubble == 'C') echo 'selected=""'; ?>>Think</option>
                                    <option value="D" <?php if($templateData['avatar']->bubble == 'D') echo 'selected=""'; ?>>Shout</option>
                                </select></td>
                            <td colspan="2"><p>Bubble Text <input type="text" name="avbubbletext" size="30" maxlength="50" value="<?php echo $templateData['avatar']->bubble_text; ?>"></p></td>
                        </tr>

                        ></table>
                    <hr>
                    <input type="submit" value="Save / Update">
                </form>
            </td>
        </tr>
    </table>
<?php } ?>