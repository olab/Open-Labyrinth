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
defined('SYSPATH') or die('No direct script access.');

$stepIndex = Session::get('installationStep', '1');
$templateData['token'] = Security::token();
$token = Arr::get($_POST, 'token', NULL);

if ($token != NULL){
    $skipInstallation = Arr::get($_POST, 'skipInstallation', NULL);
    $previousStep = Arr::get($_POST, 'previousStep', NULL);
    if ($skipInstallation != NULL){
        Installation::terminate();
    } else if ($previousStep != NULL){
        Installation::action_previousStep();
    } else {
        switch ($stepIndex){
            case 1:
                Installation::action_systemOverview();
            case 2:
                Installation::action_configuration();
                break;
            case 3:
                Installation::action_database();
                break;
            case 4:
                Installation::action_overview();
                break;
        }
    }
}
switch ($stepIndex){
    case 1:
        $templateData['pre-check'] = Installation::getPreCheckResult();
        $templateData['file_objects'] = Installation::getFileObjectsResult();
        if ((is_writable(DOCROOT.'install.php')) AND (is_dir(DOCROOT.'installation') AND is_writable(DOCROOT.'installation'))){
            $templateData['MskipInstallation'] = true;
        } else {
            $templateData['skipInstallation'] = false;
        }
    case 2:
        $olab = Session::get('installationConfiguration');
        $templateData['data'] = ($olab != null) ? json_decode($olab) : null;
        break;
    case 3:
        $olab = Session::get('installationDatabase');
        $templateData['data'] = ($olab != null) ? json_decode($olab) : null;
        break;
        break;
    case 4:
        $olab = Session::get('installationConfiguration');
        $templateData['configuration'] = ($olab != null) ? json_decode($olab) : null;
        $olab = Session::get('installationDatabase');
        $templateData['database'] = ($olab != null) ? json_decode($olab) : null;
        $templateData['pre-check'] = Installation::getPreCheckResult();
        $templateData['file_objects'] = Installation::getFileObjectsResult();
        $templateData['recommended'] = Installation::getRecommendedResult();
        break;
    case 5:
        Installation::proceed();
        exit;
        break;
}
$templateData['stepIndex'] = $stepIndex;
