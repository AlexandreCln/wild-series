<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $subscriberauthor = new User();
        $subscriberauthor->setEmail('darkangel64@gmail.com');
        $subscriberauthor->setRoles(['ROLE_SUBSCRIBERAUTHOR']);
        $subscriberauthor->setPassword($this->passwordEncoder->encodePassword(
            $subscriberauthor,
            'subscriberpassword'
        ));
        $subscriberauthor->setBio('Joueur pro de Street Fighter II.');
        $subscriberauthor->setUsername('DarkAngel64');

        $manager->persist($subscriberauthor);

        // Création d’un utilisateur de type “administrateur”
        $admin = new User();
        $admin->setEmail('alexandre.coulon97@gmail.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin,
            'adminpassword'
        ));
        $admin->setBio('Créateur du site Wild Séries.');
        $admin->setUsername('Alexandre');


        $manager->persist($admin);

        // Sauvegarde des 2 nouveaux utilisateurs :
        $manager->flush();
    }
}
