<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/categories', name: 'admin_categories_')]
final class CategoriesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoriesRepository $categoriesRepository): Response
    {
        $categories = $categoriesRepository->findBy([], ['categoryOrder' => 'asc']);

        return $this->render('admin/categories/index.html.twig', ['categories' => $categories]);
        //return $this->render('admin/categories/index.html.twig', compact('categories'));
    }
}