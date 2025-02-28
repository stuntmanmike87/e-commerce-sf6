<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/commandes', name: 'app_order_')]
final class OrderController extends AbstractController
{
    #[Route('/ajout', name: 'add')]
    public function add(SessionInterface $session, ProductRepository $productRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $panier = $session->get('panier', []);

        /** @var array<mixed> $panier */
        if ([] === $panier) {
            $this->addFlash('message', 'Votre panier est vide');

            return $this->redirectToRoute('main');
        }

        // Le panier n'est pas vide, on crée la commande
        $order = new Order();

        // On remplit la commande
        /** @var User $user */
        $user = $this->getUser();
        $order->setUser($user);
        $order->setReference(uniqid());

        // On parcourt le panier pour créer les détails de commande
        /** @var int $quantity */
        foreach ($panier as $item => $quantity) {
            $orderDetail = new OrderDetail();

            // On va chercher le produit
            $product = $productRepository->find($item);

            /** @var Product $product */
            $price = $product->getPrice();

            // On crée le détail de commande
            $orderDetail->setProduct($product);
            $orderDetail->setPrice((int) $price);
            $orderDetail->setQuantity($quantity);

            $order->addOrderDetail($orderDetail);
        }

        // On persiste et on flush
        $em->persist($order);
        $em->flush();

        $session->remove('panier');

        $this->addFlash('message', 'Commande créée avec succès');

        return $this->redirectToRoute('main');
    }
}
