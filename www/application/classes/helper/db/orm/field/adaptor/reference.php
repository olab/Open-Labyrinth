<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 26/10/2012
 * Time: 3:59 μμ
 * To change this template use File | Settings | File Templates.
 */
defined('SYSPATH') or die('No direct script access.');
class Helper_DB_ORM_Field_Adaptor_Reference extends DB_ORM_Field_Adaptor
{
    public function __construct(DB_ORM_Model $model, Array $metadata = array()) {
        parent::__construct($model, $metadata['field']);

        $this->metadata['format'] = (isset($metadata['format']))
            ? (string) $metadata['format']
            : 'Y-m-d H:i:s';
    }

    public function __get($key) {
        switch ($key) {
            case 'value':
                $value = $this->model->{$this->metadata['field']};
                $json_extras = $this->model->field->options;

                $extras = json_decode($json_extras);
                $source = $extras->source;
                if(isset($extras->label))  $label = $extras->label;
                else $label = null;
                if ( ! is_null($value)) {
                    $value = new Helper_Model_ReferredEntity($value,$source, $label);
                }
                return $value;
                break;
            default:
                if (isset($this->metadata[$key])) { return $this->metadata[$key]; }
                break;
        }
        throw new Kohana_InvalidProperty_Exception('Message: Unable to get the specified property. Reason: Property :key is either inaccessible or undefined.', array(':key' => $key));
    }

    public function __set($key, $value) {
        switch ($key) {
            case 'value':
                if ($value instanceof Helper_Model_ReferredEntity) {
                    $value = $value->uri();
                }
                $this->model->{$this->metadata['field']} = $value;
                break;
            default:
                throw new Kohana_InvalidProperty_Exception('Message: Unable to set the specified property. Reason: Property :key is either inaccessible or undefined.', array(':key' => $key, ':value' => $value));
                break;
        }
    }
}
