<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    } 
    public function findAllByFilters($filter, $min, $max){
        $tags=[
            'prix_croissant' => 'p.price asc',
            'prix_decroissant' => 'p.price desc',
            'alphabetique_croissant' => 'p.name asc',
            'alphabetique_decroissant' => 'p.name desc',
        ];

        $qb = $this->createQueryBuilder('p');

        // $qb
        //     ->andWhere("p.price BETWEEN :min AND :max")
        //     ->setParameter("min", $min)
        //     ->setParameter("max", $max);

        if(array_key_exists($filter, $tags)){
            $qb->add('orderBy', ($tags[$filter]));
        }
        return $qb->getQuery()->getResult();
    }
    public function findFilter($filter){
        $tags=[
            'prix_croissant' => 'p.price asc',
            'prix_decroissant' => 'p.price desc',
        ];

        $qb = $this->createQueryBuilder('p');

        if(array_key_exists($filter, $tags)){
            $qb->add('orderBy', ($tags[$filter]));
        }
        return $qb->getQuery()->getResult();
    }
    public function findProductsByTag(int $id,string $lang){

        $conn = $this->getEntityManager()->getConnection();
                $sql = 'SELECT product.id, photo_front, photo_back, price, quantity, slug, tags_id, product_translation.name
                FROM product
                LEFT JOIN product_translation
                ON product_translation.translatable_id = product.id
                WHERE tags_id = :param
                AND product_translation.locale = :lang';
                $request = $conn->prepare($sql);
                $resultSet  = $request->executeQuery(['param' => $id, 'lang' => $lang]);
                $products = $resultSet->fetchAll();
        return $products;
   }    
     
    // public function findByRange($min, $max):array{
    //     return $this->createQueryBuilder('p')
    //         ->where('p.price <= :max')
    //         ->andwhere('p.price >= :min')
    //         ->setParameter('min', $min)
    //         ->setParameter('max', $max)
    //         ->orderBy('p.id', 'ASC')
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }
}
