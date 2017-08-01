<?php

namespace proGestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use proGestBundle\Entity\Boutique;
use proGestBundle\Entity\Vente;

use proGestBundle\Form\BoutiqueType;

class JsonController extends Controller
{
  public function referencesAction(Request $request)
  {
      $em = $this -> get('doctrine.orm.entity_manager');

      if($request->isXmlHttpRequest())
      {
          $term = $request -> get('reference');
          $alreadyAddedProduct = $request -> get('alreadyAddedProduct');

          $array = $em -> getRepository('proGestBundle:Article')
                       -> findArticlesLike($term, $alreadyAddedProduct);

          $repository = $em -> getRepository('proGestBundle:PrixMultiple');
          foreach ($array as $key => $value) {
            $array[$key]['prixMultiple'] = $repository -> findByArticle($value['id']);
          }

          $response = new Response(json_encode($array));
          $response -> headers -> set('Content-Type', 'application/json');
          return $response;
    }
  }

  public function getMontantTotalAction($quantite, $prixUnitaire)
  {
    $montantTotal = $quantite * $prixUnitaire;
    $response = new JsonResponse();
    return $response -> setData(array(
      'montantTotal' => $montantTotal
    ));
  }

  public function recalculTotalVenteAction(Vente $vente)
  {
    // Initialisation des variables
    $msg = "";
    $total = 0;

    $variations = $vente -> getVariations();

    foreach ($variations as $key => $value) {
      // Recalcul du montant total
      $total += ($value -> getQuantite() * -1) * $value -> getPrixVente();
    }

    $response = new JsonResponse();

    return $response -> setData(array(
      'msg' => $msg,
      'total' => $total
    ));
  }

  public function calculateAction(Request $request)
  {
    $operation = $request -> get('operation');
    $montant1 = $request -> get('montant1');
    $montant2 = $request -> get('montant2');

    switch ($operation) {
      case 'addition':
        $result = $montant1 + $montant2;
        break;

      case 'soustraction':
        $result = $montant1 - $montant2;
        break;

      case 'multiplication':
        $result = $montant1 * $montant2;
        break;

      case 'division':
        $result = $montant1 / $montant2;
        break;
    }

    $response = new JsonResponse();

    return $response -> setData(array(
      'result' => $result
    ));
  }
}
