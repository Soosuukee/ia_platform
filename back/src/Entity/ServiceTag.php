<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class ServiceTag
{
    private int $id;
    private int $serviceId;
    private int $tagId;

    public function __construct(int $serviceId, int $tagId, ?int $id = null)
    {
        $this->serviceId = $serviceId;
        $this->tagId = $tagId;
        if ($id !== null) {
            $this->id = $id;
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getServiceId(): int
    {
        return $this->serviceId;
    }

    public function getTagId(): int
    {
        return $this->tagId;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'serviceId' => $this->serviceId,
        ];
    }
}
