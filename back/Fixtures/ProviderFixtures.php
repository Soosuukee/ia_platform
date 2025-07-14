<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Fixtures;

use Soosuuke\IaPlatform\Entity\Provider;
use Soosuuke\IaPlatform\Entity\AvailabilitySlot;
use Soosuuke\IaPlatform\Repository\ProviderRepository;

class ProviderFixtures
{
    public function load(): void
    {
        $providerRepo = new ProviderRepository();

        $providers = [
            ['Julie', 'Marchand', 'julie.pro@example.com', 'pass456', 'Photographe freelance', 'Shooting mode et mariage', 'France'],
            ['Kévin', 'Lambert', 'kevin.pro@example.com', 'pass456', 'Développeur IA', 'Automatisation de processus métiers', 'France'],
            ['Sophie', 'Durand', 'sophie.pro@example.com', 'pass456', 'Graphiste', 'Identité visuelle & UX/UI design', 'Belgique'],
        ];

        foreach ($providers as [$first, $last, $email, $plainPassword, $title, $presentation, $country]) {
            $provider = new Provider(
                $first,
                $last,
                $email,
                password_hash($plainPassword, PASSWORD_DEFAULT),
                $title,
                $presentation,
                $country
            );

            // Ajouter 3 créneaux de dispo à partir de demain
            $now = new \DateTimeImmutable('tomorrow 09:00');
            for ($i = 0; $i < 3; $i++) {
                $start = $now->modify("+{$i} day");
                $end = $start->modify('+2 hours');
                $slot = new AvailabilitySlot(0, $start, $end); // providerId sera défini dans le repo
                $provider->addAvailabilitySlot($slot);
            }

            $providerRepo->save($provider);
        }

        echo "✔ 3 providers avec créneaux de dispo créés\n";
    }
}
