<?php

namespace proGestBundle\Gestion;

use Symfony\Component\Config\Definition\Exception\Exception;

use proGestBundle\Entity\Article;
use proGestBundle\Entity\Fournisseur;
use proGestBundle\Entity\PrixMultiple;
use ProGestBundle\Entity\Boutique;
use ProGestBundle\Entity\Vente;
use ProGestBundle\Entity\Variation;

class Gestion
{
  protected $em;

  private $repository;

  private $application;

  private $initState;

  public function __construct(\Doctrine\ORM\EntityManager $em)
  {
    $this->em = $em;
  }

  public function isInit()
  {
    $boutiques = $this->getBoutiques();

    if (count($boutiques) == 1) {
      foreach ($boutiques as $key => $value) {
        $this -> application = $value;
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
    $repository = $this->em->getRepository('proGestBundle:Boutique');
    $boutiques = $repository->findAll();
    return $boutiques;
  }

  public function getapplication()
  {
    return $this->application;
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

    $returnPrices['base']['id'] = $article->getId();
    $returnPrices['base']['prix'] = number_format($article->getPrixAchat(), 2);
    $returnPrices['this']['id'] = $prixmultiple->getId();
    $returnPrices['this']['quantite'] = $prixmultiple->getQuantite();
    $returnPrices['this']['prix'] = number_format($prixmultiple->getPrix(), 2);

    // Le prix de référence n'est pas le seul prix multiple
    if (count($prices) > 1) {
      foreach ($prices as $key => $value) {
        // Il existe un prixmultiple supérieur au prix de référence
        if ($quantiteRef < $value->getQuantite()){
          // Le prix de référence est le plus petit des prix multiples
          if ($key > 1){
            $returnPrices['previous']['id'] = $prices[$key-2]->getId();
            $returnPrices['previous']['quantite'] = $prices[$key-2]->getQuantite();
            $returnPrices['previous']['prix'] = number_format($prices[$key-2]->getPrix(), 2);
          }
          $returnPrices['following']['id'] = $prices[$key]->getId();
          $returnPrices['following']['quantite'] = $prices[$key]->getQuantite();
          $returnPrices['following']['prix'] = number_format($prices[$key]->getPrix(), 2);
          $upperPrice = true;
          break;
        } else {
          $upperPrice = false;
        }
      }
      // Aucun prixmultiple supérieur n'a été trouvé. Le prix de référence est le plus important
      if ($upperPrice == false) {
        $prices = $this->repository->findPricesOrderDESC($article->getId());
        foreach ($prices as $key => $value) {
          // Il existe un prixmultiple plus petit que le prix de référence
          if ($quantiteRef > $value->getQuantite()){
            $returnPrices['previous']['id'] = $prices[$key]->getId();
            $returnPrices['previous']['quantite'] = $prices[$key]->getQuantite();
            $returnPrices['previous']['prix'] = number_format($prices[$key]->getPrix(), 2);
            break;
          }
        }
      }
    }
    return $returnPrices;
  }

  public function isCompatibles($prices)
  {
    // Il existe un prix previous
    if (isset($prices['previous'])) {
      // Il existe un prix suivant
      if (isset($prices['following'])) {
        if ($prices['this']['prix'] < $prices['previous']['prix'] && $prices['this']['prix'] > $prices['following']['prix']){
          return true;
        }
      // Il n'existe pas de prix suivant
      } elseif ($prices['this']['prix'] < $prices['previous']['prix']){
          return true;
      }
    // Il n'existe de prix previous
    } else {
      // Il existe un prix suivant
      if (isset($prices['following'])) {
        // Il n'existe pas de prix suivant
        if ($prices['this']['prix'] > $prices['following']['prix'] && $prices['this']['prix'] < $prices['base']['prix']){
          return true;
        }
      } elseif ($prices['this']['prix'] < $prices['base']['prix']) {
        return true;
      }
      return false;
    }
  }

  public function removeNonValidateVente()
  {
    $this -> repository = $this -> em -> getRepository('proGestBundle:Vente');
    $ventes = $this -> repository -> findByIsValidate(false);

    foreach ($ventes as $keyVente => $vente) {
      $this -> repository = $this -> em -> getRepository('proGestBundle:Variation');
      $variations = $this -> repository ->findByVente($vente);
      foreach ($variations as $keyVariation => $variation) {
        $this -> repository = $this -> em -> getRepository('proGestBundle:Article');
        $article = $this -> repository -> find($variation -> getArticle());
        $quantite = ($variation -> getQuantite()) * (-1);
        $article ->setVendu($article->getVendu() - $quantite);
        $article->setStock($article->getStock() + $quantite);
        $this -> em -> persist($article);
        $this -> em -> remove($variation);
        $this -> em -> flush();
      }
      $this -> em -> remove($vente);
      $this -> em -> flush();
    }
  }

  // Enregistre l'état quotidien des ventes au format CSV et html
  public function saveTodayState($boutique, $ventes)
  {
    $lignes[0] = array(
      "Référence",
      "Nbr Articles",
      "Mode de paiement",
      "Total Vente (" . $boutique -> getMarge() . ")",
      "Dont Marge"
    );
    foreach ($ventes as $key => $value) {
      $lignes[$key+1] = array(
        $value -> getId(),
        $value -> getNbrArticles(),
        $value -> getPaymentMode(),
        number_format($value -> getTotalVente(), 2),
        number_format($value -> getTotalVente() * ($boutique -> getMarge() / 100), 2)
      );
    }

    // Date du jour
    $date = date("Y-m-d");
    // Nom du fichier
    $chemin = $_SERVER['DOCUMENT_ROOT']. "/Etats/" . $date . "-Etat.csv";
    // Délimiteur dans le fichier
    $delimiteur = ";";

    // Création du fichier csv (le fichier est vide pour le moment)
    // w+ : consulter http://php.net/manual/fr/function.fopen.php
    $fichier_csv = fopen($chemin, 'w+');

    // Si votre fichier a vocation a être importé dans Excel,
    // vous devez impérativement utiliser la ligne ci-dessous pour corriger
    // les problèmes d'affichage des caractères internationaux (les accents par exemple)
    fprintf($fichier_csv, chr(0xEF).chr(0xBB).chr(0xBF));

    // Boucle foreach sur chaque ligne du tableau
    foreach($lignes as $ligne){
    	// chaque ligne en cours de lecture est insérée dans le fichier
    	// les valeurs présentes dans chaque ligne seront séparées par $delimiteur
    	fputcsv($fichier_csv, $ligne, $delimiteur);
    }

    // fermeture du fichier csv
    fclose($fichier_csv);

    return $chemin = "/Etats/" . $date . "-Etat.csv";
  }

  public function setFournisseurValues($boutique, $fournisseur)
  {
    $articles = $fournisseur -> getArticles();
    $ca = 0;
    $marge = 0;
    $solde = 0;

    foreach ($articles as $key => $value) {
      $ca += $value -> getPrixAchat() * $value -> getVendu();
      $marge += $value -> getPrixAchat() * $boutique -> getMarge() / 100 * $value -> getVendu();
      $solde = $ca - $marge;
    }

    $fournisseur -> setCa($ca)
                 -> setMarge($marge)
                 -> setSolde($solde);
  }
}
