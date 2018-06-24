<?php

namespace CurlRox\Core;

class OObject {
    /**
     * Dinamic call to get and setters
     *
     * @param string $name
     * @param array $arguments
     * @return void|array|string
     */
    function __call($name, $arguments) {
        if (method_exists($this, $name)) {
            $this->$name(...$arguments);
            return;
        }

        if (strpos($name, 'set') !== false) {
            $field = lcfirst(explode('set', $name)[1]);
            if (property_exists($this, $field)) {
                $this->$field = $arguments[0];
            }
        } elseif (strpos($name, 'get') !== false) {
            $field = lcfirst(explode('get', $name)[1]);
            if (property_exists($this, $field)) {
                return $this->$field;
            }
        }
    }
}