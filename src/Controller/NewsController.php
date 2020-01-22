<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Comments;
use App\Form\SubjectType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

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
    public function forum(Request $request, EntityManagerInterface $manager)
    {
        $repo = $this->getDoctrine()->getRepository(Comments::class); //Variable qui va chercher dans la base de données contenant la classe Comments
        
        $comment = new Comments(); //On creer un formulaire vide.


        $form = $this->createForm(SubjectType::class, $comment); //On creer le form, $comment recevra les valeurs.
        $form->handleRequest($request); //Analyse de la requete

        if($form->isSubmitted() && $form->isValid()) // Si la requête à lieu, est que les infos sont valide
        {
            $comment->setTitle($comment->getTitle());
            $comment->setContent($comment->getContent());
            $comment->setCreatedAt(new \DateTime());

            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('forum'); //On redirige vers la page du forum.
        }

        $comments = $repo->findAll();
        return $this->render('news/forum.html.twig', [
            "comments" => $comments,
            'form' => $form->createView() // On passe en argument le paramètre form.
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
            "comment" => $comment,
           
        ]);
    } 
}
