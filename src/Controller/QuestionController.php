<?php

namespace App\Controller;

use App\Entity\Question;
use App\Repository\QuestionRepository;
use Knp\Bundle\TimeBundle\Templating\Helper\TimeHelper;
use App\Service\MarkdownHelper;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sentry\State\HubInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
class QuestionController extends AbstractController
{
    private LoggerInterface $logger;
    private bool $isDebug;

    public function __construct(LoggerInterface $logger, bool $isDebug)
    {
        $this->logger = $logger;
        $this->isDebug = $isDebug;
    }

    //uzyjemy annotacji zamiast normalnego trasowania w routes.yaml -> wykona się funkcja homepage, wystarczy w bloku komentarza dodać trasę z biblioteki
    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage(/*EntityManagerInterface $entityManager*/QuestionRepository $repository)
    {
        //$repository = $entityManager->getRepository(Question::class);
        //$questions = $repository->findBy([],['askedAt'=>'DESC']);
        $questions = $repository->findAllAskedOrderedByNewest();

        return $this->render('question/homepage.html.twig',
        [
            'questions'=>$questions
        ]);
//        return new Response('What a bewitching controller we have conjured!');
    }

    /**
     * @Route("/questions/new")
     */
    public function new(EntityManagerInterface $entityManager)
    {
        $question = new Question();
        $question->setName('Missing pants')
            ->setSlug('missing-pants-'.rand(0, 1000))
            ->setQuestion(<<<EOF
Hi! So... I'm having a *weird* day. Yesterday, I cast a spell
to make my dishes wash themselves. But while I was casting it,
I slipped a little and I think `I also hit my pants with the spell`.
When I woke up this morning, I caught a quick glimpse of my pants
opening the front door and walking out! I've been out all afternoon
(with no pants mind you) searching for them.
Does anyone have a spell to call your pants back?
EOF
            );
        if (rand(1, 10) > 2) {
            $question->setAskedAt(new \DateTime(sprintf('-%d days', rand(1, 100))));
        }
        $question->setVotes(rand(-20, 50));

        //dd($question);
        $entityManager->persist($question);
        $entityManager->flush();
        return new Response(sprintf(
            'Well hallo! The shiny new question is id #%d, slug: %s',
            $question->getId(),
            $question->getSlug()
        ));
    }

    /**
     * @Route("/questions/{slug}", name="app_question_show")
     */
    public function show(Question $question/*$slug, EntityManagerInterface $entityManager*/)
    {

        if($this->isDebug)
        {
            $this->logger->info('Jestesmy w trybie debugowania');
        }

//        $repository = $entityManager->getRepository(Question::class);
//        /** @var Question|null $question */
//        $question = $repository->findOneBy(['slug'=>$slug]);
//        if(!$question)
//        {
//            throw $this->createNotFoundException(sprintf('no question found for slug "%s"', $slug));
//        }

        $answers = [
            'Make sure your cat is sitting `purrrfectly` still ?',
            'Honestly, I like furry shoes better than MY cat',
            'Maybe... try saying the spell backwards?',
        ];

//        $questionText = 'I\'ve been turned into a cat, any *thoughts* on how to turn back? While I\'m **adorable**, I don\'t really care for cat food.';
//        $parsedQuestionText = $markdownHelper->parse($questionText);

        return $this->render('question/show.html.twig', [
            'question' => $question,
            'answers' => $answers,
        ]);
    }

    /**
     * @Route("/questions/{slug}/vote", name="app_question_vote", methods="POST")
     */
    public function questionVote(Question $question, Request $request, EntityManagerInterface $entityManager)
    {
        $direction = $request->request->get('direction');
        if ($direction === 'up') {
            $question->upVote();
        } elseif ($direction === 'down') {
            $question->downVote();
        }
        //nie musimy dawac tutaj persist() poniewaz uzylismy juz obiektu $question w autowiringu i Doctrine wie ze ma sprawdzic ten obiekt
        $entityManager->flush();

        //przekierowanie do tej samej strony po zaglosowaniu, bo ta metoda w glebi frameworka dziedzy po klasie REsponse i zwraca obiekt Response
        return $this->redirectToRoute('app_question_show',[
            'slug'=>$question->getSlug(),
        ]);
    }

}
