<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController
{
    //uzyjemy annotacji zamiast normalnego trasowania w routes.yaml -> wykona się funkcja homepage, wystarczy w bloku komentarza dodać trasę z biblioteki
    /**
     * @Route("/")
     */
    public function homepage()
    {
        return new Response('What a bewitching controller we have conjured!');
    }

    /**
     * @Route("/question/{slug}")
     */
    public function show($slug)
    {
        return new Response(sprintf('future page to show a question "%s"!',
        ucwords(str_replace('-',' ', $slug))
        ));
    }
}
