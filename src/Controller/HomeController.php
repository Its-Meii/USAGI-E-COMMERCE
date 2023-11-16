<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Services\MailerService;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository ): Response
    {   
        return $this->render('home/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/setLocal/{local}', name: 'set_local')]
    public function setLocal($local, SessionInterface $session, Request $request):Response{ 
        $session->set('_locale', $local);

        $previousUrl = $request->headers->get("referer");

        return $this->redirect($previousUrl);     
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {   
        return $this->render('home/contact.html.twig', []);
    }
}
