<?php

namespace proGestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use proGestBundle\Entity\Article;
use proGestBundle\Entity\PrixMultiple;

use proGestBundle\Form\ArticleType;
use proGestBundle\Form\PrixMultipleType;


class PrixmultipleController extends Controller
{
  public function addAction(Article $article)
  {
    $response = new JsonResponse();

    $em = $this->getDoctrine()->getManager();
    $repository = $em->getRepository('proGestBundle:PrixMultiple');

    $service = $this->get('gestion');

    // La quantité enregistrée est-elle disponible ?
    if($repository->findProduitQuantite($article->getId(), $_POST['quantite'])){
      return $response->setData(array(
        'msg' => "La quantité que vous avez saisie existe déjà."
      ));
    } else {
      $prixmultiple = new Prixmultiple();

      $prixmultiple->setQuantite($_POST['quantite']);
      $prixmultiple->setPrix($_POST['prix']);
      $prixmultiple->setArticle($article);

      $em->persist($prixmultiple);
      $em->flush();

      //$container = $service->isInit($em);
      //$boutique = $service->getApplication();
      //$marge = (1 +($boutique->getMarge()/100));

      $html = $this->renderView('proGestBundle:Prixmultiple:show.html.twig', array(
        'prix' => $prixmultiple,
        //'marge' => $marge
      ));

      $prices = $service->getPrices($article, $prixmultiple);
      // Le prix proposé est-il cohérent avec l'existant ?
      if ($service->isCompatibles($prices)) {
        $msg = "Success";
      } else {
        $msg = "Le prix saisie pour cette tranche n'est pas compatible avec la grille tarifaire existante.";
        $em->remove($prixmultiple);
        $em->flush();
      }

      return $response->setData(array(
        'msg' => $msg,
        'html' => $html,
        'data' => $prices
      ));
    }
  }

  public function getPrixMultipleFormAction()
  {
    $prixmultiple = new PrixMultiple();

    $form = $this->createForm(new PrixMultipleType, $prixmultiple);

    $html = $this->renderView('proGestBundle:Prixmultiple:inc_tarifsForm.html.twig', array(
      'form' => $form->createView()
    ));

    $response = new JsonResponse();

    return $response->setData(array(
      'html' => $html
    ));
  }

  public function delAction(PrixMultiple $prixmultiple)
  {
    $article = $prixmultiple->getArticle();

    $service = $this->get('gestion');
    $prices = $service->getPrices($article, $prixmultiple);

    $em = $this->getDoctrine()->getManager();
    $em->remove($prixmultiple);
    $em->flush();

    $response = new JsonResponse();

    return $response->setData(array(
      'data' => $prices
    ));
  }

  // Fonction temporaire de test
  public function temporaireAction(Article $article)
  {
    $em = $this->getDoctrine()->getManager();
    $repository = $em->getRepository('proGestBundle:PrixMultiple');

    $prixmultiple = $repository->findProduitQuantite($article->getId(), 24);

    $service = $this->get('gestion');
    $prices = $service->getPrices($article, $prixmultiple['0']);
    // Le prix proposé est-il cohérent avec l'existant ?
        var_dump($prices);





    return $this->render('::temp.html.twig');

  }

}
