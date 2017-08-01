<?php

namespace proGestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use proGestBundle\Entity\Article;
use proGestBundle\Entity\Variation;
use proGestBundle\Entity\Vente;

class VenteController extends Controller
{
    public function indexAction()
    {
      $gestion = $this->get('gestion');
      if ($gestion->isInit()){
        $boutique = $gestion->getapplication();
      } else {
        $boutique = NULL;
      }

      $ca = 0;
      $arts = 0;
      $em = $this->getDoctrine()->getManager();
      $repository = $em->getRepository('proGestBundle:Vente');
      $ventes = $repository->findAll();

      foreach ($ventes as $key => $value) {
        $ca += $value->getTotalVente();
        $arts += $value->getNbrArticles();
      }

      return $this->render('proGestBundle:Vente:index.html.twig', array(
        'boutique' => $boutique,
        'ventes' => $ventes,
        'ca' => $ca,
        'arts' => $arts
      ));
    }

    public function removeNonValidVenteAction()
    {
      $gestion = $this->get('gestion');

      // Suppression des ventes non valides
      $gestion -> removeNonValidateVente();

      return $this->redirect($this-> generateUrl('ventes'));
    }

    public function showAction(Vente $vente)
    {
      $gestion = $this->get('gestion');
      if ($gestion->isInit()){
        $boutique = $gestion->getapplication();
      } else {
        $boutique = NULL;
      }

      return $this->render('proGestBundle:Vente:show.html.twig', array(
        'vente' => $vente,
        'boutique' => $boutique
      ));
    }

    public function factureAction(Vente $vente)
    {
      $gestion = $this->get('gestion');
      if ($gestion->isInit()){
        $boutique = $gestion->getapplication();
      } else {
        $boutique = NULL;
      }

      return $this->render('proGestBundle:Vente:facture.html.twig', array(
        'vente' => $vente,
        'boutique' => $boutique
      ));
    }

    public function newAction()
    {
      $gestion = $this->get('gestion');
      if ($gestion->isInit()){
        $boutique = $gestion->getapplication();
      } else {
        $boutique = NULL;
      }

      // Création de now pour différencier LocalStorage
      $now = new \DateTime();
      $now = md5($now -> format('d-m-Y H-m-s'));

      return $this->render('proGestBundle:Vente:new.html.twig', array(
        'boutique' => $boutique,
        'now' => $now
      ));
    }

    // Action permettant l'ajout d'un produit à la vente
    // Recalcul du stock et quantité vendue
    // Création d'une variation de stock
    public function articleAddAction(Vente $vente)
    {
      $error = false;
      $msg = "";

      $em = $this->getDoctrine()->getManager();
      $repository = $em->getRepository('proGestBundle:Article');

      // Opérations sur l'article ajouté à la vente et hydratation des données
      $article = $repository->find($_POST['articleId']);
      $article->setVendu($article->getVendu() + $_POST['quantite']);
      $article->setStock($article->getStock() - $_POST['quantite']);
      $em->persist($article);

      // Création d'une variation et hydratation des valeurs spécifiques
      $variation = new Variation();
      $variation -> setArticle($article);
      $variation -> setQuantite(-($_POST['quantite']));
      $variation -> setPrixVente($_POST['prixVente']);
      $variation -> setVente($vente);
      $em->persist($variation);

      // Enregistrement des données sur la base de données
      try {
        $em -> flush();
      } catch (Exception $e) {
        $error = true;
        $msg = "Il y a eu un problème lors de l'enregistrement des données.";
      }

      $html = $this->renderView('proGestBundle:Vente:inc_venteInsertForm.html.twig', array(
        'article' => $article,
        'quantite' => $_POST['quantite'],
        'prixVente' => $_POST['prixVente']
      ));

      // Calcul du nouveau montant total de la vente
      $totalFacture = $_POST['totalFacture'] + $_POST['totalProduit'];

      $response = new JsonResponse();

      return $response->setData(array(
        'html' => $html,
        'totalFacture' => $totalFacture,
        'error' => $error,
        'msg' => $msg
      ));
    }

    // Suppression d'un article d'une vente
    public function articleDelAction(Vente $vente)
    {
      $em = $this -> getDoctrine() -> getManager();
      $repository = $em -> getRepository('proGestBundle:Article');
      $article = $repository->find($_POST['articleId']);

      $repository = $em -> getRepository('proGestBundle:Variation');

      // Recherche de la variation
      $variations = $repository -> findByArticleVente($article -> getId(), $vente -> getId());
      foreach ($variations as $key => $value) {
        $quantite = ($value -> getQuantite()) * (-1);
        $article ->setVendu($article->getVendu() - $quantite);
        $article->setStock($article->getStock() + $quantite);
        $em -> persist($article);
        // Suppression de la variation
        $em -> remove($value);
        $em -> flush();
      }

      $msg = "";
      // Recalcul du total de la facture
      $totalFacture = $_POST['totalFacture'] - $_POST['totalProduit'];

      $response = new JsonResponse();

      return $response->setData(array(
        'msg' => $msg,
        'totalFacture' => $totalFacture
      ));
    }


    // Enregistrement de la vente dans la base de données
    public function saveAction(Vente $vente)
    {
      $ca = 0;
      $em = $this->getDoctrine()->getManager();
      // Hydration de la vente pour confirmer qu'elle est validée
      $vente -> setIsValidate(true);
      // Hydratation du moyen de paiement
      $vente -> setPaymentMode($_POST['paymentMode']);
      // Quelles sont les variations de cette vente
      $variations =  $vente -> getVariations();
      // Hydratation du nombre d'articles vendus
      $vente -> setNbrArticles(count($variations));
      // Calcul du chiffre d'affaire de cette vente
      foreach ($variations as $key => $value) {
        $ca += $value -> getPrixVente() * ($value->getQuantite() *-1);
      }
      $vente -> setTotalVente($ca);

      $em -> persist($vente);

      if ($em->flush()) {
        $msg = "Il y a eu un problème lors de la sauvegarde des données.";
      } else {
        $msg = "L'enregistrement s'est déroulé correctement.";
      }

      $response = new JsonResponse();

      return $response->setData(array(
        'msg' => $msg
      ));
    }

    // Annulation d'une vente
    public function annulerAction(Vente $vente)
    {
      // Les services
      $em = $this -> getDoctrine() -> getManager();

      // Chercher les variations
      $repository = $em -> getRepository('proGestBundle:Variation');
      $variations = $repository -> findByVente($vente);

      // Mise à jour des articles et suppression des variations
      $repository = $em -> getRepository('proGestBundle:Article');
      foreach ($variations as $key => $value) {
        $article = $repository -> find($value -> getArticle());
        $quantite = ($value -> getQuantite()) * (-1);
        $article ->setVendu($article->getVendu() - $quantite);
        $article->setStock($article->getStock() + $quantite);
        $em -> persist($article);
        // Suppression de la variation
        $em -> remove($value);
        //  Enregistrement en BDD
        $em -> flush();
      }

      $em -> remove($vente);
      $em -> flush();


      return $this->redirect($this-> generateUrl('ventes'));
    }

    public function addVenteAction(Request $request)
    {
      $em = $this -> get('doctrine.orm.entity_manager');

      $newVente = $request -> get('vente');
      $products = $request -> get('products');

      // On crée la vente
      $vente = new Vente();
      // Hydratation de la vente
      $vente -> setNbrArticles(count($products));
      $vente -> setTotalVente($newVente['montantTotal']);
      $vente -> setPaymentMode($newVente['paymentMode']);
      $vente -> setIsValidate(true);

      $em -> persist($vente);
      $em -> flush();

      $repository = $em -> getRepository('proGestBundle:Article');

      foreach ($products as $key => $product) {
        $article = $repository -> find($product['id']);
        $article -> setVendu($product['quantite']);
        $article -> setStock($product['stock']);
        $em -> persist($article);

        $variation = new Variation();
        $variation -> setArticle($article);
        $variation -> setVente($vente);
        $variation -> setQuantite($product['quantite']);
        $variation -> setPrixVente($product['prixUnitaire']);
        $variation -> setIsactive(true);
        $em -> persist($variation);
      }
      $em -> flush();

      $response = new JsonResponse();

      return $response -> setData(array(
        'vente' => array(
          'id' => $vente -> getId()
        )
      ));
    }
}
