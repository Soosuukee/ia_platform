<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Contract;

use DateTimeImmutable;

interface AccountHolder
{
    public function getFirstName(): string;
    public function getLastName(): string;
    public function getEmail(): string;
    public function getPassword(): string;
    public function getCountry(): string;
    public function getRole(): string;
    public function getCreatedAt(): DateTimeImmutable;
}
