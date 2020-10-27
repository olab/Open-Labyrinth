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

<h1><?php echo __('Search on term "'); ?><?php if(isset($searchText)) echo $searchText; ?>"</h1>

<table class="table table-striped table-bordered">
    <colgroup>
        <col style="width: 3%" />
        <col style="width: 2%" />
        <col style="width: 20%" />
        <col style="width: 50%" />
    </colgroup>
    <thead>
    <tr>
        <th><?php echo __('#'); ?></th>
        <th><?php echo __('Type'); ?></th>
        <th><?php echo __('URL'); ?></th>
        <th><?php echo __('Content'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php if(isset($data) && count($data) > 0) { ?>
    <?php $index = 1; foreach($data as $searcherElement) { ?>
    <tr>
        <td style="text-align: center"><?php echo $index; ?></td>
        <td style="text-align: center">
            <?php
            switch($searcherElement->type) {
                case 'node':
                    echo '<i class="icon-circle-blank" style="color: #0088cc; font-size: 18px;"></i>';
                    break;
                case 'counter':
                    echo '<i class="icon-dashboard" style="color: #0088cc; font-size: 18px;"></i>';
                    break;
                case 'question':
                    echo '<i class="icon-question-sign" style="color: #0088cc; font-size: 18px;"></i>';
                    break;
                case 'chat':
                    echo '<i class="icon-comments-alt" style="color: #0088cc; font-size: 18px;"></i>';
                    break;
                case 'section':
                    echo '<i class="icon-th-list" style="color: #0088cc; font-size: 18px;"></i>';
                    break;
                case 'vpd':
                    echo '<i class="icon-stethoscope" style="color: #0088cc; font-size: 18px;"></i>';
                    break;
                case 'dam':
                    echo '<i class="icon-tags" style="color: #0088cc; font-size: 18px;"></i>';
                    break;
            }
            ?>
        </td>
        <td>
            <a href="<?php echo $searcherElement->url; ?>"><?php echo $searcherElement->urlTitle; ?></a>
        </td>
        <td>
            <?php if($searcherElement->content != null && count($searcherElement->content) > 0) { ?>
                <?php foreach($searcherElement->content as $content) { ?>
                    <?php if($content['value'] == null || strlen($content['value']) <= 0 || empty($content['value'])) continue; ?>
                    <div>
                        <b><?php echo $content['label']; ?>: </b>
                        <?php

                        $value = strip_tags($content['value']);
                        $searchText = preg_replace('/\./', '\\.', $searcherElement->searchText);
                        $searchText = preg_replace('/\//', "\/", $searchText);
                        $searchText = preg_replace('/\*/', "\*", $searchText);
                        $searchText = preg_replace('/\+/', "\+", $searchText);
                        $regexp = '/(' . $searchText . ')/i';
                        $replace = '<span class="search-selected">${1}</span>';
                        $value = preg_replace($regexp, $replace, $value);

                        echo $value;
                        ?>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div>-</div>
            <?php } ?>
        </td>
    </tr>
    <?php $index++; } ?>
    <?php } else { ?>
    <tr>
        <td colspan="4"><?php echo __('No searched result'); ?></td>
    </tr>
    <?php } ?>
    </tbody>
</table>