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
use Knp\Component\Pager\PaginatorInterface;

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
    public function archive(EntityManagerInterface $manager, PaginatorInterface $paginator, Request $request)
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
                $news->setSource($article->source->name);
                $news->setCreatedAt(new \Datetime());
                $manager->flush();
                $manager->clear();
            }
            else
            {
                $news->setAddress($article->url);
                $news->setSource($article->source->name);
                $news->setCreatedAt(new \Datetime());
                $manager->persist($news);
                $manager->flush();
                $manager->clear();
            }
        }

        $actualTime = new \DateTime();

        $articles = $server->findAll();
        $pagination = $paginator->paginate($articles, $request->query->getInt('page', 1), 15);
        return $this->render("news/archive.html.twig", [
            "news" => $pagination,
            "actualTime" => $actualTime
        ]);
    }

    /**
     * @Route("/archive/removeOldArchive", name="removeOldArchives")
     */
    public function removeOldArchives(EntityManagerInterface $manager)
    {
        $news = new News();
        $repository = $this->getDoctrine()->getRepository(News::class);

        $news = $repository->findAll();

        foreach($news as $index)
        {
            $interval = \date_diff(new \DateTime(), $index->getCreatedAt());
            if($interval->format("%m")  >= 1)
            {
                $manager->remove($index);
                $manager->flush();
            }
        }
        return $this->redirectToRoute("archive");
    }

     /**
     * @Route("/mentions", name="mentions")
     */
    public function showMentions()
    {
        return $this->render("news/mentions.html.twig");
    }

    /**
     * @Route("/forum", name="forum")
     */
    public function forum(Request $request, EntityManagerInterface $manager, PaginatorInterface $paginator)
    {
        $subject = new Subjects(); //On creer entitée.

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
        $pagination = $paginator->paginate($subjects, $request->query->getInt('page', 1), 15);
        return $this->render('news/forum.html.twig', [
            "subjects" => $pagination,
            'form' => $form->createView() // On passe en argument le paramètre form.
        ]);
    }

    /**
     * @Route("/forum/{id}", name="subject")
     */
    public function showForumSubjects($id, Request $request, EntityManagerInterface $manager, PaginatorInterface $paginator)
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
            $comments->setReport(0);

            $manager->persist($comments);
            $manager->flush();
            return $this->redirectToRoute('subject', ['id' => $id]);
        }

        $commentsRepo = $this->getDoctrine()->getRepository(Comments::class);
        $comment = $commentsRepo->findby(['Subject' => $id]);
        $pagination = $paginator->paginate($comment, $request->query->getInt('page', 1), 8);

        return $this->render('news/subjects.html.twig', [
            "subject" => $subjects,
            "comments" => $pagination,
            "form" => $form->createView()
        ]);
    } 

    /**
     * @Route("forum/delete/{subjectId}", name="removeSubject")
     */
    public function removeSubject(EntityManagerInterface $manager, $subjectId)
    {
        $repository = $this->getDoctrine()->getRepository(Subjects::class);
        $subject = $repository->find($subjectId);
        $manager->remove($subject);
        $manager->flush();
        return $this->redirectToRoute("forum");
    }

    /**
     * @Route("/forum/{subjectId}/{commentId}", name="removeComment")
     */
    public function removeComment(EntityManagerInterface $manager, $subjectId, $commentId)
    {
        $repository = $this->getDoctrine()->getRepository(Comments::class);
        $comment = $repository->find($commentId);
        $manager->remove($comment);
        $manager->flush();
        return $this->redirectToRoute("subject", ["id" => $subjectId]);
    }

    /**
     * @Route("/forum/comment/reported/{subjectId}/{commentId}", name="reportedComment")
     */
    public function reportedComment($subjectId, $commentId, EntityManagerInterface $manager)
    {
        $comment = new Comments();
        
        $repository = $this->getDoctrine()->getRepository(Comments::class);

        $comment = $repository->find($commentId);
        $comment->setReport(1);
        $manager->persist($comment);
        $manager->flush();

        return $this->redirectToRoute("subject", ["id" => $subjectId]);
    }

    /**
     * @Route("/forum/comment/remove/{commentId}", name="removeReportedComment")
     */
    public function removeReportedComment(EntityManagerInterface $manager, $commentId)
    {
        $repository = $this->getDoctrine()->getRepository(Comments::class);
        $comment = $repository->find($commentId);
        $manager->remove($comment);
        $manager->flush();
        return $this->redirectToRoute("moderation");
    }

    /**
     * @Route("/forum/comment/authorized/{commentId}", name="authorizedReportedComment")
     */
    public function authorizedReportedComment(EntityManagerInterface $manager,$commentId)
    {
        $comment = new Comments();
        
        $repository = $this->getDoctrine()->getRepository(Comments::class);

        $comment = $repository->find($commentId);
        $comment->setReport(0);
        $manager->persist($comment);
        $manager->flush();
        return $this->redirectToRoute("moderation");
    }
}

