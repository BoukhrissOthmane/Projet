<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Product;
use App\Repository\ProductRepository;


class Cart {

     private $session;
     private $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session->get('panier', []);
        $this->productRepository = $productRepository;
    }

    public function add( $id, SessionInterface $session)
    {
     $panier = $this->session;
            if (!empty($panier[$id])) {
                $panier[$id]++;
             } else {
                $panier[$id] = 1;
             }
        $session->set('panier', $panier);



    }

    public function remove(int $id, SessionInterface $session) {
     $panier = $this->session;

            if (!empty($panier[$id])) {
                unset($panier[$id]);
            }

            $session->set('panier', $panier);
    }

    public function getFullCart() : array {

        $panier = $this->session;

                   $panierWithData = [];

                   foreach($panier as $id => $quantity) {
                       $product_object = $this->productRepository->find($id);
                       if(!$product_object){
                        if (!empty($panier[$id])) {
                            unset($panier[$id]);
                        };
                           continue;
                       }

                       $panierWithData[] = [
                           'product' => $product_object ,
                           'quantity' => $quantity
                       ];
                   }
                   return $panierWithData;
    

              
    }
    public function getTotal(): float{
        $total = 0;
        $panierwithData = $this->getFullCart();
        foreach ( $this->getFullCart() as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];;
        }
        return $total;
    }


        public function decrease($id, SessionInterface $session)
        {
            $panier = $this->session;
            if ($panier[$id] > 1) {
                $panier[$id]--;
                }
            else{
                    unset($panier[$id]);
            }
                        $session->set('panier', $panier);


        }


   
    
    
}
    
    
    
    
    
    
/*$panier = $this->session;

                   $panierWithData = [];

                   foreach($panier as $id => $quantity) {
                       $panierWithData[] = [
                           'product' => $this->productRepository->find($id),
                           'quantity' => $quantity
                       ];
                   }

                   $total = 0;

                   if ($panier->getPrice()) {
                    foreach($panierWithData as $item) {
                        $product_object = $item['product']->getPrice() * $item['quantity'];
                        if (!$product_object) {
                            $panier->remove($id);
                            continue;
                        }

                        $totalItem = $product_object;
                        $total += $totalItem;
                    }
                   }
                   
                   return [$panierWithData,$total];*/
    
    
    
    
    
    
    
    /*private $session;

    public function __construct(SessionInterface $session)
    
    {
        $this->session = $session;
    }    

    public function add($id, SessionInterface $session)
    {
        $cart = $this->session->get('cart', []);

        if(!empty($cart[$id])) {
            $cart[$id]++; 
        } else {
            $cart[$id] = 1;
        }

    }

    public function get()
    {
        return $this->session->get('cart');
    }

    public function remove(){
        return $this->session->remove('cart');
    }
        
}*/