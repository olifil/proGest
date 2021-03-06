<?php

namespace proGestBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * VariationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class VariationRepository extends EntityRepository
{
  public function findByArticleVente($articleId, $venteId)
  {
    $qb = $this -> _em -> createQueryBuilder();

    $qb->select('v')
       ->from('proGestBundle:Variation', 'v')
       ->where('v.article = :articleId')
       ->setParameter('articleId', $articleId)
       ->andWhere('v.vente = :venteId')
       ->setParameter('venteId', $venteId);

    return $qb->getQuery()
              ->getResult();
  }

  public function findInactiveByArticle($articleId)
  {
    $qb = $this -> _em -> createQueryBuilder();

    $qb ->select('v')
        ->from('proGestBundle:Variation', 'v')
        ->where('v.article = :articleId')
        ->setParameter('articleId', $articleId)
        ->andWhere('v.isActive = false');

    return $qb->getQuery()
              ->getResult();
  }
}
