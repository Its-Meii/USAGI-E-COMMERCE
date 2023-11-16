<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {   
            return $this->render('profile/showProfile.html.twig', [
            ]);     
    }

    #[Route('/profile/command', name: 'app_profile_command')]
    public function showCommand(Security $security): Response
    {       

            // dd($security->getUser()->getOrders());
            $userOrders = $security->getUser()->getOrders();

            return $this->render('profile/showProfile.html.twig', [
                'commands' => $userOrders,
            ]);     
    }


    #[Route('/profile/command/{id}', name: 'app_profile_command_details')]
    public function showCommandDetails(Order $order): Response
    {       
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
     
            return $this->render('profile/showProfile.html.twig', [
                'order_number' => $order->getId(),
                'order_date' => $order->getCreatedAt(),
                'totalAmount' => $order->getTotalPrice(),
                'order_lines' => $orderLines,
            ]);     
    }


    #[Route('/profile/change/password', name: 'app_profile_change_password')]
    public function changePassword(Request $request,Security $security,UserPasswordHasherInterface $userPasswordHasher,EntityManagerInterface $entityManager): Response
    {   
        $user = $security->getUser();
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                    $last_password = $form->get('lastPassword')->getData();
                    $new_password = $form->get('confirmNewPassword')->get('first')->getData();
                    $new_password_repeat = $form->get('confirmNewPassword')->get('second')->getData();
    
                    if(password_verify($last_password, $user->getPassword())){
                        if($new_password === $new_password_repeat){
                            $user->setPassword(
                                $userPasswordHasher->hashPassword(
                                    $user,
                                    $new_password
                                )
                            );
                            $entityManager->persist($user);
                            $entityManager->flush();
                            
                            return $this->render('profile/showProfile.html.twig', [
                                'message' => 'success',
                            ]);    
                        }
                        return $this->render('profile/showProfile.html.twig', [
                            'form' => $form,
                            'message' => 'error_same_password',
                        ]); 
                    }
                    return $this->render('profile/showProfile.html.twig', [
                        'form' => $form,
                        'message' => 'error_last_password',
                    ]); 
                }       
            return $this->render('profile/showProfile.html.twig', [
                'form' => $form,
            ]);     
    }
}

