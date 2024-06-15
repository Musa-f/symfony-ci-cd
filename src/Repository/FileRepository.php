<?php

namespace App\Repository;

use App\Entity\File;
use App\Service\FileFormatterService;
use App\Service\FileProcessingService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<File>
 *
 * @method File|null find($id, $lockMode = null, $lockVersion = null)
 * @method File|null findOneBy(array $criteria, array $orderBy = null)
 * @method File[]    findAll()
 * @method File[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    public function createFile($fileData, $resource)
    {
        $entityManager = $this->getEntityManager();
        
        $file = new File();
        $fileName = FileFormatterService::formatFileName($fileData->getClientOriginalName());
        $file->setName($fileName);
        $file->setSize(5);
        $file->setResource($resource);
        $entityManager->persist($file);
        $entityManager->flush();

        $uploadDirectory = 'uploads/' . $resource->getId(); 
        if (!file_exists($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }

        $fileData->move($uploadDirectory, $fileName);
    }

}
