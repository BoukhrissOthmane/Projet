<?php

namespace App\Controller;
use App\Classe\Mail;
use App\Entity\User;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{ 
    #[Route('/nous-contacter', name: 'contact')]
    public function index(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $this->addFlash('notice', 'Merci de nous avoir contacté. Notre équipe va vous répondre dans les meilleurs délais.');

            
            $content = "Monsieur ".$user['Lastname']. (" ") .$user['Firstname']. "<br/>Email : " .$user['email']. "<br/><br/>". $user['content'];
            dd($content);
            $mail = new Mail();
            $mail->send('bkrotm11@gmail.com', 'Monde De Basket', 'Vous avez reçu une nouvelle demande de contact.', $content );
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
