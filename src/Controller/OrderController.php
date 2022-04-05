<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use App\Repository\ProductRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/commande', name: 'order')]
    public function index(SessionInterface $session, ProductRepository $productRepository, Request $request): Response
    {
        $this->session = $session->get('panier', []);
        $this->productRepository = $productRepository;
        $cart = new Cart($session,$productRepository);

        if (!$this->getUser()->getAddresses()->getValues()) 
        {
            return $this->redirectToRoute('account_address_add');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFullCart()
        ]);
    }

    #[Route('/commande/recapitulatif', name: 'order_recap', methods:"POST")]
    public function add(SessionInterface $session, ProductRepository $productRepository, Request $request): Response
    {
        $this->session = $session->get('panier', []);
        $this->productRepository = $productRepository;
        $cart = new Cart($session,$productRepository);


        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new DateTime();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            $delivery_content = $delivery->getFirstname().' '.$delivery->getLastname();
            $delivery_content .= '<br>'.$delivery->getPhone();


            if ($delivery->getCompany()) {
                $delivery_content .= '<br>'.$delivery->getCompany();
            }
            $delivery_content .= '<br>'.$delivery->getAddress();
            $delivery_content .= '<br>'.$delivery->getPostal().' '.$delivery->getCity();
            $delivery_content .= '<br>'.$delivery->getCountry();

            //enregistrement de ma commande Order()
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->setIsPaid(0);

            $this->entityManager->persist($order);

            //enregistrement de ma commande OrderDetails()
            foreach ( $cart->getFullCart() as $product){
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
                $this->entityManager->persist($orderDetails);
            }

            $this->entityManager->flush();

            return $this->render('order/add.html.twig', [
                'cart' => $cart->getFullCart(),
                'carrier' => $carriers,
                'delivery' => $delivery_content
            ]);
            
        }

        return $this->redirectToRoute('cart');
        
        
    }
}
