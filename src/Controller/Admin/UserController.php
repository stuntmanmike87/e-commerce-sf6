<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/admin/utilisateurs/', name: 'index')]
    public function index(UserRepository $userRepository): Response
    {
        $user = $userRepository->findBy([], ['firstname' => 'asc']);

        return $this->render('admin/users/index.html.twig', ['user' => $user]);
    }
}
