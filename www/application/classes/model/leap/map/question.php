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

/**
 * @property int $id
 * @property int $map_id
 * @property int $entry_type_id
 * @property int $width
 * @property int $height
 * @property int $num_tries
 * @property int $redirect_node_id
 * @property int $type_display
 * @property int $counter_id
 * @property int|bool $show_answer
 * @property int|bool $show_submit
 * @property int|bool $is_private
 * @property string $stem
 * @property string $feedback
 * @property string $prompt
 * @property string $submit_text
 * @property string $settings
 * @property Model_Leap_Map_Question_Response[]|DB_ResultSet $responses
 * @property Model_Leap_User_Response[]|DB_ResultSet $user_responses
 * @property Model_Leap_Map_Counter $counter
 * @property Model_Leap_Map $map
 * @property Model_Leap_Map_Question_Type $type
 */
class Model_Leap_Map_Question extends Model_Leap_Base
{

    const ENTRY_TYPE_SINGLE_LINE = 1;
    const ENTRY_TYPE_MULTI_LNE = 2;
    const ENTRY_TYPE_MCQ = 3;
    const ENTRY_TYPE_PCQ = 4;
    const ENTRY_TYPE_SLIDER = 5;
    const ENTRY_TYPE_DRAG_AND_DROP = 6;
    const ENTRY_TYPE_SCT = 7;
    const ENTRY_TYPE_SJT = 8;
    const ENTRY_TYPE_CUMULATIVE= 9;
    const ENTRY_TYPE_RICH_TEXT = 10;
    const ENTRY_TYPE_TURK_TALK = 11;
    const ENTRY_TYPE_DROP_DOWN = 12;
    const ENTRY_TYPE_MCQ_GRID = 13;
    const ENTRY_TYPE_PCQ_GRID = 14;

    public function __construct()
    {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
                'unsigned' => true,
            )),
            'map_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
            )),
            'stem' => new DB_ORM_Field_String($this, array(
                'max_length' => 500,
                'nullable' => false,
                'savable' => true,
            )),
            'entry_type_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
            )),
            'width' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
            )),
            'height' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
            )),
            'feedback' => new DB_ORM_Field_String($this, array(
                'max_length' => 1000,
                'nullable' => false,
                'savable' => true,
            )),
            'prompt' => new DB_ORM_Field_Text($this, array(
                'nullable' => false,
                'savable' => true,
            )),
            'show_answer' => new DB_ORM_Field_Boolean($this, array(
                'default' => false,
                'nullable' => false,
                'savable' => true,
            )),
            'counter_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => true,
            )),
            'num_tries' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
            )),
            'show_submit' => new DB_ORM_Field_Boolean($this, array(
                'default' => false,
                'nullable' => false,
                'savable' => true,
            )),
            'redirect_node_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => true,
                'unsigned' => true,
            )),
            'submit_text' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => true,
                'savable' => true,
            )),
            'type_display' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
            )),
            'settings' => new DB_ORM_Field_Text($this, array(
                'nullable' => true,
                'savable' => true,
            )),
            'is_private' => new DB_ORM_Field_Boolean($this, array(
                'savable' => true,
                'nullable' => false,
                'default' => false
            ))
        );

        $this->relations = array(
            'counter' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('counter_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_counter',
            )),
            'map' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('map_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map',
            )),
            'type' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('entry_type_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_question_type',
            )),
            'responses' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('question_id'),
                'child_model' => 'map_question_response',
                'parent_key' => array('id'),
                'options' => array(array('order_by', array('map_question_responses.order', 'ASC')))
            )),
            'user_responses' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('question_id'),
                'child_model' => 'user_response',
                'parent_key' => array('id'),
            )),
        );
    }

    public static function data_source()
    {
        return 'default';
    }

    public static function table()
    {
        return 'map_questions';
    }

    public static function primary_key()
    {
        return array('id');
    }


    public static function getAdminBaseUrl()
    {
        return URL::base(true) . 'questionManager/question/';
    }

    public function getAdminUrl()
    {
        return static::getAdminBaseUrl() . $this->map_id . '/' . $this->entry_type_id . '/' . $this->id;
    }

    public function toxAPIExtensionObject()
    {
        $result = $this->as_array();
        $result['id'] = $this->getAdminUrl();
        $result['internal_id'] = $this->id;

        return $result;
    }

    public function toxAPIObject()
    {
        $url = $this->getAdminUrl();
        $object = array(
            'id' => $url,
            'definition' => array(
                'name' => array(
                    'en-US' => 'question (#' . $this->id . ')'
                ),
                'description' => array(
                    'en-US' => 'Question stem: ' . Model_Leap_Statement::sanitizeString($this->stem)
                ),
                'type' => 'http://adlnet.gov/expapi/activities/cmi.interaction',
                'moreInfo' => $url,
            ),

        );

        $object['definition']['extensions'][Model_Leap_Statement::getExtensionQuestionKey()] = $this->toxAPIExtensionObject();

        return $object;
    }

    public function getQuestionsByMap($mapId)
    {
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('map_id', '=', $mapId);

        $result = $builder->query();

        if ($result->is_loaded()) {
            $questions = array();
            foreach ($result as $record) {
                $questions[] = DB_ORM::model('map_question', array((int)$record['id']));
            }

            return $questions;
        }

        return array();
    }

    public function getQuestionsByMapAndTypes($mapId, $types)
    {
        $questions = array();
        $result = DB_SQL::select('default')
            ->from($this->table())
            ->where('map_id', '=', $mapId, 'AND')
            ->where('entry_type_id', 'IN', $types)
            ->column('id')
            ->query();

        foreach ($result as $record) {
            $questions[] = DB_ORM::model('map_question', array((int)$record['id']));
        }

        return $questions;
    }

    public function addQuestion($mapId, $type, $values)
    {
        switch ($type->value) {
            case "text":
                return $this->saveTextQuestion($mapId, $type, $values);
                break;
            case "ttalk":
            case "area":
                return $this->saveAreaQuestion($mapId, $type, $values);
                break;
            case 'slr':
                $this->saveSliderQuestion($mapId, $type, $values);
                break;
            default:
                $this->saveResponceQuestion($mapId, $type, $values);
                break;
        }
    }

    public function createEmptyQuestion($mapId, $typeId)
    {
        return DB_ORM::insert('map_question')
            ->column('map_id', $mapId)
            ->column('entry_type_id', $typeId)
            ->execute();
    }

    public function updateQuestion($questionId, $type, $values)
    {
        $this->id = $questionId;
        $this->load();
        switch ($type->value) {
            case "text":
                $this->updateTextQuestion($values);
                break;
            case "ttalk":
            case "area":
                $this->updateAreaQuestion($values);
                break;
            case 'slr':
                $this->updateSliderQuestion($questionId, $values);
                break;
            default:
                $this->updateResponseQuestion($values, $type->id);
                break;
        }
    }

    private function updateSliderQuestion($questionId, $values)
    {
        DB_ORM::update('map_question')
            ->set('stem', Arr::get($values, 'stem', ''))
            ->set('feedback', Arr::get($values, 'fback', ''))
            ->set('counter_id', (int)Arr::get($values, 'counter', 0))
            ->set('is_private', (int)Arr::get($values, 'is-private', 0) ? 1 : 0)
            ->set('settings', json_encode(array(
                'minValue' => Arr::get($values, 'minValue', 0),
                'maxValue' => Arr::get($values, 'maxValue', 0),
                'stepValue' => Arr::get($values, 'stepValue', 1),
                'orientation' => Arr::get($values, 'question_orientation', 'hor'),
                'showValue' => Arr::get($values, 'question_chosen_value', 0),
                'sliderSkin' => Arr::get($values, 'sliderSkin', ''),
                'abilityValue' => Arr::get($values, 'question_ability_input', 0),
                'defaultValue' => Arr::get($values, 'defaultValue', 0)
            )))
            ->where('id', '=', $questionId)
            ->execute();

        $responses = DB_ORM::model('map_question_response')->getResponsesByQuestion($questionId);

        if ($responses != null && count($responses) > 0) {
            foreach ($responses as $response) {
                if (isset($values['response_' . $response->id])) {
                    $response->from = Arr::get($values, 'from_' . $response->id, '');
                    $response->to = Arr::get($values, 'to_' . $response->id, '');
                    $response->is_correct = Arr::get($values, 'correctness_' . $response->id, 0);
                    $response->score = Arr::get($values, 'score_' . $response->id, 0);

                    $response->save();
                } else {
                    $response->delete();
                }
            }
        }

        $newResponses = array();
        foreach ($values as $key => $value) {
            if (!(strpos($key, 'interval_from_') === false)) {
                $id = str_replace('interval_from_', '', str_replace('_n', '', $key));
                if (strlen($id) > 0) {
                    $newResponses[$id]['from'] = $value;
                }
            } else {
                if (!(strpos($key, 'interval_to_') === false)) {
                    $id = str_replace('interval_to_', '', str_replace('_n', '', $key));
                    if (strlen($id) > 0) {
                        $newResponses[$id]['to'] = $value;
                    }
                } else {
                    if (!(strpos($key, 'correctness_') === false)) {
                        $id = str_replace('correctness_', '', str_replace('_n', '', $key));
                        if (strlen($id) > 0) {
                            $newResponses[$id]['correctness'] = $value;
                        }
                    } else {
                        if (!(strpos($key, 'score_') === false)) {
                            $id = str_replace('score_', '', str_replace('_n', '', $key));
                            if (strlen($id) > 0) {
                                $newResponses[$id]['score'] = $value;
                            }
                        }
                    }
                }
            }
        }

        if (count($newResponses) > 0) {
            foreach ($newResponses as $newResponse) {
                DB_ORM::insert('map_question_response')
                    ->column('question_id', $questionId)
                    ->column('from', Arr::get($newResponse, 'from', ''))
                    ->column('to', Arr::get($newResponse, 'to', ''))
                    ->column('is_correct', (int)Arr::get($newResponse, 'correctness', 0))
                    ->column('score', (int)Arr::get($newResponse, 'score', 0))
                    ->execute();
            }
        }
    }

    private function updateTextQuestion($values)
    {
        $this->stem = Arr::get($values, 'qstem', $this->stem);
        $this->width = Arr::get($values, 'qwidth', $this->width);
        $this->feedback = Arr::get($values, 'fback', $this->feedback);
        $this->prompt = Arr::get($values, 'prompt', $this->prompt);
        $this->settings = Arr::get($values, 'settings', $this->feedback);
        $this->show_submit = Arr::get($values, 'showSubmit', $this->show_submit);
        $this->submit_text = Arr::get($values, 'submitButtonText', $this->submit_text);
        $this->is_private = Arr::get($values, 'is_private', false);

        $this->save();
    }

    private function updateAreaQuestion($values)
    {
        $this->stem = Arr::get($values, 'qstem', $this->stem);
        $this->width = Arr::get($values, 'qwidth', $this->width);
        $this->height = Arr::get($values, 'qheight', $this->height);
        $this->feedback = Arr::get($values, 'fback', $this->feedback);
        $this->prompt = Arr::get($values, 'prompt', $this->prompt);
        $this->settings = Arr::get($values, 'settings', $this->feedback);
        $this->is_private = Arr::get($values, 'is_private', false);
        $this->save();
    }

    private function updateResponseQuestion($values, $typeID = null)
    {
        if ($typeID != null) {
            $this->entry_type_id = $typeID;
        }
        $this->stem = Arr::get($values, 'stem', $this->stem);
        $this->feedback = Arr::get($values, 'feedback', $this->feedback);
        $this->show_answer = Arr::get($values, 'showAnswer', $this->show_answer);
        $this->counter_id = Arr::get($values, 'counter', $this->counter_id);
        $this->num_tries = Arr::get($values, 'tries', $this->num_tries);
        $this->show_submit = Arr::get($values, 'showSubmit', $this->show_submit);
        $this->redirect_node_id = Arr::get($values, 'redirectNode', $this->redirect_node_id);
        $this->submit_text = Arr::get($values, 'submitButtonText', $this->submit_text);
        $this->type_display = Arr::get($values, 'typeDisplay', $this->submit_text);
        $this->is_private = Arr::get($values, 'is_private', false);
        $this->settings = json_encode(Arr::get($values, 'settingsJSON', []));

        $this->save();

        DB_ORM::model('map_question_response')->updateResponses($this->id, $values);
    }

    private function saveTextQuestion($mapId, $type, $values)
    {
        return DB_ORM::insert('Map_Question')
            ->column('map_id', $mapId)
            ->column('entry_type_id', $type->id)
            ->column('stem', Arr::get($values, 'qstem', ''))
            ->column('width', Arr::get($values, 'qwidth', 0))
            ->column('feedback', Arr::get($values, 'fback', ''))
            ->column('prompt', Arr::get($values, 'prompt', ''))
            ->column('settings', Arr::get($values, 'settings', ''))
            ->column('is_private', Arr::get($values, 'is_private', false))
            ->execute();
    }

    private function saveAreaQuestion($mapId, $type, $values)
    {
        return DB_ORM::insert('Map_Question')
            ->column('map_id', $mapId)
            ->column('entry_type_id', $type->id)
            ->column('stem', Arr::get($values, 'qstem', ''))
            ->column('width', Arr::get($values, 'qwidth', 0))
            ->column('height', Arr::get($values, 'qheight', 0))
            ->column('feedback', Arr::get($values, 'fback', ''))
            ->column('prompt', Arr::get($values, 'prompt', ''))
            ->column('settings', Arr::get($values, 'settings', ''))
            ->column('is_private', Arr::get($values, 'is_private', false))
            ->execute();
    }

    public function addFullQuestion($mapId, $values)
    {
        $this->map_id = $mapId;
        $this->entry_type_id = Arr::get($values, 'entry_type_id', '');;
        $this->stem = Arr::get($values, 'stem', '');
        $this->width = Arr::get($values, 'width', 0);
        $this->height = Arr::get($values, 'height', 0);
        $this->feedback = Arr::get($values, 'feedback', '');
        $this->show_answer = Arr::get($values, 'show_answer', 0);
        $this->num_tries = Arr::get($values, 'num_tries', 0);
        $this->counter_id = Arr::get($values, 'counter_id', 0);

        $this->save();

        return $this->getLastAddedQuestion($mapId);
    }

    public function getLastAddedQuestion($mapId)
    {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId)->order_by('id',
            'DESC')->limit(1);
        $result = $builder->query();

        if ($result->is_loaded()) {
            return DB_ORM::model('map_question', array($result[0]['id']));
        }

        return null;
    }

    public function duplicateQuestions($fromMapId, $toMapId, $counterMap)
    {
        if (!$toMapId) {
            return array();
        }

        $questionMap = array();

        foreach ($this->getQuestionsByMap($fromMapId) as $question) {
            $questionMap[$question->id] = DB_ORM::insert('map_question')
                ->column('map_id', $toMapId)
                ->column('stem', $question->stem)
                ->column('entry_type_id', $question->entry_type_id)
                ->column('width', $question->width)
                ->column('height', $question->height)
                ->column('feedback', $question->feedback)
                ->column('show_answer', $question->show_answer)
                ->column('num_tries', $question->num_tries)
                ->column('show_submit', $question->show_submit)
                ->column('redirect_node_id', $question->redirect_node_id)
                ->column('submit_text', $question->submit_text)
                ->column('type_display', $question->type_display)
                ->column('counter_id', Arr::get($counterMap, $question->counter_id))
                ->execute();

            DB_ORM::model('map_question_response')->duplicateResponses($question->id, $questionMap[$question->id]);
        }

        return $questionMap;
    }

    public function duplicateQuestion($questionId)
    {
        if ($questionId == null || $questionId <= 0) {
            return;
        }

        $question = DB_ORM::model('map_question', array((int)$questionId));
        if ($question == null) {
            return;
        }

        $builder = DB_ORM::insert('map_question')
            ->column('map_id', $question->map_id)
            ->column('stem', $question->stem)
            ->column('entry_type_id', $question->entry_type_id)
            ->column('width', $question->width)
            ->column('height', $question->height)
            ->column('feedback', $question->feedback)
            ->column('prompt', $question->prompt)
            ->column('show_answer', $question->show_answer)
            ->column('num_tries', $question->num_tries)
            ->column('counter_id', $question->counter_id)
            ->column('show_submit', $question->show_submit)
            ->column('redirect_node_id', $question->redirect_node_id)
            ->column('submit_text', $question->submit_text)
            ->column('type_display', $question->type_display)
            ->column('settings', $question->settings);

        $newId = $builder->execute();

        if (count($question->responses) > 0) {
            foreach ($question->responses as $response) {
                DB_ORM::insert('map_question_response')
                    ->column('question_id', $newId)
                    ->column('response', $response->response)
                    ->column('feedback', $response->feedback)
                    ->column('is_correct', $response->is_correct)
                    ->column('score', $response->score)
                    ->column('to', $response->to)
                    ->column('from', $response->from)
                    ->execute();
            }
        }
    }

    private function saveSliderQuestion($mapId, $type, $values)
    {
        $newQuestionId = DB_ORM::insert('map_question')
            ->column('map_id', $mapId)
            ->column('entry_type_id', $type->id)
            ->column('feedback', Arr::get($values, 'fback', ''))
            ->column('stem', Arr::get($values, 'stem', ''))
            ->column('counter_id', (int)Arr::get($values, 'counter', 0))
            ->column('settings', json_encode(array(
                'minValue' => Arr::get($values, 'minValue', 0),
                'maxValue' => Arr::get($values, 'maxValue', 0),
                'stepValue' => Arr::get($values, 'stepValue', 1),
                'orientation' => Arr::get($values, 'question_orientation', 'hor'),
                'showValue' => Arr::get($values, 'question_chosen_value', 0),
                'sliderSkin' => Arr::get($values, 'sliderSkin', ''),
                'abilityValue' => Arr::get($values, 'question_ability_input', 0),
                'defaultValue' => Arr::get($values, 'defaultValue', 0)
            )))
            ->column('is_private', (int)Arr::get($values, 'is_private', false) ? 1 : 0)
            ->execute();

        $responses = array();
        foreach ($values as $key => $value) {
            if (!(strpos($key, 'interval_from_') === false)) {
                $id = str_replace('interval_from_', '', str_replace('_n', '', $key));
                if (strlen($id) > 0) {
                    $responses[$id]['from'] = $value;
                }
            } else {
                if (!(strpos($key, 'interval_to_') === false)) {
                    $id = str_replace('interval_to_', '', str_replace('_n', '', $key));
                    if (strlen($id) > 0) {
                        $responses[$id]['to'] = $value;
                    }
                } else {
                    if (!(strpos($key, 'correctness_') === false)) {
                        $id = str_replace('correctness_', '', str_replace('_n', '', $key));
                        if (strlen($id) > 0) {
                            $responses[$id]['correctness'] = $value;
                        }
                    } else {
                        if (!(strpos($key, 'score_') === false)) {
                            $id = str_replace('score_', '', str_replace('_n', '', $key));
                            if (strlen($id) > 0) {
                                $responses[$id]['score'] = $value;
                            }
                        }
                    }
                }
            }
        }

        if (count($responses) > 0) {
            foreach ($responses as $response) {
                DB_ORM::insert('map_question_response')
                    ->column('question_id', $newQuestionId)
                    ->column('from', Arr::get($response, 'from', ''))
                    ->column('to', Arr::get($response, 'to', ''))
                    ->column('is_correct', (int)Arr::get($response, 'correctness', 0))
                    ->column('score', (int)Arr::get($response, 'score', 0))
                    ->execute();
            }
        }
    }

    private function saveResponceQuestion($mapId, $type, $values)
    {
        $newQuestionId = DB_ORM::insert('map_question')
            ->column('map_id', $mapId)
            ->column('entry_type_id', $type->id)
            ->column('stem', Arr::get($values, 'stem', ''))
            ->column('feedback', Arr::get($values, 'feedback', ''))
            ->column('show_answer', (int)Arr::get($values, 'showAnswer', 0))
            ->column('counter_id', (int)Arr::get($values, 'counter', 0))
            ->column('num_tries', (int)Arr::get($values, 'tries', 1))
            ->column('show_submit', (int)Arr::get($values, 'showSubmit', 0))
            ->column('redirect_node_id', (int)Arr::get($values, 'redirectNode', null))
            ->column('submit_text', Arr::get($values, 'submitButtonText', 'Submit'))
            ->column('type_display', (int)Arr::get($values, 'typeDisplay', 0))
            ->column('is_private', (int)Arr::get($values, 'is_private', false) ? 1 : 0)
            ->column('settings', json_encode(Arr::get($values, 'settingsJSON', [])))
            ->execute();

        $responses = array();
        $responsesJSONs = Arr::get($values, 'responses', null);
        if ($responsesJSONs != null && count($responsesJSONs) > 0) {
            $responseIndex = 0;
            foreach ($responsesJSONs as $responsesJSON) {
                $object = json_decode($responsesJSON, true);

                if ($object == null) {
                    continue;
                }

                if (isset($object['response'])) {
                    $responses[$responseIndex]['response'] = urldecode(str_replace('+', '&#43;',
                        base64_decode($object['response'])));
                }
                if (isset($object['feedback'])) {
                    $responses[$responseIndex]['feedback'] = urldecode(str_replace('+', '&#43;',
                        base64_decode($object['feedback'])));
                }
                if (isset($object['correctness'])) {
                    $responses[$responseIndex]['correctness'] = $object['correctness'];
                }
                if (isset($object['score'])) {
                    $responses[$responseIndex]['score'] = $object['score'];
                }
                if (isset($object['order'])) {
                    $responses[$responseIndex]['order'] = $object['order'];
                }

                $responseIndex++;
            }
        }

        if (count($responses) > 0) {
            foreach ($responses as $response) {
                DB_ORM::insert('map_question_response')
                    ->column('question_id', $newQuestionId)
                    ->column('response', Arr::get($response, 'response', ''))
                    ->column('feedback', Arr::get($response, 'feedback', ''))
                    ->column('is_correct', (int)Arr::get($response, 'correctness', 2))
                    ->column('score', (int)Arr::get($response, 'score', 0))
                    ->column('order', (int)Arr::get($response, 'order', 1))
                    ->execute();
            }
        }
    }

    public function addPickQuestion($mapId, $values)
    {
        if ($mapId == null || $mapId <= 0) {
            return;
        }

        if ($values != null && count($values) > 0) {
            $questionIDs = Arr::get($values, 'questionsIDs', null);
            if ($questionIDs == null) {
                return;
            }

            $ids = explode(' ', $questionIDs);

            $builder = DB_ORM::insert('map_question')
                ->column('map_id', $mapId)
                ->column('entry_type_id', 4)
                ->column('stem', Arr::get($values, 'qstem', ''))
                ->column('feedback', Arr::get($values, 'fback', ''))
                ->column('show_answer', (int)Arr::get($values, 'qshow', 1))
                ->column('counter_id', (int)Arr::get($values, 'scount', 0))
                ->column('num_tries', Arr::get($values, 'numtries', 1))
                ->column('show_submit', (int)Arr::get($values, 'showSubmit', 0))
                ->column('redirect_node_id', (int)Arr::get($values, 'redirectNode', null))
                ->column('submit_text', Arr::get($values, 'submitButtonText', 'Submit'))
                ->column('type_display', (int)Arr::get($values, 'typeDisplay', 0));

            $newQuestionId = $builder->execute();

            if ($ids != null && count($ids) > 0 && $newQuestionId > 0) {
                foreach ($ids as $id) {
                    if ($id == null) {
                        continue;
                    }

                    $builder = DB_ORM::insert('map_question_response')
                        ->column('question_id', $newQuestionId)
                        ->column('response', Arr::get($values, 'qresp' . $id . 't', ''))
                        ->column('feedback', Arr::get($values, 'qfeed' . $id, ''))
                        ->column('is_correct', Arr::get($values, 'qresp' . $id . 'y', 0))
                        ->column('score', Arr::get($values, 'qresp' . $id . 's', 0));

                    $builder->execute();
                }
            }
        }
    }

    public function copyQuestion($mapId, $values)
    {
        $questionID = Arr::get($values, 'questionID', null);
        $counterID = Arr::get($values, 'counterID', null);

        if ($questionID != null && is_numeric($questionID) && $questionID > 0) {
            $question = DB_ORM::model('map_question', array((int)$questionID));
            if ($question != null) {
                if ($counterID == '') {
                    $counterID = null;
                }
                $newQuestionID = DB_ORM::insert('map_question')
                    ->column('map_id', $mapId)
                    ->column('stem', $question->stem)
                    ->column('entry_type_id', $question->entry_type_id)
                    ->column('width', $question->width)
                    ->column('height', $question->height)
                    ->column('feedback', $question->feedback)
                    ->column('show_answer', $question->show_answer)
                    ->column('counter_id', $counterID)
                    ->column('num_tries', $question->num_tries)
                    ->column('show_submit', 0)
                    ->column('redirect_node_id', null)
                    ->column('submit_text', 'Submit')
                    ->column('type_display', $question->type_display)
                    ->column('settings', $question->settings)
                    ->execute();

                if (count($question->responses) > 0) {
                    foreach ($question->responses as $response) {
                        DB_ORM::insert('map_question_response')
                            ->column('question_id', $newQuestionID)
                            ->column('response', $response->response)
                            ->column('feedback', $response->feedback)
                            ->column('is_correct', $response->is_correct)
                            ->column('score', $response->score)
                            ->column('from', $response->from)
                            ->column('to', $response->to)
                            ->execute();
                    }
                }
            }
        }
    }

    public function getQuestionById($id)
    {
        $builder = DB_SQL::select('default')->from($this->table())->where('id', '=', $id);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $questions = array();
            foreach ($result as $record) {
                $questions[] = $record;
            }

            return $questions;
        }

        return null;
    }

    public function importQuestion($question, $responses)
    {
        unset($question->name_file);
        unset($question->id);

        $builder = DB_ORM::insert('map_question');
        foreach ($question as $key => $value) {
            $value = (string)$value;
            if (!is_numeric($value)) {
                $value = base64_decode($value);
            }
            $builder->column($key, $value);
        }
        $newId = $builder->execute();

        foreach ($responses as $response) {
            unset($response['id']);
            $response['question_id'] = $newId;

            $builder = DB_ORM::insert('map_question_response');
            foreach ($response as $key => $value) {
                $value = (string)$value;
                if (!is_numeric($value)) {
                    $value = base64_decode($value);
                }
                $builder->column($key, $value);
            }
            $builder->execute();
        }
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isFreeTextAllowed()
    {
        if ($this->entry_type_id != self::ENTRY_TYPE_DROP_DOWN) {
            throw new Exception('Method not allowed for object #' . $this->id);
        }

        $settings = $this->settings;

        if (empty($settings)) {
            return false;
        }

        $settings = json_decode($this->settings, true);

        return isset($settings['isFreeTextAllowed']) ? $settings['isFreeTextAllowed'] : false;
    }
}