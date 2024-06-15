<?php

namespace App\Controller\API;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/api/categories', name: 'api_categories', methods: ['GET'])]
    public function getCategories(): JsonResponse
    {
        $categories = $this->em->getRepository(Category::class)->findAll();

        return $this->json($categories, 200, [], [
            'groups' => ['category.index']
        ]);
    }

    #[Route('/api/category/{id}', name: 'api_category', methods: ['GET'])]
    public function getCategory($id): JsonResponse
    {
        $category = $this->em->getRepository(Category::class)->find($id);
        
        return $this->json($category, 200, []);
    }
}
