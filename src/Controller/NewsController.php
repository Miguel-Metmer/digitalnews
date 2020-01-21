<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Comments;

class NewsController extends AbstractController
{
    /**
     * @Route("/", name="news")
     */
    public function index()
    {
        return $this->render('news/index.html.twig');
    }

    /**
     * @Route("/forum", name="forum")
     */
    public function forum()
    {
        $repo = $this->getDoctrine()->getRepository(Comments::class);
        $comments = $repo->findAll();
        return $this->render('news/forum.html.twig', [
            "comments" => $comments
        ]);
    }

    /**
     * @Route("/forum/{id}", name="comment")
     */
    public function showForumComment($id)
    {
        $repo = $this->getDoctrine()->getRepository(Comments::class);
        $comment = $repo->find($id);

        return $this->render('news/comment.html.twig', [
            "comment" => $comment
        ]);
    } 
}
