<?php

namespace proGestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use proGestBundle\Entity\Boutique;
use proGestBundle\Entity\Variation;
use proGestBundle\Entity\Vente;

use proGestBundle\Form\BoutiqueType;

class DefaultController extends Controller
{
    public function indexAction()
    {
      $gestion = $this->get('gestion');
      if ($gestion->isInit()){
        $boutique = $gestion->getapplication();
      } else {
        $boutique = NULL;
      }

      return $this->render('proGestBundle:Default:index.html.twig', array(
        'boutique' => $boutique
      ));
    }

    // Fonction qui contrôle la navigation
    public function navigationAction(){

      $em = $this -> getDoctrine() -> getManager();
      /*$repository = $em -> getRepository('proGestBundle:Boutique');
      $boutiques = $repository -> findAll();*/

      $gestion = $this->get('gestion');
      if ($gestion -> isInit()){
        $boutique = $gestion -> getapplication();
      } else {
        $boutique = NULL;
      }

      // Retourner l'ensemble des ventes de la journée
      // Date du jour
      $date = date("Y-m-d");
      $repository = $em -> getRepository('proGestBundle:Vente');
      if (empty($ventes = $repository -> findByDateLike($date))) {
        $ventes = false;
      } else {
        $ventes = true;
      }

      return $this->render('::navigation.html.twig', array(
        'service' => $gestion,
        'boutique' => $boutique,
        'vente' => $ventes
      ));
    }

    public function addBoutiqueAction(Request $request)
    {
      $boutique = new Boutique();

      $form = $this->createForm(new BoutiqueType, $boutique);
      $form->handleRequest($request);

      if ($form->isSubmitted()){
        if (empty($_POST['boutique']['nom']) ||
            empty($_POST['boutique']['adresse']) ||
            empty($_POST['boutique']['codepostal']) ||
            empty($_POST['boutique']['ville']) ||
            empty($_POST['boutique']['marge']))
        {
          $this->get('session')->getFlashBag()->add('info', 'Certains champs obligatoires du formulaire n\'ont pas été renseignés correctement.');
        } else {
          $em = $this->getDoctrine()->getManager();
          $em->persist($boutique);
          $em->flush();
          return $this->redirectToRoute('homepage');
        }
      }
      return $this->render('proGestBundle:Default:addBoutique.html.twig', array(
        'boutique' => $boutique,
        'form' => $form->createView(),
      ));
    }

    public function editBoutiqueAction(Request $request, Boutique $boutique)
    {
        $form = $this->createForm('proGestBundle\Form\BoutiqueType', $boutique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($boutique);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Les modifications ont correctement été enregistrées.');

            return $this->redirectToRoute('homepage');
        } else {

          return $this->render('proGestBundle:Default:addBoutique.html.twig', array(
              'boutique' => $boutique,
              'form' => $form->createView(),
          ));
        }
    }

    public function initBoutiqueAction()
    {
      $em = $this->getDoctrine()->getManager();
      $service = $this->get('gestion');
      $container = $service->isInit();
      $boutique = $service->getApplication();

      $repository = $em->getrepository('proGestBundle:Fournisseur');
      $fournisseurs = $repository->findAll();
      foreach ($fournisseurs as $fournisseur) {
        $fournisseur -> setIsSold(false);
        $em -> persist($fournisseur);
        $em -> flush();
      }

      $html = $this->renderView('proGestBundle:Default:initBoutique.html.twig', array(
        'boutique' => $boutique,
        'fournisseurs' => $fournisseurs
      ));

      // Ecriture du fichier
      $file = "init.proGest.html";
      $fp = fopen($file, 'w');
      fwrite($fp, $html);
      fclose($fp);

      $boutique->setIsActive(true);
      $em->persist($boutique);
      $em->flush();

      $response = new Jsonresponse();

      return $response->setData(array(
        'html' => $html,
      ));
    }

    public function editMargeAction()
    {
        $gestion = $this->get('gestion');

        $html = null;

        if ($gestion->isInit()){
          $boutique = $gestion->getapplication();
          $html = $this -> renderView('proGestBundle:Default:edit_marge.html.twig', array(
             'boutique' => $boutique
          ));
        }

        $response = new JsonResponse();

        return $response -> setData(array(
            'html' => $html
        ));

    }

    public function setMargeAction()
    {
        $em = $this -> getDoctrine() -> getManager();
        $gestion = $this->get('gestion');

        $msg = null;

        if ($gestion->isInit()){
          $boutique = $gestion->getapplication();
          $boutique -> setMarge($_POST['marge']);
          $em -> persist($boutique);
          $em -> flush();
          $msg = true;
        }

        $response = new JsonResponse();

        return $response -> setData(array(
            'msg' => $msg
        ));
    }

    public function etatQuotidienAction()
    {
      $gestion = $this->get('gestion');

      if ($gestion->isInit()){
        $boutique = $gestion->getapplication();
      } else {
        $boutique = NULL;
      }
      // Suppression des ventes non valides
      $gestion -> removeNonValidateVente();
      // Date du jour
      $date = date("Y-m-d");

      // Retourner l'ensemble des ventes de la journée
      $em = $this -> getDoctrine() -> getManager();
      $repository = $em -> getRepository('proGestBundle:Vente');

      $ventes = $repository -> findByDateLike($date);

      $modePaiement = array('numeraire' => 0, 'cheque' => 0, 'carte' => 0, 'adherent' => 0);
      foreach ($ventes as $key => $value) {
        switch ($value -> getPaymentMode()){
          case 'numeraire':
            $modePaiement['numeraire'] += $value -> getTotalVente();
            break;
          case 'cheque':
            $modePaiement['cheque'] += $value -> getTotalVente();
            break;
          case 'carte':
            $modePaiement['carte'] += $value -> getTotalVente();
            break;
          case 'adherent':
            $modePaiement['adherent'] += $value -> getTotalVente();
            break;
        }
      }

      $csv = $gestion -> saveTodayState($boutique, $ventes);

      return $this -> render('proGestBundle:Vente:etatQuotidien.html.twig', array(
        'boutique' => $boutique,
        'ventes' => $ventes,
        'csv' => $csv,
        'modePaiement' => $modePaiement
      ));
    }

    public function bddBackupAction()
    {
      $host = "localhost";
      $user = "root";
      $pass = "";
      $db = "progest-dev";
      $rep = $_SERVER['DOCUMENT_ROOT'] . "/DB_Backup/";
      $backup = $rep.$db."_".date('d-m-Y').".sql";

      $command = "C:\wamp\bin\mysql\mysql5.6.17\bin\mysqldump --host=$host --user=$user --password=$pass $db > $backup";

      system($command);

      return $this -> redirect($this -> generateUrl('homepage'));
    }

    public function creditsAction() {
      return $this -> render('proGestBundle:Default:credits.html.twig');
    }

    public function cloreSaisonAction(Request $request) {
      // Cloture de la saison
      $em = $this -> getDoctrine() -> getManager();
      $service = $this->get('gestion');
      $container = $service->isInit();
      $boutique = $service->getApplication();

      switch ($request -> get('step')) {
        case 1:
          // Solder tous les articles
          $articles = $em -> getRepository('proGestBundle:Article') -> findAll();
          foreach ($articles as $article) {
            $article -> setLivre(0);
            $article -> setVendu(0);
            $article -> setStock(0);
            $em -> persist($article);
          }
          break;
        case 2:
          // Supprimer toutes les variations
          $variations = $em -> getRepository('proGestBundle:Variation') -> findAll();
          foreach ($variations as $variation) {
            $em -> remove($variation);
          }
          break;
        case 3:
          // Solder toutes les ventes
          $ventes = $em ->getRepository('proGestBundle:Vente') -> findAll();
          foreach ($ventes as $vente) {
            $em -> remove($vente);
          }
          // Réinitialiser la boutique
          $boutique -> setIsActive(false);
          $em -> persist($boutique);
          break;
      }

      $em -> flush();

      $response = new JsonResponse();

      return $response -> setData(array(

      ));
    }

    public function correctifAction(Request $request)
    {
      $em = $this -> get('doctrine.orm.entity_manager');
      $repository = $em -> getRepository('proGestBundle:Article');

      $msg = "Le correctif a été appliqué.";

      // Traitement des articles
      $articles = $repository -> findAll();
      foreach ($articles as $key => $article) {
        // Traitement des variations
        $variations = $article -> getVariations();
        if ( count($variations) != 0 ) {
          $quantiteVendue = 0;
          foreach ($variations as $key => $variation) {
            if ( $variation -> getPrixVente() > 0 && $variation -> getQuantite() > 0) {
              // La quantité est rendu négative
              $variation -> setQuantite($variation -> getQuantite() * (-1));
              $quantiteVendue += $variation -> getQuantite();
              $em -> persist($variation);
            }
          }
          $article -> setVendu($quantiteVendue * -1);
          $em -> persist($article);
        }
      }

      $em -> flush();

      $response = new Response($msg);
      return $response;
    }
}
