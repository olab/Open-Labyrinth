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

class Controller_Backup extends Controller_Base {

    public function action_index()
    {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Backup Database')));

        $post = $this->request->post();
        $doBackup = Arr::get($post, 'doBackup', null);
        if(!empty($doBackup)){
            $this->templateData['result'] = $this->doBackup();
        }

        $this->templateData['save_types'] = array(1=>'Download as SQL file', 2=>'Show on screen');
        $this->templateData['center'] = View::factory('backup/index')->set('templateData', $this->templateData);
        unset($this->templateData['left']);
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    private function doBackup()
    {
        $post = $this->request->post();
        $save_type = Arr::get($post, 'save_type', 2);

        $backup = new DatabaseBackup();

        $db = Database::instance('default');
        $db->connect();
        $sql = $backup->backupTables();
        $db->disconnect();
        //if we want save file on server
        //if($save_type == 1) {
        //    if (!empty($sql)) {
        //        $result = $backup->saveFile($sql);
        //        $result = $result ? 1 : 2;
        //    }else{
        //        $result = 3;
        //    }
        //}
        if($save_type == 1){
            $fileName = 'DB_backup_'.date('Y-m-d H-i-s');
            header('Content-Type: application/sql');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            die($sql);
        }else{
            $result = $sql;
        }
        return $result;
    }
}