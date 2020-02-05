<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Subjects;
use App\Entity\Users;
class ForumFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $subject = new Subjects();
        $user = new Users();

        $subject->setTitle("Test");
        $subject->setContent("Du contenu");
        $subject->setUser($user->getUsername());
        $subject->setCreatedAt(new \DateTime());

        $manager->persist($subject);

        $manager->flush();
    }
}
