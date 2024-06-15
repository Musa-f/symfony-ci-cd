<?php

namespace App\Repository;

use App\Entity\Resource;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Resource>
 *
 * @method Resource|null find($id, $lockMode = null, $lockVersion = null)
 * @method Resource|null findOneBy(array $criteria, array $orderBy = null)
 * @method Resource[]    findAll()
 * @method Resource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Resource::class);
    }

    public function addResource($data, $format, $category, $user, $sharedUsers = null)
    {
        try {
            $entityManager = $this->getEntityManager();
            $resource = new Resource();
            
            $resource->setTitle($data['title']);
            $resource->setCreationDate(new DateTime());
            $resource->setType($data['idLink']);
            $resource->setVisibility($data['visibility']);
            $resource->setActive(0);
            $resource->setFormat($format);
            $resource->setCategory($category);
            $resource->setUser($user);

            if($sharedUsers){
                foreach($sharedUsers as $sharedUser){
                    $resource->addShare($sharedUser);
                }
            }

            if(!empty($data['content']))
                $resource->setContent($data['content']);
    
            $entityManager->persist($resource);
            $entityManager->flush();
    
            return $resource;
        } catch (ORMException $e) {
            throw new \Exception("Erreur lors de la création de la resource : " . $e->getMessage());
        }
    }

    public function findNotActivatedResources()
    {
        return $this->createQueryBuilder('r')
            ->leftJoin(User::class, 'u', 'WITH', 'u.active = 1')
            ->andWhere('r.active = 0')
            ->getQuery()
            ->getResult();
    }

    public function findAllResources($user = null, $page = 1, $limit = 3)
    {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.visibility = :publicVisibility')
            ->andWhere('r.active = 1')
            ->setParameter('publicVisibility', 2)
            ->orderBy('r.creationDate', 'DESC');

        if ($user) {
            $qb->orWhere('r.visibility = :privateVisibility AND r.user = :userId')
               ->setParameter('privateVisibility', 0)
               ->setParameter('userId', $user->getId());

            $qb->orWhere(':user MEMBER OF r.shares AND r.visibility = :sharedVisibility')
               ->setParameter('sharedVisibility', 1)
               ->setParameter('user', $user->getId());

            $qb->orWhere('r.user = :user')
                ->setParameter('user', $user->getId());
        }

        $qb->setMaxResults($limit)
        ->setFirstResult(($page - 1) * $limit);

        return $qb->getQuery()->getResult();
    }

    public function findResource($user = null, $idResource)
    {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.visibility = :publicVisibility')
            ->andWhere('r.active = 1')
            ->andWhere('r.id = :idResource')
            ->setParameter('publicVisibility', 2)
            ->setParameter('idResource', $idResource)
            ->orderBy('r.creationDate', 'DESC');

        if ($user) {
            $qb->orWhere('r.visibility = :privateVisibility AND r.user = :userId')
               ->setParameter('privateVisibility', 0)
               ->setParameter('userId', $user->getId());

            $qb->orWhere(':user MEMBER OF r.shares AND r.visibility = :sharedVisibility')
               ->setParameter('sharedVisibility', 1)
               ->setParameter('user', $user->getId());

            $qb->orWhere('r.user = :user')
                ->setParameter('user', $user->getId());
        }

        return $qb->getQuery()->getResult();
    }
}