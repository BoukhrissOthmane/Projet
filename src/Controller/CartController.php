<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Controller\CartService;

class CartController extends AbstractController
{
    #[Route('/mon-panier', name: 'cart')]
    public function index(SessionInterface $session, ProductRepository $productRepository)
    {

        $cart = new Cart($session,$productRepository);
        

        return $this->render('cart/index.html.twig', [
            'items' =>  $cart->getFullCart(),
            'total' => $cart->getTotal()
        ]);
    }


    #[Route('/cart/add/{id}', name: 'add_to_cart')]
    public function add($id,  ProductRepository $productRepository, SessionInterface $session)
    {
         $cart = new Cart($session,$productRepository);
         $cart->add($id, $session);
         return $this->redirectToRoute("cart");

    }

    #[Route('/cart/remove/{id}', name: 'remove_my_cart')]
    public function remove($id, SessionInterface $session,  ProductRepository $productRepository)
    {
         $cart = new Cart($session,$productRepository);
         $cart->remove($id, $session);

    return $this->redirectToRoute('cart');
    }

    #[Route('/cart/decrease/{id}', name: 'decrease_to_cart')]
    public function decrease($id, SessionInterface $session,  ProductRepository $productRepository)
    {
        $cart = new Cart($session,$productRepository);
        $cart->decrease($id, $session);
        return $this->redirectToRoute('cart');

    }


}
/*class CartController extends AbstractController
{
    #[Route('/mon-panier', name: 'cart')]
    public function index(SessionInterface $session, ProductRepository $productRepository)
    {
        $panier = $session->get('panier', []);

        $panierWithData = []; 

        foreach($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];
        }

        $total = 0; 

        foreach($panierWithData as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }

        return $this->render('cart/index.html.twig', [
            'items' => $panierWithData,
            'total' => $total
        ]);
    }


    #[Route('/cart/add/{id}', name: 'add_to_cart')]
    public function add($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);
        if (!empty($panier[$id])) {
            $panier[$id]++;
         } else {
            $panier[$id] = 1; 
         }

    $session->set('panier', $panier);

         return $this->redirectToRoute("cart");

    }

    #[Route('/cart/remove/{id}', name: 'remove_my_cart')]
    public function remove($id, SessionInterface $session)
    {
    $panier = $session->get('panier', []);

    if (!empty($panier[$id])) {
        unset($panier[$id]);
    }

    $session->set('panier', $panier);

    return $this->redirectToRoute('cart');
    }
}

    /*#[Route('/cart/remove', name: 'remove_my_cart')]

    public function remove($id, Cart $cart)
    {
    $panier = $session->get('panier', []);|
    if (!empty($panier[$id])) {
        unset($panier[$id]);
    $session->set('panier', $panier);
    return $this->redirectToRoute("cart_index");
    
    }*/



















    /*
    #[Route('/mon-panier', name: 'cart')]
    public function index(Cart $cart): Response
    {
        
        return $this->render('cart/index.html.twig', [
            'cart' => $cart->get()
        ]);
    }

    #[Route('/cart/add/{id}', name: 'add_to_cart')]
    public function add(Cart $cart, $id): Response
    {

        $cart->add($id);

        return $this->redirectToRoute('cart');
    }

     #[Route('/cart/remove', name: 'remove_my_cart')]
     public function remove(Cart $cart): Response
     {
 
         $cart->remove();
 
         return $this->redirectToRoute('products');
     }
}*/
