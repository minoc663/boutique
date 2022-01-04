<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RegisterType;
use App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/inscription", name="register")
     */

    public function inscription(Request $request, UserPasswordEncoderInterface $encoder)
    {


        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            //dd($password); // la fonction qui permet de crypter les mots de passe 


            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $this->render('register/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}