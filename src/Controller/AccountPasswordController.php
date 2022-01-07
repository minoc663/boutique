<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountPasswordController extends AbstractController
{
    private $EntityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/compte/modification-mot-de-passe", name="account_password")
     */
    public function password(Request $request, UserPasswordEncoderInterface $encoder): Response
    {

        $notification = null;

        $user = $this->getUser(); //Je veux l'utilisateur courant qui est connecté ,  je vais appeler l'objet utilisateur et l'injecter sur la variable user
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $old_pwd = $form->get('old_password')->getData();

            if ($encoder->isPasswordValid($user, $old_pwd)) {
                //dd($old_pwd);
                //die('Ca marche'); pour faire un test

                $new_pwd = $form->get('new_password')->getData();
                //dd($new_pwd); test pour savoir si ça marche

                $password = $encoder->encodePassword($user, $new_pwd);

                $user->setPassword($password);

                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $notification = 'Votre mot de passe à bien été mis à jour';
            } else {
                $notification = "Votre mot de passe n'est pas le bon";
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification,
        ]);
    }
}