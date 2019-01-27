<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function insertAuthor(Author $author): Author
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($author);
        $entityManager->flush();

        return $author;
    }

    public function updateAuthor(Author $author)
    {
        $entityManager = $this->getEntityManager();
        $dbAuthor = $entityManager->getRepository(Author::class)->find($author->getId());

        $dbAuthor->setName($author->getName());
        $dbAuthor->setSurname($author->getSurname());
        $dbAuthor->setCountry($author->getCountry());

        $entityManager->flush();

        return $dbAuthor;
    }

    public function removeAuthor(Author $author)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($author);
        $entityManager->flush();
    }

    public function get(): ?Array
    {
        return $this->createQueryBuilder('a')
        ->getQuery()
        ->execute();
    }

    public function getCountForGrid($q = null, $country = null): ?string
    {
        $qb = $this->createQueryBuilder('a')
        ->select('count(a.id)');

        $this->gridFilterSet($qb, $q, $country);

        $qb = $qb->getQuery();

        return $qb->getSingleScalarResult();
    }

    public function getForGrid($limit, $offset, $q = null, $country = null): ?Array
    {
        $qb = $this->createQueryBuilder('a')
            ->setFirstResult( $offset )
            ->setMaxResults( $limit );

        $this->gridFilterSet($qb, $q, $country);
        
        $qb = $qb->getQuery();
        return $qb->execute();
    }

    private function gridFilterSet(&$qb, $q, $country)
    {
        if(strlen($q))
        {
            $qb->andWhere($qb->expr()->orX(
                "CONCAT(a.name,' ',a.surname) LIKE :query",
                "CONCAT(a.surname,' ',a.name) LIKE :query"
            ))->setParameter('query', $q."%");
        }
        
        if(strlen($country))
        {
            $qb->andWhere("a.country = :country")
            ->setParameter('country', $country);
        }
    }

    public function getDistinctCountries(): ?Array
    {
        return $this->createQueryBuilder('a')->select('DISTINCT a.country')->getQuery()->execute();
    }
    
    public function findOneById($id): ?Author
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
