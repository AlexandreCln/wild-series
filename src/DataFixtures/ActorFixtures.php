<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    const ACTORS = [
        'Andrew Lincoln',
        'Norman Reedus',
        'Lauren Cohan',
        'Danai Gurira',
    ];

    public function load(ObjectManager $manager)
    {
        $slugify = new Slugify();
        $faker  =  Faker\Factory::create('fr_FR');
        foreach (self::ACTORS as $actorName) {
            $actor = new Actor();
            $actor->setName($actorName);
            $actor->setSlug($slugify->generate($actor->getName()));
            $manager->persist($actor);

            $actor->addProgram($this->getReference('program_0'));
        }

        for ($i = 0; $i < 50; $i++) {
            $actor = new Actor();
            $actor->setName($faker->name);
            $actor->setSlug($slugify->generate($actor->getName()));
            $manager->persist($actor);

            $actor->addProgram($this->getReference('program_' . rand(0,5)));
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
        return [ProgramFixtures::class];
    }
}
