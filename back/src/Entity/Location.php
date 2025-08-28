<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class Location
{
    private int $id;
    private int $accountHolderId;
    private string $accountHolderType;
    private string $city;
    private ?string $state = null;
    private ?string $postalCode = null;
    private ?string $address = null;

    public function __construct(
        int $accountHolderId,
        string $accountHolderType,
        string $city,
        ?string $state = null,
        ?string $postalCode = null,
        ?string $address = null
    ) {
        $this->accountHolderId = $accountHolderId;
        $this->accountHolderType = $accountHolderType;
        $this->city = trim($city);
        $this->state = $state ? trim($state) : null;
        $this->postalCode = $postalCode ? trim($postalCode) : null;
        $this->address = $address ? trim($address) : null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAccountHolderId(): int
    {
        return $this->accountHolderId;
    }

    public function getAccountHolderType(): string
    {
        return $this->accountHolderType;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }
}
