<?php

namespace App\Repository;

use App\Entity\Character;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Character>
 *
 * @method Character|null find($id, $lockMode = null, $lockVersion = null)
 * @method Character|null findOneBy(array $criteria, array $orderBy = null)
 * @method Character[]    findAll()
 * @method Character[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CharacterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Character::class);
    }

    //    /**
    //     * @return Character[] Returns an array of Character objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Character
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findCharacterByActorId($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.Actor = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }
    public function findCharacterById($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function findCharacterByActorIdAndName($perso, $acteur)
    {
        return $this->createQueryBuilder('p')
            ->Where('p.name = :valPerso')
            ->andWhere('p.Actor = :valActeur')
            ->setParameter('valPerso', $perso)
            ->setParameter('valActeur', $acteur)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function findCharacterByNameAndByActorIdAndBySeriesId($perso, $acteur, $serie)
    {
        return $this->createQueryBuilder('p')
            ->Where('p.name = :valPerso')
            ->andWhere('p.Actor = :valActeur')
            ->andWhere('p.Series = :valSerie')
            ->setParameter('valPerso', $perso)
            ->setParameter('valActeur', $acteur)
            ->setParameter('valSerie', $serie)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
