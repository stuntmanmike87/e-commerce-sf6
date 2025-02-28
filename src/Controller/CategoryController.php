<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/category', name: 'category_')]
final class CategoryController extends AbstractController
{
    #[Route('/{slug}', name: 'list')]
    public function list(Category $category, ProductRepository $productRepository, Request $request): Response
    {
        // On va chercher le numéro de page dans l'url
        $page = $request->query->getInt('page', 1);

        // On va chercher la liste des produits de la catégorie
        $categ = $category->getSlug();
        $product = $productRepository->findProductsPaginated($page, (string) $categ, 4);

        return $this->render('category/list.html.twig', ['category' => $category, 'product' => $product]);
    }
}
