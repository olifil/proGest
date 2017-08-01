$(document).ready(function(){

  const soundpath = '//codet-dev.fr/bundles/progest/sounds/';

  setFooter();
  $('a[id^=getFicheProduit]').on('click', function(event){
    event.preventDefault();
    var idProduit = getId(this);

    // Ajout du class Loader
    $('#widget_'+idProduit).children('p.edit').addClass('loader');
    $.get(Routing.generate('getFicheProduit', {id: idProduit})).done(function(response){
      $('#widget_'+idProduit).children('p.edit').removeClass('loader');
      $('.modal-body').html('');
      $('.modal-body').append(response);
      $('#Modal').modal('show');
    });
  });

  // Gestionnaire d'évènement pour la modification de la marge
  $('#editMarge').on('click', function(e){
      event.preventDefault();
      $.get(Routing.generate('boutique_marge_edit'))
       .done(function(response){
           $('.modal-title').html('Edition du taux de marge');
           $('.modal-body').html(response.html);
           $('#modalConfirm').attr('data', 'marge_edit');
           $('#modal').modal('show');
       });
  });

  // Gestionnaire des livraisons (btnQuantite_)
  $('button[id^=btnLivraison_]').on('click', function(){
    var idProduit =getId(this);
    $('#widget_'+idProduit).children('p.edit').addClass('loader');
    var data = {
      quantite: parseInt($('#inputQuantite_'+idProduit).val())
    }
    if (data.quantite == 0 ) { return; }
    if (data.quantite > 0) {
      // Gestion AJAX de la livraison
      $.post(Routing.generate('article_setLivraison', {id: idProduit}), data)
      .done(function(response){
        $('#widget_'+idProduit).children('p.edit').removeClass('loader');
        $('#badgeStock_'+idProduit).html(response.stock);
        $('#inputQuantite_'+idProduit).val(1);
      })
      .fail(function(response){
        alert(response.msg);
      });
    } else {
      // Correction des quantités livrées
      $.post(Routing.generate('article_editLivraison', {id: idProduit}), data)
       .done(function(response){
         // La mise à jour a réussi
       })
       .fail(function(response){
         // La mise à jour a échouée
         alert(response.msg);
       });
    };

  });

  // Gestionnaire des ventes (btnQuantite_)
  $('button[id^=btnVente_]').on('click', function(){
    var idProduit =getId(this);
    var quantiteVendu = parseInt($('#inputQuantite_'+idProduit).val());
    $('#widget_'+idProduit).children('p.edit').addClass('loader');
    // Gestion AJAX de la vente
    $.get(Routing.generate('setVente', {id: idProduit, quantite: quantiteVendu})).done(function(response){
      var stock = response.stock;
      $('#badgeStock_'+idProduit).html(stock);
      $('#inputQuantite_'+idProduit).attr('max', stock);
      $('#inputQuantite_'+idProduit).val(1);
      if (stock == 0){
        $('#inputQuantite_'+idProduit).attr('disabled', '');
        $('#btnVente_'+idProduit).attr('disabled', '');
      }
      $('#widget_'+idProduit).children('p.edit').removeClass('loader');
    });
  });

});

function isset() {
  //  discuss at: http://phpjs.org/functions/isset/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: FremyCompany
  // improved by: Onno Marsman
  // improved by: Rafał Kukawski
  //   example 1: isset( undefined, true);
  //   returns 1: false
  //   example 2: isset( 'Kevin van Zonneveld' );
  //   returns 2: true

  var a = arguments,
    l = a.length,
    i = 0,
    undef;

  if (l === 0) {
    throw new Error('Empty isset');
  }

  while (i !== l) {
    if (a[i] === undef || a[i] === null) {
      return false;
    }
    i++;
  }
  return true;
}

function empty(data)
{
  if(typeof(data) == 'number' || typeof(data) == 'boolean')
  {
    return false;
  }
  if(typeof(data) == 'undefined' || data === null)
  {
    return true;
  }
  if(typeof(data.length) != 'undefined')
  {
    return data.length == 0;
  }
  var count = 0;
  for(var i in data)
  {
    if(data.hasOwnProperty(i))
    {
      count ++;
    }
  }
  return count == 0;
}

// Fonction générale qui retourne l'id d'un produit
function getId(id){
  var lien = $(id).attr('id').split("_");
  var idProduit = lien[1];
  return parseInt(idProduit);
}

function setFooter(){
  var monmail = "mailto:";
  monmail += "contact@";
  monmail += "at2com.fr.fr?subject=Contact proGest";
  $('#monMail')
  .attr('href', monmail);
}

function alert(msg){
  // Message d'avertissement
  Lobibox.notify('error', {
      title: 'Erreur',
      msg: msg
  });
}

function success(){
  // Message success
  Lobibox.notify('success', {
      title: 'success',
      sound: false,
      msg: 'L\'opération demandée a été réalisée avec succès.'
  });
}

function getBaseURL() {
    var url = location.href;
    var baseURL = url.substring(0, url.indexOf('/', 14));

    if (baseURL.indexOf('http://localhost') != -1) {
        var pathname = location.pathname;
        var index1 = url.indexOf(pathname);
        var index2 = url.indexOf("/", index1 + 1);
        var baseLocalUrl = url.substr(0, index2);

        return baseLocalUrl;
    }
    else {
        return baseURL;
    }

}
