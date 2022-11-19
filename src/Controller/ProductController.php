<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product', methods: ['GET'])]
    public function index(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();
        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'cost' => $product->getCost(),
                'price' => $product->getPrice(),
                'units' => $product->getUnits(),
                'description' => $product->getDescription(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/product', name: 'app_product_add', methods: ['POST'])]
    public function new(ManagerRegistry  $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $decoded = json_decode($request->getContent());
        $product = new Product();
        $product->setName($decoded->name);
        $product->setCost($decoded->cost);
        $product->setPrice($decoded->price);
        $product->setUnits($decoded->units);
        $product->setDescription($decoded->description);

        $entityManager->persist($product);
        $entityManager->flush();

        return $this->json('Created new product successfully with id ' . $product->getId());
    }

    #[Route('/product/{id}', name: 'app_product_delete', methods: ['DELETE'])]
    public function delete(ManagerRegistry  $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json('No product found for id ' . $id, 404);
        }

        $entityManager->remove($product);
        $entityManager->flush();

        return $this->json('Deleted a product successfully with id ' . $id);
    }
}
