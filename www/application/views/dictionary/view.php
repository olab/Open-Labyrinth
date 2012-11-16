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
?>
<table width="100%" height="100%" cellpadding="6">
    <tr>
        <td valign="top" bgcolor="#bbbbcb">
            <h4><?php echo __('Dictionary manager '); ?></h4>
            <table width="100%" cellpadding="6" bgcolor="#ffffff">
                <tr>
                    <td>
                        <p><?php echo __('Upload a file to dictionary:'); ?></p>
                        <?php if ($templateData['result'] == 'success'){
                            echo '<p style="color:green">File successfully uploaded to dictionary</p>';
                        } ?>
                        <form method="POST" enctype="multipart/form-data" action="<?php echo URL::base().'dictionaryManager/uploadFile'; ?>">
                            <table width="100%" border="0" cellspacing="6">
                                <tr>
                                    <td nowrap="">
                                        <p>
                                            <?php echo __('Select file to upload:'); ?>
                                            <input type="FILE" size="50" name="filename" />
                                            <input type="submit" name="Submit" value="<?php echo __('submit'); ?>" />
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </form>
                        <hr/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <form method="POST" action="<?php echo URL::base().'dictionaryManager/addWord'; ?>">
                            <p>
                                <?php echo __('Add word in dictionary:'); ?>
                                <input type="text" name="word" size="50" />
                                <input type="submit" name="Search" value="<?php echo __('add'); ?>">
                            </p>
                        </form>
                        <hr/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <form method="POST" action="<?php echo URL::base().'dictionaryManager/search'; ?>">
                            <p>
                            <?php echo __('Search word in dictionary (you can enter just the beginning of word):'); ?>
                            <input type="text" name="search_value" value="<?php if (isset($_POST['search_value'])){ echo $_POST['search_value'];} ?>" size="50" />
                            <input type="submit" name="Search" value="<?php echo __('search'); ?>">
                            </p>
                        </form>
                        <?php if (isset($templateData['search_results'])){
                        if ((count($templateData['search_results']) > 0)){
                            echo '<p>'.__('Results:').'</p>';
                            echo '<form method="POST" action="'.URL::base().'dictionaryManager/wordsChanges">';
                            echo '<table style="color:#000; text-align: center; border:1px solid #000;" width="100%" border="0" cellspacing="6">';
                            echo '<tr><td>№</td><td>Word</td><td>Delete</td></tr>';
                            $i = 1;
                            foreach($templateData['search_results'] as $value){
                                echo '<tr>
                                <td>'.$i.'</td>
                                <td><input type="text" style="width:100%" name="word_value['.$value['id'].']" value="'.$value['word'].'" /></td>
                                <td><input type="checkbox" name="word_ch['.$value['id'].']" value="1" /></td>
                                </tr>';
                                $i++;
                            }
                            echo '</table>';
                            echo '<input type="submit" value="'.__('Submit changes').'" />';
                            echo '</form>';
                        }else{
                            echo '<p>'.__('No words was found').'</p>';
                        }
                        } ?>

                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>