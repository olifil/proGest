$(document).ready(function(){

// PAGE // ::layout.html.twig
$('.toolbar').on('click', '#btnHomepage', function(event){
  console.log($(this).attr('id'));
  event.preventDefault();
  window.location = (Routing.generate('homepage'));
});

// PAGE : navigation.html.twig

    // Gestionnaire d'évènement sur le bouton BoutiqueAdd
    $('#btnBoutiqueAdd').on('click', function(){
      $('form[name=boutique]').submit();
    });

    $('#btnUndo').on('click', function(){
      console.log($(this).attr('id'));
      window.history.back();
    });

    // Gestionnaire d'évènement sur le bouton BoutiqueInit
    $('#btnBoutiqueInit').on('click', function(e){
      event.preventDefault();
      console.log($(this).attr('id'));
      $('.modal-title').html('Confirmation d\'action');
      $('.modal-body').html('Vous avez demandé l\'initialisation de la boutique.<br /> Voulez-vous poursuivre ?');
      $('#modalConfirm').attr('data', 'initBoutique');
      $('#modal').modal('show');
    });

    // Gestionnaire d'évènement sur le bouton btnCloreSaison
    $('#btnCloreSaison').on('click', function(e){
        event.preventDefault();
        console.log($(this).attr('id'));
        $('.modal-title').html('Confirmation d\'action');
        $('.modal-body').html('Vous avez demandé la clôture de l\'exercice.<br /> Cette opération va solder l\'ensemble des ventes et des comptes ?<br />Etes-vous sûr de vouloir poursuivre ?');
        $('#modalConfirm').attr('data', 'cloreSaison');
        $('#modal').modal('show');
    })

    // Gestionnaire d'évènement sur le bouton BoutiqueInitFileView
    $('#btnDaystate').on('click', function(){
      console.log($(this).attr('id'));
      window.open(Routing.generate('etat_quotidien'));
    });

    // Gestionnaire d'évènement sur le bouton BoutiqueInitFileView
    $('#btnBoutiqueInitFileView').on('click', function(){
      window.open(getBaseURL() + '/init.Progest.html');
    });

// PAGE : fournisseur:fournisseurs.html.twig

  // Gestionnaire d'évènement sur le bouton AddFournisseur
  $('.toolbar').on('click', '#btnAddFournisseur', function(event){
    console.log($(this).attr('id'));
    event.preventDefault();
    window.location = (Routing.generate('fournisseur-add'));
  });

  // Gestionnaire d'évènement pour le bouton PrintFournisseursList
  $('#btnPrintFournisseursList').on('click', function(){
    console.log($(this).attr('id'));
    window.open(Routing.generate('fournisseurs_print'));
  });

// PAGE :  fournisseur:add.html.twig

  // Gestionnaire d'évènement sur le bouton FournisseurSave
  $('.toolbar').on('click', '#btnFournisseurSave', function(){
    console.log($(this).attr('id'));
    $('form').submit();
  });


// PAGE : fournisseur:view.html.twig

  // Gestionnaire d'évènement sur le bouton IndexFournisseur
  $('.toolbar').on('click', '#btnIndexFournisseurs', function(){
    console.log($(this).attr('id'));
    window.location = (Routing.generate('fournisseurs'));
  });

  // Gestionnaire d'évènement sur le bouton EditFournisseur
  $('.toolbar').on('click', '#btnEditFournisseur', function(event){
    console.log($(this).attr('id'));
    event.preventDefault();
      var idFournisseur = $(this).attr('data-idFournisseur');
    window.location = (Routing.generate('fournisseur-edit', {id: idFournisseur}));
  });

  // Gestionnaire d'évènement sur le bouton NewProduit
  $('.toolbar').on('click', '#btnNewProduit', function(event){
    console.log($(this).attr('id'));
    event.preventDefault();
    var idFournisseur = $(this).attr('data-idFournisseur');
    window.location = (Routing.generate('article-add', {id: idFournisseur}));
  });

  // Gestionnaire d'évènement sur le bouton ProduitAdd de l'onglet produit
  $('#btnProduitAdd').on('click', function(event){
    console.log($(this).attr('id'));
    event.preventDefault();
    var idFournisseur = $(this).attr('data-idFournisseur');
    window.location = (Routing.generate('article-add', {id: idFournisseur}));
  });

  // Gestionnaire d'évènement pour le bouton FournisseurPrintTags
  $('#btnFournisseurPrintTags').on('click', function(){
    console.log($(this).attr('id'));
    $('#modalTags').modal('show');
  });

  // Gestionnaire d'évènement pour le bouton FournisseurEtat
  $('#btnFournisseurEtat').on('click', function(){
    console.log($(this).attr('id'));
    var fournisseurId = $('#fournisseurId').attr('data');
    window.open(Routing.generate('fournisseur_etat', {id: fournisseurId}));
  });

  // Gestionnaire d'évènement pour le bouton solde
  $('#btnSold').on('click', function(){
    console.log($(this).attr('id'));
    $('.modal-title').html('Solder un compte de Fournisseur');
    $('.modal-body').html('<p>Nous allons éditer l\'ensemble des mouvements de stocks et financiers concernant ce fournisseur.</p><p class="text-danger">Par ailleur, cette opération implique que nous allons supprimer de manière définitive l\'ensemble des données se rapportant aux articles (stocks) et aux ventes de ce fournisseur.</p><p>Etes-vous certain de vouloir continuer ?</p>');
    $('#modalConfirm').attr('data', 'SoldFournisseur');
    $('#modal').modal('show');
  });

// PAGE : fournisseur:edit.html.twig

  // Gestionnaire d'évènement sur le bouton NewProduit
  $('.toolbar').on('click', '#btnUndoEditFournisseur', function(event){
    console.log($(this).attr('id'));
    event.preventDefault();
    var idFournisseur = $(this).attr('data-idFournisseur');
    window.location = (Routing.generate('fournisseur-view', {id: idFournisseur}));
  });


// PAGE : article:articles.html.twig

  // Gestionnaire d'évènement sur le bouton AddArticle
  $('.toolbar').on('click', '#btnAddArticle', function(event){
    console.log($(this).attr('id'));
    event.preventDefault();
    window.location = (Routing.generate('article-add'));
  });

  // Gestionnaire d'évènement pour le bouton PrintArticlesList
  $('#btnPrintArticlesList').on('click', function(){
    console.log($(this).attr('id'));
    window.open(Routing.generate('articles_print'));
  });

// PAGE : article:add.html.twig

  // Gestionnaire d'évènement sur le bouton Save
  $('.toolbar').on('click', '#btnArticleSave', function(event){
    console.log($(this).attr('id'));
    event.preventDefault();
    $('form').submit();
  });

  // Gestionnaire d'évènement sur le bouton SaveAdd
  $('.toolbar').on('click', '#btnArticleSaveAdd', function(){
    console.log($(this).attr('id'));
    $('#form-add').val('true');
    $('form').submit();
  });

  // Gestionnaire d'évènement sur le bouton Undo
  $('.toolbar').on('click', '#btnArticleAddUndo', function(event){
    console.log($(this).attr('id'));
    event.preventDefault();
    var titre = $('h1').html().trim();
    switch (titre) {
      case "Création de la boutique":
        window.location = (Routing.generate('homepage'));
        break;
      case "Ajouter un fournisseur":
        window.location = (Routing.generate('fournisseurs'));
        break;
      case "Ajouter un article":
        window.location = (Routing.generate('articles'));
        break;
      default:
    }
    /*var idFournisseur = $(this).attr('data-idFournisseur')
    window.location = (Routing.generate('fournisseur-view', {id: idFournisseur}));*/
  });

  // Gestionnaire d'évènement sur le bouton InitBoutique
  $('.toolbar').on('click', '#btnInitBoutique', function(event){
    console.log($(this).attr('id'));
    event.preventDefault();
  });

// PAGE : article:edit.html.twig

  // Gestionnaire d'évènement sur le bouton ArticleEditSave
  $('.toolbar').on('click', '#btnArticleEditSave', function(){
    console.log($(this).attr('id'));
    $('form').submit();
  });

  // Gestionnaire d'évènement sur le bouton ArticleIndexFournisseur
  $('.toolbar').on('click', '#btnArticleEditUndo', function(){
    var idArticle = getId($('article[id^=idArticle_]'));
    window.location = (Routing.generate('article-view', {id: idArticle}));
  });


// PAGE : article:view.html.twig

  // Gestionnaire d'évènement sur le bouton ArticleIndexFournisseur
  $('.toolbar').on('click', '#btnArticleIndexFournisseur', function(){
    var idFournisseur = $(this).attr('data');
    window.location = (Routing.generate('fournisseur-view', {id: idFournisseur}));
  });

  // Gestionnaire d'évènement pour le bouton ArticlePrintTags
  $('#btnArticlePrintTags').on('click', function(){
    console.log($(this).attr('id'));
    $('#modalTags').modal('show');
  });

  // Gestionnaire d'évènement pour le bouton ArticlePrintTags
  $('#btnArticlesPrintTags').on('click', function(){
    console.log($(this).attr('id'));
    $('#modalTags').modal('show');
  });

});
