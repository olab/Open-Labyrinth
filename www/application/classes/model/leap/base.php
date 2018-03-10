<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Leap_Base extends DB_ORM_Model
{
    /**
     * @var array
     */
    protected static $_static_cache = [];

    /**
     * @var array
     */
    protected $_cache = [];

    /**
     * @param string $key
     * @param string $value
     */
    final public static function staticCacheSet($key, $value)
    {
        static::$_static_cache[static::staticCacheGetKeyValue($key)] = $value;
    }

    /**
     * @param string $key
     * @return bool
     */
    final public static function staticCacheHas($key)
    {
        return isset(static::$_static_cache[static::staticCacheGetKeyValue($key)]);
    }

    /**
     * @param string $key
     * @return mixed
     */
    final public static function staticCacheGet($key)
    {
        if (static::staticCacheHas($key)) {
            return static::$_static_cache[static::staticCacheGetKeyValue($key)];
        } else {
            return null;
        }
    }

    /**
     * @param string $key
     */
    final public static function staticCacheForget($key)
    {
        unset(static::$_static_cache[static::staticCacheGetKeyValue($key)]);
    }

    /**
     * @param string $key
     * @return string
     */
    private static function staticCacheGetKeyValue($key)
    {
        return $key;
    }

    /**
     * @param string $key
     * @param string $value
     */
    final public function cacheSet($key, $value)
    {
        $this->_cache[$key] = $value;
    }

    /**
     * @param string $key
     * @return bool
     */
    final public function cacheHas($key)
    {
        $cache = $this->_cache;
        return isset($cache[$key]);
    }

    /**
     * @param string $key
     * @return mixed
     */
    final public function cacheGet($key)
    {
        if ($this->cacheHas($key)) {
            return $this->_cache[$key];
        } else {
            return null;
        }
    }

    /**
     * @param string $key
     */
    final public function cacheForget($key)
    {
        unset($this->_cache[$key]);
    }

    public function as_array($with_relations = false)
    {
        $buffer = array();
        if ($with_relations) {
            foreach ($this->relations as $name => $relation) {
                $buffer[$name] = $relation->result;
            }
        }
        foreach ($this->fields as $name => $field) {
            $buffer[$name] = $field->value;
        }

        return $buffer;
    }

    public function insert($reload = false)
    {
        $self = get_class($this);
        $is_savable = call_user_func(array($self, 'is_savable'));
        if (!$is_savable) {
            throw new Kohana_Marshalling_Exception('Message: Failed to save record to database. Reason: Model is not savable.',
                array(':class' => self::get_called_class()));
        }
        $primary_key = call_user_func(array($self, 'primary_key'));
        if (!is_array($primary_key) || empty($primary_key)) {
            throw new Kohana_Marshalling_Exception('Message: Failed to save record to database. Reason: No primary key has been declared.');
        }
        $data_source = call_user_func(array($self, 'data_source'));
        $table = call_user_func(array($self, 'table'));
        $columns = array_keys($this->fields);

        $is_auto_incremented = call_user_func(array($self, 'is_auto_incremented'));
        if ($is_auto_incremented) {
            foreach ($primary_key as $column) {
                $index = array_search($column, $columns);
                if ($index !== false) {
                    unset($columns[$index]);
                }
            }
        }
        if (!empty($columns)) {
            $builder = DB_SQL::insert($data_source)
                ->into($table);
            $count = 0;
            foreach ($columns as $column) {
                if ($this->fields[$column]->savable) {
                    $builder->column($column, $this->fields[$column]->value);
                    $this->fields[$column]->modified = false;
                    $count++;
                }
            }
            if ($count > 0) {
                if ($is_auto_incremented) {
                    $this->fields[$primary_key[0]]->value = $builder->execute(true);
                } else {
                    $builder->execute();
                }
            }
            $this->metadata['saved'] = $this->hash_code();
        }

        if ($reload) {
            $this->load();
        }
    }
}