<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function insertBook(Book $book): Book
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($book);
        $entityManager->flush();

        return $book;
    }

    public function updateBook(Book $book)
    {
        $entityManager = $this->getEntityManager();
        $dbBook = $entityManager->getRepository(Book::class)->find($book->getId());

        $dbBook->setTitle($book->getTitle());
        $dbBook->setAuthor($book->getAuthor());
        $dbBook->setPublicationDate($book->getPublicationDate());
        $dbBook->setTranslations($book->getTranslations());

        $entityManager->flush();

        return $dbBook;
    }

    public function removeBook(Book $book)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($book);
        $entityManager->flush();
    }

    public function getCountForGrid($q = null, $author = null, $translation = null): ?string
    {
        $qb = $this->createQueryBuilder('b')
        ->select('count(b.id)');

        $this->gridFilterSet($qb, $q, $author, $translation);

        $qb = $qb->getQuery();

        return $qb->getSingleScalarResult();
    }

    public function getForGrid($limit, $offset, $q = null, $author = null, $translation = null): ?Array
    {
        $qb = $this->createQueryBuilder('b')
            ->orderBy('b.publication_date', 'ASC')
            ->setFirstResult( $offset )
            ->setMaxResults( $limit );

        $this->gridFilterSet($qb, $q, $author, $translation);
        
        $qb = $qb->getQuery();
        return $qb->execute();
    }

    private function gridFilterSet(&$qb, $q, $author, $translation)
    {
        if(strlen($q))
        {
            $qb->andWhere("b.title LIKE :query")->setParameter('query', $q."%");
        }
        
        if(strlen($author))
        {
            $qb->andWhere("b.author = :author")
            ->setParameter('author', $author);
        }
        
        if(strlen($translation))
        {
            $qb
            ->leftJoin('App\Entity\Translations', 't', 'WITH', 't.book = b.id')
            ->andWhere("t.name = :translation")
            ->setParameter('translation', $translation);
        }
    }

    public function getDistinctTranslations(): ?Array
    {
        return $this->createQueryBuilder('b')->select('DISTINCT t.name')->leftJoin('App\Entity\Translations', 't', 'WITH', 't.book = b.id')->orderBy('t.name', 'DESC')->getQuery()->execute();
    }

    public function getDistinctAuthors(): ?Array
    {
        return $this->createQueryBuilder('b')->select('DISTINCT a')->leftJoin('App\Entity\Author', 'a', 'WITH', 'a.id = b.author')->orderBy('a.name, a.surname', 'DESC')->getQuery()->execute();
    }
}
