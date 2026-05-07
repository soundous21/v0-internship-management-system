<?php

namespace App\Repository;

use App\Entity\Offers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OffersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        // هنا نخبر Symfony أن هذا المستودع مسؤول عن التعامل مع Offers
        parent::__construct($registry, Offers::class);
    }

}