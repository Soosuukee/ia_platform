<?php

namespace Soosuuke\IaPlatform\Fixtures;

use Soosuuke\IaPlatform\Entity\Skill;
use Soosuuke\IaPlatform\Repository\SkillRepository;

class SkillFixtures
{
    private static array $initialSkills = [
        'Machine Learning',
        'Data Analysis',
        'Computer Vision',
        'Chatbot Development',
        'Prompt Engineering',
        'Python',
        'Cloud AI Services',
        'Natural Language Processing',
        'Deep Learning',
        'Data Engineering',
        'Speech Recognition',
        'Reinforcement Learning',
        'MLOps',
    ];

    /**
     * Charge les compétences initiales et les retourne.
     *
     * @return Skill[] Liste des compétences chargées
     */
    public static function load(): array
    {
        $repo = new SkillRepository();
        $skills = [];

        foreach (self::$initialSkills as $name) {
            $skill = new Skill($name);
            $repo->save($skill);
            $skills[] = $skill;
        }

        return $skills;
    }

    /**
     * Ajoute une nouvelle compétence dynamiquement.
     *
     * @param string $name Nom de la nouvelle compétence
     * @return Skill La compétence ajoutée
     * @throws \Exception Si le nom est vide
     */
    public static function addSkill(string $name): Skill
    {
        if (empty(trim($name))) {
            throw new \Exception('Le nom de la compétence ne peut pas être vide.');
        }

        $repo = new SkillRepository();
        $skill = new Skill($name);

        // Vérifie si la compétence existe déjà
        if (!$repo->findByName($name)) {
            $repo->save($skill);
        } else {
            $skill = $repo->findByName($name);
        }

        return $skill;
    }
}
