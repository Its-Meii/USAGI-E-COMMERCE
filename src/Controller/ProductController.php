<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/product/{slug}', name: 'app_product')]
    public function index(Product $product): Response
    {
        return $this->render('product/index.html.twig', [
            'product' => $product,
        ]);
    }
    #[Route('/product', name: 'app_all_product')]
    public function AllProduct(ProductRepository $productRepo): Response
    {

        return $this->render('product/allProduct.html.twig', [
            'products' =>$productRepo->findAll(),
        ]);
    }
    
    #[Route('/collection', name: 'app_collection')]
    public function ShowCollection(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }
}
