<?php

namespace App\Repository;

use App\Entity\Personnage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Personnage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Personnage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Personnage[]    findAll()
 * @method Personnage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonnageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personnage::class);
    }

    // /**
    //  * @return Personnage[] Returns an array of Personnage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Personnage
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }findPersonnages
    */
    public function findPersonnages($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.Acteur = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }
    public function findUnPersonnage($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    public function findUnPersonnageByActeur($perso, $acteur)
    {
        return $this->createQueryBuilder('p')
            ->Where('p.nom = :valPerso')
            ->andWhere('p.Acteur = :valActeur')
            ->setParameter('valPerso', $perso)
            ->setParameter('valActeur', $acteur)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    public function findUnPersonnageByActeurAndSerie($perso, $acteur,$serie)
    {
        return $this->createQueryBuilder('p')
            ->Where('p.nom = :valPerso')
            ->andWhere('p.Acteur = :valActeur')
            ->andWhere('p.Serie = :valSerie')
            ->setParameter('valPerso', $perso)
            ->setParameter('valActeur', $acteur)
            ->setParameter('valSerie', $serie)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
