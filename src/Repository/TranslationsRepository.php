<?php

namespace App\Repository;

use App\Entity\Translations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Translations|null find($id, $lockMode = null, $lockVersion = null)
 * @method Translations|null findOneBy(array $criteria, array $orderBy = null)
 * @method Translations[]    findAll()
 * @method Translations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TranslationsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Translations::class);
    }
}
