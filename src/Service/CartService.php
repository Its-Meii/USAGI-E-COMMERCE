<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\ShippingRepository;
use App\Repository\ProductSizeRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService{
    private SessionInterface $session;
    public function __construct(RequestStack $requestStack, private ProductRepository $productRepo, private ProductSizeRepository $productSizeRepository, private OrderRepository $orderRepo, private ShippingRepository $shippingRepo, private TranslatorInterface $translator){
        $this->session = $requestStack->getSession();
    }

    public function update(Product $product, int $quantity, int $size){
        $cart = $this->getCart();
        $cart[] = ['id'=> $product->getId(), 'size'=>$size, 'quantity'=>(int)$quantity];
        $this->session->set("cart", $cart);
    }


    public function updateQuantity(array $item, int $quantity){

        $cart = $this->getCart();
        $index = array_search($item, $cart);
        $cart[$index] = ['id'=> $item['id'], 'size'=>$item['size'], 'quantity'=>(int)$quantity];
        // dd($cart);
        $this->session->set("cart", $cart);
    }


    public function getCart(){
        return $this->session->get('cart',[]);
    }

    public function clearCart(){
        return $this->session->remove('cart');
    }

    public function getStripeData(){
        foreach($this->getCart() as $cartItem){
            $data[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [       
                        'images'=> [$this->productRepo->find($cartItem['id'])->getPhotoFront()],              
                        'name' => $this->productRepo->find($cartItem['id'])->getName(),
                        // 'description' => $this->productRepo->find($cartItem['id'])->getDescription(),
                        // 'size'=> $this->productSizeRepository->find($cartItem['size'])->getName(),    
                    ],
                    'unit_amount' => $this->productRepo->find($cartItem['id'])->getPrice(),
                ],
                'quantity' => $cartItem['quantity'],
            ];
        }
        $data[]= [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [                             
                    'name' => $this->translator->trans('shipping_stripe').' '.$this->shippingRepo->find($this->orderRepo->getLastOrderShipping())->getName(),
                ],
                'unit_amount' => $this->shippingRepo->find($this->orderRepo->getLastOrderShipping())->getPrice(),
            ],
            'quantity' => 1,
        ];
        return $data;
    }
}
