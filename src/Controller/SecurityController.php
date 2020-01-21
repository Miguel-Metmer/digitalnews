<?php

namespace App\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



use App\Entity\Users;
use App\Form\RegistrationType;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function Registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new Users(); //On creer un utilisateur vide.

        $form = $this->createForm(RegistrationType::class, $user); //On creer le formulaire, l'utilisateur nouvelement crée recevra les valeurs du formulaire.

        $form->handleRequest($request); //Analyse de la requete

        if($form->isSubmitted() && $form->isValid()) // Si la requête à lieu, est que les infos sont valide
        {
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();
        }

        return $this->render("security/registration.html.twig", [
            'form' => $form->createView() // On passe en argument le paramètre form.
        ]);
    }

    /**
     * @Route("/connexion", name="security_connexion")
     */
    public function Connexion()
    {
        $user = new Users(); //On creer un utilisateur vide.

        $form = $this->createForm(RegistrationType::class, $user); //On creer le formulaire, l'utilisateur nouvelement crée recevra les valeurs du formulaire.


        return $this->render("security/connexion.html.twig", [
            'form' => $form->createView()
        ]);
    }
}
