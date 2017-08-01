$(document).ready(function(){


  // Gestionnaire d'évènement sur le bouton ArticleEdit
  $('#descriptif').on('click', '#btnArticleEdit', function(){
    console.log($(this).attr('id'));
    var idArticle = $('#articleId').attr('data');
    console.log(idArticle);
    $.get(Routing.generate('article-getEditForm', {id: idArticle}))
    .done(function(response){
      $('#modal').addClass('bs-example-modal-lg');
      $('.modal-dialog').addClass('modal-lg');
      $('.modal-title').html('Edition du produit');
      $('.modal-body').html(response.html);
      $('#modalConfirm').attr('data', 'articleEdit');
      $('#modal').modal('show');
    })
    .fail(function(response){

    });
  });

  // Gestionnaire d'évènement sur le bouton DelArticle
  $('#descriptif').on('click', '#btnArticleDel', function(){
    console.log($(this).attr('id'));
    $('.modal-title').html('Confirmation de suppression');
    $('.modal-body').html('Etes-vous certain de vouloir supprimer cet article ?');
    $('#modalConfirm').attr('data', 'articleDel');
    $('#modal').modal('show');
  });

});
