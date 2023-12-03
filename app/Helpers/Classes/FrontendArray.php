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
    private $_ids;
    private $_attributes;

    public function __construct($ids, $attributes)
    {
        $this->_ids = is_array($ids) ? $ids : ['id' => $ids];
        $this->_attributes = $attributes;
    }

    public function __isset($key)
    {
        return isset($this->_ids[$key]) || isset($this->_attributes[$key]);
    }

    public function __get($key)
    {
        return $this->_ids[$key] ?? $this->_attributes[$key];
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
        return isset($this->_ids[$offset]) || isset($this->_attributes[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->_ids[$offset] ?? $this->_attributes[$offset] ?? null;
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
