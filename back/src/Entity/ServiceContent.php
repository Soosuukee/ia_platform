<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class ServiceContent
{
    private int $id;
    private int $serviceSectionId;
    private string $content;

    public function __construct(int $serviceSectionId, string $content)
    {
        $this->serviceSectionId = $serviceSectionId;
        $this->content = trim($content);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getServiceSectionId(): int
    {
        return $this->serviceSectionId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'serviceSectionId' => $this->serviceSectionId,
        ];
    }
}
