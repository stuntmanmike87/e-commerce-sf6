<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    #[Route('/cart/', name: 'index')]
    public function index(SessionInterface $session, ProductRepository $productRepository): Response
    {
        $panier = $session->get('panier', []);

        // On initialise des variables
        $data = [];
        $total = 0;

        /** @var array $panier */
        /** @var int $id */
        /** @var int $quantity */
        foreach ($panier as $id => $quantity) {
            /** @var Product $product */
            $product = $productRepository->find($id);

            $data[] = [
                'product' => $product,
                'quantity' => $quantity,
            ];
            /* @var int $total */
            $total += (int) $product->getPrice() * $quantity;
        }

        return $this->render('cart/index.html.twig', ['data' => $data, 'total' => $total]);
    }

    #[Route('/cart/add/{id}', name: 'add')]
    public function add(Product $product, SessionInterface $session): RedirectResponse
    {
        // On récupère l'id du produit
        $id = $product->getId();

        // On récupère le panier existant
        /** @var array<int> $panier */
        $panier = $session->get('panier', []);

        // On ajoute le produit dans le panier s'il n'y est pas encore
        // Sinon on incrémente sa quantité
        if (null === $panier[$id]) {// if(empty($panier[$id])){
            $panier[$id] = 1;
        } else {
            ++$panier[$id]; // $panier[$id]++;
        }

        $session->set('panier', $panier);

        // On redirige vers la page du panier
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart/remove/{id}', name: 'remove')]
    public function remove(Product $product, SessionInterface $session): RedirectResponse
    {
        // On récupère l'id du produit
        $id = $product->getId();

        // On récupère le panier existant
        /** @var array<int> $panier */
        $panier = $session->get('panier', []);

        // On retire le produit du panier s'il n'y a qu'1 exemplaire
        // Sinon on décrémente sa quantité
        if (null !== $panier[$id]) {// if(!empty($panier[$id])){
            if ($panier[$id] > 1) {
                --$panier[$id]; // $panier[$id]--;
            } else {
                unset($panier[$id]); // $panier[$id] = null;
            }
        }

        $session->set('panier', $panier);

        // On redirige vers la page du panier
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart/delete/{id}', name: 'delete')]
    public function delete(Product $product, SessionInterface $session): RedirectResponse
    {
        // On récupère l'id du produit
        $id = $product->getId();

        // On récupère le panier existant
        /** @var array<int> $panier */
        $panier = $session->get('panier', []);

        if (null !== $panier[$id]) {// if(!empty($panier[$id])){
            unset($panier[$id]); // $panier[$id] = null;
        }

        $session->set('panier', $panier);

        // On redirige vers la page du panier
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart/empty', name: 'empty')]
    public function empty(SessionInterface $session): RedirectResponse
    {
        $session->remove('panier');

        return $this->redirectToRoute('cart_index');
    }
}
