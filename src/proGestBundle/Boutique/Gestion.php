<?php

namespace proGestBundle\Gestion;

use Symfony\Component\Config\Definition\Exception\Exception;

use proGestBundle\Entity\Article;
use proGestBundle\Entity\PrixMultiple;

class Gestion
{
  protected $em;

  private $repository;

  private $boutique;

  private $initState;

  public function __construct(\Doctrine\ORM\EntityManager $em)
  {
    $this->em = $em;
    $this->repository = $this->em->getRepository('proGestBundle:boutique');
  }

  public function isInit()
  {
    $boutiques = $this->getBoutiques();

    if (count($boutiques) == 1) {
      foreach ($boutiques as $key => $value) {
        $this->boutique = $value;
      }
      return true;
    } else {
      return false;
    }
  }

  public function isActive()
  {
    $boutique = $this->repository->find(7);
    $isactive = $boutique->getIsActive();
  }

  private function getBoutiques(){
    $boutiques = $this->repository->findAll();
    return $boutiques;
  }

  public function getBoutique()
  {
    return $this->boutique;
  }

  public function initState()
  {
    return $this->initState;
  }

  public function getPrices(Article $article, PrixMultiple $prixmultiple)
  {
    $this->repository = $this->em->getRepository('proGestBundle:Article');
    $prices = $this->repository->findPricesOrderASC($article->getId());
    $quantiteRef = $prixmultiple->getQuantite();


    $returnPrices['this']['id'] = $prixmultiple->getId();
    $returnPrices['this']['quantite'] = $prixmultiple->getQuantite();
    $returnPrices['this']['prix'] = $prixmultiple->getPrix();

    foreach ($prices as $key => $value) {
      if ($quantiteRef < $value->getQuantite()){
        $returnPrices['previous']['id'] = $prices[$key-2]->getId();
        $returnPrices['previous']['quantite'] = $prices[$key-2]->getQuantite();
        $returnPrices['previous']['prix'] = $prices[$key-2]->getPrix();

        $returnPrices['following']['id'] = $prices[$key]->getId();
        $returnPrices['following']['quantite'] = $prices[$key]->getQuantite();
        $returnPrices['following']['prix'] = $prices[$key]->getPrix();
      }
      return $returnPrices;
    }
  }
}
