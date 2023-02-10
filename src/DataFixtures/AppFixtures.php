<?php

namespace App\DataFixtures;

use App\Entity\Question;
use App\Entity\Answer;
use App\Factory\AnswerFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Factory\QuestionFactory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //QuestionFactory::new()->create();
        $questions =  QuestionFactory::createMany(20);

        QuestionFactory::new()
            ->unpublished()
            ->createMany(5)
        ;

        AnswerFactory::createMany(100, function() use ($questions) {
            return [
                'question' => $questions[array_rand($questions)]
            ];
        });


       $manager->flush();
    }
}
