<?php

namespace App\Controller;

use App\Service\MarkdownHelper;
use Psr\Log\LoggerInterface;
use Sentry\State\HubInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function homepage(Environment $twigEnvironment)
    {
        return $this->render('question/homepage.html.twig');
//        return new Response('What a bewitching controller we have conjured!');
    }

    /**
     * @Route("/questions/{slug}", name="app_question_show")
     */
    public function show($slug, MarkdownHelper $markdownHelper)
    {

        if($this->isDebug)
        {
            $this->logger->info('Jestesmy w trybie debugowania');
        }

        //throw new \Exception('bad stuff happend!');
        //dump($isDebug);
        //dump($this->getParameter('cache_adapter'));
        $answers = [
            'Make sure your cat is sitting `purrrfectly` still ?',
            'Honestly, I like furry shoes better than MY cat',
            'Maybe... try saying the spell backwards?',
        ];

        $questionText = 'I\'ve been turned into a cat, any *thoughts* on how to turn back? While I\'m **adorable**, I don\'t really care for cat food.';
        $parsedQuestionText = $markdownHelper->parse($questionText);

       //dump($cache);

        //dump($this);

        return $this->render('question/show.html.twig', [
            'question' => ucwords(str_replace('-',' ', $slug)),
            'questionText' => $parsedQuestionText,
            'answers' => $answers,
        ]);
    }
}
