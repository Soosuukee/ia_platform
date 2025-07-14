<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Fixtures;

use Soosuuke\IaPlatform\Entity\Client;
use Soosuuke\IaPlatform\Repository\ClientRepository;

class ClientFixtures
{
    public function load(): void
    {
        $clientRepo = new ClientRepository();

        $clients = [
            ['Alice', 'Dupont', 'alice@example.com', 'pass123', 'France'],
            ['Bob', 'Martin', 'bob@example.com', 'pass123', 'Belgique'],
            ['Clara', 'Moreau', 'clara@example.com', 'pass123', 'Canada'],
            ['David', 'Lemoine', 'david@example.com', 'pass123', 'Suisse'],
            ['Eva', 'Dubois', 'eva@example.com', 'pass123', 'Luxembourg'],
            ['Franck', 'Roche', 'franck@example.com', 'pass123', 'France'],
        ];

        foreach ($clients as [$first, $last, $email, $plainPassword, $country]) {
            $client = new Client(
                $first,
                $last,
                $email,
                password_hash($plainPassword, PASSWORD_DEFAULT),
                $country
            );
            $clientRepo->save($client);
        }

        echo "✔ 6 clients créés\n";
    }
}
