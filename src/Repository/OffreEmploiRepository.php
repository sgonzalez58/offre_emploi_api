<?php

namespace App\Repository;

use App\Entity\OffreEmploi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OffreEmploi>
 *
 * @method OffreEmploi|null find($id, $lockMode = null, $lockVersion = null)
 * @method OffreEmploi|null findOneBy(array $criteria, array $orderBy = null)
 * @method OffreEmploi[]    findAll()
 * @method OffreEmploi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OffreEmploiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OffreEmploi::class);
    }

    public function add(OffreEmploi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(OffreEmploi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return OffreEmploi[] Returns an array of OffreEmploi objects
     */
    public function findByCommunes(array $communes, $limit=null, $offset=null): array
    {
        $result = $this->createQueryBuilder('o');
        $result->Where('o.commune IS NULL');
        foreach($communes as $commune){
            $result
                ->orWhere('o.commune = '. $commune->getId());
        };
        if (!is_null($offset)){
            $result->setFirstResult($offset);
        }
        if (!is_null($limit)){
            $result->setMaxResults($limit);
        }
        return $result
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?OffreEmploi
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
