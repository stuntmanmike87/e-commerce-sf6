<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Categories;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories', name: 'categories_')]
final class CategoriesController extends AbstractController
{
    #[Route('/{slug}', name: 'list')]
    public function list(Categories $category, ProductsRepository $productsRepository, Request $request): Response
    {
        //On va chercher le numéro de page dans l'url
        $page = $request->query->getInt('page', 1);

        //On va chercher la liste des produits de la catégorie
        $categ = $category->getSlug();
        $products = $productsRepository->findProductsPaginated($page, (string)$categ, 4);

        return $this->render('categories/list.html.twig', ['category' => $category, 'products' => $products]);
        //return $this->render('categories/list.html.twig', compact('category', 'products'));
    }
}