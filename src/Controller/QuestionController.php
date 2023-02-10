<?php

namespace App\Controller;

use App\Entity\Question;
use App\Repository\AnswerRepository;
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

        return new Response('Sounds like a GREAT feature for v2!');
    }

    /**
     * @Route("/questions/{slug}", name="app_question_show")
     */
    public function show(Question $question/*$slug, EntityManagerInterface $entityManager*//*, AnswerRepository $answerRepository*/)
    {

        if($this->isDebug)
        {
            $this->logger->info('Jestesmy w trybie debugowania');
        }
        //$answers = $question->getAnswers();

        return $this->render('question/show.html.twig', [
            'question' => $question,
            //'answers' => $answers,
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
