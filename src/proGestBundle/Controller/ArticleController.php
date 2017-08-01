<?php

namespace proGestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use proGestBundle\Entity\Article;
use proGestBundle\Entity\Fournisseur;
use proGestBundle\Entity\Variation;
use proGestBundle\Entity\PrixMultiple;

use proGestBundle\Form\ArticleType;
use proGestBundle\Form\PrixMultipleType;


class ArticleController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('proGestBundle:Article');
        $articles = $repository->findAllOrderASC();

        return $this->render('proGestBundle:Article:articles.html.twig', array(
            'articles' => $articles
        ));
    }

    public function indexPrintAction()
    {
        $em = $this->getDoctrine()->getManager();

        $service = $this->get('gestion');
        $container = $service->isInit();
        $boutique = $service->getApplication();

        $repository = $em->getRepository('proGestBundle:Article');
        $articles = $repository->findAll();

        $ca = 0 ;
        $marge = 0 ;
        $solde = 0 ;

        foreach ($articles as $key => $value) {
          $ca += $value->getPrixAchat() * $value->getVendu();
          $marge += $value->getPrixAchat() * $boutique->getMarge() / 100 * $value->getVendu();
          $solde = $ca - $marge;
        }

        return $this->render('proGestBundle:Article:articlesPrint.html.twig', array(
            'boutique' => $boutique,
            'articles' => $articles,
            'ca' => $ca,
            'marge' => $marge,
            'solde' => $solde
        ));
    }

    public function viewAction(Article $article)
    {
      $em = $this->getDoctrine()->getManager();

      $repository = $em->getRepository('proGestBundle:Article');

      $prixMultiple = $repository->findPricesOrderASC($article->getId());

      $service = $this->get('gestion');
      $container = $service->isInit();
      $boutique = $service->getApplication();

      switch (count($prixMultiple))
      {
        case 0:
          $tableautarif[0]['id'] = 0;
          $tableautarif[0]['de'] = 1;
          $tableautarif[0]['a'] = "...";
          $tableautarif[0]['prix'] = number_format($article->getPrixAchat(), 2);
          break;
        case 1:
          foreach ($prixMultiple as $key => $tarif) {
            $tableautarif[0]['id'] = 0;
            $tableautarif[0]['de'] = 1;
            $tableautarif[0]['a'] = $tarif->getQuantite() - 1;
            $tableautarif[0]['prix'] = number_format($article->getPrixAchat(), 2);
            $tableautarif[1]['id'] = $tarif->getId();
            $tableautarif[1]['de'] = $tarif->getQuantite();
            $tableautarif[1]['a'] = "...";
            $tableautarif[1]['prix'] = number_format($tarif->getPrix(), 2);
          }
          break;
        default:
          foreach ($prixMultiple as $key => $tarif) {
            if ($key == 0) {
              $tableautarif[$key]['id'] = 0;
              $tableautarif[$key]['de'] = 1;
              $tableautarif[$key]['a'] = $tarif->getQuantite() - 1;
              $tableautarif[$key]['prix'] = number_format($article->getPrixAchat(), 2);
              $tableautarif[$key+1]['id'] = $tarif->getid();
              $tableautarif[$key+1]['de'] = $tarif->getQuantite();
              $tableautarif[$key+1]['prix'] = number_format($tarif->getPrix(), 2);
            } else {
              $tableautarif[$key+1]['id'] = $tarif->getid();
              $tableautarif[$key+1]['de'] = $tarif->getQuantite();
              $tableautarif[$key]['a'] = $tarif->getQuantite() -1;
              $tableautarif[$key+1]['prix'] = number_format($tarif->getPrix(), 2);
              $tableautarif[$key+1]['a'] = "...";
            }
          }
      }

      // On récupère la liste des variations actives
      $repository = $em -> getRepository('proGestBundle:Variation');
      $variations = $repository -> findInactiveByArticle($article -> getId());
      foreach ($variations as $key => $value) {
        $article -> removeVariation($value);
      }

      return $this->render('proGestBundle:Article:view.html.twig', array(
          'article' => $article,
          'tableautarif' => $tableautarif
      ));
    }

    public function addAction()
    {
      $em = $this->getDoctrine()->getManager();

      if (isset($_GET['id'])){
        $idFournisseur = $_GET['id'];
      } else {
        $idFournisseur = NULL;
      }
      $article = new Article();
      $form = $this->createForm(new ArticleType, $article);

      $request = $this->get('request');
      if ($request->getMethod() == 'POST') {
          $form->bind($request);
      }

      if ($form->isValid()) {
          $repository = $em->getRepository('proGestBundle:Fournisseur');
          $article->setFournisseur($repository->find($_POST['fournisseur']));
          if (isset($_POST['article']['livre']) && $_POST['article']['livre'] > 0 ){
            $article->setStock($_POST['article']['livre']);
            $variation = new Variation();
            $variation->setQuantite($_POST['article']['livre']);
            $variation->setArticle($article);
            $em->persist($variation);
          }
          $article->setIsActive(true);
          $em->persist($article);
          $em->flush();

          $this->setReference($article);
          if ($_POST['form-add'] === 'true'){
            return $this->redirect($this->generateUrl('article-add', array('id' => $article->getFournisseur()->getId())));
          } else {
            return $this->redirect($this->generateUrl('article-view', array(
                'id' => $article->getId()
            )));
          }
      }

      $em = $this->getDoctrine()->getManager();
      $repository = $em->getRepository('proGestBundle:Fournisseur');
      $fournisseurs = $repository->findAll();

      return $this->render('proGestBundle:Article:add.html.twig', array(
          'form' => $form->createView(),
          'fournisseurs' => $fournisseurs,
          'idFournisseur' => $idFournisseur,
      ));
    }

    public function getEditFormAction(Article $article)
    {
        $gestion = $this->get('gestion');
        if ($gestion -> isInit()){
          $boutique = $gestion -> getapplication();
        } else {
          $boutique = NULL;
        }

        $form = $this->createForm(new ArticleType, $article);

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('proGestBundle:Fournisseur');
        $fournisseurs = $repository->findAll();

        $html =   $this->renderView('proGestBundle:Article:edit.html.twig', array(
            'form' => $form->createView(),
            'article' => $article,
            'fournisseurs' => $fournisseurs,
            'boutique' => $boutique
        ));

        $response = new JsonResponse();

        return $response->setData(array(
          'html' => $html
        ));
    }

    public function editAction(Article $article)
    {
      $em = $this->getDoctrine()->getManager();
      $fournisseur = $em->getRepository('proGestBundle:Fournisseur')->find($_POST['fournisseur']);

      $article->setNom($_POST['nom']);
      $article->setFournisseur($fournisseur);
      $article->setDescriptif($_POST['descriptif']);
      $article->setPrixAchat($_POST['prixAchat']);


      $em->persist($article);
      $em->flush();

      $html =   $this->renderView('proGestBundle:Article:inc_descriptif.html.twig', array(
          'article' => $article,
      ));

      $response = new JsonResponse();

      return $response->setData(array(
        'html' => $html,
        'titre' => $article->getNom()
      ));
    }

    public function delAction(Article $article)
    {
        $fournisseur = $article->getFournisseur();

        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        return $this->redirect($this->generateUrl('fournisseur-view', array(
          'id' => $fournisseur->getId()
        )));
    }

    public function livraisonAction(Article $article)
    {
      $variation = new Variation();
      $variation->setQuantite($_POST['quantite']);
      $variation->setArticle($article);
      $em = $this->getDoctrine()->getManager();
      $em->persist($variation);
      $article->setLivre($article->getLivre() + $variation->getQuantite());
      $article->setStock($article->getStock() + $variation->getQuantite());
      $em->flush();

      $stock = $article->getStock();
      $msg = "";

      $response = new JsonResponse();

      return $response->setData(array(
        'msg' => $msg,
        'stock' => $stock
      ));
    }

    public function editLivraisonAction(Article $article)
    {
      $em = $this -> getDoctrine() -> getManager();
      $repository = $em -> getRepository('proGestBundle:Variation');
      $variations = $repository -> findByArticle($article);
      var_dump($variations);
      $nbrVariations = count($variations);
      if ( $nbrVariations == 0 ) {
        if ($article -> getStock() == 0 ) {
          $msg = "Il ne peut y avoir de stock négatif pour un produit";
        }
        $msg = "Aucune variation de stock n'a été trouvée !";
      } else if ($nbrVariations == 1) {
        // $quantite = $variations -> getQuantite() - $_POST['quantite'];
        // $variations -> setQuantite($quantite);
        // $em -> persist($variations);
        // $article -> setLivre($quantite);
        // $article -> setStock( $article ->getStock() - $_POST['quantite']);
        // $em -> persist($article);
        // $em -> flush();
        // $msg = "Le stock a bien été mis à jour";
      }

      $stock = "";
      $msg = "";
      $response = new JsonResponse();

      return $response->setData(array(
        'msg' => $msg,
        'stock' => $stock
      ));
    }

    public function articleTagsAction(Article $article = null, $tagStart, $quantite )
    {
       $tagStart = $tagStart - 1;

      return $this -> render('proGestBundle:Fournisseur:tags.html.twig', array(
        'article' => $article,
        'tagStart' => $tagStart,
        'quantite' => $quantite
      ));
    }

    private function setReference(Article $article)
    {
      $em = $this->getDoctrine()->getManager();

      $nomFournisseur = substr($article->getFournisseur()->getNom(), 0, 3);
      $idArticle = $article->getId();

      if ($idArticle > 999 ) {
        $idArticle = $idArticle;
      } elseif ($idArticle > 99){
        $idArticle = "0".$idArticle;
      } elseif ($idArticle > 9){
        $idArticle = "00".$idArticle;
      } elseif ($idArticle > 0){
        $idArticle = "000".$idArticle;
      }
      $service =$this->get('gestion');
      $container = $service->isInit($em);
      $boutique = $service->getApplication();
      $prixVente = $article->getPrixAchat();

      $article->setReference($nomFournisseur."-".$idArticle."-".number_format($prixVente, 2)." €");

      $em->persist($article);
      $em->flush();
    }
}
