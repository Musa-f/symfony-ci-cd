<?php

namespace App\Controller\API;

use App\Entity\Category;
use App\Entity\File;
use App\Entity\Format;
use App\Entity\Resource;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ResourceController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/api/resources', name: 'api_get_resources', methods:['GET'])]
    public function getAllResources(Request $request): JsonResponse
    {
        $currentUser = $this->getUser();
        $page = $request->query->getInt('page', 1); 

        $resources = $this->em->getRepository(Resource::class)
            ->findAllResources(
                $currentUser ? $currentUser : null,  
                $page
            );

        $totalResources = count($resources);

        return $this->json([
            'total' => $totalResources,
            'page' => $page,
            'data' => $resources
        ], 200, [], [
            'groups' => [
                'resource.index', 
                'category.index', 
                'format.index',
                'file.index',
                'user.index'
            ]
        ]);
    }

    #[Route('/api/resources/{id}', name: 'api_get_resource', methods:['GET'])]
    public function getResource($id) : JsonResponse
    {
        $resource = $this->em->getRepository(Resource::class)
            ->findResource(
                null,  
                $id
            );

        return $this->json($resource, 200, [], [
            'groups' => [
                'resource.index', 
                'category.index', 
                'format.index',
                'file.index',
                'user.index'
            ]
        ]);
    }

    #[Route('/api/resources/add', name: 'api_add_resource', methods:['POST'])]
    public function addResource(Request $request) : JsonResponse
    {
        $content = $request->getContent();
        $data = json_decode($content, true);

        if(isset($data['users']))
        {
            foreach($data['users'] as $sharedIdUser) {
                $sharedUsers[] = $this->em->getRepository(User::class)->find($sharedIdUser);
            }
        }

        $user = $this->em->getRepository(User::class)->find(intval($data['idUser']));
        $format = $this->em->getRepository(Format::class)->find(intval($data['idFormat']));
        $category = $this->em->getRepository(Category::class)->find(intval($data['idCategory']));
        $ressource = $this->em->getRepository(Resource::class)->addResource($data, $format, $category, $user, isset($sharedUsers) ?? $sharedUsers);

        if(!empty($file)) {
            $this->em->getRepository(File::class)->createFile($file, $ressource);
        }

        $message = "Ressource créée avec succès";

        return $this->json($message, 200);
    }

    #[Route('/api/resources/{id}', name: 'api_delete_resource', methods:['DELETE'])]
    public function deleteResource($id) : JsonResponse
    {
        $resource = $this->em->getRepository(Resource::class)->find($id);
        $this->em->remove($resource);
        $this->em->flush();
        
        return $this->json($resource, 200, [], [
            'groups' => [
                'resource.index'
            ]
        ]);
    }
}
