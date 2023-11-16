<?php

namespace App\Controller;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Security $security, Request $request): Response
    {
            if($security->getUser()){
                return $this->redirectToRoute('app_profile');
            }
            $error = $authenticationUtils->getLastAuthenticationError();
            
            $lastUsername = $authenticationUtils->getLastUsername();
    
                return $this->render('security/index.html.twig', [
                    'last_username' => $lastUsername,
                    'error'         => $error,
                ]);
    }
    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout()
    {
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
