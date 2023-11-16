<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class FilterController extends AbstractController
{
    #[Route('/filter', name: 'app_filter')]
    public function filter(Request $request, ProductRepository $productRepository): Response
    {
        $sortBy = $request->get('orderBy');
        $products = $productRepository->findFilter($sortBy);    
       
            return $this->render('product/allProduct.html.twig', [
                'products' =>  $products,
                'maxRange' => $productRepository->findOneBy([],['price'=>'DESC'])->getPrice(),
                'minRange' => $productRepository->findOneBy([],['price'=>'ASC'])->getPrice(),
            ]);
        
    }
}

