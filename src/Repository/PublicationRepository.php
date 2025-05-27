<?php

namespace App\Repository;

use App\Entity\Publication;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Publication>
 */
class PublicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publication::class);
    }

    /**
     * Récupère toutes les publications sauf celles des utilisateurs bloqués
     */
    public function findePublicationsBloquer(User $currentUser): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.user', 'u')
            ->where('NOT EXISTS (
                SELECT 1 FROM App\Entity\CompteBloquer cb 
                WHERE cb.user = :currentUser 
                AND cb.userBlocked = u.id
            )')
            ->setParameter('currentUser', $currentUser)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les publications d'un utilisateur spécifique s'il n'est pas bloqué
     */
    // public function findPublicationsNoBloquer(User $author, User $currentUser): array
    // {
    //     return $this->createQueryBuilder('p')
    //         ->leftJoin('p.author', 'a')
    //         ->where('p.author = :author')
    //         ->andWhere('NOT EXISTS (
    //             SELECT 1 FROM App\Entity\User u 
    //             JOIN u.blockedUsers b 
    //             WHERE u.id = :currentUser AND b.id = :author
    //         )')
    //         ->setParameter('author', $author)
    //         ->setParameter('currentUser', $currentUser)
    //         ->orderBy('p.createdAt', 'DESC')
    //         ->getQuery()
    //         ->getResult();
    // }


     public function findPostOrdre(): array
    {
        return $this->createQueryBuilder('p')
            
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
            
    }
}
