<?php

namespace proGestBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * VenteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class VenteRepository extends EntityRepository
{
  public function findByDateLike($term)
  {

  	$qb = $this->createQueryBuilder('v');
  	$qb ->select('v')
  	->where('v.dateVente LIKE :term')
  	->setParameter('term', '%'.$term.'%');

  	$array = $qb->getQuery()
  	->getResult();

  	return $array;
  }
}
