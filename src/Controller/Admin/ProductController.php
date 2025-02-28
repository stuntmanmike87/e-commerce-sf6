<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Image;
use App\Entity\Product;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use App\Service\PictureService;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/produits', name: 'admin_product_')]
final class ProductController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProductRepository $productRepository): Response
    {
        $produits = $productRepository->findAll();

        return $this->render('admin/product/index.html.twig', ['produits' => $produits]);
    }

    #[Route('/ajout', name: 'add')]
    public function add(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        PictureService $pictureService,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // On crée un "nouveau produit"
        $product = new Product();

        // On crée le formulaire
        $productForm = $this->createForm(ProductFormType::class, $product);

        // On traite la requête du formulaire
        $productForm->handleRequest($request);

        // On vérifie si le formulaire est soumis ET valide
        if ($productForm->isSubmitted() && $productForm->isValid()) {
            // On récupère les images
            /** @var Collection<int, Image> $images */
            $images = $productForm->get('images')->getData();

            foreach ($images as $image) {
                // On définit le dossier de destination
                $folder = 'product';

                // On appelle le service d'ajout
                /** @var UploadedFile $image */
                $fichier = $pictureService->add($image, $folder, 300, 300);

                $img = new Image();
                $img->setName($fichier);
                $product->addImage($img);
            }

            // On génère le slug
            $prod = $product->getName();
            $slug = $slugger->slug((string) $prod);
            $product->setSlug((string) $slug);

            // On arrondit le prix
            // $prix = $product->getPrice() * 100;
            // $product->setPrice($prix);

            // On stocke
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès');

            // On redirige
            return $this->redirectToRoute('admin_product_index');
        }

        // return $this->render('admin/product/add.html.twig',[
        //     'productForm' => $productForm->createView()
        // ]);

        return $this->render('admin/product/add.html.twig', ['productForm' => $productForm]);
    }

    #[Route('/edition/{id}', name: 'edit')]
    public function edit(
        Product $product,
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        PictureService $pictureService,
    ): Response {
        // On vérifie si l'utilisateur peut éditer avec le Voter
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);

        // On divise le prix par 100
        // $prix = $product->getPrice() / 100;
        // $product->setPrice($prix);

        // On crée le formulaire
        $productForm = $this->createForm(ProductFormType::class, $product);

        // On traite la requête du formulaire
        $productForm->handleRequest($request);

        // On vérifie si le formulaire est soumis ET valide
        if ($productForm->isSubmitted() && $productForm->isValid()) {
            // On récupère les images
            /** @var Collection<int, Image> $images */
            $images = $productForm->get('images')->getData();

            /** @var Image $image */
            foreach ($images as $image) {
                // On définit le dossier de destination
                $folder = 'product';

                // On appelle le service d'ajout
                /** @var UploadedFile $image */
                $fichier = $pictureService->add($image, $folder, 300, 300);

                $img = new Image();
                $img->setName($fichier);
                $product->addImage($img);
            }

            // On génère le slug
            $prod = $product->getName();
            $slug = $slugger->slug((string) $prod);
            $product->setSlug((string) $slug);

            // On arrondit le prix
            // $prix = $product->getPrice() * 100;
            // $product->setPrice($prix);

            // On stocke
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit modifié avec succès');

            // On redirige
            return $this->redirectToRoute('admin_product_index');
        }

        return $this->render('admin/product/edit.html.twig', [
            'productForm' => $productForm, // ->createView(),
            'product' => $product,
        ]);
    }

    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(Product $product): Response
    {
        // On vérifie si l'utilisateur peut supprimer avec le Voter
        $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);

        return $this->render('admin/product/index.html.twig');
    }

    #[Route('/suppression/image/{id}', name: 'delete_image', methods: ['DELETE'])]
    public function deleteImage(
        Image $image,
        Request $request,
        EntityManagerInterface $em,
        PictureService $pictureService,
    ): JsonResponse {
        // On récupère le contenu de la requête
        /** @var array<string> $data */
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if ($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])) {
            // Le token csrf est valide
            // On récupère le nom de l'image
            $nom = $image->getName();

            if ($pictureService->delete((string) $nom, 'product', 300, 300)) {
                // On supprime l'image de la base de données
                $em->remove($image);
                $em->flush();

                return new JsonResponse(['success' => true], Response::HTTP_OK);
            }

            // La suppression a échoué
            return new JsonResponse(['error' => 'Erreur de suppression'], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['error' => 'Token invalide'], Response::HTTP_BAD_REQUEST);
    }
}
