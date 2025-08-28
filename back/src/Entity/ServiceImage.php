<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class ServiceImage
{
    private int $id;
    private int $serviceContentId;
    private string $url;

    public function __construct(int $serviceContentId, string $url)
    {
        $this->serviceContentId = $serviceContentId;
        $this->url = trim($url);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getServiceContentId(): int
    {
        return $this->serviceContentId;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
