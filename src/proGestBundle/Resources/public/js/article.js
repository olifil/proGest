$(document).ready(function()
{

  // Gestionnaire d'évènement pour le bouton PrixMultipleAdd
  $('#btnPrixMultipleAdd').on('click', function(){
    console.log($(this).attr('id'));
    $('#loader').modal('show');
    $.get(Routing.generate('prixmultiple_getPrixMultipleForm'))
    .done(function(response){
      $('#modal-body').html(response.html);
      $('#modalConfirm').attr('data', 'PrixMultipleAdd');
      $('.modal-title').html('Paramétrage des prix multiples');
      $('#modal').modal('show');
    })
    .fail(function(response){
    })
    .always(function(){
      $('#loader').modal('hide');
    });
  });

  // Gestionnaire d'évènement pour le bouton PrixMultipleDel
  $('#tarifs-body').on('click', 'button[id^=btnPrixMultipleDel]', function(){
    console.log($(this).attr('id'));
    var idPrixMultiple = getId(this);
    $('.modal-title').html('Confirmation de suppression');
    $('.modal-title').attr('data', idPrixMultiple);
    $('#modal-body').html('Etes-vous certain de vouloir supprimer ce tarif ?');
    $('#modalConfirm').attr('data', 'PrixMultipleDel');
    $('#modal').modal('show');
  });

});
