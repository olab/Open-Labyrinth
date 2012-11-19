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

class Controller_DictionaryManager extends Controller_Base {
    public function action_index() {
        $this->templateData['search_results'] = null;
        $this->templateData['result'] = $this->request->param('id', NULL);
        $dictionaryView = View::factory('dictionary/view');
        $dictionaryView->set('templateData', $this->templateData);

        unset($this->templateData['left']);
        $this->templateData['center'] = $dictionaryView;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_uploadFile(){
        if($_FILES) {
            $result = DB_ORM::model('dictionary')->uploadFile($_FILES);
            if ($result){
                Request::initial()->redirect(URL::base().'dictionaryManager/index/success');
            }else{
                Request::initial()->redirect(URL::base().'dictionaryManager/index/error');
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_addWord(){
        if (isset($_POST['word'])){
            $term = str_replace("'", "''", htmlspecialchars($_POST['word']));
            DB_ORM::model('dictionary')->addWord($term);
            Request::initial()->redirect(URL::base().'dictionaryManager');
        }else{
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_search(){
        $this->templateData['result'] = null;
        if (isset($_POST['search_value'])){
            $term = htmlspecialchars($_POST['search_value']);
            $this->templateData['search_results'] = DB_ORM::model('dictionary')->getWords($term);
            $dictionaryView = View::factory('dictionary/view');
            $dictionaryView->set('templateData', $this->templateData);

            unset($this->templateData['left']);
            $this->templateData['center'] = $dictionaryView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        }else{
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_wordsChanges(){
        if (isset($_POST['word_value'])){
            foreach($_POST['word_value'] as $key => $value){
                $term['id'] = $key;
                $term['word'] = $value;
                if (isset($_POST['word_ch'][$key])){
                    $term['delete'] = $_POST['word_ch'][$key];
                }else{
                    $term['delete'] = 0;
                }

                DB_ORM::model('dictionary')->updateWord($term);
            }
            Request::initial()->redirect(URL::base().'dictionaryManager');
        }else{
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_getjson() {
        $term = htmlspecialchars($_GET['term']);
        $result = DB_ORM::model('dictionary')->getWordsName($term);
        echo json_encode($result);
        die();
    }
}