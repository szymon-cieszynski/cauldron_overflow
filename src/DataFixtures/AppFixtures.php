<?php

namespace App\DataFixtures;

use App\Entity\Question;
use App\Entity\Answer;
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

        $answer = new Answer();
        $answer->setContent('This question is the best? I wish... I knew the answer.');
        $answer->setUsername('weaverryan');
        $question = new Question();
        $question->setName('How to un-disappear your wallet.');
        $question->setQuestion('... I should not have done this...');

        $answer->setQuestion($question); //przypisywanie pytania do odpowiedzi -> wsadzamy caly obiekt, nie id pytania!!

        $manager->persist($answer);
        $manager->persist($question);
        $manager->flush();
    }
}
