<?php

namespace proGestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use proGestBundle\Entity\Fournisseur;
use proGestBundle\Entity\Article;
//use proGestBundle\Entity\VariationStock;

use proGestBundle\Form\FournisseurType;

use Symfony\Component\HttpFoundation\JsonResponse;

class FournisseurController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('proGestBundle:Fournisseur');
        $fournisseurs = $repository->findAllOrderASC();

        return $this->render('proGestBundle:Fournisseur:fournisseurs.html.twig', array(
            'fournisseurs' => $fournisseurs,
        ));
    }

    public function indexPrintAction()
    {
      $em = $this->getDoctrine()->getManager();

      $service = $this->get('gestion');
      $container = $service->isInit();
      $boutique = $service->getApplication();

      $repository = $em->getRepository('proGestBundle:Fournisseur');
      $fournisseurs = $repository->findAll();

      return $this->render('proGestBundle:Fournisseur:fournisseursPrint.html.twig', array(
          'fournisseurs' => $fournisseurs,
          'boutique' => $boutique
      ));
    }

    public function viewAction(Fournisseur $fournisseur)
    {
        $gestion = $this->get('gestion');
        if ($gestion -> isInit()){
          $boutique = $gestion -> getapplication();
        } else {
          $boutique = NULL;
        }

        $gestion -> setFournisseurValues($boutique, $fournisseur);

        return $this->render('proGestBundle:Fournisseur:view.html.twig', array(
            'boutique' => $boutique,
            'fournisseur' => $fournisseur
        ));
    }

    public function etatPrintAction(Fournisseur $fournisseur)
    {
      $gestion = $this->get('gestion');
      $container = $gestion -> isInit();
      $boutique = $gestion -> getApplication();

      $gestion -> setFournisseurValues($boutique, $fournisseur);

        return $this->render('proGestBundle:Fournisseur:etatPrint.html.twig', array(
            'fournisseur' => $fournisseur,
            'boutique' => $boutique
        ));
    }

    public function addAction()
    {
        $fournisseur = new Fournisseur();
        $form = $this->createForm(new FournisseurType, $fournisseur);

        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
        }

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $this->formatFournisseur($fournisseur);

            $em->persist($fournisseur);
            $em->flush();

            return $this->redirect($this->generateUrl('fournisseur-view', array(
                'id' => $fournisseur->getId(),
            )));
        }
        return $this->render('proGestBundle:Fournisseur:add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function editAction(Fournisseur $fournisseur)
    {
        $form = $this->createForm(new FournisseurType, $fournisseur);
        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
        }

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $this->formatFournisseur($fournisseur);

            $em->flush();

            return $this->redirect($this->generateUrl('fournisseur-view', array(
                'id' => $fournisseur->getId(),
            )));
        }

        return $this->render('proGestBundle:Fournisseur:edit.html.twig', array(
            'fournisseur' => $fournisseur,
            'form' => $form->createView()
        ));
    }

    public function delAction(Fournisseur $fournisseur)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($fournisseur);
        $em->flush();

        return $this->redirect($this->generateUrl('fournisseurs'));
    }

    public function etatAction(Fournisseur $fournisseur)
    {
      $gestion = $this->get('gestion');
      $container = $gestion -> isInit();
      $boutique = $gestion -> getApplication();

      return $this->render('proGestBundle:Fournisseur:etat.html.twig', array(
        'fournisseur' => $fournisseur,
        'boutique' => $boutique,
      ));
    }

    public function soldAction(Fournisseur $fournisseur)
    {
      $em = $this -> getDoctrine() -> getManager ();
      $gestion = $this->get('gestion');
      $container = $gestion -> isInit();
      $boutique = $gestion -> getApplication();

      $gestion -> setFournisseurValues($boutique, $fournisseur);

      $articles = $fournisseur -> getArticles();

      $fournisseur -> setCheque($this -> calculCheque($fournisseur, $boutique));
      $fournisseur -> setIsSold(true);
      $em -> persist($fournisseur);
      $em -> flush();

    // On rend la vue détaillée
      $vue = $this -> renderView('proGestBundle:Fournisseur:sold.html.twig', array(
              'boutique' => $boutique,
              'fournisseur' => $fournisseur,
              'articles' => $articles
            ));

    // On rend la lettre
      $lettre = $this -> renderView('proGestBundle:Fournisseur:soldLetter.html.twig', array(
        'fournisseur' => $fournisseur,
        'boutique' => $boutique,
      ));

    // Sauvegarder la vue dans un fichier
      $file = $_SERVER['DOCUMENT_ROOT']. "/Etats/Soldes/" . $fournisseur -> getNom() . ".html";
      $fp = fopen($file, 'w');
      fwrite($fp, $vue);
      fclose($fp);

    // Sauvegarder la lettre dans un fichier
      $file = $_SERVER['DOCUMENT_ROOT']. "/Etats/Soldes/" . $fournisseur -> getNom() . "-courrier.html";
      $fp = fopen($file, 'w');
      fwrite($fp, $lettre);
      fclose($fp);

    // Opérations de solde
      $fournisseurSold = $fournisseur;
      $articlesSold = $articles;

      // Pour chaque article
      $chemin = $_SERVER['DOCUMENT_ROOT']. "/Etats/Soldes/" . $fournisseur -> getNom() . "-solde.csv";
      // Délimiteur dans le fichier
      $delimiteur = ";";

      // Création du fichier csv (le fichier est vide pour le moment)
      // w+ : consulter http://php.net/manual/fr/function.fopen.php
      $fichier_csv = fopen($chemin, 'w+');
      // Si votre fichier a vocation a être importé dans Excel,
      // vous devez impérativement utiliser la ligne ci-dessous pour corriger
      // les problèmes d'affichage des caractères internationaux (les accents par exemple)
      fprintf($fichier_csv, chr(0xEF).chr(0xBB).chr(0xBF));

      foreach ($articlesSold as $key => $value) {
        $variations = $em -> getRepository('proGestBundle:Variation') -> findByArticle($value);
        // exporter le detail au format csv
        foreach ($variations as $keyVariation => $variation) {
          if ( $variation -> getVente() ) {
            $vente = $variation -> getVente() -> getId();
          } else {
            $vente = "Livraison";
          }
          // chaque ligne en cours de lecture est insérée dans le fichier
        	// les valeurs présentes dans chaque ligne seront séparées par $delimiteur
          $ligne = array(
            $value -> getId(),
            $value -> getNom(),
            $variation -> getId(),
            $vente,
            $variation -> getDate() -> format('Y m d'),
            $variation -> getQuantite(),
            $variation -> getPrixVente()
          );
        	fputcsv($fichier_csv, $ligne, $delimiteur);

          $variation -> setIsActive(false);
          $em -> remove($variation);
          $em -> flush();
        }
        $value -> setLivre(0);
        $value -> setVendu(0);
        $value -> setStock(0);
        $em -> persist($value);
        $em -> flush();
      }

      // fermeture du fichier csv
      fclose($fichier_csv);

      return new JsonResponse();
    }

    public function RenderLetterAction(Fournisseur $fournisseur)
    {
      $gestion = $this->get('gestion');
      $container = $gestion -> isInit();
      $boutique = $gestion -> getApplication();

      return $this -> render('proGestBundle:Fournisseur:soldLetter.html.twig', array(
        'fournisseur' => $fournisseur,
        'boutique' => $boutique,
      ));
    }

    /*
     * Fonctions controlant l'impression des étiquettes
     */

    public function fournisseursTagsAction($tagStart)
    {
      $tagStart = $tagStart - 1;
      $em = $this -> getDoctrine() -> getManager();
      $repository = $em -> getRepository('proGestBundle:Fournisseur');
      $fournisseurs = $repository -> findAll();

      return $this -> render('proGestBundle:Fournisseur:tags.html.twig', array(
        'fournisseurs' => $fournisseurs,
        'tagStart' => $tagStart
      ));
    }

    public function fournisseurTagsAction(Fournisseur $fournisseur = null, $tagStart )
    {
      if ($fournisseur != null ) {
        $articles = $fournisseur -> getArticles();
      }

       $tagStart = $tagStart - 1;

      return $this -> render('proGestBundle:Fournisseur:tags.html.twig', array(
        'articles' => $articles,
        'tagStart' => $tagStart
      ));
    }

    private function formatFournisseur(Fournisseur $fournisseur)
    {
      $fournisseur->setNom(strtoupper($fournisseur->getNom()));
      $fournisseur->setPrenom(ucfirst($fournisseur->getPrenom()));

    }

    private function calculCheque(Fournisseur $fournisseur, $boutique)
    {
      // Calcul du Chiffre d'Affaire réalisé par ce fournisseur
      $articles = $fournisseur -> getArticles();
      $ca = 0;
      foreach ($articles as $key => $value) {
        $ca += $value -> getPrixAchat() * $value -> getVendu();
      }

      // Calcul du montant du chèque à établir
      $cheque = $ca - ($ca * ($boutique -> getMarge() / 100));

      return $cheque;
    }
}
