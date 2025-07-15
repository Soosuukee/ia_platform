<?php

namespace Soosuuke\IaPlatform\Fixtures;

use Soosuuke\IaPlatform\Entity\Provider;
use Soosuuke\IaPlatform\Entity\AvailabilitySlot;
use Soosuuke\IaPlatform\Entity\ProviderSkill;
use Soosuuke\IaPlatform\Entity\ProviderDiploma;
use Soosuuke\IaPlatform\Entity\ProvidedService;
use Soosuuke\IaPlatform\Entity\CompletedWork;
use Soosuuke\IaPlatform\Entity\CompletedWorkMedia;
use Soosuuke\IaPlatform\Repository\ProviderRepository;
use Soosuuke\IaPlatform\Repository\SkillRepository;
use Soosuuke\IaPlatform\Repository\ProviderSkillRepository;
use Soosuuke\IaPlatform\Repository\ProviderDiplomaRepository;
use Soosuuke\IaPlatform\Repository\ProvidedServiceRepository;
use Soosuuke\IaPlatform\Repository\CompletedWorkRepository;
use Soosuuke\IaPlatform\Repository\CompletedWorkMediaRepository;

class ProviderFixtures
{
    public function load(): void
    {
        $providerRepo = new ProviderRepository();
        $skillRepo = new SkillRepository();
        $providerSkillRepo = new ProviderSkillRepository();
        $providerDiplomaRepo = new ProviderDiplomaRepository();
        $providedServiceRepo = new ProvidedServiceRepository();
        $completedWorkRepo = new CompletedWorkRepository();
        $completedWorkMediaRepo = new CompletedWorkMediaRepository();

        // Charger les skills depuis SkillFixtures
        $skills = $skillRepo->findAll();

        $providers = [
            ['Julie', 'Marchand', 'julie.pro@example.com', 'pass456', 'Spécialiste en vision par ordinateur', 'Détection d’objets et analyse d’images pour le retail', 'France', 'pfp/pfp1.jpg'],
            ['Kévin', 'Lambert', 'kevin.pro@example.com', 'pass456', 'Développeur IA', 'Automatisation de processus métiers via modèles GPT et LLMs', 'France', 'pfp/pfp2.jpg'],
            ['Sophie', 'Durand', 'sophie.pro@example.com', 'pass456', 'Experte en NLP', 'Analyse de sentiments et génération de textes multilingues', 'Belgique', 'pfp/pfp3.jpg'],
        ];

        foreach ($providers as $index => [$first, $last, $email, $plainPassword, $title, $presentation, $country, $profilePicture]) {
            $provider = new Provider(
                $first,
                $last,
                $email,
                password_hash($plainPassword, PASSWORD_DEFAULT),
                $title,
                $presentation,
                $country,
                $profilePicture
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

            // Ajouter 1 à 3 compétences aléatoires
            $assigned = array_rand($skills, rand(1, 3));
            $assigned = is_array($assigned) ? $assigned : [$assigned];

            foreach ($assigned as $skillIndex) {
                $providerSkillRepo->save(new ProviderSkill(
                    $provider->getId(),
                    $skills[$skillIndex]->getId()
                ));
            }

            // Ajouter 1 à 2 diplômes
            $diplomas = [
                ['Master en Informatique', 'Université de Paris', 'Spécialisation en vision par ordinateur', '2018-09-01', '2020-06-01'],
                ['Certificat en IA Avancée', 'École Polytechnique', 'Focus sur les réseaux neuronaux', '2021-01-01', '2021-06-01'],
            ];
            $diplomaCount = rand(1, 2);
            for ($i = 0; $i < $diplomaCount; $i++) {
                [$title, $institution, $description, $startDate, $endDate] = $diplomas[$i % count($diplomas)];
                $start = new \DateTimeImmutable($startDate);
                $end = new \DateTimeImmutable($endDate);
                $diploma = new ProviderDiploma(
                    $title,
                    $institution,
                    $description,
                    $start,
                    $end,
                    $provider->getId()
                );
                $providerDiplomaRepo->save($diploma);
            }

            // Ajouter 1 à 3 services
            $services = [
                ['Analyse d’images', 'Détection d’objets en temps réel', 150, 300, 60],
                ['Développement de modèle IA', 'Création de modèles personnalisés', 300, 500, 120],
                ['Consultation IA', 'Conseils stratégiques', 100, 150, 30],
            ];
            $serviceCount = rand(1, 3);
            for ($i = 0; $i < $serviceCount; $i++) {
                [$title, $description, $minprice, $maxprice, $durationMinutes] = $services[$i % count($services)];
                $service = new ProvidedService(
                    $title,
                    $description,
                    $minprice,
                    $maxprice,
                    $durationMinutes,
                    $provider->getId(),
                );
                $providedServiceRepo->save($service);
            }

            // Ajouter les completed works (associés par index)
            $completedWorks = [
                [
                    'IA Solutions France',
                    'Architecte Solutions IA Senior',
                    'Conception de systèmes RAG traitant 10M+ de requêtes/jour.',
                    '2022-01-01',
                    null, // encore en poste
                ],
                [
                    'Dynamique IA',
                    'Ingénieur ML',
                    'Infrastructure de fine-tuning LLM pour entreprises tech.',
                    '2020-06-01',
                    '2022-12-31',
                ],
                [
                    'VisionTech',
                    'Consultant IA',
                    'Détection d’objets par vision IA dans l’industrie.',
                    '2019-03-01',
                    '2020-05-30',
                ],
            ];
            [$company, $title, $description, $start, $end] = $completedWorks[$index];
            $startDate = new \DateTimeImmutable($start);
            $endDate = $end ? new \DateTimeImmutable($end) : null;
            $work = new CompletedWork(
                $provider->getId(),
                $company,
                $title,
                $description,
                $startDate,
                $endDate
            );
            $completedWorkRepo->save($work);

            // Ajouter les médias associés aux completed works
            $mediaEntries = [
                [1, 'image', 'workmedia/work1.jpg'],
                [1, 'image', 'workmedia/work1.jpg'],
                [2, 'image', 'workmedia/work2.jpg'],
                [3, 'image', 'workmedia/work3.jpg'],
                [3, 'pdf', 'workmedia/work3_rapport.pdf'],
            ];
            foreach ($mediaEntries as [$workIndex, $type, $url]) {
                if ($workIndex === $index + 1) { // Associer les médias par index (1-based)
                    $media = new CompletedWorkMedia($work->getId(), $type, $url);
                    $completedWorkMediaRepo->save($media);
                }
            }
        }

        echo "✔ 3 providers avec créneaux de dispo + compétences + diplômes + services + completed works + médias créés\n";
    }
}
