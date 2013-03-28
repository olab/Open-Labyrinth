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
if (isset($templateData['map']) and isset($templateData['avatar'])) {
    ?>
    <script type="text/javascript" language="javascript" src="<?php echo URL::base() ?>scripts/avatar.js"></script>
    <script type="text/javascript" src="<?php echo URL::base(); ?>scripts/farbtastic/farbtastic.js"></script>
    <script type="text/javascript" language="javascript">
        window.onload = function () {
            AvatarSetup({
                <?php
                $keys = array('skintone'=>'skin_1', 'clothcolor'=>'cloth', 'nose'=>'nose', 'hairtype'=>'hair', 'environment'=>'environment', 'accessory1'=>'accessory_1', 'accessory2'=>'accessory_2', 'accessory3'=>'accessory_3', 'sex'=>'sex', 'mouth'=>'mouth', 'outfit'=>'outfit', 'bubble'=>'bubble', 'bubbletext'=>'bubble_text', 'age'=>'age', 'eyes'=>'eyes', 'haircolor'=>'hair_color');
                $setupArray = null;
                foreach($keys as $k => $v){
                    if ($templateData['avatar']->$v != null){
                        $setupArray[] = $k.': "'.$templateData['avatar']->$v.'"';
                    }
                }
                if (count($setupArray) > 0){
                    echo implode(',', $setupArray);
                }
                ?>
            });
        }
    </script>
    <div class="page-header">
        <h1>Avatar Editor: "<?php echo $templateData['avatar']->id; ?>"</h1></div>
<form class="form-horizontal" method="POST"
      action="<?php echo URL::base() . 'avatarManager/updateAvatar/' . $templateData['map']->id . '/' . $templateData['avatar']->id; ?>"
      name="avatar_form">
    <div class="row-fluid">
    <div class="span6">
        <canvas style="border: 1px solid #000;" id="avatar_canvas"></canvas>
    </div>
    <div class="span6">

    <fieldset class="fieldset">


    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a data-toggle="tab" href="#basic">Basic</a></li>
        <li><a data-toggle="tab" href="#face">Face</a></li>
        <li><a data-toggle="tab" href="#hairskin">Hair &amp; Skin</a></li>
        <li><a data-toggle="tab" href="#clothing">Clothing</a></li>
        <li><a data-toggle="tab" href="#speech">Speech</a></li>
        <li><a data-toggle="tab" href="#scene">Scene</a></li>
    </ul>

    <div class="tab-content">
    <div class="tab-pane active" id="basic">
        <div class="control-group">
            <label for="sex" class="control-label">Sex</label>

            <div class="controls">
                <select id="sex" size="1" name="avsex">
                    <option <?php if ($templateData['avatar']->sex == 'male') echo 'selected=""'; ?> value="male">Male
                    </option>
                    <option <?php if ($templateData['avatar']->sex == 'female') echo 'selected=""'; ?> value="female">
                        Female
                    </option>
                </select>
            </div>

        </div>
        <div class="control-group">
            <label for="age" class="control-label">Age</label>

            <div class="controls">
                <select id="age" size="1" name="avage">
                    <option <?php if ($templateData['avatar']->age == '20') echo 'selected=""'; ?> value="20">20+
                    </option>
                    <option <?php if ($templateData['avatar']->age == '40') echo 'selected=""'; ?> value="40">40+
                    </option>
                    <option <?php if ($templateData['avatar']->age == '60') echo 'selected=""'; ?> value="60">60+
                    </option>
                </select>
            </div>

        </div>


    </div>
    <div class="tab-pane" id="face">

        <div class="control-group">
            <label for="mouth" class="control-label">Mouth Shape</label>

            <div class="controls">
                <select id="mouth" size="1" name="avmouth">
                    <option <?php if ($templateData['avatar']->mouth == 'smile') echo 'selected=""'; ?> value="smile">
                        Smile
                    </option>
                    <option <?php if ($templateData['avatar']->mouth == 'indifferent') echo 'selected=""'; ?>
                        value="indifferent">Indifferent
                    </option>
                    <option <?php if ($templateData['avatar']->mouth == 'frown') echo 'selected=""'; ?> value="frown">
                        Frown
                    </option>
                </select>
            </div>

        </div>
        <div class="control-group">
            <label for="eyes" class="control-label">Eyes</label>

            <div class="controls">
                <select id="eyes" size="1" name="aveyes">
                    <option <?php if ($templateData['avatar']->eyes == 'open') echo 'selected=""'; ?> value="open">
                        Open
                    </option>
                    <option <?php if ($templateData['avatar']->eyes == 'close') echo 'selected=""'; ?> value="close">
                        Closed
                    </option>
                </select>
            </div>

        </div>
        <div class="control-group">
            <label for="nose" class="control-label">Nose Type</label>

            <div class="controls">
                <select id="nose" size="1" name="avnose">
                    <option <?php if ($templateData['avatar']->nose == 'nostrils') echo 'selected=""'; ?>
                        value="nostrils">Nostrils Only
                    </option>
                    <option <?php if ($templateData['avatar']->nose == 'petit') echo 'selected=""'; ?> value="petit">
                        Petit
                    </option>
                    <option <?php if ($templateData['avatar']->nose == 'wide') echo 'selected=""'; ?> value="wide">
                        Wide
                    </option>
                </select>
            </div>

        </div>
    </div>
    <div class="tab-pane" id="hairskin">
        <div class="control-group">
            <label for="hairtype" class="control-label">Hair Type</label>

            <div class="controls">
                <select id="hairtype" size="1" name="avhair">
                    <option <?php if ($templateData['avatar']->hair == 'none') echo 'selected=""'; ?> value="none">
                        Bald
                    </option>
                    <option <?php if ($templateData['avatar']->hair == 'shaved') echo 'selected=""'; ?> value="shaved">
                        Shaved
                    </option>
                    <option <?php if ($templateData['avatar']->hair == 'longblonde') echo 'selected=""'; ?>
                        value="longblonde">Long/Blonde
                    </option>
                    <option <?php if ($templateData['avatar']->hair == 'short') echo 'selected=""'; ?> value="short">
                        Short
                    </option>
                    <option <?php if ($templateData['avatar']->hair == 'curly') echo 'selected=""'; ?> value="curly">
                        Curly
                    </option>
                    <option <?php if ($templateData['avatar']->hair == 'bob') echo 'selected=""'; ?> value="bob">Bob
                    </option>
                    <option <?php if ($templateData['avatar']->hair == 'longred') echo 'selected=""'; ?>value="longred">
                        Long/Red
                    </option>
                    <option <?php if ($templateData['avatar']->hair == 'grandpa') echo 'selected=""'; ?>value="grandpa">
                        Grandpa
                    </option>
                    <option <?php if ($templateData['avatar']->hair == 'granny') echo 'selected=""'; ?> value="granny">
                        Granny
                    </option>
                    <option <?php if ($templateData['avatar']->hair == 'youngman') echo 'selected=""'; ?>
                        value="youngman">Young man
                    </option>
                    <option <?php if ($templateData['avatar']->hair == 'long') echo 'selected=""'; ?> value="long">
                        Long
                    </option>
                </select>
            </div>

        </div>

        <div class="control-group">
            <label for="haircolor" class="control-label">Hair Color</label>

            <div class="controls">
                <select id="haircolor" size="1" name="avhaircolor">
                    <optgroup label="Blonde">
                        <option
                            value="FFFFCC" <?php if ($templateData['avatar']->hair_color == 'FFFFCC') echo 'selected=""'; ?>>
                            BlondLight
                        </option>
                        <option
                            value="C5AB7A" <?php if ($templateData['avatar']->hair_color == 'C5AB7A') echo 'selected=""'; ?>>
                            BlondMedium
                        </option>
                        <option
                            value="987F56" <?php if ($templateData['avatar']->hair_color == '987F56') echo 'selected=""'; ?>>
                            BlondDark
                        </option>
                        <option
                            value="B89778" <?php if ($templateData['avatar']->hair_color == 'B89778') echo 'selected=""'; ?>>
                            honey blonde
                        </option>
                        <option
                            value="E6CEA8" <?php if ($templateData['avatar']->hair_color == 'E6CEA8') echo 'selected=""'; ?>>
                            straw blonde
                        </option>
                        <option
                            value="FFF5E1" <?php if ($templateData['avatar']->hair_color == 'FFF5E1') echo 'selected=""'; ?>>
                            white blonde
                        </option>
                    </optgroup>
                    <optgroup label="Brunette">
                        <option
                            value="4B2601" <?php if ($templateData['avatar']->hair_color == '4B2601') echo 'selected=""'; ?>>
                            BrunettLight
                        </option>
                        <option
                            value="330E00" <?php if ($templateData['avatar']->hair_color == '330E00') echo 'selected=""'; ?>>
                            BrunettMedium
                        </option>
                        <option
                            value="1A0D00" <?php if ($templateData['avatar']->hair_color == '1A0D00') echo 'selected=""'; ?>>
                            BrunettDark
                        </option>
                        <option
                            value="3B3024" <?php if ($templateData['avatar']->hair_color == '3B3024') echo 'selected=""'; ?>>
                            Dark brown
                        </option>
                        <option
                            value="6A4E42" <?php if ($templateData['avatar']->hair_color == '6A4E42') echo 'selected=""'; ?>>
                            Chestnut
                        </option>
                        <option
                            value="977961" <?php if ($templateData['avatar']->hair_color == '977961') echo 'selected=""'; ?>>
                            Ash brown
                        </option>
                    </optgroup>
                    <optgroup label="Auburn">
                        <option
                            value="CC6633" <?php if ($templateData['avatar']->hair_color == 'CC6633') echo 'selected=""'; ?>>
                            Auburn - Light
                        </option>
                        <option
                            value="CC3300" <?php if ($templateData['avatar']->hair_color == 'CC3300') echo 'selected=""'; ?>>
                            Auburn - Medium
                        </option>
                        <option
                            value="990000" <?php if ($templateData['avatar']->hair_color == '990000') echo 'selected=""'; ?>>
                            Auburn - Dark
                        </option>
                        <option
                            value="91553D" <?php if ($templateData['avatar']->hair_color == '91553D') echo 'selected=""'; ?>>
                            Pure auburn
                        </option>
                        <option
                            value="8D4A43" <?php if ($templateData['avatar']->hair_color == '8D4A43') echo 'selected=""'; ?>>
                            Pure russet
                        </option>
                    </optgroup>
                    <optgroup label="Monochrome">
                        <option
                            value="FFFFFF" <?php if ($templateData['avatar']->hair_color == 'FFFFFF') echo 'selected=""'; ?>>
                            White
                        </option>
                        <option
                            value="CCCCCC" <?php if ($templateData['avatar']->hair_color == 'CCCCCC') echo 'selected=""'; ?>>
                            Grey - Light
                        </option>
                        <option
                            value="877575" <?php if ($templateData['avatar']->hair_color == '877575') echo 'selected=""'; ?>>
                            Grey - Medium
                        </option>
                        <option
                            value="484040" <?php if ($templateData['avatar']->hair_color == '484040') echo 'selected=""'; ?>>
                            Grey - Dark
                        </option>
                        <option
                            value="71635A" <?php if ($templateData['avatar']->hair_color == '71635A') echo 'selected=""'; ?>>
                            Dark grey 2
                        </option>
                        <option
                            value="B7A69E" <?php if ($templateData['avatar']->hair_color == 'B7A69E') echo 'selected=""'; ?>>
                            Mid gray 2
                        </option>
                        <option
                            value="D6C4C2" <?php if ($templateData['avatar']->hair_color == 'D6C4C2') echo 'selected=""'; ?>>
                            Light gray 2
                        </option>
                        <option
                            value="090806" <?php if ($templateData['avatar']->hair_color == '090806') echo 'selected=""'; ?>>
                            Black
                        </option>
                    </optgroup>
                </select>
            </div>

        </div>
        <div class="control-group">
            <label for="skintone" class="control-label">Skin Tone (Fill)</label>

            <div class="controls">
                <select id="skintone" size="1" name="avskin1">
                    <option
                        value="563522" <?php if ($templateData['avatar']->skin_1 == '563522') echo 'selected=""'; ?>>
                        Dark
                    </option>
                    <option
                        value="D9B39A" <?php if ($templateData['avatar']->skin_1 == 'D9B39A') echo 'selected=""'; ?>>
                        Light
                    </option>
                    <option
                        value="AA8458" <?php if (($templateData['avatar']->skin_1 == 'AA8458') || ($templateData['avatar']->skin_1 == null)) echo 'selected=""'; ?>>
                        Medium
                    </option>
                    <option
                        value="EDE4C8" <?php if ($templateData['avatar']->skin_1 == 'EDE4C8') echo 'selected=""'; ?>>
                        Pale 1
                    </option>
                    <option
                        value="FFDCB1" <?php if ($templateData['avatar']->skin_1 == 'FFDCB1') echo 'selected=""'; ?>>
                        Pale 2
                    </option>
                    <option
                        value="EFD6BD" <?php if ($templateData['avatar']->skin_1 == 'EFD6BD') echo 'selected=""'; ?>>
                        Pale 3
                    </option>
                    <option
                        value="E1ADA4" <?php if ($templateData['avatar']->skin_1 == 'E1ADA4') echo 'selected=""'; ?>>
                        Pale 4
                    </option>
                    <option
                        value="E2B98F" <?php if ($templateData['avatar']->skin_1 == 'E2B98F') echo 'selected=""'; ?>>
                        Medium 1
                    </option>
                    <option
                        value="BB6D4A" <?php if ($templateData['avatar']->skin_1 == 'BB6D4A') echo 'selected=""'; ?>>
                        Medium 2
                    </option>
                    <option
                        value="E0B184" <?php if ($templateData['avatar']->skin_1 == 'E0B184') echo 'selected=""'; ?>>
                        Medium 3
                    </option>
                    <option
                        value="BD9778" <?php if ($templateData['avatar']->skin_1 == 'BD9778') echo 'selected=""'; ?>>
                        Medium 4
                    </option>
                    <option
                        value="A58869" <?php if ($templateData['avatar']->skin_1 == 'A58869') echo 'selected=""'; ?>>
                        Dark 1
                    </option>
                    <option
                        value="710200" <?php if ($templateData['avatar']->skin_1 == '710200') echo 'selected=""'; ?>>
                        Dark 2
                    </option>
                    <option
                        value="720000" <?php if ($templateData['avatar']->skin_1 == '720000') echo 'selected=""'; ?>>
                        Dark 3
                    </option>
                    <option
                        value="380000" <?php if ($templateData['avatar']->skin_1 == '380000') echo 'selected=""'; ?>>
                        Dark 4
                    </option>
                    <option
                        value="FFCC66" <?php if ($templateData['avatar']->skin_1 == 'FFCC66') echo 'selected=""'; ?>>
                        Jaundice light
                    </option>
                    <option
                        value="CC9900" <?php if ($templateData['avatar']->skin_1 == 'CC9900') echo 'selected=""'; ?>>
                        Jaundice medium
                    </option>
                    <option
                        value="996600" <?php if ($templateData['avatar']->skin_1 == '996600') echo 'selected=""'; ?>>
                        Jaundice dark
                    </option>
                    <option
                        value="FFCCEE" <?php if ($templateData['avatar']->skin_1 == 'FFCCEE') echo 'selected=""'; ?>>
                        Cyanosed light
                    </option>
                    <option
                        value="CC9999" <?php if ($templateData['avatar']->skin_1 == 'CC9999') echo 'selected=""'; ?>>
                        Cyanosed medium
                    </option>
                    <option
                        value="996666" <?php if ($templateData['avatar']->skin_1 == '996666') echo 'selected=""'; ?>>
                        Cyanosed dark
                    </option>
                </select>
            </div>

        </div>

        <div class="control-group">
            <label for="avskin2" class="control-label">Skin Tone (Outline)</label>

            <div class="controls">
                <select onchange="OnChange();" size="1" name="avskin2" id="avskin2">
                    <option value="330E00">Dark</option>
                    <option value="AD7863">Light</option>
                    <option selected="" value="8C572F">Medium</option>
                </select>
            </div>

        </div>
    </div>
    <div class="tab-pane" id="clothing">
        <div class="control-group">
            <label for="outfit" class="control-label">Outfit</label>

            <div class="controls">
                <select id="outfit" size="1" name="avoutfit">
                    <option <?php if ($templateData['avatar']->outfit == 'none') echo 'selected=""'; ?> value="none">
                        Naked
                    </option>
                    <option <?php if ($templateData['avatar']->outfit == 'woolyjumper') echo 'selected=""'; ?>
                        value="woolyjumper">Wooly Jumper
                    </option>
                    <option <?php if ($templateData['avatar']->outfit == 'shirtandtie') echo 'selected=""'; ?>
                        value="shirtandtie">Shirt and Tie
                    </option>
                    <option <?php if ($templateData['avatar']->outfit == 'nurse') echo 'selected=""'; ?> value="nurse">
                        Nurse Uniform
                    </option>
                    <option <?php if ($templateData['avatar']->outfit == 'scrubs_blue') echo 'selected=""'; ?>
                        value="scrubs_blue">Scrubs (Blue)
                    </option>
                    <option <?php if ($templateData['avatar']->outfit == 'scrubs_green') echo 'selected=""'; ?>
                        value="scrubs_green">Scrubs (Green)
                    </option>
                    <option <?php if ($templateData['avatar']->outfit == 'vest') echo 'selected=""'; ?> value="vest">
                        Vest
                    </option>
                    <option <?php if ($templateData['avatar']->outfit == 'gown') echo 'selected=""'; ?> value="gown">
                        Gown (White)
                    </option>
                    <option <?php if ($templateData['avatar']->outfit == 'pyjamas_female') echo 'selected=""'; ?>
                        value="pyjamas_female">Pyjamas (Female)
                    </option>
                    <option <?php if ($templateData['avatar']->outfit == 'pyjamas_male') echo 'selected=""'; ?>
                        value="pyjamas_male">Pyjamas (Male)
                    </option>
                    <option <?php if ($templateData['avatar']->outfit == 'doctor_male') echo 'selected=""'; ?>
                        value="doctor_male">Doctor (Male)
                    </option>
                    <option <?php if ($templateData['avatar']->outfit == 'doctor_female') echo 'selected=""'; ?>
                        value="doctor_female">Doctor (Female)
                    </option>
                    <option <?php if ($templateData['avatar']->outfit == 'neck') echo 'selected=""'; ?> value="neck">
                        Pattern turtle neck
                    </option>
                    <option <?php if ($templateData['avatar']->outfit == 'blackshirt') echo 'selected=""'; ?>
                        value="blackshirt">Black striped shirt
                    </option>
                    <option <?php if ($templateData['avatar']->outfit == 'winterjacket') echo 'selected=""'; ?>
                        value="winterjacket">Winter jacket
                    </option>
                    <option <?php if ($templateData['avatar']->outfit == 'vneck') echo 'selected=""'; ?> value="vneck">
                        V-neck
                    </option>
                    <option <?php if ($templateData['avatar']->outfit == 'fleece') echo 'selected=""'; ?>value="fleece">
                        Fleece
                    </option>
                    <option <?php if ($templateData['avatar']->outfit == 'sweater') echo 'selected=""'; ?>
                        value="sweater">Sweater
                    </option>
                </select>
            </div>

        </div>
        <div class="control-group">
            <label for="clothcolor" class="control-label">Cloth Color</label>

            <div class="controls">
                <input id="clothcolor" type="text" value="<?php echo $templateData['avatar']->cloth; ?>" size="6"
                       name="avcloth">
                <input type="hidden" id="clothCoorPicker"
                       value="<?php echo strlen($templateData['avatar']->cloth) <= 0 ? '#FFFFFF' : '#' . $templateData['avatar']->cloth; ?>"/>

                <div id="clothColorContainer"></div>
            </div>

        </div>

        <div class="control-group">
            <label for="" class="control-label">Accessories</label>

            <div class="controls">
                <?php
                for ($i = 1; $i < 4; $i++) {
                    $key = 'accessory_' . $i;
                    ?>

                    <select id="avaccessory<?php echo $i ?>" size="1" name="avaccessory<?php echo $i ?>">
                        <optgroup label="Personal">
                            <option <?php if ($templateData['avatar']->$key == 'none') echo 'selected=""'; ?>
                                value="none">None
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'glasses') echo 'selected=""'; ?>
                                value="glasses">Glasses
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'sunglasses') echo 'selected=""'; ?>
                                value="sunglasses">Sunglasses
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'bindi') echo 'selected=""'; ?>
                                value="bindi">Bindi
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'moustache') echo 'selected=""'; ?>
                                value="moustache">Moustache
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'freckles') echo 'selected=""'; ?>
                                value="freckles">Freckles
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'blusher') echo 'selected=""'; ?>
                                value="blusher">Blusher
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'earrings') echo 'selected=""'; ?>
                                value="earrings">Earrings
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'beads') echo 'selected=""'; ?>
                                value="beads">Beads
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'neckerchief') echo 'selected=""'; ?>
                                value="neckerchief">Neckerchief
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'redscarf') echo 'selected=""'; ?>
                                value="redscarf">Red scarf
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'beanie') echo 'selected=""'; ?>
                                value="beanie">Beanie - regular
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'buttonscarf') echo 'selected=""'; ?>
                                value="buttonscarf">Button scarf
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'baseballcap') echo 'selected=""'; ?>
                                value="baseballcap">Baseball cap
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'winterhat') echo 'selected=""'; ?>
                                value="winterhat">Winter hat
                            </option>
                        </optgroup>
                        <optgroup label="Clinical">
                            <option <?php if ($templateData['avatar']->$key == 'mask') echo 'selected=""'; ?>
                                value="mask">Mask
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'stethoscope') echo 'selected=""'; ?>
                                value="stethoscope">Stethoscope
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'oxygenmask') echo 'selected=""'; ?>
                                value="oxygenmask">Oxygen Mask
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'surgeoncap') echo 'selected=""'; ?>
                                value="surgeoncap">Surgeon Cap
                            </option>
                        </optgroup>
                        <optgroup label="Injuries">
                            <option <?php if ($templateData['avatar']->$key == 'eyepatch') echo 'selected=""'; ?>
                                value="eyepatch">Eye Patch
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'scratches') echo 'selected=""'; ?>
                                value="scratches">Scratches
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'splitlip') echo 'selected=""'; ?>
                                value="splitlip">Split Lip
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'blackeyeleft') echo 'selected=""'; ?>
                                value="blackeyeleft">Black Eye (Left)
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'blackeyeright') echo 'selected=""'; ?>
                                value="blackeyeright">Black Eye (Right)
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'headbandage') echo 'selected=""'; ?>
                                value="headbandage">Head Bandage
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'sunglasses') echo 'selected=""'; ?>
                                value="sunglasses">Sunglasses
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'neckbrace') echo 'selected=""'; ?>
                                value="neckbrace">Neck Brace
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'tearssmall') echo 'selected=""'; ?>
                                value="tearssmall">Tears - small
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'tearslarge') echo 'selected=""'; ?>
                                value="tearslarge">Tears - large
                            </option>
                            <option <?php if ($templateData['avatar']->$key == 'sweat') echo 'selected=""'; ?>
                                value="sweat">Sweat
                            </option>
                        </optgroup>
                    </select>
                    <?php
                    switch ($i) {
                        case '1':
                            echo '<p><i>Bottom Layer</i></p>';
                            break;
                        case '2':
                            echo '<p><i>Middle Layer</i></p>';
                            break;
                        case '3':
                            echo '<p><i>Top Layer</i></p>';
                            break;
                    }
                    ?>

                <?php
                }
                ?>
            </div>

        </div>

    </div>
    <div class="tab-pane" id="speech">
        <div class="control-group">
            <label for="bubble" class="control-label">Speech Bubble</label>

            <div class="controls">
                <select id="bubble" name="avbubble">
                    <option <?php if ($templateData['avatar']->bubble == 'none') echo 'selected=""'; ?> value="none">
                        None
                    </option>
                    <option <?php if ($templateData['avatar']->bubble == 'normal') echo 'selected=""'; ?>value="normal">
                        Normal
                    </option>
                    <option <?php if ($templateData['avatar']->bubble == 'think') echo 'selected=""'; ?> value="think">
                        Think
                    </option>
                    <option <?php if ($templateData['avatar']->bubble == 'shout') echo 'selected=""'; ?> value="shout">
                        Shout
                    </option>
                </select>
            </div>

        </div>

        <div class="control-group">
            <label for="bubbletext" class="control-label">Bubble Text</label>

            <div class="controls">
                <input type="text" value="<?php echo $templateData['avatar']->bubble_text; ?>"
                       id="bubbletext" name="avbubbletext">
            </div>

        </div>

    </div>
    <div class="tab-pane" id="scene">
        <div class="control-group">
            <label for="bgcolor" class="control-label">Background Color</label>

            <div class="controls">
                <input id="bgcolor" type="text" value="<?php echo $templateData['avatar']->bkd; ?>" size="6"
                       name="avbkd">
                <input type="hidden" id="avBgPicker"
                       value="<?php echo strlen($templateData['avatar']->bkd) <= 0 ? '#FFFFFF' : '#' . $templateData['avatar']->bkd; ?>"/>

                <div id="avBgPickerContainer"></div>
            </div>

        </div>

        <div class="control-group">
            <label for="environment" class="control-label">Environment</label>

            <div class="controls">
                <select id="environment" size="1" name="avenvironment">
                    <option value="none">None</option>
                    <optgroup label="Clinical">
                        <option <?php if ($templateData['avatar']->environment == 'ambulancebay') echo 'selected=""'; ?>
                            value="ambulancebay">Ambulance Bay
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'bedpillow') echo 'selected=""'; ?>
                            value="bedpillow">Bed/Pillow
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'hospital') echo 'selected=""'; ?>
                            value="hospital">Hospital Corridor
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'waitingroom') echo 'selected=""'; ?>
                            value="waitingroom">Waiting Room
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'insideambulance') echo 'selected=""'; ?>
                            value="insideambulance">Inside Ambulance
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'xray') echo 'selected=""'; ?>
                            value="xray">X-ray
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'ca') echo 'selected=""'; ?>
                            value="ca">CA ambulance
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'medivachelicopter') echo 'selected=""'; ?>
                            value="medivachelicopter">Medivac helicopter
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'heartmonitor') echo 'selected=""'; ?>
                            value="heartmonitor">Heart monitor
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'stopsign') echo 'selected=""'; ?>
                            value="stopsign">Sign
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'bedside') echo 'selected=""'; ?>
                            value="bedside">Bedside
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'ambulance2') echo 'selected=""'; ?>
                            value="ambulance2">Ambulance 2
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'machine') echo 'selected=""'; ?>
                            value="machine">Machine
                        </option>
                    </optgroup>
                    <optgroup label="General - inside">
                        <option <?php if ($templateData['avatar']->environment == 'livingroom') echo 'selected=""'; ?>
                            value="livingroom">Living Room
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'basicoffice') echo 'selected=""'; ?>
                            value="basicoffice">Basic office
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'basicroom') echo 'selected=""'; ?>
                            value="basicroom">Basic room
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'corridor') echo 'selected=""'; ?>
                            value="corridor">Corridor
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'room') echo 'selected=""'; ?>
                            value="room">Room
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'pillowb') echo 'selected=""'; ?>
                            value="pillowb">Pillow B
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'concourse') echo 'selected=""'; ?>
                            value="concourse">Concourse
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'officecubicle') echo 'selected=""'; ?>
                            value="officecubicle">Office cubicle
                        </option>
                    </optgroup>
                    <optgroup label="General = outside">
                        <option <?php if ($templateData['avatar']->environment == 'residentialstreet') echo 'selected=""'; ?>
                            value="residentialstreet">Residential Street
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'highstreet') echo 'selected=""'; ?>
                            value="highstreet">High Street
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'cityskyline') echo 'selected=""'; ?>
                            value="cityskyline">City Skyline
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'lakeside') echo 'selected=""'; ?>
                            value="lakeside">Lakeside
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'suburbs') echo 'selected=""'; ?>
                            value="suburbs">Suburbs
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'summer') echo 'selected=""'; ?>
                            value="summer">Summer
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'longroad') echo 'selected=""'; ?>
                            value="longroad">Long road
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'downtown') echo 'selected=""'; ?>
                            value="downtown">Downtown
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'winter') echo 'selected=""'; ?>
                            value="winter">Winter
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'outsidelake') echo 'selected=""'; ?>
                            value="outsidelake">Outside lake
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'field') echo 'selected=""'; ?>
                            value="field">Field
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'roadside') echo 'selected=""'; ?>
                            value="roadside">Roadside
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'forestriver') echo 'selected=""'; ?>
                            value="forestriver">Forest river
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'parkinglot') echo 'selected=""'; ?>
                            value="parkinglot">Parking lot
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'stopsign') echo 'selected=""'; ?>
                            value="stopsign">Stop sign
                        </option>
                        <option <?php if ($templateData['avatar']->environment == 'yieldsign') echo 'selected=""'; ?>
                            value="yieldsign">Yield sign
                        </option>
                    </optgroup>
                </select>
            </div>

        </div>
    </div>
    </div>


    </fieldset>

    <input autocomplete="off" name="image_data" id="image_data" type="hidden"/>
    <input autocomplete="off" name="save_exit_value" id="save_exit_value" type="hidden" value="0"/>






    </div>
    </div>
    <div class="form-actions">
        <div class="pull-right">
            <div class="btn-group">
                <input type="button" class="btn btn-large btn-primary" id="save" value="Save" name="save">
                <input type="button" class="btn btn-large btn-primary btn-inverse" id="save_exit" value="Save & exit"
                       name="save_exit">
            </div>
        </div>
    </div>

    </form>





<?php } ?>