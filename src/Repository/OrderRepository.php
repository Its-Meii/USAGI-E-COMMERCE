<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function save(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getCA(){

        $conn = $this->getEntityManager()->getConnection();
                $sql = 'SELECT SUM(total_price) as total,  CONCAT(MONTH(created_at), "-", YEAR(created_at)) as orderDate
                FROM eshopusagi.order
                WHERE eshopusagi.order.status = "Commande payée"
                GROUP BY orderDate
                ';
                $request = $conn->prepare($sql);
                $resultSet  = $request->executeQuery([]);
                $results = $resultSet->fetchAll();
        return $results;
   } 
   public function getLastOrderShipping(){

        $conn = $this->getEntityManager()->getConnection();
                $sql = 'SELECT shipping_id
                FROM eshopusagi.order
                ORDER BY eshopusagi.order.id DESC
                LIMIT 1
                ';
                $request = $conn->prepare($sql);
                $resultSet  = $request->executeQuery([]);
                $results = $resultSet->fetchOne();
        return $results;
    }       
//    /**
//     * @return Order[] Returns an array of Order objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Order
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
