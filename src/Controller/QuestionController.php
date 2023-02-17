<?php

namespace App\Controller;

use App\Entity\Question;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use Knp\Bundle\TimeBundle\Templating\Helper\TimeHelper;
use App\Service\MarkdownHelper;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @Route("/{page<\d+>}", name="app_homepage")
     */
    public function homepage(/*EntityManagerInterface $entityManager*/QuestionRepository $repository, int $page = 1)
    {
        //$repository = $entityManager->getRepository(Question::class);
        //$questions = $repository->findBy([],['askedAt'=>'DESC']);
        $queryBuilder = $repository->createAskedOrderedByNewestQueryBuilder();

        $pagerfanta = new Pagerfanta(
            new QueryAdapter($queryBuilder)
        );

        $pagerfanta->setMaxPerPage(5);
        $pagerfanta->setCurrentPage($page);

        return $this->render('question/homepage.html.twig',
        [
            'pager'=>$pagerfanta
        ]);
//        return new Response('What a bewitching controller we have conjured!');
    }

    /**
     * @Route("/questions/new")
     * @IsGranted("ROLE_USER")
     */
    public function new()
    {
        //$this->denyAccessUnlessGranted('ROLE_USER');
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
     * @Route("/questions/edit/{slug}", name="app_question_edit")
     */
    public function edit(Question $question)
    {
        $this->denyAccessUnlessGranted('EDIT', $question);

        return $this->render('question/edit.html.twig', [
            'question' => $question,
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
