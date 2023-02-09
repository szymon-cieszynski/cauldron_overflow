<?php

namespace App\DataFixtures;

use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Factory\QuestionFactory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //QuestionFactory::new()->create();
        QuestionFactory::createMany(20);

        QuestionFactory::new()
            ->unpublished()
            ->createMany(5)
        ;
    }
}
