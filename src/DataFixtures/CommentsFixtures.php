<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Comments;

class CommentsFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $comment = new Comments(); // On creer l'objet Comments
        $comment->setTitle("Parlons des actualités");
        $comment->setContent("Phasellus viverra ut elit sed tristique. Donec id orci finibus, volutpat erat in, sodales massa. Pellentesque lacinia elementum turpis, nec bibendum augue ornare vitae. Integer sagittis odio in ex sagittis tincidunt. Ut nec vestibulum nulla. Quisque et purus lobortis, finibus nisl quis, suscipit arcu. Aenean pretium, purus vel aliquam lobortis, mi nisl hendrerit mi, eu fringilla ex urna ut mi. ");
        $comment->setCreatedAt(new \DateTime());
        $manager->persist($comment); // On le fait persister dans le temps.

        $manager->flush(); // On envoie dans la base de données.
    }
}
