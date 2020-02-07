<?php

namespace App\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



use App\Entity\Users;
use App\Form\RegistrationType;
use App\Form\ConnexionType;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function Registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new Users(); //On creer un utilisateur vide.

        $form = $this->createForm(RegistrationType::class, $user); //On creer le formulaire, l'utilisateur nouvellement crée recevra les valeurs du formulaire.

        $form->handleRequest($request); //Analyse de la requete

        if($form->isSubmitted() && $form->isValid()) // Si la requête à lieu, est que les infos sont valide
        {
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('security_connexion'); //On redirige vers la page de connexion.
        }

        return $this->render("security/registration.html.twig", [
            'form' => $form->createView() // On passe en argument le paramètre form.
        ]);

    }

    /**
     * @Route("/connexion", name="security_connexion")
     */
    public function Connexion(Request $request)
    {
        $user = new Users(); //On creer un utilisateur vide.

        $form = $this->createForm(ConnexionType::class, $user); //On creer le formulaire, l'utilisateur nouvellement crée recevra les valeurs du formulaire.


        return $this->render("security/connexion.html.twig", [
            'form' => $form->createView()
        ]);


    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function Logout()
    {

    }

    /**
     * @Route("/moderation", name="moderation")
     */
    public function moderation()
    {
        $user = new Users();

        $repo = $this->getDoctrine()->getRepository(Users::class);

        $user = $repo->findAll();
        return $this->render("security/moderation.html.twig", [
            "user" => $user,
        ]);
    }

    /**
     * @Route("/moderation/{id}", name="moderation_role_administrator")
     */
    public function moderationChangeRoleToAdministrator($id, EntityManagerInterface $manager)
    {
        $user = new Users();

        $repo = $this->getDoctrine()->getRepository(Users::class);

        $user = $repo->find($id);

        $user->setRoles(array("ROLE_ADMINISTRATOR"));
        $manager->flush();
        $manager->clear();

        return $this->redirectToRoute("moderation");
    }

    /**
     * @Route("/moderation/admin/{id}", name="moderation_role_moderator")
     */
    public function moderationChangeRoleToModerator($id, EntityManagerInterface $manager)
    {
        $user = new Users();

        $repo = $this->getDoctrine()->getRepository(Users::class);

        $user = $repo->find($id);

        $user->setRoles(array("ROLE_MODERATOR"));
        $manager->flush();
        $manager->clear();

        return $this->redirectToRoute("moderation");
    }

}
