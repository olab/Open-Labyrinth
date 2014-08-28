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
$user = Auth::instance()->get_user(); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=10,9,8" />
        <title><?php Breadcrumbs::render('breadcrumbs/title'); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <link rel="stylesheet" type="text/css" href="<?php echo ScriptVersions::get(URL::base().'css/jquery-ui-1.9.1.custom.css'); ?>">
        <link rel="stylesheet" type="text/css" href="<?php echo ScriptVersions::get(URL::base().'scripts/bootstrap/css/bootstrap.css'); ?>">
        <link rel="stylesheet" type="text/css" href="<?php echo ScriptVersions::get(URL::base().'scripts/bootstrap/css/bootstrap-responsive.css'); ?>">
        <link rel="stylesheet" type="text/css" href="<?php echo ScriptVersions::get(URL::base().'css/font-awesome.min.css'); ?>">
	    <link rel="stylesheet" type="text/css" href="<?php echo ScriptVersions::get(URL::base().'css/jquery.cropzoom.css'); ?>">
        <link rel="stylesheet" type="text/css" href="<?php echo ScriptVersions::get(URL::base().'scripts/datepicker/css/datepicker.css'); ?>">
        <link rel="stylesheet" type="text/css" href="<?php echo ScriptVersions::get(URL::base().'css/basic.css'); ?>">
        <link rel="stylesheet" type="text/css" href="<?php echo ScriptVersions::get(URL::base().'css/font.css'); ?>">
        <link rel="stylesheet" type="text/css" href="<?php echo ScriptVersions::get(URL::base().'scripts/farbtastic/farbtastic.css'); ?>">
        <link rel="stylesheet" type="text/css" href="<?php echo ScriptVersions::get(URL::base().'scripts/browser/css/BrowserUpdateWarning.css'); ?>">
        <!--[if IE 7]><link rel="stylesheet" type="text/css" href="<?php echo URL::base(); ?>css/font-awesome-ie7.min.css" /><![endif]-->
        <!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
        <link rel="shortcut icon" href="<?php echo URL::base(); ?>images/ico/favicon.ico" />

        <script type="text/javascript" src="<?php echo URL::base(); ?>scripts/jquery-1.7.2.min.js"></script>
    </head>
    <body>
        <div style="position: fixed;top:50%;left:50%;z-index: 1500;" id="collaboration_message" class="alert alert-success hide"><span id="collaboration_message_text">Message</span></div>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <a href="<?php echo URL::base(); ?>" class="brand">
                        <img src="<?php echo URL::base(); ?>images/openlabyrinth-header.png" alt="" />
                        <span>Open</span>Labyrinth
                    </a><?php
                    if (Auth::instance()->logged_in()) { ?>
                        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </a>
                        <div class="pull-right">
                            <div class="btn-group">
                                <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="icon-user"></i><?php echo $user->nickname; ?>
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo URL::base().'usermanager/viewUser/'.$user->id; ?>"><?php echo __('View Profile'); ?></a></li>
                                    <li><a href="<?php echo URL::base().'usermanager/editUser/'.$user->id; ?>"><?php echo __('Edit Profile'); ?></a></li>
                                    <li class="divider"></li>
                                    <li><a href="<?php echo URL::base(); ?>home/logout"><?php echo __('Logout'); ?></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="nav-collapse"><?php
                            $type_name = $user->type->name; ?>
                            <ul class="nav">
                                <li><a href="<?php echo URL::base(); ?>"><?php echo __('Home'); ?></a></li><?php
                                if($type_name != 'learner') { ?>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo __('Labyrinths'); ?> <b class="caret"></b></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="<?php echo URL::base().'authoredLabyrinth'; ?>"><?php echo __('My Labyrinths'); ?></a></li>
                                        <li><a href="<?php echo URL::base().'collectionManager'; ?>"><?php echo __('My Collections'); ?></a></li>
                                        <li class="divider"></li>
                                        <li class="nav-header"><?php echo __('Create Labyrinth'); ?></li>
                                        <li><a href="<?php echo URL::base().'labyrinthManager/caseWizard'; ?>"><?php echo __('Create Step-by-Step'); ?></a></li>
                                        <li><a href="<?php echo URL::base().'labyrinthManager/addManual'; ?>"><?php echo __('Create Manually'); ?></a></li>
                                        <li class="divider"></li>
                                        <li class="nav-header"><?php echo __('Import Labyrinths'); ?></li>
                                        <li><a href="<?php echo URL::base().'exportImportManager/import'; ?>"><?php echo __('Medbiquitous VP'); ?></a></li>
                                        <li class="divider"></li>
                                        <li class="nav-header"><?php echo __('Export Labyrinths'); ?></li>
                                        <li><a href="<?php echo URL::base().'exportImportManager/exportMVP'; ?>"><?php echo __('Medbiquitous VP'); ?></a></li>
                                        <li><a href="<?php echo URL::base().'exportImportManager/exportAdvanced'; ?>"><?php echo __('Advanced export'); ?></a></li>
                                    </ul>
                                </li><?php
                                    if ($type_name != 'author') { ?>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo __('Tools'); ?><b class="caret"></b></a>
                                        <ul class="dropdown-menu"><?php
                                            if ($user->modeUI == 'advanced') { ?>
                                            <li><a href="<?php echo URL::base(); ?>remoteServiceManager"><?php echo __('Remote Services'); ?></a></li>
                                            <li><a href="<?php echo URL::base().'usermanager'; ?>"><?php echo __('Users & Groups'); ?></a></li>
                                            <li class="divider"></li><?php
                                            }
                                            if ($type_name == 'superuser') { ?>
                                                <li><a href="<?php echo URL::base(); ?>systemManager"><?php echo __('System Settings'); ?></a></li><?php
                                                if ($user->modeUI == 'advanced') { ?>
                                                <li><a href="<?php echo URL::base(); ?>TodayTipManager/index">Today's tips</a></li>
                                                <li class="divider"></li>
                                                <li><a href="<?php echo URL::base(); ?>metadata/manager"><?php echo __('Metadata'); ?></a></li>
                                                <li><a href="<?php echo URL::base(); ?>vocabulary/manager"><?php echo __('Semantics'); ?></a></li><?php
                                                }
                                            } ?>
                                        </ul>
                                    </li><?php
                                    }
                                }
                                if($type_name == 'author' OR $type_name == 'superuser' OR $type_name == 'Director') { ?>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo __('Scenarios'); ?><b class="caret"></b></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="<?php echo URL::base(); ?>webinarManager/my"><?php echo __('My Scenarios'); ?></a></li><?php
                                            if ($user->modeUI == 'advanced') { ?>
                                            <li class="divider"></li>
                                            <li><a href="<?php echo URL::base(); ?>webinarManager/index"><?php echo __('Manage Scenarios'); ?></a></li><?php
                                            } ?>
                                        </ul>
                                    </li><?php
                                    if ($user->modeUI == 'advanced') { ?>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo __('Sets'); ?><b class="caret"></b></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="<?php echo URL::base().'patient/index'; ?>"><?php echo __('Managment'); ?></a></li>
                                            <li><a href="<?php echo URL::base().'patient/connection'; ?>"><?php echo __('Connection'); ?></a></li>
                                        </ul>
                                    </li><?php
                                    }
                                } else {
                                    if ($type_name != 'reviewer') {?>
                                    <li><a href="<?php echo URL::base().'collectionManager'; ?>"><?php echo __('My Collections'); ?></a></li><?php
                                    } ?>
                                    <li><a href="<?php echo URL::base().'webinarManager/my'; ?>"><?php echo __('My Scenarios'); ?></a></li><?php
                                } ?>
                                <li><a href="<?php echo URL::base(); ?>dforumManager"><?php echo __('Forums'); ?></a></li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo __('Help'); ?><b class="caret"></b></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="<?php echo URL::base().'home/about'; ?>">About</a></li>
                                        <li><a href="<?php echo URL::base().'home/userGuide'; ?>" target="_blank">User Guide</a></li>
                                        <li class="divider"></li>
                                        <li><a href="<?php echo URL::base().'base/ui/easy'; ?>" class="<?php if ($user->modeUI == 'easy') echo 'active btn-info'; ?>">Easy UI</a></li>
                                        <li><a href="<?php echo URL::base().'base/ui/advanced'; ?>" class="<?php if ($user->modeUI == 'advanced') echo 'active btn-info'; ?>">Advanced UI</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div><?php
                    } ?>
                </div>
            </div>
        </div>
        <div class="root-error-container">
            <div id="rootNodeMessage" class="alert alert-error root-alert hide">
                <button type="button" class="root-error-close close">×</button>
                <?php echo __('You do not have access to this case.'); ?>
            </div>
        </div><?php
    if (isset($templateData)){ ?>
        <div class="container-fluid">
            <div class="row-fluid"><?php
                if (Auth::instance()->logged_in()) { ?>
                    <div id="sidebar" class="span2"><?php
                        if (isset($templateData['labyrinthSearch']) AND isset($templateData['map'])) { ?>
                            <form action="<?php echo URL::base().'labyrinthManager/search/'.(isset($templateData['map']) AND ! is_numeric($templateData['map']) ? $templateData['map']->id : $templateData['map']); ?>" method="get">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="icon-search"></i></span>
                                    <input class="span10" id="searchText" name="s" type="text" value="<?php if(isset($templateData['searchText'])) echo $templateData['searchText']; ?>" placeholder="Labyrinth Search">
                                </div>
                            </form><?php
                        } else { ?>
                            <form action="<?php echo URL::base(); ?>home/search" method="post">
                                <input type="hidden" name="scope" value="t" />
                                <div class="input-prepend">
                                    <span class="add-on"><i class="icon-search"></i></span>
                                    <input class="span10" id="searchterm" name="searchterm" type="text" placeholder="Labyrinth Search">
                                </div>
                            </form><?php
                        } ?>

                        <div class="sidebar-nav">
                            <?php echo Arr::get($templateData, 'left', ''); ?>
                        </div>
                    </div>
                    <div id="content" class="span<?php echo (isset($templateData['right']) ? 8 : 10); ?>">
                        <div><?php Breadcrumbs::render(); ?></div>
                        <div class="row-fluid"><?php
                            echo Arr::get($templateData, 'error');
                            echo Arr::get($templateData, 'center'); ?>
                        </div>
                    </div><?php
                    if (isset($templateData['left'])) { ?>
					<div>
                        <a href="javascript:void(0)" class="toggles"><i class="icon-chevron-left"></i></a>
                    </div><?php
                    }
                    if (isset($templateData['right'])) { ?>
                        <div class="span2"><?php echo $templateData['right']; ?></div><?php
                    }
                } else { ?>
                    <div class="span3"><?php echo Arr::get($templateData, 'left'); ?></div>
                    <div class="span9"><?php echo Arr::get($templateData, 'center'); ?></div><?php
                } ?>
            </div>
        </div>
        <input type="hidden" id="browserWarningImages" value="<?php echo URL::base(); ?>scripts/browser/images/"/>

        <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/jquery-ui-1.9.1.custom.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/jquery.cookie.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/browser/js/BrowserUpdateWarning_jQuery.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/application.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/bootstrap/js/bootstrap.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/datepicker/js/bootstrap-datepicker.js'); ?>"></script>
        <script type="text/javascript">
            var historyAjaxCollaborationURL = '<?php echo URL::base().'home/historyAjaxCollaboration/'.Arr::get($templateData, 'user_id', 0); ?>',
                userHasBlockedAccess = <?php echo Arr::get($templateData, 'userHasBlockedAccess', 0); ?>,
                currentUserReadOnly = '<?php echo Arr::get($templateData, 'currentUserReadOnly', NULL); ?>',
                historyOfAllUsers = eval('(<?php echo Arr::get($templateData, 'historyOfAllUsers', '[]'); ?>)'),
                historyShowWarningPopup = <?php echo Arr::get($templateData, 'historyShowWarningPopup', 0); ?>;
            <?php
            if (isset($templateData['username'])) { ?>
                var currentUser = '<?php echo $templateData['username']; ?>';
            <?php } ?>
        </script><?php
    } else { ?>
        <div style="margin: 10px 100px;">You must choose chat</div><?php
    }; ?>
    </body>
</html>