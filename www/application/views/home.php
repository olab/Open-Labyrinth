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
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=10,9,8" />
        <title><?php Breadcrumbs::render('breadcrumbs/title'); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <link rel="stylesheet" href="<?php echo URL::base(); ?>css/jquery-ui-1.9.1.custom.css" />
        <link rel="stylesheet" href="<?php echo URL::base(); ?>scripts/bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" href="<?php echo URL::base(); ?>scripts/bootstrap/css/bootstrap-responsive.css" />
        <link rel="stylesheet" href="<?php echo URL::base(); ?>css/font-awesome.min.css" />
	<link rel="stylesheet" href="<?php echo URL::base(); ?>css/jquery.cropzoom.css" type="text/css" />

        <link rel="stylesheet" href="<?php echo URL::base(); ?>scripts/datepicker/css/datepicker.css" />
        <link rel="stylesheet" href="<?php echo ScriptVersions::get(URL::base().'css/basic.css'); ?>" />
        <link rel="stylesheet" href="<?php echo URL::base(); ?>scripts/farbtastic/farbtastic.css" type="text/css" />
        <!--[if IE 7]>
        <link rel="stylesheet" href="<?php echo URL::base(); ?>css/font-awesome-ie7.min.css" />

        <![endif]-->
        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        
        <link rel="stylesheet" href="<?php echo URL::base(); ?>scripts/browser/css/BrowserUpdateWarning.css" type="text/css" />
        
        <link rel="shortcut icon" href="<?php echo URL::base(); ?>images/ico/favicon.ico" />
        <link rel="apple-touch-icon-precomposed" href="<?php echo URL::base(); ?>images/ico/apple-touch-icon-57-precomposed.png" />
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo URL::base(); ?>images/ico/apple-touch-icon-72-precomposed.png" />
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo URL::base(); ?>images/ico/apple-touch-icon-114-precomposed.png" />
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo URL::base(); ?>images/ico/apple-touch-icon-144-precomposed.png" />
        <script type="text/javascript" src="<?php echo URL::base(); ?>scripts/jquery/jquery-1.8.2.js"></script>
    </head>
    <body>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <a href="<?php echo URL::base(); ?>" class="brand"><img src="<?php echo URL::base(); ?>images/openlabyrinth-header.png" alt="" /> <span>Open</span>Labyrinth</a>
                    <?php
                    if (Auth::instance()->logged_in()) {
                        ?>
                        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </a>
                        <div class="pull-right">
                            <div class="btn-group">
                                <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="icon-user"></i> <?php echo Auth::instance()->get_user()->nickname; ?>
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo URL::base(); ?>usermanager/viewUser/<?php echo Auth::instance()->get_user()->id; ?>"><?php echo __('View Profile'); ?></a></li>
                                    <li><a href="<?php echo URL::base(); ?>usermanager/editUser/<?php echo Auth::instance()->get_user()->id; ?>"><?php echo __('Edit Profile'); ?></a></li>
                                    <li class="divider"></li>
                                    <li><a href="<?php echo URL::base(); ?>home/logout"><?php echo __('Logout'); ?></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="nav-collapse">
                            <ul class="nav">
                                <li><a href="<?php echo URL::base(); ?>"><?php echo __('Home'); ?></a></li>
                                <?php if(Auth::instance()->get_user()->type->name != 'learner') { ?>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo __('Labyrinths'); ?> <b class="caret"></b></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="<?php echo URL::base() . 'authoredLabyrinth'; ?>"><?php echo __('My Labyrinths'); ?></a></li>
                                        <li><a href="<?php echo URL::base() . 'collectionManager'; ?>"><?php echo __('My Collections'); ?></a></li>
                                        <li class="divider"></li>
                                        <li class="nav-header"><?php echo __('Create Labyrinth'); ?></li>
                                        <li><a href="<?php echo URL::base() . 'labyrinthManager/caseWizard'; ?>"><?php echo __('Create Step-by-Step'); ?></a></li>
                                        <li><a href="<?php echo URL::base() . 'labyrinthManager/addManual'; ?>"><?php echo __('Create Manually'); ?></a></li>
                                        <li class="divider"></li>
                                        <li class="nav-header"><?php echo __('Import Labyrinths'); ?></li>
                                        <li><a href="<?php echo URL::base() . 'exportImportManager/importVUE'; ?>"><?php echo __('VUE'); ?></a></li>
                                        <li><a href="<?php echo URL::base() . 'exportImportManager/importMVP'; ?>"><?php echo __('Medbiquitous VP'); ?></a></li>
                                        <li class="divider"></li>
                                        <li class="nav-header"><?php echo __('Export Labyrinths'); ?></li>
                                        <li><a href="<?php echo URL::base() . '#'; ?>"><?php echo __('XML'); ?></a></li>
                                        <li><a href="<?php echo URL::base() . 'exportImportManager/exportMVP'; ?>"><?php echo __('Medbiquitous ANSI'); ?></a></li>
                                    </ul>
                                </li>
                                <?php if(Auth::instance()->get_user()->type->name != 'author') { ?>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo __('Tools'); ?> <b class="caret"></b></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="<?php echo URL::base(); ?>presentationManager"><?php echo __('Manage Presentations'); ?></a></li>
                                        <li><a href="<?php echo URL::base(); ?>remoteServiceManager"><?php echo __('Manage Remote Services'); ?></a></li>
                                        <li><a href="<?php echo URL::base() . 'usermanager'; ?>"><?php echo __('Manage Users & Groups'); ?></a></li>
                                        <?php
                                        if (Auth::instance()->get_user()->type->name == 'superuser') {
                                            ?>
                                            <li class="divider"></li>
                                            <li><a href="<?php echo URL::base(); ?>systemManager"><?php echo __('System Settings'); ?></a></li>
                                            <?php
                                        }
                                        ?>

                                        <li><a href="<?php echo URL::base(); ?>TodayTipManager/index">Today's tips</a></li>

                                        <?php if (Auth::instance()->get_user()->type->name == 'superuser') { ?>
                                            <li class="divider"></li>
                                            <li><a href="<?php echo URL::base(); ?>metadata/manager"><?php echo __('Manage Metadata'); ?></a></li>
                                            <li>
                                            <a href="<?php echo URL::base(); ?>vocabulary/manager"><?php echo __('Manage Semantics'); ?></a></li>
                                        <?php
                                        }
                                        ?>
                                    </ul>
                                </li>
                                <?php } ?>
                                <?php } ?>

                                <?php if(Auth::instance()->get_user()->type->name == 'author' || Auth::instance()->get_user()->type->name == 'superuser') { ?>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo __('Scenario Management'); ?> <b class="caret"></b></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="<?php echo URL::base(); ?>webinarManager/my"><?php echo __('My Scenarios'); ?></a></li>
                                            <li class="divider"></li>
                                            <li><a href="<?php echo URL::base(); ?>webinarManager/index"><?php echo __('Manage Scenarios'); ?></a></li>
                                        </ul>
                                    </li>
                                <?php } else { ?>
                                    <?php if (Auth::instance()->get_user()->type->name != 'reviewer') {?>
                                        <li><a href="<?php echo URL::base() . 'collectionManager'; ?>"><?php echo __('My Collections'); ?></a></li>
                                    <?php } ?>
                                    <li><a href="<?php echo URL::base(); ?>webinarManager/my"><?php echo __('My Scenarios'); ?></a></li>
                                <?php } ?>

                                <li><a href="<?php echo URL::base(); ?>dforumManager"><?php echo __('Forums'); ?></a></li>
                                <li><a href="#"><?php echo __('Help'); ?></a></li>
                            </ul>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="root-error-container">
            <div id="rootNodeMessage" class="alert alert-error root-alert hide">
                <button type="button" class="root-error-close close">×</button>
                <?php echo __('You have not set a root node, so your labyrinth will not play. Please return to the visual editor, and click on the starting node -> Actions -> Set as Root'); ?>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row-fluid">
                <?php
                if (Auth::instance()->logged_in()) {
                    ?>
                    <div id="sidebar" class="span2">
                        <?php if(isset($templateData['labyrinthSearch']) && isset($templateData['map'])) { ?>
                            <form action="<?php echo URL::base(); ?>labyrinthManager/search<?php echo '/' . (isset($templateData['map']) && !is_numeric($templateData['map'])  ? $templateData['map']->id : $templateData['map']); ?>" method="get">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="icon-search"></i></span>
                                    <input class="span10" id="searchText" name="s" type="text" value="<?php if(isset($templateData['searchText'])) echo $templateData['searchText']; ?>" placeholder="Labyrinth Search">
                                </div>
                            </form>
                        <?php } else { ?>
                            <form action="<?php echo URL::base(); ?>home/search" method="post">
                                <input type="hidden" name="scope" value="t" />
                                <div class="input-prepend">
                                    <span class="add-on"><i class="icon-search"></i></span>
                                    <input class="span10" id="searchterm" name="searchterm" type="text" placeholder="Labyrinth Search">
                                </div>
                            </form>
                        <?php } ?>

                        <div class="sidebar-nav">
                            <?php if (isset($templateData['left'])) echo $templateData['left']; ?>
                        </div>
                    </div>
                    <div id="content" class="span<?php echo (isset($templateData['right']) ? 8 : 10); ?>">
                        <div>
                            <?php Breadcrumbs::render(); ?>
                        </div>

                        <div class="row-fluid">
                            <?php if (isset($templateData['error'])) echo $templateData['error']; ?>
                            <?php if (isset($templateData['center'])) echo $templateData['center']; ?>
                        </div>
                    </div>
                    <?php if(isset($templateData['left'])) ?>
					<div>
                        <a href="javascript:void(0)" class="toggles"><i class="icon-chevron-left"></i></a>
                    </div>
                    <?php ?>
                    <?php
                    if (isset($templateData['right'])) {
                        ?>
                        <div class="span2">
                            <?php echo $templateData['right']; ?>
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <div class="span3">
                        <?php if (isset($templateData['left'])) echo $templateData['left']; ?>
                    </div>
                    <div class="span9">
                        <?php if (isset($templateData['center'])) echo $templateData['center']; ?>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <input type="hidden" id="browserWarningImages" value="<?php echo URL::base(); ?>scripts/browser/images/"/>

        <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/jquery-ui-1.9.1.custom.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/jquery.cookie.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/browser/js/BrowserUpdateWarning_jQuery.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/application.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/bootstrap/js/bootstrap.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/datepicker/js/bootstrap-datepicker.js'); ?>"></script>
    </body>
</html>