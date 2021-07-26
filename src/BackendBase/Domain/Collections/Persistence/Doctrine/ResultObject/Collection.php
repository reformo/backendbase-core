<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Persistence\Doctrine\ResultObject;

use BackendBase\Shared\Persistence\ResultObject;
use JsonSerializable;

use function gettype;
use function json_decode;

use const JSON_OBJECT_AS_ARRAY;

class Collection implements JsonSerializable
{
    use ResultObject;

    /**
     * @method Collection id() : string
     * @method Collection key() : string
     * @method Collection name() : string
     * @method Collection slug() : string
     * @method Collection parentId() : ? string
     * @method Collection isActive() : int
     **/
    private string $id;
    private string $key;
    private string $name;
    private string $slug       = '';
    private $metadata          = null;
    private ?string $parentId  = null;
    private ?string $parentKey = null;

    private int $isActive = 1;

    public function metadata(): ?array
    {
        if (gettype($this->metadata) === 'string') {
            return json_decode($this->metadata, (bool) JSON_OBJECT_AS_ARRAY);
        }

        return $this->metadata;
    }
}
