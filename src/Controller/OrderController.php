<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Entity\OrderDetail;
use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Service\CartService;
use App\Service\StripeService;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\ShippingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(): Response
    {
        return $this->render('order/index.html.twig', []);
    }

    #[Route('/checkout', name: 'app_checkout')]
    public function checkout(CartService $cartService, StripeService $stripeService, SessionInterface $session, Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepo, ProductSizeRepository $productSizeRepository, Security $security, ShippingRepository $shippingRepo): Response
    {   
        $order = new Order();
        
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $totalPrice = 0;
                $cart = $session->get('cart', []);
                foreach($cart as $cartItem){
        
                    $product = $productRepo->find($cartItem['id']);
        
                    $orderDetail = new OrderDetail();
                    $orderDetail->setProduct($product);
                    $orderDetail->setQuantity($cartItem['quantity']);
                    $orderDetail->setSize($productSizeRepository->find($cartItem['size']));
                    $orderDetail->setPrice($product->getPrice()*$cartItem['quantity']);   
                    $order->addOrderDetail($orderDetail);

                    $totalPrice += $product->getPrice()*$cartItem['quantity'];
                }
                $user = $security->getUser();
                $order->setUser($user);
                $order->setShipping($shippingRepo->find($request->request->get('shipping')));
                $totalPrice += $order->getShipping()->getPrice();

                $order->setTotalPrice($totalPrice);
                $order->setStatus('Commande créée');
                $order->setCreatedAt(new \DateTimeImmutable);

                // dd($request->request->get('register'));
                if($request->request->get('register')!==null){
                    // dd($order->getAddress());
                    $user->setAddress($order->getAddress());
                    $user->setZipcode($order->getZipcode());
                    $user->setCity($order->getCity());
                    $entityManager->persist($user); 
                }
                $entityManager->persist($order); 
              
                $data = $cartService->getStripeData();
                $stripeUrl = $stripeService->createCart($data);
            
                $entityManager->flush();

                $session->set('order_id', $order->getId());

                return $this->redirect($stripeUrl, 303);
            }     

        return $this->render('order/index.html.twig', [
            'form' => $form,
            'shippings' => $shippingRepo->findAll(),
        ]);
    }

    #[Route('/checkout/success/{token}', name: 'app_checkout_success')]
    public function checkoutSuccess(string $token, SessionInterface $sessionInterface, OrderRepository $orderRepository, CartService $cartService, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('stripe_token', $token)) {
            $id = $sessionInterface->get('order_id');
            $order = $orderRepository->find($id);
            $order->setStatus('Commande payée');

            // METS A JOUR LA QUENTITE
            foreach($order->getOrderDetails() as $orderProduct){
                $newQuantity = $orderProduct->getProduct()->getQuantity()-$orderProduct->getQuantity();
                $product = $orderProduct->getProduct();
                $product->setQuantity($newQuantity);
                $entityManager->persist($product);
            }
            $entityManager->persist($order);
            $entityManager->flush();
            $cartService->clearCart();
            $sessionInterface->remove('order_id');

            // RECUPERE LE DETAIL DE LA COMMANDE
            $orderDetails = $order->getOrderDetails();
            $orderLines = [];

            foreach($orderDetails as $orderDetail){
                if($orderDetail->getProduct()){
                    $product = $orderDetail->getProduct();
               
                    $orderLines []= [
                        'productSlug' => $product->getSlug(),
                        'productImg' => $product->getPhotoFront(),
                        'productName' => $product->getName(),
                        'quantity' => $orderDetail->getQuantity(),
                        'subTotal' => $orderDetail->getQuantity() * $product->getPrice(),
                        'size' => $orderDetail->getSize(),
                    ];
                }
                else{
                    $orderLines []= [
                        'quantity' => $orderDetail->getQuantity(),
                        'subTotal' => $order->getTotalPrice(),
                        'size' => $orderDetail->getSize(),
                    ];
                }
               
            }
            
            return $this->render('checkout/index.html.twig', [
                'message' => 'success',
                'order' => $order,
                'order_lines' => $orderLines,
            ]);
        }
        return new Response();
    }
    #[Route('/checkout/error', name: 'app_checkout_error')]
    public function checkoutError(SessionInterface $sessionInterface, OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
    {
        $id = $sessionInterface->get('order_id');
        $order = $orderRepository->find($id);
        $order->setStatus('Echec de la commande');
        
        $entityManager->persist($order);
        $entityManager->flush();
        
        // RECUPERE LE DETAIL DE LA COMMANDE
        $orderDetails = $order->getOrderDetails();
            $orderLines = [];

            foreach($orderDetails as $orderDetail){
                if($orderDetail->getProduct()){
                    $product = $orderDetail->getProduct();
               
                    $orderLines []= [
                        'productSlug' => $product->getSlug(),
                        'productImg' => $product->getPhotoFront(),
                        'productName' => $product->getName(),
                        'quantity' => $orderDetail->getQuantity(),
                        'subTotal' => $orderDetail->getQuantity() * $product->getPrice(),
                        'size' => $orderDetail->getSize(),
                    ];
                }
                else{
                    $orderLines []= [
                        'quantity' => $orderDetail->getQuantity(),
                        'subTotal' => $order->getTotalPrice(),
                        'size' => $orderDetail->getSize(),
                    ];
                }
               
            }
        return $this->render('checkout/index.html.twig', [
            'message' => 'error',
            'order' => $order,
            'order_lines' => $orderLines,
        ]);
    }
}