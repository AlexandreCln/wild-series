<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 300; $i++) {
            $episode = new Episode();
            $episode->setNumber($faker->numberBetween(1, 22));
            $episode->setTitle($faker->realText($maxNbChars = 50, $indexSize = 2));
            $episode->setSynopsis($faker->text);
            $manager->persist($episode);

            $episode->setSeason($this->getReference('season_' . rand(1, 49)));
        }

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [SeasonFixtures::class];
    }
}
