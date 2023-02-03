<?php

namespace App\Controller;

use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Twig\Environment;
class QuestionController extends AbstractController
{
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
    public function show($slug, MarkdownParserInterface $markdownParser, CacheInterface $cache)
    {
        $answers = [
            'Make sure your cat is sitting `purrrfectly` still ?',
            'Honestly, I like furry shoes better than MY cat',
            'Maybe... try saying the spell backwards?',
        ];

        $questionText = 'I\'ve been turned into a cat, any *thoughts* on how to turn back? While I\'m **adorable**, I don\'t really care for cat food.';
        $parsedQuestionText = $cache->get('markdown_'.md5($questionText), function() use ($questionText, $markdownParser) {
            return $markdownParser->transformMarkdown($questionText);
        });

        dd($markdownParser);

        //dump($this);

        return $this->render('question/show.html.twig', [
            'question' => ucwords(str_replace('-',' ', $slug)),
            'questionText' => $parsedQuestionText,
            'answers' => $answers,
        ]);
    }
}
