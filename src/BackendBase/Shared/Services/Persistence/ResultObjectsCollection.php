<?php

declare(strict_types=1);

namespace BackendBase\Shared\Services\Persistence;

use Iterator;
use JsonSerializable;
use function count;

abstract class ResultObjectsCollection implements Iterator, JsonSerializable
{
    private int $index;
    private array $items;

    public function __construct(?array $items = [])
    {
        $this->index = 0;
        $this->items = $items ?? [];
    }

    public function rewind() : void
    {
        $this->index = 0;
    }

    public function push($item) : void
    {
        $this->items[count($this->items)] = $item;
    }

    public function current()
    {
        return $this->items[$this->index];
    }

    public function key()
    {
        return $this->index;
    }

    public function next() : void
    {
        ++$this->index;
    }

    public function valid() : bool
    {
        return isset($this->items[$this->index]);
    }

    public function count() : int
    {
        return count($this->items);
    }

    public function toArray() : array
    {
        $data = [];
        foreach ($this->items as $item) {
            $data[] = $item->toArray();
        }

        return $data;
    }

    public function jsonSerialize() : array
    {
        return $this->items;
    }
}
