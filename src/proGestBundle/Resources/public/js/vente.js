$(document).ready(function(){

  searchProduct = 0;
  totalVente = 0;
  totalFacture = 0;

  // Gestionnaire d'évènement pour le bouton Wait
  $('#btnWait').on('click', function(){
    console.log($(this).attr('id'));
    // Ouvrir une nouvelle page
    window.open(Routing.generate('vente-new'));
  });

  // Gestionnaire d'évènement sur le bouton VenteSave
  $('#btnVenteSave').on('click', function(){
    $('#totalFacture').removeAttr('disabled');
    var totalFacture = $('#totalFacture').val();
    if (totalFacture != 0 ) {
      var venteId = $('#venteId').attr('data');
      var data = {
        total: totalFacture,
        paymentMode: $('#paymentMode').val()
      };
      $.post(Routing.generate('vente_save', {id: venteId}), data)
       .done(function(response){
         success(response.msg);
         window.location = Routing.generate('vente_show', {id: venteId});
       })
       .fail(function(response){
         alert(response.msg);
       });
     } else {
       alert("Aucun article n\'a été saisi.");
     }
  });

  // Gestionnaire d'évènement pour le bouton Annuler : Annuler la vente
  $('#btnVenteUndo').on('click', function(){
    console.log($(this).attr('id'));
    var venteId = $('#venteId').attr('data');
    // Redirection
    window.location = Routing.generate('vente_annuler', {id: venteId});
  });

  // Gestionnaire d'évènement pour le bouton ProductAdd
  // Permet l'ajout du produit à la vente
  $('#btnProductAdd').on('click', function(){
    console.log($(this).attr('id'));
    totalFacture = $('#totalFacture').val();
    console.log(totalFacture + "€");
    var venteId = $('#venteId').attr('data');
    var totalProduit = parseFloat($('#montantTotal').val());
    var data = {
      articleId: $('#insertForm').attr('data'),
      quantite: $('#quantite').val(),
      prixVente: $('#prixAchat').val(),
      totalFacture: totalFacture,
      totalProduit: totalProduit
    };
    $.post(Routing.generate('vente_article_add', {id: venteId}), data)
      .done(function(response){
        if (response.error == false){
          // Insertion du produit dans le tableau
          $('#detail').append(response.html);
          // Mise à jour du montant total de la vente
          var totalFacture = response.totalFacture;
          $('#totalFacture').val(totalFacture);
          // Réinitialisation du formulaire de recherche de produits
          $('#insertForm input').val('');
          $('#quantite').val('1');
        } else {
          alert(response.msg);
        }
      })
      .fail(function(response){
        alert(response.msg);
      });
  });

  // Gestionnaire d'évènement pour les boutons ProductDel_id
  // Permet la suppression d'un article de la vente
  $('#detail').on('click', 'button[id^=btnProductDel_]', function(){
    console.log("Suppression");
    var venteId = $('#venteId').attr('data');
    var articleId = getId($(this));
    var totalProduit = $('#articlePrixTotal_' + articleId).val();
    totalFacture = $('#totalFacture').val();
    var data = {
      articleId: articleId,
      totalFacture: totalFacture,
      totalProduit: totalProduit
    };
    $.post(Routing.generate('vente_article_del', {id: venteId}), data)
     .success(function(response){
       // Suppression de la ligne de tableau correspondante
       $('#ligne_'+ articleId).remove();
       totalFacture = response.totalFacture;
       // Mettre à jour le montant total
       $('#totalFacture').val(totalFacture);

     })
     .fail(function(response){
       alert(response.msg);
     });
  });

  // Gestionnaire d'évènement pour le bouton FactureShow
  $('#btnFactureShow').on('click', function(){
    console.log($(this).attr('id'));
    var venteId = $('#venteId').attr('data');
    console.log(venteId);
    window.open(Routing.generate('facture', {id: venteId}));
  });

  // Gestionnaire d'évènement sur le champ Reference
  $('#reference').on('change', function(){
    console.log($(this).attr('id'));
    $('#insertForm').removeAttr('data');
    $('#nom').val('');
    $('#quantite').attr('max', '');
    $('#prixAchat').val('');
    $('#montantTotal').val('');
    $('#btnProductAdd').attr('disabled', '');
  });

  // Gestionnaire d'évènement pour la modification du champ quantité
  $('#quantite').on('change', function(){
    var quantite = parseInt($('#quantite').val());
    console.log(quantite);
    if (quantite < parseInt($('#quantite').attr('max')) + 1) {
      // La quantité saisie n'est pas supérieure à la quantité maximale
      if( parseFloat($('#prixAchat').val()) > 0 ){
        if (searchProduct['prixMultiple'].length > 0) {
          // Le produit a plusieurs prix
          // Chercher quel est le prix qui correspond à la quantité saisie
          $.each(searchProduct['prixMultiple'], function(index, value) {
            if (quantite < value.quantite) {
              if (index == 0 ) {
                $('#prixAchat').val(searchProduct['prixAchat']);
                return false;
              } else {
                // La quantité est inférieure à l'une des tranches
                  console.log('Prix inférieur à la quantité');
                  // La quantité ne correspond pas au prix de base
                  $('#prixAchat').val(searchProduct['prixMultiple'][index - 1]['prix']);
                  return false;
              }
            }
            // La quantité est supérieure à toute les tranches
            $('#prixAchat').val(searchProduct['prixMultiple'][index]['prix']);
          });
          // Calcul du montant total en fonction de cette quantité maximale
          var prixUnitaire = parseFloat($('#prixAchat').val());
        } else {
          // Le produit n'a qu'un seul prix
          var prixUnitaire = parseFloat($('#prixAchat').val());
        }
        // Faire calculer à php le montant total
        $.post(Routing.generate('get_MontantTotal', {quantite: quantite, prixUnitaire: prixUnitaire}))
         .done(function(response){
           $('#montantTotal').val(response.montantTotal);
         });
      }
    } else {
      // La quantité saisie est supérieure à celle en stock
      // Affichage d'un message d'erreur
      alert('Le stock pour ce produit est limité à '+ $('#quantite').attr('max') +" unités.");
      // Initialisation de la quantité à la valeur maximale du stock
      $('#quantite').val($('#quantite').attr('max'));
      quantite = parseInt($('#quantite').val());
      var prixUnitaire = parseFloat($('#prixAchat').val());
      // Faire calculer à php le montant total
      $.post(Routing.generate('get_MontantTotal', {quantite: quantite, prixUnitaire: prixUnitaire}))
       .done(function(response){
         $('#montantTotal').val(response.montantTotal);
       });
    }
  })

    // Gestionnaire d'évènement sur le select "paymentMode"
    $('#paymentMode').on('change', function(){
        var paymentMode = $(this).val();
        if (paymentMode == 'numeraire') {
            $('.masque').slideDown(500);
        } else {
            $('.masque').slideUp(500);
        }
        $('#encaisse').val(0);
        $('#monnaie').val(0);
    });

    // Gestionnaire d'évènement pour le rendu monnaie
    $('#encaisse').on('change', function(){
      if ($.isNumeric($(this).val())) {
        var encaisse = parseFloat($(this).val());
        console.log(encaisse);
        var montantTotal = parseFloat($('#totalFacture').val());
        console.log(montantTotal);
        var monnaie = encaisse - montantTotal;
        if (monnaie < 0 ) {
          alert('La somme encaissée n\'est pas suffisante');
          $('#monnaie').val("Erreur");
        } else {
          monnaie = (Math.round(monnaie * 100))/100;
          $('#monnaie').val(monnaie);
        }
      } else {
        alert("Veuillez saisir un nombre.");
      }
    });

    // Gestionnaire d'évènement pour le recalcul du montant total d'une vente
    $('#btnRecalculTotal').on('click', function(){
      // De quelle vente parle t-on ?
      var venteId = $('#venteId').attr('data');
      // Recalcul du montant total
      $.get(Routing.generate('recalcul_total', {id: venteId}))
       .done(function(response){
         // Mise à jour de la valeur du montant total
         $('#totalFacture').val(response.total);
       })
       .fail(function(response){
         alert(response.msg)
       });
    });
});
