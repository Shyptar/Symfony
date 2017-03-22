<?php
// src/OC/PlatformBundle/Entity/AdvertRepository.php

namespace OC\PlatformBundle\Entity;

use Doctrine\ORM\EntityRepository;

class AdvertRepository extends EntityRepository
{
  public function getAdverts()
  {
    $query = $this->createQueryBuilder('a')
      ->orderBy('a.date', 'DESC')
      ->getQuery()
    ;

    return $query->getResult();
  }
}