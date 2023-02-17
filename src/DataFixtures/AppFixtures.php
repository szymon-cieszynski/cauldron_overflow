<?php

namespace App\DataFixtures;

use App\Entity\Question;
use App\Entity\Answer;
use App\Entity\QuestionTag;
use App\Entity\Tag;
use App\Factory\AnswerFactory;
use App\Factory\QuestionTagFactory;
use App\Factory\TagFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Factory\QuestionFactory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne([
            'email'=>'abraca_admin@example.com',
            'roles' => ['ROLE_ADMIN']
        ]);
        UserFactory::createOne([
            'email'=>'abraca_user@example.com',
        ]);
        UserFactory::createMany(10);

        TagFactory::createMany(100);

        $questions = QuestionFactory::createMany(20, function() {
            return [
                'owner' => UserFactory::random(),
            ];
        });

        QuestionTagFactory::createMany(100, function() {
            return [
                'tag' => TagFactory::random(),
                'question' => QuestionFactory::random(),
            ];
        });

        QuestionFactory::new()
            ->unpublished()
            ->createMany(5)
        ;

        AnswerFactory::createMany(100, function() use ($questions) {
            return [
                'question' => $questions[array_rand($questions)]
            ];
        });

        AnswerFactory::new(function() use ($questions) {
            return [
                'question' => $questions[array_rand($questions)]
            ];
        })->needsApproval()->many(20)->create();



//        $question = QuestionFactory::createOne()->object();;
//        $tag1 = new Tag();
//        $tag1->setName('dinosaurs');
//        $tag2 = new Tag();
//        $tag2->setName('monster trucks');
//
//        $manager->persist($tag1);
//        $manager->persist($tag2);
//
////        $question->addTag($tag1);
////        $question->addTag($tag2);
//
//        $tag1->addQuestion($question);
//        $tag2->addQuestion($question);

        $manager->flush();
    }
}
