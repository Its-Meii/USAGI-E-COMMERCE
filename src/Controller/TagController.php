<?php

namespace App\Controller;

use App\Entity\Tags;
use App\Repository\TagRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class TagController extends AbstractController
{
    public function getTags(TagRepository $tagRepo): Response
    {
        return $this->render('partials/tags.html.twig', [
            'tags' => $tagRepo->findAll(),
        ]);
    }

    #[Route('/collection/{slug}', name: 'app_tag_show')]
    public function showByTag(Tags $tag, ProductRepository $productRepository, Request $request): Response
    {
        $lang = $request->getSession()->get('_locale');
        if($lang === null){
            $lang='en';
        };
        return $this->render('collection/drawCollection.html.twig', [
            'tag' => $tag,
            'products' => $productRepository->findProductsByTag($tag->getId(), $lang),
        ]);
    }
}
