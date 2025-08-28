<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class ServiceContent
{
    private int $id;
    private int $serviceContentId;
    private string $content;

    public function __construct(int $serviceContentId, string $content)
    {
        $this->serviceContentId = $serviceContentId;
        $this->content = trim($content);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getServiceContentId(): int
    {
        return $this->serviceContentId;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
