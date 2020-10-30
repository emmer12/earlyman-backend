<?php

namespace App\Traits;

trait ApiResponseMetaData {
    /**
     * Adds extra meta data to API resources response.
     */

     /**
      * @var bool
      */
     protected $success;

     /**
      * @var string
      */
     protected $code;

     /**
      * @var string
      */
     protected $message;

     /**
      * @var string
      */
      protected $word;

    public function success($value)
    {
        $this->success = $value;
        return $this;
    }

    public function code($value)
    {
        $this->code = $value;
        return $this;
    }

    public function message($value)
    {
        $this->message = $value;
        return $this;
    }

    public function word($request)
    {
        dd(get_parent_class ());

        // Get the parenent class;
        // Get the plural form too;
        // Return the values appropiately.

        $word = ($this->is_assoc(parent::toArray($request))) ? 'property' : 'properties';
    }

    public static function is_assoc(array $array)
    {
        // Keys of the array
        $keys = array_keys($array);

        // If the array keys of the keys match the keys, then the array must
        // not be associative (e.g. the keys array looked like {0:0, 1:1...}).
        return array_keys($keys) !== $keys;
    }
}