<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/produit', name: 'product_')]
final class ProductController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig');
    }

    #[Route('/{slug}', name: 'details')]
    public function details(Product $product): Response
    {
        return $this->render('product/details.html.twig', ['product' => $product]);
    }
}
