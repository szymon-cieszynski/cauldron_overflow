<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/comments/{id}/vote/{direction<up|down>}", methods="POST")
     */
    public function commentVote($id, $direction, LoggerInterface $logger)
    {
        if($direction === 'up')
        {
            $logger->info('Voting up!');
            $currentVoteCount = rand(7, 100); //randowmowa liczba losowa od 7 do 100 włącznie
        }else{
            $logger->info('Voting down!');
            $currentVoteCount = rand(0, 5);
        }
        return $this->json(['votes'=> $currentVoteCount]); //to samo co new JsonResponse
    }
}