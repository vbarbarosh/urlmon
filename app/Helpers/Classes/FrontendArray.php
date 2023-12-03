<?php

namespace App\Helpers\Classes;

use App\Exceptions\NotImplemented;
use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * @property $id
 */
class FrontendArray implements Arrayable, ArrayAccess, JsonSerializable
{
    private $_id;
    private $_attributes;

    public function __construct($id, $attributes)
    {
        $this->_id = $id;
        $this->_attributes = $attributes;
    }

    public function __isset($key)
    {
        if ($key == 'id') {
            return isset($this->_id);
        }
        return isset($this->_attributes[$key]);
    }

    public function __get($key)
    {
        if ($key == 'id') {
            return $this->_id;
        }
        return $this->_attributes[$key];
    }

    public function toArray(): array
    {
        return $this->_attributes;
    }

    public function jsonSerialize()
    {
        return $this->_attributes;
    }

    public function offsetExists($offset): bool
    {
        return ($offset === 'id') || in_array($offset, $this->_attributes);
    }

    public function offsetGet($offset)
    {
        if ($offset === 'id') {
            return $this->_id;
        }
        return $this->_attributes[$offset] ?? null;
    }

    /**
     * @throws NotImplemented
     */
    public function offsetSet($offset, $value)
    {
        throw new NotImplemented();
    }

    /**
     * @throws NotImplemented
     */
    public function offsetUnset($offset)
    {
        throw new NotImplemented();
    }
}
