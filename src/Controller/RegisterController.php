<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
#use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    private $entityManager;

        public function __construct(EntityManagerInterface $entityManager)
        {
            $this->entityManager = $entityManager;
        }

    #[Route('/inscription', name: 'register')]
    
    public function index(Request $request, UserPasswordHasherInterface $encoder): Response
    {
        $notification = null;
        
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        
        $form-> handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();

            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());

            if (!$search_email) {
                $password = $encoder->hashPassword($user, $user->getPassword());
                $user->setPassword($password);
            
                $this->entityManager ->persist($user);
                $this->entityManager ->flush();

                $mail = new Mail();
                $content = "Bonjour ".$user->getFirstname()."<br/>Bienvenue sur le monde de sport de basket.<br/><br/>";
                $mail->send($user->getEmail(), $user->getFirstname(), 'Bienvenue sur Monde De Basket', $content);

                $notification = "Votre inscription s'est correctement déroulée. Vous pouvez dès à présent vous connecter à votre compte.";

            } else {
                $notification = "L'email que vous avez renseigné existe déjà.";
            }  
        }


        return $this->render('register/index.html.twig',[
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
