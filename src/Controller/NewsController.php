<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users;
use App\Entity\Subjects;
use App\Entity\Comments;
use App\Entity\News;
use App\Form\SubjectType;
use App\Form\CommentType;
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
     * @Route("/archive", name="archive")
     */
    public function archive(EntityManagerInterface $manager)
    {
        $news = new News();
        $apiNews = file_get_contents("https://newsapi.org/v2/top-headlines?country=fr&pageSize=40&apiKey=a727be71caab4420b0862dad9c71c661");
        $data = json_decode($apiNews);

        $server = $this->getDoctrine()->getRepository(News::class);

        //Nouveautées globales
        foreach ($data->articles as $article)
        {
            if($server->findBy(["address" => $article->url]))
            {
                $news->setAddress($article->url);
                $manager->flush();
                $manager->clear();
            }
            else
            {
                $news->setAddress($article->url);
                $manager->persist($news);
                $manager->flush();
                $manager->clear();
            }
        }

        $articles = $server->findAll();
        return $this->render("news/archive.html.twig", [
            "news" => $articles,
        ]);
    }

    /**
     * @Route("/forum", name="forum")
     */
    public function forum(Request $request, EntityManagerInterface $manager)
    {
        $subject = new Subjects(); //On creer un formulaire vide.

        $form = $this->createForm(SubjectType::class, $subject); //On creer le form, $subject recevra les valeurs.
        $form->handleRequest($request); //Analyse de la requete

        if($form->isSubmitted() && $form->isValid()) // Si la requête à lieu, est que les infos sont valide
        {
            $subject->setTitle($subject->getTitle());
            $subject->setContent($subject->getContent());
            $subject->setCreatedAt(new \DateTime());
            $subject->setUser($this->getUser());

            $manager->persist($subject);
            $manager->flush();
            return $this->redirectToRoute('forum'); //On redirige vers la page du forum.
        }

        $repo = $this->getDoctrine()->getRepository(Subjects::class); //Variable qui va chercher dans la base de données contenant la classe Comments
        $subjects = $repo->findAll();
        return $this->render('news/forum.html.twig', [
            "subjects" => $subjects,
            'form' => $form->createView() // On passe en argument le paramètre form.
        ]);
    }

    /**
     * @Route("/forum/{id}", name="subject")
     */
    public function showForumSubjects($id, Request $request, EntityManagerInterface $manager)
    {
        $repo = $this->getDoctrine()->getRepository(Subjects::class);
        $subjects = $repo->find($id);

        $comments = new Comments();
        $form = $this->createForm(CommentType::class, $comments);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $comments->setContent($comments->getContent());
            $comments->setCreatedAt(new \DateTime());
            $comments->setUser($this->getUser());
            $comments->setSubject($subject = $repo->find($id));

            $manager->persist($comments);
            $manager->flush();
            return $this->redirectToRoute('subject', ['id' => $id]);
        }

        $commentsRepo = $this->getDoctrine()->getRepository(Comments::class);
        $comment = $commentsRepo->findby(['Subject' => $id]);

        return $this->render('news/subjects.html.twig', [
            "subject" => $subjects,
            "comments" => $comment,
            "form" => $form->createView()
        ]);
    } 
}
