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
 * @property int $parent_id
 * @property int $question_id
 * @property int $is_correct
 * @property int $score
 * @property int $order
 * @property string $response
 * @property string $feedback
 * @property string $from
 * @property string $to
 * @property Model_Leap_Map_Question_Response $parent
 * @property Model_Leap_Map_Question $question
 */
class Model_Leap_Map_Question_Response extends DB_ORM_Model
{
    const IS_CORRECT_CORRECT = 1;
    const IS_CORRECT_NEUTRAL = 2;
    const IS_CORRECT_INCORRECT = 0;

    public static function getCorrectness()
    {
        return [
            self::IS_CORRECT_CORRECT => 'Correct',
            self::IS_CORRECT_NEUTRAL => 'Neutral',
            self::IS_CORRECT_INCORRECT => 'Incorrect',
        ];
    }

    public function __construct()
    {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
                'unsigned' => true,
            )),
            'parent_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => true,
                'savable' => true,
            )),
            'question_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
            )),
            'response' => new DB_ORM_Field_String($this, array(
                'max_length' => 250,
                'nullable' => false,
                'savable' => true,
            )),
            'feedback' => new DB_ORM_Field_Text($this, array(
                'nullable' => false,
                'savable' => true,
            )),
            'is_correct' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 4,
                'nullable' => false,
                'savable' => true,
            )),
            'score' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
            )),
            'from' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => true,
                'savable' => true,
            )),
            'to' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => true,
                'savable' => true,
            )),
            'order' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => false,
                'unsigned' => true,
                'default' => 0
            )),
        );

        $this->relations = array(
            'question' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('question_id'),
                'parent_key' => array('id'),
                'parent_model' => 'Map_Question',
            )),
            'parent' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('parent_id'),
                'parent_key' => array('id'),
                'parent_model' => 'Map_Question',
            )),
        );
    }

    public static function data_source()
    {
        return 'default';
    }

    public static function table()
    {
        return 'map_question_responses';
    }

    public static function primary_key()
    {
        return array('id');
    }

    /**
     * @param int $questionId
     * @param mixed $response
     * @return self|static|bool
     */
    public static function getByQuestionAndAnswer($questionId, $response)
    {
        return DB_ORM::select('Map_Question_Response')
            ->where('response', '=', $response)
            ->where('question_id', '=', $questionId)
            ->limit(1)
            ->query()
            ->fetch(0);
    }

    /**
     * @return string
     */
    public function getIsCorrectHTML()
    {
        switch ($this->is_correct) {
            case 0:
                return '<img src="' . URL::base() . 'images/cross.jpg"> ';
                break;
            case 1:
                return '<img src="' . URL::base() . 'images/tick.jpg"> ';
                break;
        }

        return ''; //neutral
    }

    /**
     * @return string
     */
    public function getFeedbackHTML()
    {
        return ($this->feedback != null && strlen($this->feedback) > 0 ? '(' . $this->feedback . ')' : '');
    }

    public function addFullResponses($questionId, $values)
    {
        $this->question_id = $questionId;
        $this->response = Arr::get($values, 'response', '');
        $this->feedback = Arr::get($values, 'feedback', '');
        $this->is_correct = Arr::get($values, 'is_correct', 0);
        $this->score = Arr::get($values, 'score', 0);

        $this->save();

        return true;
    }

    public function getResponsesByQuestion($questionId)
    {
        $builder = DB_SQL::select('default')->from($this->table())->where('question_id', '=', (int)$questionId);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $responses = array();
            foreach ($result as $record) {
                $responses[] = DB_ORM::model('map_question_response', array((int)$record['id']));
            }

            return $responses;
        }

        return null;
    }

    public function updateResponses($questionId, $values)
    {
        $responses = $this->getResponsesByQuestion($questionId);

        $responsesFromJSON = $this->prepareResponsesFromJSON(Arr::get($values, 'responses', null));

        if ($responses != null && count($responses) > 0) {
            foreach ($responses as $response) {
                if ($responsesFromJSON != null && isset($responsesFromJSON['old']) && isset($responsesFromJSON['old'][$response->id])) {
                    $response->response = Arr::get($responsesFromJSON['old'][$response->id], 'response', '');
                    $response->feedback = Arr::get($responsesFromJSON['old'][$response->id], 'feedback', '');
                    $response->is_correct = Arr::get($responsesFromJSON['old'][$response->id], 'correctness', 2);
                    $response->score = Arr::get($responsesFromJSON['old'][$response->id], 'score', 0);
                    $response->order = Arr::get($responsesFromJSON['old'][$response->id], 'order', 1);

                    $response->save();
                } else {
                    $response->delete();
                }
            }
        }

        if ($responsesFromJSON != null && isset($responsesFromJSON['new']) && count($responsesFromJSON['new']) > 0) {
            foreach ($responsesFromJSON['new'] as $response) {
                DB_ORM::insert('map_question_response')
                    ->column('question_id', $questionId)
                    ->column('response', Arr::get($response, 'response', ''))
                    ->column('feedback', Arr::get($response, 'feedback', ''))
                    ->column('is_correct', Arr::get($response, 'correctness', 2))
                    ->column('score', Arr::get($response, 'score', 0))
                    ->column('order', Arr::get($response, 'order', 1))
                    ->execute();
            }
        }
    }

    private function prepareResponsesFromJSON($responses)
    {
        if ($responses == null) {
            return null;
        }

        $result = array();
        foreach ($responses as $responseJSON) {
            $object = json_decode($responseJSON, true);

            if ($object == null) {
                continue;
            }

            $responseData = array();

            if (isset($object['response'])) {
                $responseData['response'] = urldecode(str_replace('+', '&#43;', base64_decode($object['response'])));
            }
            if (isset($object['feedback'])) {
                $responseData['feedback'] = urldecode(str_replace('+', '&#43;', base64_decode($object['feedback'])));
            }
            if (isset($object['correctness'])) {
                $responseData['correctness'] = $object['correctness'];
            }
            if (isset($object['score'])) {
                $responseData['score'] = $object['score'];
            }
            if (isset($object['order'])) {
                $responseData['order'] = $object['order'];
            }

            if (isset($object['id']) && $object['id'] > 0) {
                $responseData['id'] = $object['id'];
                $result['old'][$object['id']] = $responseData;
            } else {
                $result['new'][] = $responseData;
            }
        }

        return $result;
    }

    public function deleteByQuestion($questionId)
    {
        $builder = DB_ORM::delete('map_question_response')->where('question_id', '=', (int)$questionId);
        $builder->execute();
    }

    public function duplicateResponses($fromQuestionId, $toQuestionId)
    {
        $resp = $this->getResponsesByQuestion($fromQuestionId);

        if ($resp == null || $toQuestionId == null || $toQuestionId <= 0) {
            return;
        }

        foreach ($resp as $r) {
            $builder = DB_ORM::insert('map_question_response')
                ->column('question_id', $toQuestionId)
                ->column('response', $r->response)
                ->column('feedback', $r->feedback)
                ->column('is_correct', $r->is_correct)
                ->column('score', $r->score);

            $builder->execute();
        }
    }

    public function exportMVP($questionId)
    {
        return DB_SQL::select('default')->from('map_question_responses')->where('question_id', '=',
            $questionId)->query()->as_array();
    }
}
