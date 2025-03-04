<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    #[Route('/admin/categories/', name: 'index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findBy([], ['categoryOrder' => 'asc']);

        return $this->render('admin/category/index.html.twig', ['category' => $category]);
    }
}
