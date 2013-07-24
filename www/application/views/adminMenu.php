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

<div class="row-fluid">
    <div class="span8">
        <div class="pull-left">
            <img src="<?php echo URL::base(); ?>images/openlabyrinth-large.png" alt="" class="brand-large" />
        </div>
        <h1><?php echo __('Welcome to <span class="text-info">OpenLabyrinth</strong>'); ?></h1>
        <p class="lead">
            <?php echo __('OpenLabyrinth is a standards compliant open source virtual
                    patient authoring and player environment. It is maintained
                    by a group of enthusiasts who love the capability of
                    OpenLabyrinth for virtual patients.'); ?>
        </p>
        <?php if(isset($templateData['todayTip']) && $templateData['todayTip'] != null) { ?>
                <div class="box">
                    <h4 class="box-header round-top">OpenLabyrinth Tips-of-the-Day</h4>
                    <div class="box-container-toggle">
                        <div class="box-content">
                            <div>
                                <b><?php echo $templateData['todayTip']->title; ?></b>
                            </div>
                            <div>
                                <?php echo $templateData['todayTip']->text; ?>
                            </div>
                        </div>
                    </div>
                </div>
        <?php } ?>
    </div>
    <div class="span4">
        <?php if (isset($templateData['latestAuthoredLabyrinths'])) : ?>
        <div class="box">
            <h4 class="box-header round-top"><?php echo __('Latest Authored Labyrinths'); ?></h4>
            <div class="box-container-toggle">
                <div class="box-content">
                    <ul class="unstyled">
                    <?php
                    foreach ($templateData['latestAuthoredLabyrinths'] as $map) {
                        ?>
                        <li style="margin-bottom: 10px">
                            <div class="row-fluid">
                                <div class="pull-left">
                                    <a href="<?php echo URL::base() . 'labyrinthManager/global/' . $map->id; ?>"><?php echo substr($map->name, 0, 40); ?></a>
                                </div>
                                <div class="pull-right">
                                    <?php if(isset($templateData['rootNodeMap']) && isset($templateData['rootNodeMap'][$map->id]) && $templateData['rootNodeMap'][$map->id] != null) { ?>
                                        <a class="btn btn-mini btn-success" href="<?php echo URL::base(); ?>renderLabyrinth/index/<?php echo $map->id; ?>" target="_blank">
                                            <i class="icon-play icon-white"></i>
                                            Play
                                        </a>
                                    <?php } else { ?>
                                        <a class="btn btn-mini btn-success show-root-error" href="javascript:void(0)">
                                            <i class="icon-play icon-white"></i>
                                            Play
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>
                        </li>
                        <?php
                    }
                    ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php if (isset($templateData['latestPlayedLabyrinths'])) : ?>
        <div class="box">
            <h4 class="box-header round-top"><?php echo __('Latest Played Labyrinths'); ?></h4>
            <div class="box-container-toggle">
                <div class="box-content">
                    <ul class="unstyled">
                    <?php
                    foreach ($templateData['latestPlayedLabyrinths'] as $map) {
                        ?>
                        <li style="margin-bottom: 10px">
                            <div class="row-fluid">
                                <div class="pull-left">
                                    <a href="<?php echo URL::base() . 'labyrinthManager/global/' . $map->id; ?>"><?php echo substr($map->name, 0, 40); ?></a>
                                </div>
                                <div class="pull-right">
                                    <?php if(isset($templateData['rootNodeMap']) && isset($templateData['rootNodeMap'][$map->id]) && $templateData['rootNodeMap'][$map->id] != null) { ?>
                                        <a class="btn btn-mini btn-success" href="<?php echo URL::base(); ?>renderLabyrinth/index/<?php echo $map->id; ?>" target="_blank">
                                            <i class="icon-play icon-white"></i>
                                            Play
                                        </a>
                                    <?php } else { ?>
                                        <a class="btn btn-mini btn-success show-root-error" href="javascript:void(0)">
                                            <i class="icon-play icon-white"></i>
                                            Play
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>
                        </li>
                        <?php
                    }
                    ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

