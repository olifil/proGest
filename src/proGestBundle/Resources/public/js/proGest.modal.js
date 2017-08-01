$(document).ready(function()
{

  $('#modal').on('click', '#modalConfirm', function(){
    console.log($(this).attr('id'));
    var action = $('#modalConfirm').attr('data');
    console.log(action);
    switch (action) {
      case "articleDel":
        removeArticle();
        break;
      case "articleEdit":
        editArticle();
        break;
      case "initBoutique":
        initBoutique();
        break;
      case "marge_edit":
          margeEdit();
          break;
      case "cloreSaison":
        cloreSaison();
        break;
      case "PrixMultipleAdd":
        PrixMultipleAdd();
        break;
      case "PrixMultipleDel":
        PrixMultipleDel();
        break;
      case "SoldFournisseur":
        SoldFournisseur();
        break;
      default :
        $('#modal').modal('hide');
    }
  });

  function editArticle()
  {
    var idArticle = $('#articleId').attr('data');
    var data = {
      nom: $('#article_nom').val(),
      fournisseur: $('#fournisseur').val(),
      descriptif: CKEDITOR.instances['article_descriptif'].getData(),
      prixAchat: $('#article_prixAchat').val()
    };
    $.post(Routing.generate('article-edit', {id: idArticle}), data)
    .done(function(response){
      $('#titre-page').html(response.titre);
      $('.tab-content').children('#descriptif').html(response.html);
    })
    .fail(function(response){
      alert(response.msg);
    });
    $('#modal').modal('hide');
  }

  function initBoutique()
  {
    $.get(Routing.generate('boutique-init'))
    .done(function(response){
      window.location = (Routing.generate('homepage'));
      $('#btnBoutiqueInit').remove();
      $('#boutiqueState').append('<button id="btnBoutiqueInitFileView" type="button" class="btn btn-info navbar-btn" title="Afficher le fichier d\'initialisation"><span class="fa fa-file-o"></span></button>');
      success();
    })
    .fail(function(response){
      alert(response.msg);
    });
    $('#modal').modal('hide');
  }

  function margeEdit()
  {
      data = {
          marge: $('#inputMarge').val(),
      };
      $.post(Routing.generate('boutique_marge_set'), data)
       .done(function(response){
           if (response.msg == true){
               $('.modal-title').html();
               $('.modal-body').html();
               $('#modalConfirm').attr('data', '');
               $('.marge').html(data.marge);
               $('#modal').modal('hide');
           }
       });
  };

  function cloreSaison()
  {
      console.log("Cloture");
      $.when($.ajax({
          url: Routing.generate('saison-clore'),
          type: 'POST',
          async: false,
          data: {
            'step': 1
          },
          complete: function(response) {
            console.log('Step 1 - Achevé');
          }
        })
      ).then(cloreSaison2());

  }

  function cloreSaison2()
  {
    $.when($.ajax({
      url: Routing.generate('saison-clore'),
      type: 'POST',
      async: false,
      data: {
        'step': 2
      },
      complete: function(response) {
        console.log('Step 2 - Achevé');
      }
    })
  ).then(cloreSaison3())
  }

  function cloreSaison3()
  {
    $.ajax({
      url: Routing.generate('saison-clore'),
      type: 'POST',
      async: false,
      data: {
        'step': 3
      },
      complete: function(response) {
        console.log('Step 3 - Achevé');
        window.location.href = Routing.generate('homepage');
      }
    });
  }

  function PrixMultipleAdd() {
    var idArticle = $('#articleId').attr('data');
    console.log(idArticle);
    var quantite = parseInt($('#prix_multiple_quantite').val());
    var prix = parseFloat($('#prix_multiple_prix').val());
    if (!isNaN(quantite) && !isNaN(prix)){
      var data = {
        quantite: quantite,
        prix: prix
      };
      console.log(data);
      $.post(Routing.generate('prixmultiple_add', {id: idArticle}), data)
      .done(function(response){
        if (isset(response.msg) && response.msg != "Success"){
          alert(response.msg);
          return;
        }
        console.log(response.data);
        // Y a t-il une ligne précédente
        if (isset(response.data.previous)) {
          if (isset(response.data.following)) {
            console.log('Un précédent et un suivant');
            $('#a_'+response.data.previous.id).html(response.data.this.quantite - 1);
            $('#line_'+response.data.previous.id).after(response.html);
            $('#a_'+response.data.this.id).html(response.data.following.quantite - 1);
          } else {
            console.log('Un précédent mais pas de suivant');
            $('#a_'+response.data.previous.id).html(response.data.this.quantite - 1);
            $('#line_'+response.data.previous.id).after(response.html);
          }
        } else if (isset(response.data.following)) {
          console.log('Pas de précédent mais un suivant');
          $('#a_0').html(response.data.this.quantite - 1);
          $('#first-line').after(response.html);
          $('#a_'+response.data.this.id).html(response.data.following.quantite - 1);
        } else {
          console.log('Pas de précédent et pas de suivant');
          $('#a_0').html(response.data.this.quantite - 1);
          $('#first-line').after(response.html);
        }
        success();
        $('#modal').modal('hide');
      })
      .fail(function(response){
        alert(response.msg);
      });
    } else {
      alert('Il y a un problème dans les valeurs que vous avez saisies.');
    }
  }

  function PrixMultipleDel()
  {
    var idPrixmultiple = $('.modal-title').attr('data');
    $.post(Routing.generate('prixmultiple_del', {id: idPrixmultiple}))
    .done(function(response){
      console.log(response.data);
      // Y a t-il une ligne précédente
      if (isset(response.data.previous)) {
        if (isset(response.data.following)) {
          console.log('Un précédent et un suivant');
          $('#a_'+response.data.previous.id).html(response.data.following.quantite - 1);
          $('#line_'+idPrixmultiple).remove();
        } else {
          console.log('Un précédent mais pas de suivant');
          $('#a_'+response.data.previous.id).html('...');
          $('#line_'+idPrixmultiple).remove();
        }
      } else if (isset(response.data.following)) {
        console.log('Pas de précédent mais un suivant');
        $('#a_0').html(response.data.following.quantite - 1);
        $('#first-line').next().remove();
      } else {
        console.log('Pas de précédent et pas de suivant');
        $('#a_0').html('...');
        $('#first-line').next().remove();
      }
      success();
      // sélectionner la colonne "à" du dernier tr de tarifs-body
    })
    .fail(function(response){

    });
    $('#modal').modal('hide');
  }

  function removeArticle()
  {
    var idArticle = $('#articleId').attr('data');
    window.location = (Routing.generate('article-del', {id: idArticle}));
  }

  function SoldFournisseur()
  {
    var fournisseurId = $('#fournisseurId').attr('data');
    console.log(fournisseurId);
    $('#modal').modal('hide');
    $('#btnSold').remove();
    $.get(Routing.generate('fournisseur_sold',{id:fournisseurId}))
     .done(function(){
      window.location.href = Routing.generate('fournisseur-view', {id: fournisseurId});
     });
    $('#compte_ca, #compte_marge, #compte_solde').html('0.00 €');
    $('span[id^=badgeStock_]').html('0');
  }

  // Gestionnaire d'évènement pour la fermeture de la fenêtre modale
  $('#modal').on('hidden.bs.modal', function(){
    $('#modal').removeClass('bs-example-modal-lg');
    $('.modal-dialog').removeClass('modal-lg');
    $('.modal-title').html('');
    $('#modal-body').html('');
    $('#modalConfirm').removeAttr('data');
  });

});
