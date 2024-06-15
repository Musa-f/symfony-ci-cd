<?php

namespace App\Controller\API;

use App\Entity\Format;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FormatController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/api/formats', name: 'api_formats', methods: ['GET'])]
    public function getFormats(): JsonResponse
    {
        $formats = $this->em->getRepository(Format::class)->findAll();
        
        return $this->json($formats, 200, [], [
            'groups' => ['format.index']
        ]);
    }

    #[Route('/api/formats/{id}', name: 'api_format', methods: ['GET'])]
    public function getFormat($id): JsonResponse
    {
        $format = $this->em->getRepository(Format::class)->find($id);
        
        return $this->json($format, 200, [], [
            'groups' => ['format.index']
        ]);
    }
}
