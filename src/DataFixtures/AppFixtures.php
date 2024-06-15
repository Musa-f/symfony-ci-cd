<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Format;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $formatTypes = [
            'Vidéo',
            'Cours',
            'Article / Fiche de lecture',
            'Activité / Jeu à réaliser',
            'Carte défi',
            'Jeu en ligne'
        ];

        foreach ($formatTypes as $type) {
            $format = new Format();
            $format->setType($type);
            $manager->persist($format);
        }

        $categoryNames = [
            'Communication',
            'Culture',
            'Développement personnel',
            'Intelligence émotionnelle',
            'Loisirs',
            'Monde professionnel',
            'Parentalité',
            'Qualité de vie',
            'Recherche de sens',
            'Santé physique',
            'Santé psychique',
            'Spiritualité',
            'Vie affective'
        ];

        foreach ($categoryNames as $name) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
        }

        $manager->flush();
    }
}
