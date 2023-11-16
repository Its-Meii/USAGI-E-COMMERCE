<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductSize;
use App\Service\CartService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\UX\Turbo\TurboBundle;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{  
    #[Route('/cart', name: 'app_cart')]
    public function index(CartService $cartService, ProductRepository $productRepository, ProductSizeRepository $productSizeRepository): Response
    {   
        $cart = $cartService->getCart();
           

        if($cart){
            $products = [];
            $cartProductId=[];
          
            $carts = [];
            foreach($cart as $cartItem){
                array_push($cartProductId, $cartItem['id']);
                $carts[] = [
                    'product' =>$productRepository->find($cartItem['id']),
                    'size' =>$productSizeRepository->find($cartItem['size']),
                    'quantity' =>$cartItem['quantity'],
                ];
            }
            foreach ($productRepository->findAll() as $product) {
                if (!in_array($product->getId(),$cartProductId)) {
                    if($product->getQuantity()>0){
                        array_push($products, $product);
                    }       
                }      
            }
            return $this->render('cart/index.html.twig', [
                'cart' => $carts,
                'products' => $products,

            ]);
        }
        return $this->render('cart/index.html.twig', [
            'cart' => '',
            'products' => $productRepository->findAll(),
        ]);
      
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function add(Product $product, CartService $cartService, Request $request, ProductSizeRepository $productSizeRepository): Response
     {   
        if(TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()){
            $submittedToken = $request->query->get('token');
            if ($this->isCsrfTokenValid('add-product-cart', $submittedToken)) {
                $quantity = $request->query->getInt('quantity');
                $sizeId = $request->query->getInt('size');
                $size = $productSizeRepository->find($sizeId);
                if($size === null){
                    $referer = $request->headers->get('referer');
                    return new RedirectResponse($referer);
                }
                if($quantity <= 0){
                    $quantity = 1;
                } 
                if($quantity > $product->getQuantity()){
                    $referer = $request->headers->get('referer');
                    return new RedirectResponse($referer);
                }
                $SameProductInCart =  null;
                foreach($cartService->getCart() as $cartItem){
                    if($cartItem['id'] === $product->getId() && $cartItem['size'] === $size){
                        $SameProductInCart = $cartItem;
                    }
                }
                if($SameProductInCart){
                    $cartService->updateQuantity($SameProductInCart, $quantity);
                }
                else{
                    $cartService->update($product, $quantity,$sizeId);
                }     
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

                return $this->render('cart/add-cart-stream.html.twig', [
                    'id' => $product->getId(),
                    'size' => $sizeId,
                ]);
            }
        }
        return new Response();
    }  

    #[Route('/cart/update/{id}', name: 'app_cart_update')]
    public function update(Product $product, CartService $cartService, Request $request, ProductSizeRepository $productSizeRepository): Response
     {   
        if(TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()){
            $submittedToken = $request->query->get('token');
            if ($this->isCsrfTokenValid('update-product-cart', $submittedToken)) {
                $quantity = $request->query->getInt('quantity');
                $sizeId = $request->query->getInt('size');
                $size = $productSizeRepository->find($sizeId);
                if($size === null){
                    $referer = $request->headers->get('referer');
                    return new RedirectResponse($referer);
                }
                if($quantity <= 0){
                    $quantity = 1;
                } 
            
                $SameProductInCart = null;
                foreach($cartService->getCart() as $cartItem){
                    if($cartItem['id'] === $product->getId() && $cartItem['size'] === $sizeId){
                        $SameProductInCart = $cartItem;
                    }
                }
                if($SameProductInCart){
                    $cartService->updateQuantity($SameProductInCart, $quantity);
                }
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
                return $this->render('cart/add-cart-stream.html.twig', ['id' => $product->getId(), 'size' =>$sizeId]);
            }
        }
        return new Response();
    }  
    #[Route('/cart/delete/{productId}/{size}', name: 'app_cart_delete')]
    public function delete(int $productId,int $size, SessionInterface $session, Request $request): Response
    {   
        if(TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()){
            $submittedToken = $request->query->get('token');
            if ($this->isCsrfTokenValid('delete-product-cart', $submittedToken)) {
                $cart = $session->get('cart', []);
                foreach($cart as $cartItem){
                    if($cartItem['id'] === $productId && $cartItem['size'] === $size){
                        $index = array_search($cartItem,$cart);
                        unset($cart[$index]);
                        $session->set('cart', $cart);
                        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
                        return $this->redirectToRoute('app_cart');
                    }
                }
            }
        }
        return new Response();
    }   

    #[Route('/cart/quantity', name: 'app_cart_quantity')]
    public function quantity(SessionInterface $session): Response
    {   
        $totalQuantity = 0;
        $cart = $session->get('cart', []);
        foreach($cart as $cartItem){
            $totalQuantity += $cartItem['quantity'];
        }
       
        return new Response($totalQuantity);
    }   

    #[Route('/cart/totalPrice', name: 'app_cart_totalPrice')]
    public function totalPrice(SessionInterface $session, ProductRepository $productRepository): Response
    {   
        $totalPrice = 0;
        $cart = $session->get('cart', []);
        foreach($cart as $cartItem){
            $id = $cartItem['id'];
            $quantity = $cartItem['quantity'];
            $product = $productRepository->find($id);
            $productPrice = $product->getPrice();

            $priceWithQuantity = $productPrice*$quantity;
            $totalPrice += $priceWithQuantity;
        }
       
        return new Response($totalPrice);
    }   
    #[Route('/cart/totalPriceForEachProduct/{id}/{sizeId}', name: 'app_cart_totalPriceForEachProduct')]
    public function totalPriceForEachProduct(Product $product,int $sizeId, SessionInterface $session): Response
    {   
        // dd($product,$sizeId);
        $cart = $session->get('cart', []);
        foreach($cart as $cartItem){
            if($cartItem['id'] === $product->getId() && $cartItem['size'] === $sizeId){
                // dd($product,$sizeId);
                $totalPriceForEachProduct = $product->getPrice()*$cartItem['quantity'];
            }
        }
        return new Response($totalPriceForEachProduct);
    }   

    #[Route('/cart/clear', name: 'app_cart_clear')]
    public function clear(CartService $cartService): Response
    {       
            $cartService->clearCart();
            return $this->redirectToRoute('app_cart');
    }   
}
